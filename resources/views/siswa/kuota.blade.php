@extends('layouts.mobile')
@section('title', 'Sisa Kuota')
@section('content')
<div class="row head"><a href="{{ route('dashboard') }}" class="iconbtn back">@include('partials.icons', ['n' => 'back'])</a><h1>Sisa Kuota</h1></div>

@forelse($daftar as $d)
@php
  $sisa = $d->total_sesi - $d->sesi_terpakai;
  $dipesan = (int) ($terjadwal[$d->id_pendaftaran] ?? 0);
  $bisa = max($sisa - $dipesan, 0);
@endphp
<div class="card {{ $d->status === 'aktif' ? 'accent' : '' }}">
  <div class="row between"><span class="eyebrow">{{ $d->nama_paket }}</span><span class="tag {{ $d->status }}">{{ ucfirst($d->status) }}</span></div>
  <div class="row" style="align-items:baseline;gap:10px;margin-top:8px"><span class="bigno">{{ $sisa }}</span><span class="muted">/ {{ $d->total_sesi }} pertemuan</span></div>
  <div class="seg">@for($i = 1; $i <= $d->total_sesi; $i++)<i class="{{ $i <= $sisa ? 'on' : '' }}"></i>@endfor</div>
  <div style="margin-top:14px">
    <div class="kv"><span>Sesi selesai (terpakai)</span><b>{{ $d->sesi_terpakai }}</b></div>
    <div class="kv"><span>Sedang terjadwal</span><b>{{ $dipesan }}</b></div>
    <div class="kv"><span>Masih bisa dipesan</span><b style="color:var(--accent)">{{ $bisa }}</b></div>
  </div>
</div>
@empty
<div class="card muted">Anda belum memiliki paket kursus.</div>
@endforelse
<p class="muted" style="font-size:12px">Kuota berkurang setelah sesi dikonfirmasi selesai oleh pelatih (BR-05).</p>
@endsection
