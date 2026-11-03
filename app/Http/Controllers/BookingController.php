<?php

namespace App\Http\Controllers;

use App\Exceptions\AturanBisnisException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * CORE MODULE: UC-04 Melakukan Booking Jadwal Latihan (+ UC-03 lihat jadwal kosong, UC-05 batal booking).
 * Alur: Start -> Input -> Validation -> Processing -> Database -> Result
 */
class BookingController extends Controller
{
    /** Pendaftaran aktif milik siswa yang sedang login (dipakai oleh semua method). */
    private function pendaftaranAktif()
    {
        $siswa = DB::table('siswa')->where('id_pengguna', auth()->id())->first();
        if (! $siswa) {
            return null;
        }

        return DB::table('pendaftaran')
            ->join('paket_kursus', 'paket_kursus.id_paket', '=', 'pendaftaran.id_paket')
            ->where('pendaftaran.id_siswa', $siswa->id_siswa)
            ->where('pendaftaran.status', 'aktif')
            ->select('pendaftaran.*', 'paket_kursus.transmisi', 'paket_kursus.nama_paket')
            ->orderByDesc('pendaftaran.id_pendaftaran')
            ->first();
    }

    /** Kuota yang masih boleh dipesan = total - terpakai - yang sedang terjadwal. */
    private function kuotaTersedia($daftar): int
    {
        $terjadwal = DB::table('booking')
            ->where('id_pendaftaran', $daftar->id_pendaftaran)
            ->where('status', 'terjadwal')->count();

        return $daftar->total_sesi - $daftar->sesi_terpakai - $terjadwal;
    }

    /** UC-03: halaman jadwal kosong. */
    public function index(Request $request)
    {
        $daftar = $this->pendaftaranAktif();
        $pelatih = DB::table('pelatih')->where('status_aktif', 1)->orderBy('nama')->get();

        $request->validate([
            'id_pelatih' => ['nullable', 'integer'],
            'tanggal' => ['nullable', 'date'],
        ], ['tanggal.date' => 'Format tanggal tidak valid.']);

        $slots = collect();
        if ($daftar && $request->filled('id_pelatih') && $request->filled('tanggal')) {
            $slots = DB::table('slot_jadwal')
                ->join('mobil', 'mobil.id_mobil', '=', 'slot_jadwal.id_mobil')
                ->where('slot_jadwal.id_pelatih', $request->id_pelatih)
                ->whereDate('slot_jadwal.tanggal', $request->tanggal)
                ->where('mobil.transmisi', $daftar->transmisi) // transmisi mobil harus sesuai paket
                ->where('mobil.status', 'tersedia')
                ->orderBy('jam_mulai')
                ->select('slot_jadwal.*', 'mobil.merek_model')
                ->get();
        }

        return view('siswa.booking', [
            'daftar' => $daftar,
            'kuota' => $daftar ? $this->kuotaTersedia($daftar) : 0,
            'pelatih' => $pelatih,
            'slots' => $slots,
        ]);
    }

