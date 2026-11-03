<?php

namespace App\Http\Controllers;

use App\Models\PaketKursus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/** UC-01 Mendaftarkan Siswa Baru (Admin). Menyimpan akun, data siswa, dan pendaftaran paket sekaligus. */
class SiswaController extends Controller
{
    /** Daftar siswa beserta paket dan sisa kuotanya. */
    public function index()
    {
        $rows = DB::table('pendaftaran')
            ->join('siswa', 'siswa.id_siswa', '=', 'pendaftaran.id_siswa')
            ->join('pengguna', 'pengguna.id_pengguna', '=', 'siswa.id_pengguna')
            ->join('paket_kursus', 'paket_kursus.id_paket', '=', 'pendaftaran.id_paket')
            ->orderByDesc('pendaftaran.id_pendaftaran')
            ->select('siswa.nama', 'siswa.nomor_telepon', 'pengguna.username', 'paket_kursus.nama_paket',
                'pendaftaran.tanggal_daftar', 'pendaftaran.total_sesi', 'pendaftaran.sesi_terpakai', 'pendaftaran.status')
            ->get();

        return view('admin.siswa.index', compact('rows'));
    }

    public function create()
    {
        return view('admin.siswa.form', [
            'paket' => PaketKursus::where('status_aktif', true)->orderBy('nama_paket')->get(),
        ]);
    }

    public function store(Request $request)
    {
        // Validasi di server: required, format, unik, dan paket harus aktif
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'nomor_telepon' => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'username' => ['required', 'alpha_dash', 'min:4', 'max:50', 'unique:pengguna,username'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'id_paket' => ['required', 'integer', 'exists:paket_kursus,id_paket,status_aktif,1'],
            'tanggal_daftar' => ['required', 'date'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.max' => 'Nama lengkap maksimal 100 karakter.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'nomor_telepon.regex' => 'Format nomor telepon tidak valid. Contoh: 081234567890.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip, dan garis bawah.',
            'username.min' => 'Username minimal 4 karakter.',
            'username.unique' => 'Username sudah dipakai. Gunakan username lain.',
            'password.required' => 'Password awal wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'id_paket.required' => 'Paket kursus wajib dipilih.',
            'id_paket.exists' => 'Paket yang dipilih tidak tersedia atau sudah nonaktif.',
            'tanggal_daftar.required' => 'Tanggal daftar wajib diisi.',
            'tanggal_daftar.date' => 'Format tanggal daftar tidak valid.',
        ]);

        // Tiga tabel diisi dalam satu transaksi: gagal di tengah jalan -> semuanya dibatalkan.
        DB::transaction(function () use ($data) {
            $paket = DB::table('paket_kursus')->where('id_paket', $data['id_paket'])->first();

            $idPengguna = DB::table('pengguna')->insertGetId([
                'username' => $data['username'],
                'password_hash' => Hash::make($data['password']),
                'role' => 'siswa',
                'status_aktif' => 1,
            ]);

            $idSiswa = DB::table('siswa')->insertGetId([
                'id_pengguna' => $idPengguna,
                'nama' => $data['nama'],
                'nomor_telepon' => $data['nomor_telepon'],
                'alamat' => $data['alamat'] ?? null,
            ]);

            // total_sesi dan harga disalin dari paket saat daftar agar riwayat tidak berubah jika paket diedit
            DB::table('pendaftaran')->insert([
                'id_siswa' => $idSiswa,
                'id_paket' => $paket->id_paket,
                'tanggal_daftar' => $data['tanggal_daftar'],
                'total_sesi' => $paket->jumlah_sesi,
                'sesi_terpakai' => 0,
                'harga_dibayar' => $paket->harga,
                'status' => 'aktif',
            ]);
        });

        return redirect()->route('siswa.index')->with('sukses', 'Pendaftaran siswa berhasil disimpan. Paket kursus sudah aktif.');
    }
}
