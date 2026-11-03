<!doctype html>
<html lang="id">
<head>
@include('partials.head')
<title>@yield('title', 'Kursus Mengemudi')</title>
</head>
<body>
@php
  $role = auth()->user()->role;
  $ini = strtoupper(substr(auth()->user()->username, 0, 2));
@endphp
<div class="shell">
  <aside class="sidebar">
    <div class="logo"><i>@include('partials.icons', ['n' => 'box'])</i> NAMA KURSUS</div>
    <div class="eyebrow">Menu</div>
    <nav class="nav">
      <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'on' : '' }}">@include('partials.icons', ['n' => 'grid'])Dashboard</a>
      @if($role === 'admin')
        <div class="grp {{ request()->routeIs('siswa.*') ? 'on' : '' }}">@include('partials.icons', ['n' => 'users'])Siswa</div>
        <a class="sub {{ request()->routeIs('siswa.create') ? 'on' : '' }}" href="{{ route('siswa.create') }}">Pendaftaran Siswa Baru</a>
        <a class="sub {{ request()->routeIs('siswa.index') ? 'on' : '' }}" href="{{ route('siswa.index') }}">Daftar Siswa</a>
        <div class="grp {{ request()->routeIs('paket.*') ? 'on' : '' }}">@include('partials.icons', ['n' => 'box'])Data Master</div>
        <a class="sub {{ request()->routeIs('paket.*') ? 'on' : '' }}" href="{{ route('paket.index') }}">Paket Kursus</a>
        <a href="{{ route('sesi.index') }}" class="{{ request()->routeIs('sesi.*') ? 'on' : '' }}" style="margin-top:4px">@include('partials.icons', ['n' => 'check'])Konfirmasi Sesi</a>
      @elseif($role === 'owner')
        <div class="grp {{ request()->routeIs('laporan.*') ? 'on' : '' }}">@include('partials.icons', ['n' => 'report'])Laporan</div>
        <a class="sub {{ request()->routeIs('laporan.index') ? 'on' : '' }}" href="{{ route('laporan.index') }}">Laporan Siswa &amp; Transaksi</a>
        <a class="sub {{ request()->routeIs('laporan.produktivitas') ? 'on' : '' }}" href="{{ route('laporan.produktivitas') }}">Rekap Produktivitas Pelatih</a>
      @endif
    </nav>
    <div class="me">
      <div class="avatar">{{ $ini }}</div>
      <div><b>{{ auth()->user()->username }}</b><small>{{ ucfirst($role) }}</small></div>
      <form method="post" action="{{ route('logout') }}">@csrf<button type="submit" title="Keluar">@include('partials.icons', ['n' => 'out'])</button></form>
    </div>
  </aside>
  <div class="content">
    <div class="topbar"><span>@yield('crumb', 'Dashboard')</span><div class="avatar" style="width:28px;height:28px;font-size:10px">{{ $ini }}</div></div>
    <div class="page">
      @include('partials.flash')
      @yield('content')
    </div>
  </div>
</div>
</body>
</html>
