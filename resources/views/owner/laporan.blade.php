@extends('layouts.desktop')
@section('title', 'Laporan Siswa & Transaksi')
@section('crumb', 'Laporan / Laporan Siswa & Transaksi')
@section('content')
<h1>Laporan Siswa &amp; Transaksi</h1>
<div class="sub">Pantau jumlah siswa dan total transaksi per periode.</div>

<form method="get" action="{{ route('laporan.index') }}" class="filters">
  <div class="field"><label for="dari">Dari tanggal</label><div class="inp">@include('partials.icons', ['n' => 'cal'])<input id="dari" type="date" name="dari" value="{{ $dari }}"></div></div>
  <div class="field"><label for="sampai">Sampai tanggal</label><div class="inp">@include('partials.icons', ['n' => 'cal'])<input id="sampai" type="date" name="sampai" value="{{ $sampai }}"></div></div>
  <button class="btn auto" type="submit" style="height:46px">Tampilkan</button>
  <a class="btn sec auto" style="margin-left:auto;height:46px" href="{{ route('laporan.ekspor', array_filter(['dari' => $dari, 'sampai' => $sampai])) }}">@include('partials.icons', ['n' => 'dl'])Ekspor</a>
</form>
@if($errors->any())<div class="alert bad">@include('partials.icons', ['n' => 'alert'])<span>{{ $errors->first() }}</span></div>@endif

<div class="grid" style="margin-bottom:22px">
  <div class="stat"><div class="eyebrow">Jumlah siswa</div><div class="v">{{ $jumlahSiswa }}</div></div>
  <div class="stat hot"><div class="eyebrow">Total transaksi</div><div class="v">Rp {{ number_format($totalTransaksi, 0, ',', '.') }}</div></div>
  <div class="stat"><div class="eyebrow">Paket aktif</div><div class="v">{{ $paketAktif }}</div></div>
</div>

<div class="sec-title">Rincian pendaftaran &amp; transaksi</div>
<div class="card" style="padding:8px 12px">
<table>
  <thead><tr><th>No</th><th>Nama Siswa</th><th>Paket</th><th>Tanggal Daftar</th><th>Harga Dibayar</th><th>Status</th></tr></thead>
  <tbody>
  @forelse($rows as $i => $r)
    <tr>
      <td>{{ ($halaman - 1) * $perHalaman + $i + 1 }}</td>
      <td class="n">{{ $r->nama }}</td><td>{{ $r->nama_paket }}</td>
      <td>{{ \Illuminate\Support\Carbon::parse($r->tanggal_daftar)->format('d/m/Y') }}</td>
      <td>Rp {{ number_format($r->harga_dibayar, 0, ',', '.') }}</td>
      <td><span class="tag {{ $r->status }}">{{ ucfirst($r->status) }}</span></td>
    </tr>
  @empty
    <tr><td colspan="6" class="muted">Tidak ada data pada periode ini.</td></tr>
  @endforelse
  </tbody>
</table>
</div>

<div class="row between" style="margin-bottom:18px">
  <span class="muted" style="font-size:12px">@if($total > 0)Menampilkan {{ ($halaman - 1) * $perHalaman + 1 }}-{{ min($halaman * $perHalaman, $total) }} dari {{ $total }} data @else Tidak ada data @endif</span>
  <div class="pager">
    @if($halaman > 1)<a href="{{ route('laporan.index', array_filter(['dari' => $dari, 'sampai' => $sampai, 'page' => $halaman - 1])) }}">&lsaquo;</a>@endif
    @for($p = 1; $p <= $jumlahHalaman; $p++)
      @if($p === $halaman)<span class="cur">{{ $p }}</span>@else<a href="{{ route('laporan.index', array_filter(['dari' => $dari, 'sampai' => $sampai, 'page' => $p])) }}">{{ $p }}</a>@endif
    @endfor
    @if($halaman < $jumlahHalaman)<a href="{{ route('laporan.index', array_filter(['dari' => $dari, 'sampai' => $sampai, 'page' => $halaman + 1])) }}">&rsaquo;</a>@endif
  </div>
</div>
<a class="btn sec auto" href="{{ route('laporan.produktivitas', array_filter(['dari' => $dari, 'sampai' => $sampai])) }}">Lihat rekap produktivitas pelatih @include('partials.icons', ['n' => 'arrow'])</a>
@endsection
