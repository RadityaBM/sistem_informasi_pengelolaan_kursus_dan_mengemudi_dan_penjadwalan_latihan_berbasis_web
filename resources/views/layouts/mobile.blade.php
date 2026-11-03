<!doctype html>
<html lang="id">
<head>
@include('partials.head')
<title>@yield('title', 'Kursus Mengemudi')</title>
</head>
<body class="mobile">
@php $role = auth()->user()->role; @endphp
@php $ini = strtoupper(substr(auth()->user()->username, 0, 2)); @endphp
{{-- Versi WEB (layar lebar): sidebar. Versi MOBILE (layar kecil): navigasi bawah. Dipilih otomatis lewat CSS. --}}
<aside class="sidebar rsp-side">
  <div class="logo"><i>@include('partials.icons', ['n' => 'box'])</i> NAMA KURSUS</div>
  <div class="eyebrow">Menu</div>
  <nav class="nav">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'on' : '' }}">@include('partials.icons', ['n' => 'home'])Beranda</a>
    @if($role === 'siswa')
      <a href="{{ route('booking.index') }}" class="{{ request()->routeIs('booking.index', 'booking.berhasil') ? 'on' : '' }}">@include('partials.icons', ['n' => 'cal'])Booking Jadwal</a>
      <a href="{{ route('booking.saya') }}" class="{{ request()->routeIs('booking.saya') ? 'on' : '' }}">@include('partials.icons', ['n' => 'list'])Booking Saya</a>
      <a href="{{ route('kuota') }}" class="{{ request()->routeIs('kuota') ? 'on' : '' }}">@include('partials.icons', ['n' => 'pie'])Kuota</a>
    @else
      <a href="{{ route('sesi.index') }}" class="{{ request()->routeIs('sesi.*') && ! request()->boolean('belum') ? 'on' : '' }}">@include('partials.icons', ['n' => 'cal'])Jadwal Mengajar</a>
      <a href="{{ route('sesi.index', ['belum' => 1]) }}" class="{{ request()->routeIs('sesi.*') && request()->boolean('belum') ? 'on' : '' }}">@include('partials.icons', ['n' => 'check'])Konfirmasi Sesi</a>
    @endif
  </nav>
  <div class="me">
    <div class="avatar">{{ $ini }}</div>
    <div><b>{{ auth()->user()->username }}</b><small>{{ ucfirst($role) }}</small></div>
    <form method="post" action="{{ route('logout') }}">@csrf<button type="submit" title="Keluar">@include('partials.icons', ['n' => 'out'])</button></form>
  </div>
</aside>

<div class="phone">
  <div class="inner">
    @include('partials.flash')
    @yield('content')
  </div>

  <nav class="bottomnav">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'on' : '' }}">@include('partials.icons', ['n' => 'home'])Beranda</a>
    @if($role === 'siswa')
      <a href="{{ route('booking.index') }}" class="{{ request()->routeIs('booking.index', 'booking.berhasil') ? 'on' : '' }}">@include('partials.icons', ['n' => 'cal'])Booking</a>
      <a href="{{ route('booking.saya') }}" class="{{ request()->routeIs('booking.saya') ? 'on' : '' }}">@include('partials.icons', ['n' => 'list'])Booking Saya</a>
      <a href="{{ route('kuota') }}" class="{{ request()->routeIs('kuota') ? 'on' : '' }}">@include('partials.icons', ['n' => 'pie'])Kuota</a>
    @else
      <a href="{{ route('sesi.index') }}" class="{{ request()->routeIs('sesi.*') && ! request()->boolean('belum') ? 'on' : '' }}">@include('partials.icons', ['n' => 'cal'])Jadwal</a>
      <a href="{{ route('sesi.index', ['belum' => 1]) }}" class="{{ request()->routeIs('sesi.*') && request()->boolean('belum') ? 'on' : '' }}">@include('partials.icons', ['n' => 'check'])Konfirmasi</a>
    @endif
  </nav>
</div>
@stack('scripts')
</body>
</html>
