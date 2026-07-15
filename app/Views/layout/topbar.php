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
<header class="fixed top-0 left-0 md:left-64 right-0 h-16 bg-[#0A0D14]/80 backdrop-blur-2xl flex items-center justify-between px-4 md:px-6 z-20 border-b border-white/5 shadow-[0_4px_30px_rgba(0,0,0,0.5)]">

  <div class="flex items-center gap-3">
    <!-- Hamburger — mobile only -->
    <button onclick="openSidebar()" class="md:hidden w-9 h-9 flex items-center justify-center rounded-xl hover:bg-white/5 transition-colors text-gray-400">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
    <p class="text-[13px] font-bold text-white uppercase tracking-wider font-display"><?= esc($label) ?></p>
  </div>

  <div class="relative flex items-center gap-2">
    <button onclick="toggleUserMenu()"
            class="flex items-center gap-2.5 px-2.5 md:px-3 py-1.5 rounded-xl hover:bg-white/5 transition-colors border border-transparent hover:border-white/10">
      <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#00F0FF] to-[#0099FF] p-[1px] shadow-[0_0_10px_rgba(0,240,255,0.2)] flex items-center justify-center">
        <div class="w-full h-full bg-[#0A0D14] rounded-[7px] flex items-center justify-center text-white text-xs font-bold">
          <?= esc($authInitial) ?>
        </div>
      </div>
      <span class="hidden sm:inline text-sm font-semibold text-white"><?= esc($authName) ?></span>
      <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>

    <div id="user-menu" class="hidden absolute right-0 top-full mt-2 w-52 bg-[#121620]/60 rounded-2xl shadow-xl border border-white/5 z-50 overflow-hidden">
      <div class="px-4 py-3 border-b border-white/5">
        <p class="text-sm font-semibold text-white"><?= esc($authName) ?></p>
        <p class="text-xs text-gray-400 mt-0.5"><?= esc($authRole) ?></p>
      </div>
      <div class="p-1">
        <a href="/logout"
           class="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-xl transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
