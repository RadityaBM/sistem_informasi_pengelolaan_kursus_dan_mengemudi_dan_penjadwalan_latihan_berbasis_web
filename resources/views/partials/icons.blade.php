@php
$ikon = [
  'home'  => '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>',
  'cal'   => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
  'list'  => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
  'check' => '<circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/>',
  'grid'  => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
  'box'   => '<path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/>',
  'out'   => '<path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>',
  'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
  'info'  => '<circle cx="12" cy="12" r="9"/><path d="M12 8h.01M11 12h1v5h1"/>',
  'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
  'user'  => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>',
  'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2 20c0-3.5 3-5.5 7-5.5s7 2 7 5.5"/><path d="M17 4.5a3.5 3.5 0 010 7M22 20c0-2.6-1.6-4.2-4-5"/>',
  'lock'  => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/>',
  'pie'   => '<path d="M21 12a9 9 0 11-9-9v9z"/><path d="M15 3.5A9 9 0 0120.5 9H15z"/>',
  'report'=> '<path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9z"/><path d="M14 3v6h6M8 13h8M8 17h5"/>',
  'dl'    => '<path d="M12 3v12M7 10l5 5 5-5M4 21h16"/>',
  'plus'  => '<path d="M12 5v14M5 12h14"/>',
  'chev'  => '<path d="M6 9l6 6 6-6"/>',
  'back'  => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
  'alert' => '<circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/>',
];
@endphp
<svg class="i" viewBox="0 0 24 24">{!! $ikon[$n] ?? '' !!}</svg>
