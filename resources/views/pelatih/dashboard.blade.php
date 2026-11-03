@extends('layouts.mobile')
@section('title', 'Beranda Pelatih')
@section('content')
@php
  $nama = $pelatih->nama ?? auth()->user()->username;
  $ini = collect(explode(' ', $nama))->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
@endphp
<div class="row between head">
  <div class="row"><div class="avatar">{{ $ini }}</div><div><div class="muted" style="font-size:12px">Halo,</div><b style="font:700 17px var(--font-head)">{{ $nama }}</b></div></div>
  <form method="post" action="{{ route('logout') }}" class="rsp-hide" style="margin:0">@csrf<button class="iconbtn" type="submit" title="Keluar">@include('partials.icons', ['n' => 'out'])</button></form>
</div>
<div class="card accent"><div class="eyebrow">Sesi terjadwal hari ini</div><div class="bigno" style="margin-top:8px">{{ $hariIni }}</div></div>
<a class="btn" href="{{ route('sesi.index') }}" style="margin-bottom:10px">Buka Jadwal Mengajar @include('partials.icons', ['n' => 'arrow'])</a>
<a class="btn sec" href="{{ route('sesi.index', ['belum' => 1]) }}">Konfirmasi Sesi</a>
@endsection
