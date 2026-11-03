<?php

namespace App\Http\Controllers;

use App\Exceptions\AturanBisnisException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/** UC-07 Kalender Pelatih dan UC-08 Konfirmasi Sesi Selesai (BR-04, BR-05). */
class SesiController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['tanggal' => ['nullable', 'date']], ['tanggal.date' => 'Format tanggal tidak valid.']);
        // Mode "Konfirmasi": semua sesi terjadwal yang sudah berlangsung dan belum dikonfirmasi
        $belum = $request->boolean('belum');
        $tanggal = $request->input('tanggal', today()->toDateString());
        $user = auth()->user();
        $pelatih = DB::table('pelatih')->where('id_pengguna', $user->id_pengguna)->first();

        $query = DB::table('booking')
            ->join('slot_jadwal', 'slot_jadwal.id_slot', '=', 'booking.id_slot')
            ->join('pendaftaran', 'pendaftaran.id_pendaftaran', '=', 'booking.id_pendaftaran')
            ->join('siswa', 'siswa.id_siswa', '=', 'pendaftaran.id_siswa')
            ->join('mobil', 'mobil.id_mobil', '=', 'slot_jadwal.id_mobil')
            ->join('pelatih', 'pelatih.id_pelatih', '=', 'slot_jadwal.id_pelatih')
            ->where('booking.status', '!=', 'dibatalkan')
            ->orderBy('slot_jadwal.tanggal')
            ->orderBy('slot_jadwal.jam_mulai')
            ->select('booking.*', 'slot_jadwal.tanggal', 'slot_jadwal.jam_mulai', 'slot_jadwal.jam_selesai',
                'siswa.nama as nama_siswa', 'mobil.merek_model', 'pelatih.nama as nama_pelatih');

        if ($belum) {
            $query->where('booking.status', 'terjadwal')
                ->whereRaw('TIMESTAMP(slot_jadwal.tanggal, slot_jadwal.jam_mulai) <= NOW()');
        } else {
            $query->whereDate('slot_jadwal.tanggal', $tanggal);
        }

        // Admin melihat semua sesi; Pelatih hanya sesi miliknya sendiri
        if ($user->role !== 'admin') {
            $query->where('slot_jadwal.id_pelatih', $pelatih->id_pelatih ?? 0);
        }
        $sesi = $query->get();

        if ($user->role === 'admin') {
            return view('admin.sesi', compact('sesi', 'tanggal'));
        }

        return view('pelatih.jadwal', compact('sesi', 'tanggal', 'belum'));
    }

    public function selesai($id)
    {
        $user = auth()->user();

        try {
            DB::transaction(function () use ($id, $user) {
                $b = DB::table('booking')
                    ->join('slot_jadwal', 'slot_jadwal.id_slot', '=', 'booking.id_slot')
                    ->where('booking.id_booking', $id)
                    ->select('booking.*', 'slot_jadwal.id_pelatih', 'slot_jadwal.tanggal', 'slot_jadwal.jam_mulai')
                    ->lockForUpdate()->first();

                if (! $b) {
                    throw new AturanBisnisException('Sesi tidak ditemukan.');
                }

                // BR-04: hanya pelatih yang bersangkutan (atau admin) yang boleh menyelesaikan sesi
                $pelatih = DB::table('pelatih')->where('id_pengguna', $user->id_pengguna)->first();
                if ($user->role !== 'admin' && (! $pelatih || $pelatih->id_pelatih !== $b->id_pelatih)) {
                    throw new AturanBisnisException('Anda bukan pelatih pada sesi ini, sehingga tidak dapat mengonfirmasinya.');
                }
                if ($b->status !== 'terjadwal') {
                    throw new AturanBisnisException('Sesi ini sudah dikonfirmasi atau dibatalkan sebelumnya.');
                }
                if (Carbon::parse($b->tanggal.' '.$b->jam_mulai)->isFuture()) {
                    throw new AturanBisnisException('Sesi belum berlangsung, belum dapat ditandai selesai.');
                }

                DB::table('booking')->where('id_booking', $b->id_booking)->update([
                    'status' => 'selesai',
                    'waktu_selesai' => now(),
                    'dikonfirmasi_oleh' => $user->id_pengguna,
                ]);

                // BR-05 / UC-12: kuota baru berkurang setelah sesi dikonfirmasi selesai
                DB::table('pendaftaran')->where('id_pendaftaran', $b->id_pendaftaran)->increment('sesi_terpakai');
                DB::table('pendaftaran')
                    ->where('id_pendaftaran', $b->id_pendaftaran)
                    ->whereColumn('sesi_terpakai', '>=', 'total_sesi')
                    ->update(['status' => 'selesai']);
            });
        } catch (AturanBisnisException $e) {
            return back()->with('galat', $e->getMessage());
        }

        return back()->with('sukses', 'Sesi ditandai selesai dan kuota siswa berkurang 1 pertemuan.');
    }
}