    /** UC-04: proses booking. Seluruh pemeriksaan BR dilakukan di server, dalam satu transaksi. */
    public function store(Request $request)
    {
        // 1. Input + validasi format
        $data = $request->validate(
            ['id_slot' => ['required', 'integer', 'exists:slot_jadwal,id_slot']],
            ['id_slot.required' => 'Pilih salah satu slot jadwal terlebih dahulu.',
             'id_slot.exists' => 'Slot yang dipilih tidak ditemukan.']
        );

        $idBooking = null;

        try {
            // 2-4. Validasi business rule + proses + simpan ke database (atomik)
            DB::transaction(function () use ($data, &$idBooking) {
                $daftar = $this->pendaftaranAktif();

                // BR-01: siswa harus terdaftar dan punya paket aktif
                if (! $daftar) {
                    throw new AturanBisnisException('Anda belum memiliki paket kursus aktif. Hubungi admin untuk mendaftar.');
                }

                // kunci baris pendaftaran & slot agar dua booking bersamaan tidak lolos bersama
                $daftar = DB::table('pendaftaran')->where('id_pendaftaran', $daftar->id_pendaftaran)->lockForUpdate()->first()
                    ?? $daftar;
                $paketTransmisi = DB::table('paket_kursus')->where('id_paket', $daftar->id_paket)->value('transmisi');

                // BR-03: harus masih punya sisa kuota
                if ($this->kuotaTersedia($daftar) <= 0) {
                    throw new AturanBisnisException('Kuota pertemuan Anda sudah habis. Silakan hubungi admin untuk menambah paket.');
                }

                $slot = DB::table('slot_jadwal')
                    ->join('mobil', 'mobil.id_mobil', '=', 'slot_jadwal.id_mobil')
                    ->where('slot_jadwal.id_slot', $data['id_slot'])
                    ->select('slot_jadwal.*', 'mobil.transmisi')
                    ->lockForUpdate()->first();

                // BR-02: satu slot hanya untuk satu siswa
                if ($slot->status !== 'tersedia') {
                    throw new AturanBisnisException('Slot sudah terisi. Silakan pilih slot lain.');
                }
                if ($slot->transmisi !== $paketTransmisi) {
                    throw new AturanBisnisException('Mobil pada slot ini tidak sesuai dengan transmisi paket Anda.');
                }
                if (Carbon::parse($slot->tanggal.' '.$slot->jam_mulai)->isPast()) {
                    throw new AturanBisnisException('Slot ini sudah lewat. Silakan pilih slot yang akan datang.');
                }

                $idBooking = DB::table('booking')->insertGetId([
                    'id_slot' => $slot->id_slot,
                    'id_pendaftaran' => $daftar->id_pendaftaran,
                    'waktu_booking' => now(),
                    'status' => 'terjadwal',
                ]);
                DB::table('slot_jadwal')->where('id_slot', $slot->id_slot)->update(['status' => 'terpesan']);
                // BR-05: kuota TIDAK dikurangi di sini, baru saat sesi dikonfirmasi selesai
            });
        } catch (AturanBisnisException $e) {
            // 5. Result (gagal): pesan spesifik, daftar slot dimuat ulang
            return back()->with('galat', $e->getMessage());
        }

        // 5. Result (berhasil): layar Booking Berhasil (High Fidelity-03b)
        return redirect()->route('booking.berhasil', $idBooking);
    }

    /** Layar Booking Berhasil: detail sesi yang baru dipesan (hanya milik sendiri). */
    public function berhasil($id)
    {
        $siswa = DB::table('siswa')->where('id_pengguna', auth()->id())->first();

        $b = DB::table('booking')
            ->join('pendaftaran', 'pendaftaran.id_pendaftaran', '=', 'booking.id_pendaftaran')
            ->join('slot_jadwal', 'slot_jadwal.id_slot', '=', 'booking.id_slot')
            ->join('pelatih', 'pelatih.id_pelatih', '=', 'slot_jadwal.id_pelatih')
            ->join('mobil', 'mobil.id_mobil', '=', 'slot_jadwal.id_mobil')
            ->where('booking.id_booking', $id)
            ->where('pendaftaran.id_siswa', $siswa->id_siswa ?? 0)
            ->select('booking.*', 'slot_jadwal.tanggal', 'slot_jadwal.jam_mulai', 'slot_jadwal.jam_selesai',
                'pelatih.nama as nama_pelatih', 'mobil.merek_model', 'mobil.plat_nomor')
            ->first();

        abort_if(! $b, 404);

        return view('siswa.berhasil', compact('b'));
    }

