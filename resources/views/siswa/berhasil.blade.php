@extends('layouts.mobile')
@section('title', 'Booking Berhasil')
@section('content')
<div class="center">
  <div class="okring">@include('partials.icons', ['n' => 'check'])</div>
  <h1 style="font-size:28px">Booking Berhasil</h1>
  <p class="muted" style="margin:6px 0 22px">Sesi latihanmu sudah dicatat.</p>
</div>

<div class="card accent">
  <div class="eyebrow" style="margin-bottom:6px">Detail sesi</div>
  <div class="kv"><span>Tanggal</span><b>{{ \Illuminate\Support\Carbon::parse($b->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</b></div>
  <div class="kv"><span>Jam</span><b>{{ substr($b->jam_mulai,0,5) }} - {{ substr($b->jam_selesai,0,5) }}</b></div>
  <div class="kv"><span>Pelatih</span><b>{{ $b->nama_pelatih }}</b></div>
  <div class="kv"><span>Mobil</span><b>{{ $b->merek_model }} / {{ $b->plat_nomor }}</b></div>
  <div class="kv"><span>Status</span><b style="color:var(--accent)">{{ ucfirst($b->status) }}</b></div>
</div>

<p class="muted" style="font-size:12px;margin:0 0 18px">Kuota berkurang setelah sesi dikonfirmasi selesai oleh pelatih.</p>
<a class="btn" href="{{ route('dashboard') }}" style="margin-bottom:10px">Kembali ke Beranda @include('partials.icons', ['n' => 'arrow'])</a>
<a class="btn sec" href="{{ route('booking.saya') }}">Lihat Booking Saya</a>
@endsection
