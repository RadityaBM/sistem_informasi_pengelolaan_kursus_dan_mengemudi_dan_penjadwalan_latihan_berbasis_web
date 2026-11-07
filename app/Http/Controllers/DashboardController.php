<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'siswa':
                $siswa = DB::table('siswa')->where('id_pengguna', $user->id_pengguna)->first();
                $daftar = $siswa ? DB::table('pendaftaran')
                    ->join('paket_kursus', 'paket_kursus.id_paket', '=', 'pendaftaran.id_paket')
                    ->where('id_siswa', $siswa->id_siswa)->where('pendaftaran.status', 'aktif')
                    ->select('pendaftaran.*', 'paket_kursus.nama_paket')
                    ->orderByDesc('id_pendaftaran')->first() : null;
                $berikut = $daftar ? DB::table('booking')
                    ->join('slot_jadwal', 'slot_jadwal.id_slot', '=', 'booking.id_slot')
                    ->join('pelatih', 'pelatih.id_pelatih', '=', 'slot_jadwal.id_pelatih')
                    ->join('mobil', 'mobil.id_mobil', '=', 'slot_jadwal.id_mobil')
                    ->where('booking.id_pendaftaran', $daftar->id_pendaftaran)
                    ->where('booking.status', 'terjadwal')
                    ->orderBy('tanggal')->orderBy('jam_mulai')
                    ->first() : null;
                return view('siswa.dashboard', compact('siswa', 'daftar', 'berikut'));

            case 'pelatih':
                $pelatih = DB::table('pelatih')->where('id_pengguna', $user->id_pengguna)->first();
                $hariIni = $pelatih ? DB::table('slot_jadwal')
                    ->join('booking', function ($j) {
                        $j->on('booking.id_slot', '=', 'slot_jadwal.id_slot')->where('booking.status', 'terjadwal');
                    })
                    ->where('id_pelatih', $pelatih->id_pelatih)->whereDate('tanggal', today())->count() : 0;
                return view('pelatih.dashboard', compact('pelatih', 'hariIni'));

            case 'owner':
                $jumlahSiswa = DB::table('siswa')->count();
                $totalTransaksi = DB::table('pendaftaran')->sum('harga_dibayar');
                $perPelatih = DB::table('pelatih')
                    ->leftJoin('slot_jadwal', 'slot_jadwal.id_pelatih', '=', 'pelatih.id_pelatih')
                    ->leftJoin('booking', function ($j) {
                        $j->on('booking.id_slot', '=', 'slot_jadwal.id_slot')->where('booking.status', 'selesai');
                    })
                    ->groupBy('pelatih.id_pelatih', 'pelatih.nama')
                    ->select('pelatih.nama', DB::raw('COUNT(booking.id_booking) as selesai'))
                    ->get();
                return view('owner.dashboard', compact('jumlahSiswa', 'totalTransaksi', 'perPelatih'));

            default: // admin
                $ringkas = [
                    'Siswa' => DB::table('siswa')->count(),
                    'Pelatih' => DB::table('pelatih')->count(),
                    'Mobil' => DB::table('mobil')->count(),
                    'Paket Kursus' => DB::table('paket_kursus')->count(),
                ];
                return view('admin.dashboard', compact('ringkas'));
        }
    }
}
