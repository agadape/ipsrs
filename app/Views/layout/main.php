<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $title ?? 'IPSRS' ?> — RSUD Kota Yogyakarta</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] },
          colors: { brand: { DEFAULT: '#4F46E5', hover: '#4338CA' } }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); background-attachment: fixed; }
    .scrollbar-dark::-webkit-scrollbar { width: 3px; }
    .scrollbar-dark::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-dark::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 999px; }
    .sidebar-active { background: linear-gradient(90deg, rgba(79,70,229,0.15) 0%, transparent 100%); border-left: 3px solid #6366f1; }
    .card { background: rgba(255,255,255,0.75); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius: 1.25rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 10px 15px -3px rgba(0,0,0,0.03); border: 1px solid rgba(255,255,255,0.7); }
    .badge { display:inline-flex; align-items:center; gap:.375rem; padding:.25rem .625rem; border-radius:9999px; font-size:.75rem; font-weight:500; white-space:nowrap; }
    .flash-success { background:#ecfdf5; border:1px solid #6ee7b7; color:#065f46; }
    .flash-error   { background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; }
    #sidebar { transition: transform 0.25s cubic-bezier(0.4,0,0.2,1); }
  </style>
</head>
<body class="min-h-screen">

<?php $isLoggedIn = session()->has('user_id'); ?>

<?php if ($isLoggedIn): ?>
<?= view('layout/sidebar') ?>
<?= view('layout/topbar') ?>

<!-- Mobile sidebar overlay -->
<div id="sidebar-overlay" onclick="closeSidebar()"
     class="fixed inset-0 bg-black/50 z-20 hidden md:hidden"></div>
<?php endif; ?>

<main id="main-content" class="<?= $isLoggedIn ? 'md:ml-60 pt-14' : 'pt-6' ?> min-h-screen flex justify-center">
  <div class="p-4 md:p-6 w-full <?= $isLoggedIn ? 'max-w-[1400px]' : 'max-w-[800px]' ?>">

    <?php if (session()->getFlashdata('success')): ?>
      <div class="flash-success rounded-xl px-4 py-3 mb-4 text-sm flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <?= session()->getFlashdata('success') ?>
      </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="flash-error rounded-xl px-4 py-3 mb-4 text-sm">
        <?= session()->getFlashdata('error') ?>
      </div>
    <?php endif; ?>

    <?= view($content_view, get_defined_vars()) ?>
  </div>
</main>

<script>
function openSidebar() {
  document.getElementById('sidebar').classList.remove('-translate-x-full');
  document.getElementById('sidebar-overlay').classList.remove('hidden');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.add('-translate-x-full');
  document.getElementById('sidebar-overlay').classList.add('hidden');
}
</script>
</body>
</html>
