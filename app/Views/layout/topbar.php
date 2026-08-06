<?php
$labels = [
    '/ipsrs'              => 'Dashboard',
    '/ipsrs/aset'         => 'Data Aset',
    '/ipsrs/aset/tambah'  => 'Tambah Aset',
    '/ipsrs/aset/mutasi'  => 'Mutasi Aset',
    '/ipsrs/lk'           => 'Laporan Kerusakan',
    '/ipsrs/lk/baru'      => 'Buat LK Baru',
    '/ipsrs/preventif'    => 'Lembar Preventif',
    '/ipsrs/stok'         => 'Stok & Suku Cadang',
    '/ipsrs/stok/riwayat' => 'Riwayat Transaksi',
    '/ipsrs/vendor'       => 'Data Vendor',
    '/ipsrs/laporan'      => 'Laporan',
];
$path    = current_url(true)->getPath();
$label   = $labels[$path]
    ?? (str_contains($path, '/aset/')      ? 'Detail Aset'
     : (str_contains($path, '/lk/')        ? 'Detail LK'
     : (str_contains($path, '/preventif/') ? 'Lembar Kerja Preventif'
     : 'IPSRS')));

$authName    = session('user_name')    ?? 'User';
$authInitial = session('user_initial') ?? strtoupper(substr($authName, 0, 1));
$authRole    = session('user_role')    ?? 'Pengguna';
?>
<header class="fixed top-0 left-0 md:left-64 right-0 h-14 bg-white flex items-center justify-between px-4 md:px-6 z-20 border-b border-slate-200">

  <div class="flex items-center gap-3">
    <!-- Hamburger — mobile only -->
    <button onclick="openSidebar()" class="md:hidden w-8 h-8 flex items-center justify-center rounded-md hover:bg-slate-100 transition-colors text-slate-600">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
    <p class="text-sm font-semibold text-slate-900"><?= esc($label) ?></p>
  </div>

  <div class="relative flex items-center gap-2">
    <button onclick="toggleUserMenu()"
            class="flex items-center gap-2.5 px-2 md:px-2.5 py-1.5 rounded-md hover:bg-slate-100 transition-colors">
      <div class="w-7 h-7 rounded-full bg-slate-800 flex items-center justify-center text-white text-[11px] font-semibold">
        <?= esc($authInitial) ?>
      </div>
      <span class="hidden sm:inline text-sm font-medium text-slate-700"><?= esc($authName) ?></span>
      <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <div id="user-menu" class="hidden absolute right-0 top-full mt-1 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-100">
        <p class="text-sm font-semibold text-slate-900"><?= esc($authName) ?></p>
        <p class="text-xs text-slate-500 mt-0.5"><?= esc($authRole) ?></p>
      </div>
      <div class="py-1">
        <a href="/logout"
           class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-slate-50 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
          Keluar
        </a>
      </div>
    </div>

    <div id="user-backdrop" class="hidden fixed inset-0 z-40" onclick="toggleUserMenu()"></div>
  </div>
</header>

<script>
function toggleUserMenu() {
  var menu = document.getElementById('user-menu');
  var back = document.getElementById('user-backdrop');
  var isHidden = menu.classList.contains('hidden');
  menu.classList.toggle('hidden', !isHidden);
  back.classList.toggle('hidden', !isHidden);
}
</script>
