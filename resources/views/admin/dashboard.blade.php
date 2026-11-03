@extends('layouts.desktop')
@section('title', 'Dashboard Admin')
@section('crumb', 'Dashboard')
@section('content')
<h1>Dashboard</h1><div class="sub">Ringkasan data master kursus.</div>
<div class="grid">
@foreach($ringkas as $nama => $jumlah)
  <div class="card accent"><div class="eyebrow">{{ $nama }}</div><div class="num">{{ $jumlah }}</div></div>
@endforeach
</div>
<a class="btn auto" href="{{ route('paket.index') }}" style="margin-top:8px">Kelola Paket Kursus @include('partials.icons', ['n' => 'arrow'])</a>
@endsection
