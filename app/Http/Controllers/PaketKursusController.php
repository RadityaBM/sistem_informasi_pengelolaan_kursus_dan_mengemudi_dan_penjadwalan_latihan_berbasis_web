<?php

namespace App\Http\Controllers;

use App\Models\PaketKursus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** CRUD data master Paket Kursus (UC-02, BR-06: hanya Admin). */
class PaketKursusController extends Controller
{
    private function aturan(?int $id = null): array
    {
        return [
            'nama_paket' => ['required', 'string', 'max:100',
                Rule::unique('paket_kursus', 'nama_paket')->ignore($id, 'id_paket')],
            'transmisi' => ['required', Rule::in(['manual', 'matic'])],
            'jumlah_sesi' => ['required', 'integer', 'min:1', 'max:100'],
            'harga' => ['required', 'numeric', 'min:0'],
        ];
    }

    private function pesan(): array
    {
        return [
            'nama_paket.required' => 'Nama paket wajib diisi.',
            'nama_paket.unique' => 'Nama paket sudah dipakai. Gunakan nama lain.',
            'nama_paket.max' => 'Nama paket maksimal 100 karakter.',
            'transmisi.required' => 'Transmisi wajib dipilih.',
            'transmisi.in' => 'Transmisi harus manual atau matic.',
            'jumlah_sesi.required' => 'Jumlah sesi wajib diisi.',
            'jumlah_sesi.integer' => 'Jumlah sesi harus berupa angka bulat.',
            'jumlah_sesi.min' => 'Jumlah sesi harus lebih besar dari 0.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga tidak boleh negatif.',
        ];
    }

    public function index()
    {
        return view('admin.paket.index', ['paket' => PaketKursus::orderBy('id_paket')->get()]);
    }

    public function create()
    {
        return view('admin.paket.form', ['paket' => new PaketKursus(['status_aktif' => true])]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(), $this->pesan());
        $data['status_aktif'] = $request->boolean('status_aktif');
        PaketKursus::create($data);

        return redirect()->route('paket.index')->with('sukses', 'Data paket kursus berhasil disimpan.');
    }

    public function edit(PaketKursus $paket)
    {
        return view('admin.paket.form', compact('paket'));
    }

    public function update(Request $request, PaketKursus $paket)
    {
        $data = $request->validate($this->aturan($paket->id_paket), $this->pesan());
        $data['status_aktif'] = $request->boolean('status_aktif');
        $paket->update($data);

        return redirect()->route('paket.index')->with('sukses', 'Data paket kursus berhasil diperbarui.');
    }

    public function destroy(PaketKursus $paket)
    {
        // Perlindungan relationship: paket yang sudah dipakai pendaftaran tidak boleh dihapus.
        if (DB::table('pendaftaran')->where('id_paket', $paket->id_paket)->exists()) {
            return redirect()->route('paket.index')
                ->with('galat', 'Paket tidak dapat dihapus karena sudah dipakai pendaftaran. Nonaktifkan paket jika tidak ingin ditawarkan lagi.');
        }

        $paket->delete();

        return redirect()->route('paket.index')->with('sukses', 'Data paket kursus berhasil dihapus.');
    }
}
