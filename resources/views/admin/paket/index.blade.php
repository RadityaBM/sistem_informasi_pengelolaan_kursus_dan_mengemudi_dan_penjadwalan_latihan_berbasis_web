@extends('layouts.desktop')
@section('title', 'Paket Kursus')
@section('crumb', 'Data Master / Paket Kursus')
@section('content')
<div class="row between" style="margin-bottom:6px"><div><h1>Paket Kursus</h1><div class="sub" style="margin:0">Kelola paket yang ditawarkan.</div></div>
  <a class="btn auto" href="{{ route('paket.create') }}">+ Tambah Paket</a></div>
<div class="card" style="margin-top:18px;padding:8px 12px">
<table>
  <thead><tr><th>ID</th><th>Nama Paket</th><th>Transmisi</th><th>Sesi</th><th>Harga</th><th>Status</th><th>Aksi</th></tr></thead>
  <tbody>
  @forelse($paket as $p)
    <tr>
      <td>{{ $p->id_paket }}</td>
      <td class="n">{{ $p->nama_paket }}</td>
      <td>{{ ucfirst($p->transmisi) }}</td>
      <td>{{ $p->jumlah_sesi }}</td>
      <td>Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
      <td><span class="tag {{ $p->status_aktif ? 'aktif' : 'nonaktif' }}">{{ $p->status_aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
      <td><div class="row" style="gap:8px">
        <a class="btn sec sm" href="{{ route('paket.edit', $p) }}">Edit</a>
        <form method="post" action="{{ route('paket.destroy', $p) }}" onsubmit="return confirm('Hapus paket {{ $p->nama_paket }}?')">
          @csrf @method('DELETE')<button class="btn danger sm" type="submit">Hapus</button></form></div></td>
    </tr>
  @empty
    <tr><td colspan="7" class="muted">Belum ada data paket.</td></tr>
  @endforelse
  </tbody>
</table>
</div>
@endsection
