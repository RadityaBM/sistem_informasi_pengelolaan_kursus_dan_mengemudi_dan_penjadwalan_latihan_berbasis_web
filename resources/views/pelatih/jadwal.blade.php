@extends('layouts.mobile')
@section('title', 'Jadwal Mengajar')
@section('content')
@php

  $tgl = \Illuminate\Support\Carbon::parse($tanggal)->locale('id');
  $awal = $tgl->copy()->startOfWeek();
  $selesai = $sesi->where('status', 'selesai')->count();
  $total = $sesi->count();
  $ini = strtoupper(substr(auth()->user()->username, 0, 2));
@endphp
<div class="row between head">
  @if($belum)
    <div><h1>Konfirmasi Sesi</h1><div class="muted" style="font-size:12px">Sesi yang sudah berlangsung dan menunggu konfirmasi</div></div>
  @else
    <div><h1>Jadwal Mengajar</h1><div class="muted" style="font-size:12px">{{ $tgl->translatedFormat('l, d F Y') }}</div></div>
  @endif
  <div class="avatar">{{ $ini }}</div>
</div>

@unless($belum)
<div class="days">
  @for($i = 0; $i < 7; $i++)
    @php $d = $awal->copy()->addDays($i)->locale('id'); @endphp
    <a href="{{ route('sesi.index', ['tanggal' => $d->toDateString()]) }}" class="{{ $d->isSameDay($tgl) ? 'on' : '' }}">{{ strtoupper($d->translatedFormat('D')) }}<b>{{ $d->format('d') }}</b></a>
  @endfor
</div>
@endunless

<div class="row between" style="margin-bottom:12px"><h2 style="font-size:17px">{{ $belum ? 'Menunggu konfirmasi' : 'Sesi hari ini' }}</h2><span class="tag nonaktif">{{ $total }} sesi</span></div>

@forelse($sesi as $s)
  @php $mulai = \Illuminate\Support\Carbon::parse($s->tanggal.' '.$s->jam_mulai); @endphp
  <div class="card {{ $s->status === 'selesai' ? 'done' : 'accent' }}">
    @if($belum)<div class="muted" style="font-size:11px;margin-bottom:4px">{{ $mulai->locale('id')->translatedFormat('l, d F Y') }}</div>@endif
    <div class="row between"><b style="font:700 15px var(--font-head)">{{ substr($s->jam_mulai,0,5) }} - {{ substr($s->jam_selesai,0,5) }}</b><span class="tag {{ $s->status }}">{{ ucfirst($s->status) }}</span></div>
    <div style="margin-top:8px"><b>{{ $s->nama_siswa }}</b><div class="muted" style="font-size:12px">Mobil: {{ $s->merek_model }}</div></div>
    @if($s->status === 'terjadwal')
      <div style="margin-top:12px;text-align:right">
        <button type="button" class="btn sm js-selesai" {{ $mulai->isFuture() ? 'disabled' : '' }}
          data-url="{{ route('sesi.selesai', $s->id_booking) }}" data-info="{{ $s->nama_siswa }} · {{ substr($s->jam_mulai,0,5) }} - {{ substr($s->jam_selesai,0,5) }}">Tandai selesai</button>
      </div>
    @endif
  </div>
@empty
  <div class="card muted">{{ $belum ? 'Tidak ada sesi yang menunggu konfirmasi.' : 'Tidak ada sesi pada tanggal ini.' }}</div>
@endforelse

<div class="row muted" style="gap:8px;font-size:12px;margin:4px 0 14px">@include('partials.icons', ['n' => 'info'])Tombol aktif hanya untuk sesi yang sudah berlangsung.</div>
@unless($belum)
<div class="card"><div class="eyebrow">Progres hari ini</div>
  <b style="display:block;margin:6px 0 10px;font:700 16px var(--font-head)">{{ $selesai }} dari {{ $total }} sesi selesai</b>
  <div class="seg" style="margin:0"><i class="on" style="flex:{{ $selesai }}"></i><i style="flex:{{ max($total - $selesai, 0) }}"></i></div></div>
@endunless

<div class="modal" id="modal">
  <form class="box" method="post" id="modal-form">
    @csrf
    <div class="ic">@include('partials.icons', ['n' => 'alert'])</div>
    <h3>Tandai sesi selesai?</h3>
    <div id="modal-info" style="font-size:13px"></div>
    <div class="muted" style="font-size:12px;margin-top:2px">Kuota siswa akan berkurang 1 pertemuan.</div>
    <div class="acts"><button type="button" class="btn sec" id="modal-batal">Batal</button><button type="submit" class="btn">Ya, selesai</button></div>
  </form>
</div>

@push('scripts')
<script>
var modal = document.getElementById('modal');
document.querySelectorAll('.js-selesai').forEach(function (b) {
  b.addEventListener('click', function () {
    if (b.disabled) return;
    document.getElementById('modal-form').action = b.dataset.url;
    document.getElementById('modal-info').textContent = b.dataset.info;
    modal.classList.add('show');
  });
});
document.getElementById('modal-batal').addEventListener('click', function () { modal.classList.remove('show'); });
modal.addEventListener('click', function (e) { if (e.target === modal) modal.classList.remove('show'); });
</script>
@endpush
@endsection
