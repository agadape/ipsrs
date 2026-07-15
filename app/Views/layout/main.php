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
  <!-- CDNs for UI/UX Enhancements -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); background-attachment: fixed; }
    .scrollbar-dark::-webkit-scrollbar { width: 3px; }
    .scrollbar-dark::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-dark::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 999px; }
    .sidebar-active { background: linear-gradient(90deg, rgba(79,70,229,0.15) 0%, transparent 100%); border-left: 3px solid #6366f1; }
    .card { background: rgba(255,255,255,0.75); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius: 1.25rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 10px 15px -3px rgba(0,0,0,0.03); border: 1px solid rgba(255,255,255,0.7); }
    .badge { display:inline-flex; align-items:center; gap:.375rem; padding:.25rem .625rem; border-radius:9999px; font-size:.75rem; font-weight:500; white-space:nowrap; }
    
    /* Custom DataTables styling to match theme */
    .dataTables_wrapper { padding: 1rem; }
    .dataTables_wrapper .dataTables_filter { margin-bottom: 1rem; float: right; text-align: right; }
    .dataTables_wrapper .dataTables_filter input { border-radius: 0.75rem; border: 1px solid #e2e8f0; padding: 0.35rem 0.75rem; margin-left: 0.5rem; outline: none; font-size: 0.875rem; background: #f8fafc; }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: #6366f1; box-shadow: 0 0 0 2px #e0e7ff; background: #fff; }
    .dataTables_wrapper .dataTables_length { margin-bottom: 1rem; float: left; font-size: 0.875rem; color: #64748b; }
    .dataTables_wrapper .dataTables_length select { border-radius: 0.5rem; border: 1px solid #e2e8f0; padding: 0.25rem 2rem 0.25rem 0.5rem; margin: 0 0.5rem; outline: none; background: #f8fafc; font-size: 0.875rem; }
    .dataTables_wrapper .dataTables_info { padding-top: 1rem; font-size: 0.875rem; color: #64748b; float: left; }
    .dataTables_wrapper .dataTables_paginate { padding-top: 1rem; float: right; display: flex; gap: 0.25rem; font-size: 0.875rem; }
    .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0.25rem 0.75rem !important; margin: 0 !important; border-radius: 0.5rem !important; border: 1px solid #e2e8f0 !important; background: #fff !important; color: #475569 !important; cursor: pointer; transition: all 0.2s; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #f8fafc !important; color: #0f172a !important; border-color: #cbd5e1 !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #4f46e5 !important; color: #fff !important; border-color: #4f46e5 !important; font-weight: 600; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { opacity: 0.5; cursor: not-allowed; }
    table.dataTable { border-collapse: collapse !important; border-spacing: 0 !important; width: 100% !important; margin-bottom: 0 !important; border-bottom: none !important; }
    table.dataTable thead th { border-bottom: 1px solid #f1f5f9 !important; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; font-weight: 700; padding: 1rem 1.25rem !important; }
    table.dataTable tbody td { border-bottom: 1px solid #f1f5f9 !important; padding: 0.875rem 1.25rem !important; }
    table.dataTable.no-footer { border-bottom: none !important; }
    .dataTables_wrapper::after { content: ""; display: table; clear: both; }
    
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

    <?= view($content_view, get_defined_vars()) ?>
  </div>
</main>

<script>
// SweetAlert Toast Configuration
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
});

// Flash Messages Handled via SweetAlert2
<?php if (session()->getFlashdata('success')): ?>
  Toast.fire({ icon: 'success', title: '<?= addslashes(session()->getFlashdata('success')) ?>' });
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
  Toast.fire({ icon: 'error', title: '<?= addslashes(session()->getFlashdata('error')) ?>' });
<?php endif; ?>

// Global Confirmation for Delete Actions
function confirmDelete(url) {
  Swal.fire({
    title: 'Apakah Anda yakin?',
    text: "Data yang dihapus tidak dapat dikembalikan!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#9ca3af',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = url;
    }
  });
}

function confirmFormSubmit(event, formElement, message = 'Data yang dihapus tidak dapat dikembalikan!') {
  event.preventDefault();
  Swal.fire({
    title: 'Apakah Anda yakin?',
    text: message,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#9ca3af',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      // Bypasses the SweetAlert on the actual submit
      formElement.submit();
    }
  });
}

// Form Loading State Prevent Double Submit
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
      const btn = this.querySelector('button[type="submit"]');
      if (btn && !btn.hasAttribute('data-no-loading')) {
        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;
      }
    });
  });
});

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
