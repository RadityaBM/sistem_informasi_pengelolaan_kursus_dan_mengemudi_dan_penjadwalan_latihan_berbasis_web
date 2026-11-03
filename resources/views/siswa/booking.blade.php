@extends('layouts.mobile')
@section('title', 'Booking Jadwal')
@section('content')
<div class="row head"><a href="{{ route('dashboard') }}" class="iconbtn back">@include('partials.icons', ['n' => 'back'])</a><h1>Booking Jadwal</h1></div>

@if(! $daftar)
  <div class="card">Anda belum memiliki paket kursus aktif (BR-01). Hubungi admin untuk mendaftar.</div>
@else
<form method="get" action="{{ route('booking.index') }}">
  <div class="field"><label for="id_pelatih">Pelatih</label>
    <div class="inp sel">@include('partials.icons', ['n' => 'user'])
      <select id="id_pelatih" name="id_pelatih">
        <option value="">Pilih pelatih</option>
        @foreach($pelatih as $p)<option value="{{ $p->id_pelatih }}" @selected(request('id_pelatih') == $p->id_pelatih)>{{ $p->nama }}</option>@endforeach
      </select></div></div>
  <div class="field"><label for="tanggal">Tanggal</label>
    <div class="inp">@include('partials.icons', ['n' => 'cal'])
      <input id="tanggal" type="date" name="tanggal" value="{{ request('tanggal') }}" min="{{ today()->toDateString() }}" class="{{ $errors->has('tanggal') ? 'bad' : '' }}"></div>
    @error('tanggal')<div class="err">{{ $message }}</div>@enderror</div>
  <button class="btn sec" type="submit">Tampilkan Slot</button>
</form>

@if(request()->filled('id_pelatih') && request()->filled('tanggal'))
<form method="post" action="{{ route('booking.store') }}" style="margin-top:22px">
  @csrf
  <h2 style="font-size:19px">Slot tersedia</h2>
  <div class="legend"><span>Tersedia</span><span class="p">Dipilih</span><span>Terisi</span></div>
  @error('id_slot')<div class="alert bad">@include('partials.icons', ['n' => 'alert'])<span>{{ $message }}</span></div>@enderror

  @if($slots->isEmpty())
    <p class="muted">Tidak ada slot untuk pelatih dan tanggal ini (sesuai transmisi paket Anda).</p>
  @else
  <div class="slots">
    @foreach($slots as $s)
      @php $penuh = $s->status !== 'tersedia'; @endphp
      <label class="slot {{ $penuh ? 'full' : '' }}">
        <input type="radio" name="id_slot" value="{{ $s->id_slot }}" data-jam="{{ substr($s->jam_mulai,0,5) }} - {{ substr($s->jam_selesai,0,5) }}" {{ $penuh ? 'disabled' : '' }}>
        <span>{{ substr($s->jam_mulai,0,5) }}</span>
      </label>
    @endforeach
  </div>

  <div class="card summary" id="ringkasan" style="display:none">
    <div class="eyebrow">Ringkasan pilihan</div>
    <b id="rk-waktu" style="display:block;margin:6px 0 2px;font:700 16px var(--font-head)"></b>
    <span class="muted" style="font-size:12px">{{ optional($pelatih->firstWhere('id_pelatih', (int) request('id_pelatih')))->nama }} &middot; Sisa kuota {{ $kuota }} pertemuan</span>
  </div>

  <button class="btn" type="submit">Konfirmasi Booking @include('partials.icons', ['n' => 'arrow'])</button>
  <a class="btn ghost" href="{{ route('dashboard') }}" style="margin-top:6px">Batal</a>
  @endif
</form>
@endif
@endif

@push('scripts')
<script>
document.querySelectorAll('.slot input').forEach(function (el) {
  el.addEventListener('change', function () {
    var tgl = document.getElementById('tanggal').value;
    var d = tgl ? new Date(tgl + 'T00:00:00').toLocaleDateString('id-ID', {weekday:'long', day:'2-digit', month:'short'}) : '';
    document.getElementById('rk-waktu').textContent = d + ' | ' + el.dataset.jam;
    document.getElementById('ringkasan').style.display = 'block';
  });
});
</script>
@endpush
@endsection
