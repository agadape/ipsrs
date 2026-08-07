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
          fontFamily: { sans: ['Inter', 'sans-serif'] },
          colors: { brand: { DEFAULT: '#0f172a', hover: '#1e293b' } }
        }
      }
    }
  </script>
  <!-- CDNs for UI/UX Enhancements -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
    <link rel="preconnect" href="https://rsms.me/">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
      :root { font-family: 'Inter', sans-serif; }
      @supports (font-variation-settings: normal) {
        :root { font-family: 'Inter var', sans-serif; }
      }
      body { background-color: #f8fafc; color: #0f172a; -webkit-font-smoothing: antialiased; }
      .scrollbar-dark::-webkit-scrollbar { width: 4px; height: 4px; }
      .scrollbar-dark::-webkit-scrollbar-track { background: transparent; }
      .scrollbar-dark::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
      .sidebar-active { background-color: #f1f5f9; color: #0f172a; font-weight: 500; }
      .sidebar-active::before { content: ''; position: absolute; left: 0; top: 0.5rem; bottom: 0.5rem; width: 3px; background-color: #0f172a; border-radius: 0 4px 4px 0; }
      
      .card { background: #ffffff; border-radius: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
      .badge { display:inline-flex; align-items:center; gap:0.25rem; padding:0.125rem 0.5rem; border-radius:0.375rem; font-size:0.75rem; font-weight:500; white-space:nowrap; border: 1px solid transparent; }
      
      /* Crisp Enterprise DataTables styling */
      .dataTables_wrapper { padding: 1rem 1.25rem; }
      .dataTables_wrapper .dataTables_filter { margin-bottom: 1rem; float: right; text-align: right; }
      .dataTables_wrapper .dataTables_filter input { border-radius: 0.375rem; border: 1px solid #cbd5e1; padding: 0.25rem 0.625rem; margin-left: 0.5rem; outline: none; font-size: 0.8125rem; background: #fff; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); transition: all 0.2s; }
      .dataTables_wrapper .dataTables_filter input:focus { border-color: #94a3b8; box-shadow: 0 0 0 1px #94a3b8; }
      .dataTables_wrapper .dataTables_length { margin-bottom: 1rem; float: left; font-size: 0.8125rem; color: #475569; }
      .dataTables_wrapper .dataTables_length select { border-radius: 0.375rem; border: 1px solid #cbd5e1; padding: 0.25rem 1.75rem 0.25rem 0.5rem; margin: 0 0.375rem; outline: none; background: #fff; font-size: 0.8125rem; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
      .dataTables_wrapper .dataTables_info { padding-top: 1rem; font-size: 0.8125rem; color: #475569; float: left; }
      .dataTables_wrapper .dataTables_paginate { padding-top: 1rem; float: right; display: flex; gap: 0.25rem; font-size: 0.8125rem; }

      /* Select2 Tailwind Integration */
      .select2-container .select2-selection--single {
          height: 2.375rem !important; /* Matches py-2 */
          padding: 0.25rem 0.75rem !important; /* Matches px-3 */
          background-color: #fff !important;
          border: 1px solid #e2e8f0 !important; /* border-slate-200 */
          border-radius: 0.375rem !important; /* rounded-md */
          font-size: 0.875rem !important; /* text-sm */
          display: flex !important;
          align-items: center !important;
          box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
      }
      .select2-container--default .select2-selection--single .select2-selection__arrow {
          height: 2.375rem !important;
          right: 0.5rem !important;
      }
      .select2-container--default .select2-selection--single .select2-selection__rendered {
          color: #334155 !important; /* text-slate-700 */
          padding-left: 0 !important;
          line-height: normal !important;
      }
      .select2-container--default.select2-container--focus .select2-selection--single {
          border-color: #6366f1 !important; /* focus:ring-indigo-500 */
          box-shadow: 0 0 0 1px #6366f1 !important;
          outline: none !important;
      }
      .select2-dropdown {
          border: 1px solid #e2e8f0 !important;
          border-radius: 0.375rem !important;
          box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
          font-size: 0.875rem !important;
      }
      .select2-results__option {
          padding: 0.5rem 0.75rem !important;
          color: #334155 !important;
      }
      .select2-container--default .select2-results__option--highlighted[aria-selected] {
          background-color: #6366f1 !important; /* bg-indigo-500 */
          color: white !important;
      }
      .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0.25rem 0.5rem !important; margin: 0 !important; border-radius: 0.375rem !important; border: 1px solid transparent !important; background: transparent !important; color: #475569 !important; cursor: pointer; transition: all 0.2s; }
      .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #f1f5f9 !important; color: #0f172a !important; border-color: transparent !important; }
      .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #fff !important; color: #0f172a !important; border-color: #cbd5e1 !important; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05) !important; font-weight: 500; }
      .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { opacity: 0.4; cursor: not-allowed; }
      
      table.dataTable { border-collapse: collapse !important; border-spacing: 0 !important; width: 100% !important; margin-bottom: 0 !important; border-bottom: none !important; }
      table.dataTable thead th { border-bottom: 1px solid #e2e8f0 !important; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.025em; color: #64748b; font-weight: 500; padding: 0.75rem 1rem !important; background-color: #f8fafc; }
      table.dataTable tbody td { border-bottom: 1px solid #f1f5f9 !important; padding: 0.75rem 1rem !important; font-size: 0.875rem; color: #334155; }
      table.dataTable tbody tr:hover td { background-color: #f8fafc !important; }
      table.dataTable.no-footer { border-bottom: 1px solid #e2e8f0 !important; }
      .dataTables_wrapper::after { content: ""; display: table; clear: both; }
      
      #sidebar { transition: transform 0.2s ease-in-out; }
    </style>
  </head>
  <body class="min-h-screen bg-slate-50 text-slate-900">

<?php $isLoggedIn = session()->has('user_id'); ?>

<?php if ($isLoggedIn): ?>
<?= view('layout/sidebar') ?>
<?= view('layout/topbar') ?>

<!-- Mobile sidebar overlay -->
<div id="sidebar-overlay" onclick="closeSidebar()"
     class="fixed inset-0 bg-black/50 z-20 hidden md:hidden"></div>
<?php endif; ?>

<main id="main-content" class="<?= $isLoggedIn ? 'md:ml-64 pt-14' : 'pt-6' ?> min-h-screen flex justify-center bg-slate-50">
  <div class="p-6 md:p-8 w-full max-w-7xl">

    <?= view($content_view, get_defined_vars()) ?>
  </div>
</main>

<script>
const Toast = Swal.mixin({
  toast: true,
  position: 'bottom-right',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: false,
  customClass: {
    popup: 'rounded-md shadow-lg border border-slate-200 text-sm font-medium',
  },
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

// Global Select2 Init with Fuzzy Word Matching
function fuzzySelect2Matcher(params, data) {
  if (!params.term || $.trim(params.term) === '') {
    return data;
  }
  if (typeof data.text === 'undefined') {
    return null;
  }
  var terms = params.term.toLowerCase().split(/\s+/).filter(Boolean);
  var text = data.text.toLowerCase();
  var match = terms.every(function(term) {
    return text.indexOf(term) > -1;
  });
  return match ? data : null;
}

$(document).ready(function() {
  if ($.fn.select2) {
    $('.select2').select2({
      width: '100%',
      matcher: fuzzySelect2Matcher,
      selectionCssClass: 'border-slate-200 shadow-sm focus:ring-1 focus:ring-indigo-500 rounded-md py-1.5',
    });
  }
});
</script>
</body>
</html>
