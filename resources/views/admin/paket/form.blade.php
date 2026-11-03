@extends('layouts.desktop')
@section('title', $paket->exists ? 'Edit Paket' : 'Tambah Paket')
@section('crumb', 'Data Master / Paket Kursus / '.($paket->exists ? 'Edit' : 'Tambah'))
@section('content')
<h1>{{ $paket->exists ? 'Edit Paket Kursus' : 'Tambah Paket Kursus' }}</h1>
<div class="sub">Lengkapi data paket. Tanda <span style="color:var(--accent)">*</span> wajib diisi.</div>
<form class="card" method="post" novalidate style="max-width:720px"
      action="{{ $paket->exists ? route('paket.update', $paket) : route('paket.store') }}">
  @csrf
  @if($paket->exists) @method('PUT') @endif

  <div class="field"><label for="nama_paket">Nama paket <span class="req">*</span></label>
    <input id="nama_paket" name="nama_paket" type="text" placeholder="Contoh: Manual Reguler 8 Sesi" value="{{ old('nama_paket', $paket->nama_paket) }}" class="{{ $errors->has('nama_paket') ? 'bad' : '' }}">
    @error('nama_paket')<div class="err">{{ $message }}</div>@enderror</div>

  <div class="form2">
    <div class="field"><label for="transmisi">Transmisi <span class="req">*</span></label>
      <select id="transmisi" name="transmisi" class="{{ $errors->has('transmisi') ? 'bad' : '' }}">
        <option value="">Pilih transmisi</option>
        @foreach(['manual','matic'] as $t)<option value="{{ $t }}" @selected(old('transmisi', $paket->transmisi) === $t)>{{ ucfirst($t) }}</option>@endforeach
      </select>
      @error('transmisi')<div class="err">{{ $message }}</div>@enderror</div>
    <div class="field"><label for="jumlah_sesi">Jumlah sesi <span class="req">*</span></label>
      <input id="jumlah_sesi" name="jumlah_sesi" type="number" placeholder="8" value="{{ old('jumlah_sesi', $paket->jumlah_sesi) }}" class="{{ $errors->has('jumlah_sesi') ? 'bad' : '' }}">
      @error('jumlah_sesi')<div class="err">{{ $message }}</div>@enderror</div>
  </div>

  <div class="field"><label for="harga">Harga (Rp) <span class="req">*</span></label>
    <input id="harga" name="harga" type="number" step="0.01" placeholder="1000000" value="{{ old('harga', $paket->harga) }}" class="{{ $errors->has('harga') ? 'bad' : '' }}">
    @error('harga')<div class="err">{{ $message }}</div>@enderror</div>

  <label class="row" style="gap:10px;margin:4px 0 18px;cursor:pointer"><input type="checkbox" name="status_aktif" value="1" @checked(old('status_aktif', $paket->status_aktif))> Paket aktif (ditawarkan)</label>

  <div class="row"><button class="btn auto" type="submit">Simpan @include('partials.icons', ['n' => 'arrow'])</button><a class="btn sec auto" href="{{ route('paket.index') }}">Batal</a></div>
</form>
@endsection
