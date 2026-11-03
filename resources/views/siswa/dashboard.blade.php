@extends('layouts.mobile')
@section('title', 'Beranda Siswa')
@section('content')
@php
  $nama = $siswa->nama ?? auth()->user()->username;
  $ini = collect(explode(' ', $nama))->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
@endphp
<div class="row between head">
  <div class="row"><div class="avatar">{{ $ini }}</div><div><div class="muted" style="font-size:12px">Halo,</div><b style="font:700 17px var(--font-head)">{{ $nama }}</b></div></div>
  <div class="row" style="gap:8px">@if($daftar)<span class="tag aktif">{{ $daftar->nama_paket }}</span>@endif
    <form method="post" action="{{ route('logout') }}" class="rsp-hide" style="margin:0">@csrf<button class="iconbtn" type="submit" title="Keluar">@include('partials.icons', ['n' => 'out'])</button></form></div>
</div>

@if($daftar)
@php $sisa = $daftar->total_sesi - $daftar->sesi_terpakai; @endphp
<div class="card">
  <div class="eyebrow">Sisa kuota</div>
  <div class="row" style="align-items:baseline;gap:10px;margin-top:8px"><span class="bigno">{{ $sisa }}</span><span class="muted">/ {{ $daftar->total_sesi }} pertemuan</span></div>
  <div class="seg">@for($i = 1; $i <= $daftar->total_sesi; $i++)<i class="{{ $i <= $sisa ? 'on' : '' }}"></i>@endfor</div>
</div>

<div class="card accent">
  <div class="row between"><span class="eyebrow">Sesi berikutnya</span>@if($berikut)<span class="tag terjadwal">Terjadwal</span>@endif</div>
  @if($berikut)
    @php $ip = collect(explode(' ', $berikut->nama_pelatih))->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode(''); @endphp
    <h2 style="font-size:20px;margin:10px 0 6px">{{ \Illuminate\Support\Carbon::parse($berikut->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</h2>
    <div class="row muted" style="gap:6px">@include('partials.icons', ['n' => 'clock']) {{ substr($berikut->jam_mulai,0,5) }} - {{ substr($berikut->jam_selesai,0,5) }}</div>
    <div class="row" style="margin-top:12px;gap:10px"><div class="avatar" style="width:30px;height:30px;font-size:10px;border-color:var(--line)">{{ $ip }}</div>
      <div><b>{{ $berikut->nama_pelatih }}</b><div class="muted" style="font-size:12px">Mobil: {{ $berikut->merek_model }}</div></div></div>
  @else
    <p class="muted" style="margin:10px 0 0">Belum ada sesi terjadwal.</p>
  @endif
</div>

<div class="card hero">
  <div class="t"><b>Siap latihan?</b><span class="muted" style="font-size:12px">Pilih slot terbaikmu.</span></div>
  @include('partials.car')
</div>

<a class="btn" href="{{ route('booking.index') }}" style="margin-bottom:10px">Booking Jadwal @include('partials.icons', ['n' => 'arrow'])</a>
<a class="btn sec" href="{{ route('booking.saya') }}">Lihat Booking Saya</a>
@else
<div class="card">Anda belum memiliki paket kursus aktif. Hubungi admin untuk mendaftar.</div>
@endif
@endsection
