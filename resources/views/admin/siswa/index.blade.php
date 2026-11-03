@extends('layouts.desktop')
@section('title', 'Daftar Siswa')
@section('crumb', 'Siswa / Daftar Siswa')
@section('content')
<div class="row between" style="margin-bottom:6px"><div><h1>Daftar Siswa</h1><div class="sub" style="margin:0">Siswa beserta paket dan sisa kuotanya.</div></div>
  <a class="btn auto" href="{{ route('siswa.create') }}">+ Pendaftaran Baru</a></div>
<div class="card" style="margin-top:18px;padding:8px 12px">
<table>
  <thead><tr><th>Nama</th><th>Username</th><th>Telepon</th><th>Paket</th><th>Tgl Daftar</th><th>Sisa Kuota</th><th>Status</th></tr></thead>
  <tbody>
  @forelse($rows as $r)
    <tr>
      <td class="n">{{ $r->nama }}</td><td>{{ $r->username }}</td><td>{{ $r->nomor_telepon }}</td><td>{{ $r->nama_paket }}</td>
      <td>{{ \Illuminate\Support\Carbon::parse($r->tanggal_daftar)->format('d/m/Y') }}</td>
      <td>{{ $r->total_sesi - $r->sesi_terpakai }} / {{ $r->total_sesi }}</td>
      <td><span class="tag {{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
    </tr>
  @empty
    <tr><td colspan="7" class="muted">Belum ada siswa terdaftar.</td></tr>
  @endforelse
  </tbody>
</table>
</div>
@endsection
