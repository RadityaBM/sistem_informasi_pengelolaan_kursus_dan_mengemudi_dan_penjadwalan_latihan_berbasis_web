@extends('layouts.mobile')
@section('title', 'Booking Saya')
@section('content')
@php
  $mendatang = $bookings->where('status', 'terjadwal')->sortBy(fn ($b) => $b->tanggal.' '.$b->jam_mulai);
  $riwayat = $bookings->where('status', '!=', 'terjadwal');
@endphp
<div class="row head"><a href="{{ route('dashboard') }}" class="iconbtn back">@include('partials.icons', ['n' => 'back'])</a><h1>Booking Saya</h1></div>

<div class="sec-title">Mendatang</div>
@forelse($mendatang as $b)
<div class="card accent">
  <div class="row between">
    <b style="font:700 15px var(--font-head)">{{ \Illuminate\Support\Carbon::parse($b->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</b>
    <span class="tag terjadwal">Terjadwal</span>
  </div>
  <div class="row muted" style="gap:6px;margin:8px 0 4px">@include('partials.icons', ['n' => 'clock']) {{ substr($b->jam_mulai,0,5) }} - {{ substr($b->jam_selesai,0,5) }}</div>
  <div class="muted" style="font-size:12px"><b style="color:var(--text)">{{ $b->nama_pelatih }}</b> &middot; Mobil: {{ $b->merek_model }}</div>
  <form method="post" action="{{ route('booking.batal', $b->id_booking) }}" onsubmit="return confirm('Batalkan booking ini?')" style="margin-top:12px;text-align:right">
    @csrf<button class="btn danger sm" type="submit">Batalkan</button></form>
</div>
@empty
<div class="card muted">Belum ada sesi mendatang.</div>
@endforelse

<div class="sec-title" style="margin-top:22px">Riwayat</div>
@forelse($riwayat as $b)
<div class="card {{ $b->status === 'selesai' ? 'done' : '' }}">
  <div class="row between">
    <b style="font:700 15px var(--font-head)">{{ \Illuminate\Support\Carbon::parse($b->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</b>
    <span class="tag {{ $b->status }}">{{ ucfirst($b->status) }}</span>
  </div>
  <div class="row muted" style="gap:6px;margin:8px 0 4px">@include('partials.icons', ['n' => 'clock']) {{ substr($b->jam_mulai,0,5) }} - {{ substr($b->jam_selesai,0,5) }}</div>
  <div class="muted" style="font-size:12px"><b style="color:var(--text)">{{ $b->nama_pelatih }}</b> &middot; Mobil: {{ $b->merek_model }}</div>
</div>
@empty
<div class="card muted">Belum ada riwayat sesi.</div>
@endforelse

<a class="btn" href="{{ route('booking.index') }}" style="margin-top:6px">Booking Jadwal Baru @include('partials.icons', ['n' => 'arrow'])</a>
@endsection
