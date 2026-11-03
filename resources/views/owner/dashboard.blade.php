@extends('layouts.desktop')
@section('title', 'Dashboard Owner')
@section('crumb', 'Dashboard')
@section('content')
<h1>Dashboard Owner</h1><div class="sub">Pantau kinerja kursus.</div>
<div class="grid" style="margin-bottom:14px">
  <div class="card accent"><div class="eyebrow">Jumlah siswa</div><div class="num">{{ $jumlahSiswa }}</div></div>
  <div class="card accent"><div class="eyebrow">Total transaksi</div><div class="num">Rp {{ number_format($totalTransaksi, 0, ',', '.') }}</div></div>
</div>
<div class="card"><h2 style="font-size:18px;margin-bottom:10px">Sesi selesai per pelatih</h2>
  <table><thead><tr><th>Pelatih</th><th>Sesi selesai</th></tr></thead><tbody>
  @foreach($perPelatih as $r)<tr><td class="n">{{ $r->nama }}</td><td>{{ $r->selesai }}</td></tr>@endforeach
  </tbody></table></div>
@endsection
