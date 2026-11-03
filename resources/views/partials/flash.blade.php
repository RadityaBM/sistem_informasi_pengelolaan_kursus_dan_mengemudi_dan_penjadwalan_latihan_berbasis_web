@if(session('sukses'))<div class="alert ok">@include('partials.icons', ['n' => 'check'])<span>{{ session('sukses') }}</span></div>@endif
@if(session('galat'))<div class="alert bad">@include('partials.icons', ['n' => 'alert'])<span>{{ session('galat') }}</span></div>@endif
