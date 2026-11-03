<!doctype html>
<html lang="id">
<head>
@include('partials.head')
<title>Masuk | Kursus Mengemudi</title>
</head>
<body>
<div class="login">
  <section class="art">
    <div class="logo" style="margin:0"><i>@include('partials.icons', ['n' => 'box'])</i> NAMA KURSUS</div>
    <div class="art-body">
      <h1>Belajar<br>mengemudi<br><em>tanpa ribet.</em></h1>
      <p>Booking jadwal, pantau kuota, dan kelola sesi latihan dalam satu sistem.</p>
      @include('partials.car')
    </div>
    <div class="feat"><span>Booking real-time</span><span>Kuota otomatis</span><span>Laporan transaksi</span></div>
  </section>
  <section class="panel">
    <form method="post" action="{{ route('login.proses') }}" novalidate>
      @csrf
      <div class="eyebrow" style="color:var(--accent)">Selamat datang</div>
      <h2>Masuk ke Sistem</h2>
      <p class="muted" style="margin:0 0 20px">Gunakan akun yang diberikan Admin saat pendaftaran.</p>

      @if(session('sukses'))<div class="alert ok">@include('partials.icons', ['n' => 'check'])<span>{{ session('sukses') }}</span></div>@endif
      @error('login')<div class="alert bad">@include('partials.icons', ['n' => 'alert'])<span>{{ $message }}</span></div>@enderror

      <div class="field"><label for="username">Username <span class="req">*</span></label>
        <div class="inp">@include('partials.icons', ['n' => 'user'])
          <input id="username" name="username" type="text" placeholder="Masukkan username" value="{{ old('username') }}" class="{{ $errors->has('username') ? 'bad' : '' }}" autofocus></div>
        @error('username')<div class="err">{{ $message }}</div>@enderror</div>
      <div class="field"><label for="password">Password <span class="req">*</span></label>
        <div class="inp">@include('partials.icons', ['n' => 'lock'])
          <input id="password" name="password" type="password" placeholder="Masukkan password" class="{{ $errors->has('password') ? 'bad' : '' }}">
          <button type="button" class="toggle" id="tgl">Tampilkan</button></div>
        @error('password')<div class="err">{{ $message }}</div>@enderror</div>
      <button class="btn" type="submit">Masuk @include('partials.icons', ['n' => 'arrow'])</button>
      <p class="muted" style="text-align:center;font-size:12px;margin-top:16px">Lupa password? Hubungi Admin.</p>
      <p class="muted" style="text-align:center;font-size:10px;letter-spacing:.1em;margin-top:28px;opacity:.6">&copy; NAMA KURSUS</p>
    </form>
  </section>
</div>
<script>
document.getElementById('tgl').addEventListener('click', function () {
  var i = document.getElementById('password'), s = i.type === 'password';
  i.type = s ? 'text' : 'password'; this.textContent = s ? 'Sembunyikan' : 'Tampilkan';
});
</script>
</body>
</html>
