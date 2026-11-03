@extends('layouts.desktop')
@section('title', 'Pendaftaran Siswa Baru')
@section('crumb', 'Siswa / Pendaftaran Siswa Baru')
@section('content')
<h1>Pendaftaran Siswa Baru</h1>
<div class="sub">Lengkapi data siswa dan pilih paket kursus. Tanda <span style="color:var(--accent)">*</span> wajib diisi.</div>

<form method="post" action="{{ route('siswa.store') }}" novalidate>
  @csrf
  <div class="cols">
    <div class="card">
      <div class="sect">Data Siswa</div>
      <div class="form2">
        <div class="field"><label for="nama">Nama lengkap <span class="req">*</span></label>
          <input id="nama" name="nama" type="text" placeholder="Nama siswa" value="{{ old('nama') }}" class="{{ $errors->has('nama') ? 'bad' : '' }}">
          @error('nama')<div class="err">{{ $message }}</div>@enderror</div>
        <div class="field"><label for="nomor_telepon">Nomor telepon <span class="req">*</span></label>
          <input id="nomor_telepon" name="nomor_telepon" type="text" placeholder="08xx xxxx xxxx" value="{{ old('nomor_telepon') }}" class="{{ $errors->has('nomor_telepon') ? 'bad' : '' }}">
          @error('nomor_telepon')<div class="err">{{ $message }}</div>@enderror</div>
      </div>
      <div class="field"><label for="alamat">Alamat (opsional)</label>
        <textarea id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap" class="input" style="resize:vertical;font:inherit">{{ old('alamat') }}</textarea>
        @error('alamat')<div class="err">{{ $message }}</div>@enderror</div>

      <div class="sect">Akun Login Siswa</div>
      <div class="form2">
        <div class="field"><label for="username">Username <span class="req">*</span></label>
          <input id="username" name="username" type="text" placeholder="Username unik" value="{{ old('username') }}" class="{{ $errors->has('username') ? 'bad' : '' }}">
          @error('username')<div class="err">{{ $message }}</div>@enderror</div>
        <div class="field"><label for="password">Password awal <span class="req">*</span></label>
          <div class="inp">@include('partials.icons', ['n' => 'lock'])<input id="password" name="password" type="password" placeholder="Minimal 8 karakter" class="{{ $errors->has('password') ? 'bad' : '' }}"></div>
          @error('password')<div class="err">{{ $message }}</div>@enderror</div>
      </div>
      <div class="form2">
        <div class="field"><label>Role</label><input type="text" value="Siswa" disabled></div>
        <div class="hint" style="align-self:end;padding-bottom:22px">Password disimpan dalam bentuk hash.</div>
      </div>
    </div>

    <div class="card">
      <div class="sect">Paket Kursus</div>
      <div class="field"><label for="id_paket">Pilih paket <span class="req">*</span></label>
        <div class="inp sel"><select id="id_paket" name="id_paket" class="{{ $errors->has('id_paket') ? 'bad' : '' }}" style="padding-left:14px !important">
          <option value="">Pilih paket</option>
          @foreach($paket as $p)
            <option value="{{ $p->id_paket }}" data-transmisi="{{ ucfirst($p->transmisi) }}" data-sesi="{{ $p->jumlah_sesi }}" data-harga="{{ number_format($p->harga, 0, ',', '.') }}" @selected(old('id_paket') == $p->id_paket)>{{ $p->nama_paket }}</option>
          @endforeach
        </select></div>
        @error('id_paket')<div class="err">{{ $message }}</div>@enderror</div>
      <div class="field"><label for="tanggal_daftar">Tanggal daftar <span class="req">*</span></label>
        <input id="tanggal_daftar" name="tanggal_daftar" type="date" value="{{ old('tanggal_daftar', today()->toDateString()) }}" class="{{ $errors->has('tanggal_daftar') ? 'bad' : '' }}">
        @error('tanggal_daftar')<div class="err">{{ $message }}</div>@enderror</div>

      <div class="sumbox">
        <div class="eyebrow" style="margin-bottom:6px">Ringkasan paket</div>
        <div class="kv"><span>Transmisi</span><b id="s-transmisi">-</b></div>
        <div class="kv"><span>Jumlah sesi</span><b id="s-sesi">-</b></div>
        <div class="kv"><span>Harga dibayar</span><b id="s-harga">-</b></div>
        <div class="kv"><span>Status paket</span><b class="okc">Aktif</b></div>
      </div>
      <div class="hint" style="margin:0 0 16px">Total sesi dan harga disalin dari paket saat pendaftaran.</div>

      <button class="btn" type="submit">Simpan Pendaftaran @include('partials.icons', ['n' => 'arrow'])</button>
      <a class="btn sec" href="{{ route('siswa.index') }}" style="margin-top:10px">Batal</a>
    </div>
  </div>
</form>

<script>
var pk = document.getElementById('id_paket');
function ringkas() {
  var o = pk.options[pk.selectedIndex];
  document.getElementById('s-transmisi').textContent = o && o.value ? o.dataset.transmisi : '-';
  document.getElementById('s-sesi').textContent = o && o.value ? o.dataset.sesi + ' pertemuan' : '-';
  document.getElementById('s-harga').textContent = o && o.value ? 'Rp ' + o.dataset.harga : '-';
}
pk.addEventListener('change', ringkas); ringkas();
</script>
@endsection
