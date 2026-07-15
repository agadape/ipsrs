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
            sans: ['"Onest"', 'sans-serif'],
            display: ['"Syne"', 'sans-serif']
          },
          colors: { 
            brand: { DEFAULT: '#CCFF00', hover: '#B3E600' }
          },
          animation: {
            'fade-up': 'fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards'
          },
          keyframes: {
            fadeUp: {
              '0%': { opacity: '0', transform: 'translateY(20px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' }
            }
          }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Onest:wght@400;500;600;700&family=Syne:wght@500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    body { 
      font-family: 'Onest', sans-serif; 
      background-color: #0A0D14;
      background-image: 
        radial-gradient(circle at 15% 50%, rgba(204, 255, 0, 0.05), transparent 25%),
        radial-gradient(circle at 85% 30%, rgba(0, 240, 255, 0.05), transparent 25%),
        linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
      background-size: 100% 100%, 100% 100%, 40px 40px, 40px 40px;
      color: #F3F4F6;
    }
    h1, h2, h3, h4, h5, h6, .font-display { font-family: 'Syne', sans-serif; }
    
    .scrollbar-dark::-webkit-scrollbar { width: 4px; }
    .scrollbar-dark::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-dark::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 999px; }
    
    .sidebar-active { background: linear-gradient(90deg, rgba(204, 255, 0, 0.1) 0%, transparent 100%); border-left: 3px solid #CCFF00; color: #CCFF00 !important; }
    
    .card { 
      background: rgba(18, 22, 32, 0.6); 
      backdrop-filter: blur(20px); 
      -webkit-backdrop-filter: blur(20px); 
      border-radius: 1.25rem; 
      box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37); 
      border: 1px solid rgba(255, 255, 255, 0.05); 
    }
    
    .badge { display:inline-flex; align-items:center; gap:.375rem; padding:.25rem .75rem; border-radius:9999px; font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; white-space:nowrap; }
    .flash-success { background:rgba(204, 255, 0, 0.1); border:1px solid rgba(204, 255, 0, 0.3); color:#CCFF00; backdrop-filter: blur(10px); }
    .flash-error   { background:rgba(255, 51, 102, 0.1); border:1px solid rgba(255, 51, 102, 0.3); color:#FF3366; backdrop-filter: blur(10px); }
    
    #sidebar { transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1); border-right: 1px solid rgba(255,255,255,0.05); background: rgba(10, 13, 20, 0.8); backdrop-filter: blur(20px); }
    
    /* Staggered Animations for Anti-Slop Delight */
    .stagger-1 { opacity: 0; animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards; }
    .stagger-2 { opacity: 0; animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.2s forwards; }
    .stagger-3 { opacity: 0; animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.3s forwards; }
    .stagger-4 { opacity: 0; animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) 0.4s forwards; }
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
