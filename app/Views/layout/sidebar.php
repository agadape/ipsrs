<?php
$path = current_url(true)->getPath();

$authName    = session('user_name')    ?? 'User';
$authInitial = session('user_initial') ?? strtoupper(substr($authName, 0, 1));
$authRole    = session('user_role')    ?? 'Pengguna';

function navLink(string $href, string $label, string $icon, string $current, ?int $badge = null): string {
    // Exact match is always active
    if ($current === $href) {
        $active = true;
    } else {
        // If not exact match, check if it's a sub-page (e.g., /ipsrs/aset/edit/1)
        // We must prevent sibling sidebar items from highlighting their parent.
        // E.g., /ipsrs/aset/tambah should NOT highlight /ipsrs/aset
        $isSubPage = ($href !== '/ipsrs' && str_starts_with($current, $href . '/'));
        
        // Exclude specific known sibling paths from triggering the parent's active state
        if ($href === '/ipsrs/aset' && (str_starts_with($current, '/ipsrs/aset/tambah') || str_starts_with($current, '/ipsrs/aset/mutasi'))) {
            $isSubPage = false;
        }
        if ($href === '/ipsrs/stok' && str_starts_with($current, '/ipsrs/stok/riwayat')) {
            $isSubPage = false;
        }

        $active = $isSubPage;
    }

    $cls = $active
        ? 'text-indigo-700 font-bold bg-indigo-50 shadow-sm shadow-indigo-100 ring-1 ring-indigo-500/10'
        : 'text-gray-500 hover:text-indigo-600 hover:bg-indigo-50/50 font-medium';
    $badgeHtml = ($badge !== null && $badge > 0)
        ? "<span class='text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-600'>{$badge}</span>"
        : '';
    return <<<HTML
    <a href="{$href}" onclick="closeSidebar()" class="flex items-center justify-between gap-3 px-4 py-3 rounded-2xl text-[14px] transition-all duration-300 {$cls}">
      <span class="flex items-center gap-3">{$icon} {$label}</span>
      {$badgeHtml}
    </a>
    HTML;
}

function ico(string $d, string $size = '18'): string {
    return "<svg xmlns='http://www.w3.org/2000/svg' width='{$size}' height='{$size}' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>{$d}</svg>";
}
?>
<aside id="sidebar" class="fixed left-0 top-0 h-screen w-64 flex flex-col z-30 -translate-x-full md:translate-x-0 bg-white/70 backdrop-blur-2xl border-r border-white/80 shadow-[4px_0_24px_rgba(0,0,0,0.03)] transition-transform duration-300">

  <!-- Logo + close button on mobile -->
  <div class="flex items-center justify-between px-6 py-6">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-md shadow-indigo-500/30 flex items-center justify-center shrink-0 text-white">
        <?= ico('<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>') ?>
      </div>
      <div>
        <p class="text-indigo-950 font-black text-[15px] leading-none tracking-tight">IPSRS</p>
        <p class="text-indigo-600/70 font-medium text-[10px] mt-1 leading-none uppercase tracking-wider">RSUD YK</p>
      </div>
    </div>
    <button onclick="closeSidebar()" class="md:hidden text-gray-400 hover:text-gray-700 bg-gray-100 p-1.5 rounded-xl transition-colors">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
      </svg>
    </button>
  </div>

  <nav class="flex-1 overflow-y-auto px-4 py-2 space-y-6">
    <div>
      <?= navLink('/ipsrs', 'Dashboard', ico('<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>'), $path) ?>
    </div>

    <?php if ($authRole !== 'pelapor'): ?>
    <div>
      <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-4 mb-2">Inventaris</p>
      <div class="space-y-1">
        <?= navLink('/ipsrs/aset',        'Daftar Aset', ico('<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>'), $path) ?>
        <?= navLink('/ipsrs/aset/tambah', 'Tambah Aset', ico('<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>'), $path) ?>
        <?= navLink('/ipsrs/aset/mutasi', 'Mutasi Aset', ico('<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/>'), $path) ?>
      </div>
    </div>
    <?php endif; ?>

    <div>
      <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-4 mb-2">Pemeliharaan</p>
      <div class="space-y-1">
        <?= navLink('/ipsrs/lk',        'Lap. Kerusakan',   ico('<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>'), $path) ?>
        <?php if ($authRole !== 'pelapor'): ?>
        <?= navLink('/ipsrs/preventif', 'Lembar Preventif', ico('<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>'), $path) ?>
        <?= navLink('/ipsrs/kanibal',   'Kanibal Alat',     ico('<path d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>'), $path) ?>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($authRole !== 'pelapor'): ?>
    <div>
      <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-4 mb-2">Logistik</p>
      <div class="space-y-1">
        <?= navLink('/ipsrs/stok',         'Stok & Suku Cadang', ico('<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>'), $path) ?>
        <?= navLink('/ipsrs/stok/riwayat', 'Riwayat Transaksi',  ico('<polyline points="12 8 12 12 14 14"/><path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5"/>'), $path) ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($authRole !== 'pelapor'): ?>
    <div>
      <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-4 mb-2">Sistem</p>
      <div class="space-y-1">
        <?= navLink('/ipsrs/pengguna',        'Data Pengguna', ico('<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'), $path) ?>
        <?= navLink('/ipsrs/kategori-aset',  'Kategori Aset', ico('<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>'), $path) ?>
        <?= navLink('/ipsrs/kode-kerusakan', 'Kode Kerusakan', ico('<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>'), $path) ?>
        <?= navLink('/ipsrs/vendor',         'Data Vendor',    ico('<path d="M3 21h18M3 7v14M21 7v14M6 11h.01M6 15h.01M10 11h.01M10 15h.01M14 11h.01M14 15h.01M18 11h.01M18 15h.01M5 7l7-4 7 4"/>'), $path) ?>
        <?= navLink('/ipsrs/laporan',        'Laporan',        ico('<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>'), $path) ?>
      </div>
    </div>
    <?php endif; ?>
  </nav>

  <!-- User Profile Section -->
  <div class="px-5 py-5 border-t border-gray-200/50 bg-white/40 backdrop-blur-xl">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[13px] font-bold shrink-0 shadow-md shadow-indigo-500/20">
        <?= esc($authInitial) ?>
      </div>
      <div class="min-w-0">
        <p class="text-gray-800 text-sm font-bold truncate leading-tight"><?= esc($authName) ?></p>
        <p class="text-gray-500 text-[11px] mt-0.5 truncate font-medium"><?= esc($authRole) ?></p>
      </div>
      <a href="/logout" class="ml-auto text-gray-400 hover:text-red-500 bg-white hover:bg-red-50 p-2 rounded-xl transition-colors shrink-0 shadow-sm border border-gray-100" title="Keluar">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
      </a>
    </div>
  </div>
</aside>