    /** UC-06: Sisa kuota. */
    public function kuota()
    {
        $siswa = DB::table('siswa')->where('id_pengguna', auth()->id())->first();

        $daftar = $siswa ? DB::table('pendaftaran')
            ->join('paket_kursus', 'paket_kursus.id_paket', '=', 'pendaftaran.id_paket')
            ->where('pendaftaran.id_siswa', $siswa->id_siswa)
            ->select('pendaftaran.*', 'paket_kursus.nama_paket', 'paket_kursus.transmisi')
            ->orderByDesc('pendaftaran.id_pendaftaran')->get() : collect();

        $terjadwal = DB::table('booking')->where('status', 'terjadwal')
            ->whereIn('id_pendaftaran', $daftar->pluck('id_pendaftaran'))
            ->selectRaw('id_pendaftaran, COUNT(*) as n')->groupBy('id_pendaftaran')
            ->pluck('n', 'id_pendaftaran');

        return view('siswa.kuota', compact('daftar', 'terjadwal'));
    }

    /** Riwayat booking milik siswa. */
    public function saya()
    {
        $daftar = $this->pendaftaranAktif();
        $siswa = DB::table('siswa')->where('id_pengguna', auth()->id())->first();

        $bookings = $siswa ? DB::table('booking')
            ->join('pendaftaran', 'pendaftaran.id_pendaftaran', '=', 'booking.id_pendaftaran')
            ->join('slot_jadwal', 'slot_jadwal.id_slot', '=', 'booking.id_slot')
            ->join('pelatih', 'pelatih.id_pelatih', '=', 'slot_jadwal.id_pelatih')
            ->join('mobil', 'mobil.id_mobil', '=', 'slot_jadwal.id_mobil')
            ->where('pendaftaran.id_siswa', $siswa->id_siswa)
            ->orderByDesc('slot_jadwal.tanggal')->orderByDesc('slot_jadwal.jam_mulai')
            ->select('booking.*', 'slot_jadwal.tanggal', 'slot_jadwal.jam_mulai', 'slot_jadwal.jam_selesai',
                'pelatih.nama as nama_pelatih', 'mobil.merek_model')
            ->get() : collect();

        return view('siswa.booking_saya', [
            'bookings' => $bookings,
            'daftar' => $daftar,
            'kuota' => $daftar ? $this->kuotaTersedia($daftar) : 0,
        ]);
    }

    /** UC-05: batalkan booking (hanya milik sendiri, status terjadwal, sesi belum dimulai). */
    public function batal($id)
    {
        $siswa = DB::table('siswa')->where('id_pengguna', auth()->id())->first();

        try {
            DB::transaction(function () use ($id, $siswa) {
                $b = DB::table('booking')
                    ->join('pendaftaran', 'pendaftaran.id_pendaftaran', '=', 'booking.id_pendaftaran')
                    ->join('slot_jadwal', 'slot_jadwal.id_slot', '=', 'booking.id_slot')
                    ->where('booking.id_booking', $id)
                    ->where('pendaftaran.id_siswa', $siswa->id_siswa ?? 0) // hanya milik sendiri
                    ->select('booking.*', 'slot_jadwal.tanggal', 'slot_jadwal.jam_mulai')
                    ->lockForUpdate()->first();

                if (! $b) {
                    throw new AturanBisnisException('Booking tidak ditemukan.');
                }
                if ($b->status !== 'terjadwal') {
                    throw new AturanBisnisException('Hanya booking berstatus terjadwal yang dapat dibatalkan.');
                }
                if (Carbon::parse($b->tanggal.' '.$b->jam_mulai)->isPast()) {
                    throw new AturanBisnisException('Sesi sudah dimulai atau lewat, booking tidak dapat dibatalkan.');
                }

                DB::table('booking')->where('id_booking', $b->id_booking)->update(['status' => 'dibatalkan']);
                DB::table('slot_jadwal')->where('id_slot', $b->id_slot)->update(['status' => 'tersedia']);
            });
        } catch (AturanBisnisException $e) {
            return back()->with('galat', $e->getMessage());
        }

        return back()->with('sukses', 'Booking berhasil dibatalkan. Slot kembali tersedia.');
    }
}
