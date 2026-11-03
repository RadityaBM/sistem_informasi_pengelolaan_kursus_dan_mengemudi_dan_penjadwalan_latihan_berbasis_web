@extends('layouts.desktop')
@section('title', 'Konfirmasi Sesi')
@section('crumb', 'Konfirmasi Sesi')
@section('content')

<h1>Konfirmasi Sesi</h1><div class="sub">Tandai sesi yang sudah berlangsung sebagai selesai (BR-04, BR-05).</div>
<form class="card" method="get" action="{{ route('sesi.index') }}"><div class="row" style="align-items:flex-end">
  <div class="field" style="margin:0;flex:0 0 220px"><label for="tanggal">Tanggal</label><input id="tanggal" type="date" name="tanggal" value="{{ $tanggal }}"></div>
  <button class="btn sec auto" type="submit">Tampilkan</button></div>
  @error('tanggal')<div class="err">{{ $message }}</div>@enderror</form>
<div class="card" style="padding:8px 12px"><table>
  <thead><tr><th>Jam</th><th>Siswa</th><th>Pelatih</th><th>Mobil</th><th>Status</th><th></th></tr></thead><tbody>
  @forelse($sesi as $s)
    @php $mulai = \Illuminate\Support\Carbon::parse($s->tanggal.' '.$s->jam_mulai); @endphp
    <tr><td class="n">{{ substr($s->jam_mulai,0,5) }} - {{ substr($s->jam_selesai,0,5) }}</td><td>{{ $s->nama_siswa }}</td><td>{{ $s->nama_pelatih }}</td><td>{{ $s->merek_model }}</td>
      <td><span class="tag {{ $s->status }}">{{ ucfirst($s->status) }}</span></td>
      <td>@if($s->status === 'terjadwal')
        <form method="post" action="{{ route('sesi.selesai', $s->id_booking) }}" onsubmit="return confirm('Kuota siswa akan berkurang 1 pertemuan. Tandai sesi ini selesai?')">@csrf
          <button class="btn sm" type="submit" {{ $mulai->isFuture() ? 'disabled' : '' }}>Tandai selesai</button></form>@endif</td></tr>
  @empty
    <tr><td colspan="6" class="muted">Tidak ada sesi pada tanggal ini.</td></tr>
  @endforelse
</tbody></table></div>
@endsection
