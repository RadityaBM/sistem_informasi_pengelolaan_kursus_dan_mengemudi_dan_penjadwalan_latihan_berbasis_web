@extends('layouts.desktop')
@section('title', 'Rekap Produktivitas Pelatih')
@section('crumb', 'Laporan / Rekap Produktivitas Pelatih')
@section('content')
<h1>Rekap Produktivitas Pelatih</h1>
<div class="sub">Jumlah sesi latihan yang selesai per pelatih.</div>

<form method="get" action="{{ route('laporan.produktivitas') }}" class="filters">
  <div class="field"><label for="dari">Dari tanggal</label><div class="inp">@include('partials.icons', ['n' => 'cal'])<input id="dari" type="date" name="dari" value="{{ $dari }}"></div></div>
  <div class="field"><label for="sampai">Sampai tanggal</label><div class="inp">@include('partials.icons', ['n' => 'cal'])<input id="sampai" type="date" name="sampai" value="{{ $sampai }}"></div></div>
  <button class="btn auto" type="submit" style="height:46px">Tampilkan</button>
</form>
@if($errors->any())<div class="alert bad">@include('partials.icons', ['n' => 'alert'])<span>{{ $errors->first() }}</span></div>@endif

<div class="card" style="padding:8px 12px">
<table>
  <thead><tr><th>No</th><th>Pelatih</th><th>Sesi Selesai</th></tr></thead>
  <tbody>
  @forelse($rows as $i => $r)<tr><td>{{ $i + 1 }}</td><td class="n">{{ $r->nama }}</td><td>{{ $r->selesai }}</td></tr>
  @empty<tr><td colspan="3" class="muted">Belum ada data pelatih.</td></tr>@endforelse
  </tbody>
</table>
</div>
<a class="btn sec auto" href="{{ route('laporan.index') }}">@include('partials.icons', ['n' => 'back'])Kembali ke laporan</a>
@endsection
