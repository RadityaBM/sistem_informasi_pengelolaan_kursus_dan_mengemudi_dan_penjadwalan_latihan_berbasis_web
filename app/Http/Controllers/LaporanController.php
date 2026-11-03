<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** UC-09 Laporan Siswa & Transaksi dan UC-10 Rekap Produktivitas Pelatih (Owner). */
class LaporanController extends Controller
{
    private const PER_HALAMAN = 10;

    private function validasi(Request $request): array
    {
        return $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
            'page' => ['nullable', 'integer', 'min:1'],
        ], [
            'dari.date' => 'Format tanggal awal tidak valid.',
            'sampai.date' => 'Format tanggal akhir tidak valid.',
            'sampai.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);
    }

    private function query(array $f)
    {
        $q = DB::table('pendaftaran')
            ->join('siswa', 'siswa.id_siswa', '=', 'pendaftaran.id_siswa')
            ->join('paket_kursus', 'paket_kursus.id_paket', '=', 'pendaftaran.id_paket');

        if (! empty($f['dari'])) {
            $q->whereDate('pendaftaran.tanggal_daftar', '>=', $f['dari']);
        }
        if (! empty($f['sampai'])) {
            $q->whereDate('pendaftaran.tanggal_daftar', '<=', $f['sampai']);
        }

        return $q;
    }

    public function index(Request $request)
    {
        $f = $this->validasi($request);

        $jumlahSiswa = (clone $this->query($f))->distinct()->count('pendaftaran.id_siswa');
        $totalTransaksi = (clone $this->query($f))->sum('pendaftaran.harga_dibayar');
        $paketAktif = DB::table('paket_kursus')->where('status_aktif', 1)->count();

        $total = (clone $this->query($f))->count();
        $halaman = min(max((int) ($f['page'] ?? 1), 1), max((int) ceil($total / self::PER_HALAMAN), 1));

        $rows = $this->query($f)
            ->orderByDesc('pendaftaran.tanggal_daftar')->orderByDesc('pendaftaran.id_pendaftaran')
            ->offset(($halaman - 1) * self::PER_HALAMAN)->limit(self::PER_HALAMAN)
            ->select('siswa.nama', 'paket_kursus.nama_paket', 'pendaftaran.tanggal_daftar',
                'pendaftaran.harga_dibayar', 'pendaftaran.status')
            ->get();

        return view('owner.laporan', [
            'rows' => $rows, 'total' => $total, 'halaman' => $halaman,
            'jumlahHalaman' => max((int) ceil($total / self::PER_HALAMAN), 1),
            'perHalaman' => self::PER_HALAMAN, 'jumlahSiswa' => $jumlahSiswa,
            'totalTransaksi' => $totalTransaksi, 'paketAktif' => $paketAktif,
            'dari' => $f['dari'] ?? '', 'sampai' => $f['sampai'] ?? '',
        ]);
    }

    /** Ekspor CSV mengikuti filter yang sedang dipakai. */
    public function ekspor(Request $request)
    {
        $f = $this->validasi($request);

        $rows = $this->query($f)
            ->orderByDesc('pendaftaran.tanggal_daftar')
            ->select('siswa.nama', 'paket_kursus.nama_paket', 'pendaftaran.tanggal_daftar',
                'pendaftaran.harga_dibayar', 'pendaftaran.status')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No', 'Nama Siswa', 'Paket', 'Tanggal Daftar', 'Harga Dibayar', 'Status']);
            foreach ($rows as $i => $r) {
                fputcsv($out, [$i + 1, $r->nama, $r->nama_paket, $r->tanggal_daftar, $r->harga_dibayar, $r->status]);
            }
            fclose($out);
        }, 'laporan-siswa-transaksi.csv', ['Content-Type' => 'text/csv']);
    }

    /** UC-10: jumlah sesi selesai per pelatih pada periode tertentu (berdasarkan tanggal sesi). */
    public function produktivitas(Request $request)
    {
        $f = $this->validasi($request);

        $q = DB::table('pelatih')
            ->leftJoin('slot_jadwal', function ($j) use ($f) {
                $j->on('slot_jadwal.id_pelatih', '=', 'pelatih.id_pelatih');
                if (! empty($f['dari'])) {
                    $j->where('slot_jadwal.tanggal', '>=', $f['dari']);
                }
                if (! empty($f['sampai'])) {
                    $j->where('slot_jadwal.tanggal', '<=', $f['sampai']);
                }
            })
            ->leftJoin('booking', function ($j) {
                $j->on('booking.id_slot', '=', 'slot_jadwal.id_slot')->where('booking.status', 'selesai');
            })
            ->groupBy('pelatih.id_pelatih', 'pelatih.nama')
            ->orderByDesc('selesai')->orderBy('pelatih.nama')
            ->select('pelatih.nama', DB::raw('COUNT(booking.id_booking) as selesai'))
            ->get();

        return view('owner.produktivitas', [
            'rows' => $q, 'dari' => $f['dari'] ?? '', 'sampai' => $f['sampai'] ?? '',
        ]);
    }
}
