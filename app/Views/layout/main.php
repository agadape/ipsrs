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
          fontFamily: { 
            sans: ['"Schibsted Grotesk"', 'sans-serif'],
            display: ['"Bricolage Grotesque"', 'sans-serif']
          },
          colors: { brand: { DEFAULT: '#0F766E', hover: '#0D9488' } },
          animation: {
            'fade-up': 'fadeUp 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards'
          },
          keyframes: {
            fadeUp: {
              '0%': { opacity: '0', transform: 'translateY(15px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' }
            }
          }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;600;700&family=Schibsted+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    body { 
      font-family: 'Schibsted Grotesk', sans-serif; 
      background-color: #F4F7F7;
      background-image: radial-gradient(#d4d4d8 1px, transparent 1px);
      background-size: 24px 24px;
      color: #334155;
    }
    h1, h2, h3, h4, h5, h6, .font-display { font-family: 'Bricolage Grotesque', sans-serif; letter-spacing: -0.02em; }
    
    .scrollbar-dark::-webkit-scrollbar { width: 4px; }
    .scrollbar-dark::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-dark::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 999px; }
    
    .sidebar-active { background: linear-gradient(90deg, rgba(13, 148, 136, 0.08) 0%, transparent 100%); border-left: 4px solid #0F766E; }
    
    .card { 
      background: rgba(255, 255, 255, 0.85); 
      backdrop-filter: blur(12px); 
      -webkit-backdrop-filter: blur(12px); 
      border-radius: 1rem; 
      box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 0 3px rgba(15, 23, 42, 0.02); 
      border: 1px solid rgba(255, 255, 255, 1); 
    }
    
    .badge { display:inline-flex; align-items:center; gap:.375rem; padding:.25rem .75rem; border-radius:9999px; font-size:.7rem; font-weight:600; white-space:nowrap; }
    .flash-success { background:#ecfdf5; border:1px solid #6ee7b7; color:#065f46; box-shadow: 0 4px 12px rgba(52, 211, 153, 0.1); }
    .flash-error   { background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; box-shadow: 0 4px 12px rgba(248, 113, 113, 0.1); }
    
    #sidebar { transition: transform 0.25s cubic-bezier(0.2, 0.8, 0.2, 1); }
    
    /* Subtle Staggered Animations */
    .stagger-1 { opacity: 0; animation: fadeUp 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) 0.05s forwards; }
    .stagger-2 { opacity: 0; animation: fadeUp 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) 0.1s forwards; }
    .stagger-3 { opacity: 0; animation: fadeUp 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) 0.15s forwards; }
    .stagger-4 { opacity: 0; animation: fadeUp 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) 0.2s forwards; }
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
