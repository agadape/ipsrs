<?php $total = count($pengguna ?? []); ?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Data Pengguna</h1>
    <p class="text-sm text-slate-500 mt-1">Kelola akun pengguna sistem IPSRS</p>
  </div>
</div>

<!-- Tambah Pengguna -->
<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-5">
    <h2 class="text-sm font-semibold text-slate-800">Tambah Pengguna</h2>
  </div>
  <form method="POST" action="/ipsrs/pengguna/tambah">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Email <span class="text-red-500">*</span></label>
        <input type="email" name="email" value="<?= esc(old('email') ?? '') ?>" required
               placeholder="user@rsud.go.id"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" name="nama_lengkap" value="<?= esc(old('nama_lengkap') ?? '') ?>" required
               placeholder="Nama lengkap"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Password <span class="text-red-500">*</span></label>
        <input type="password" name="password" required
               placeholder="Minimal 6 karakter"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Role <span class="text-red-500">*</span></label>
        <select name="role" required
                class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
          <option value="">-- Pilih Role --</option>
          <?php foreach (['Admin', 'Teknisi', 'Pelapor', 'Manajemen'] as $r): ?>
          <option value="<?= $r ?>" <?= old('role') === $r ? 'selected' : '' ?>><?= $r ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Unit <span class="text-red-500">*</span></label>
        <input type="text" name="unit" value="<?= esc(old('unit') ?? '') ?>" required
               placeholder="Contoh: IGD, ICU"
               class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
      </div>
    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit"
              class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors shadow-sm">
        Simpan Pengguna
      </button>
    </div>
  </form>
</div>

<!-- Search -->
<div class="card p-4 mb-6">
  <form method="GET" action="/ipsrs/pengguna" class="flex flex-wrap gap-4 items-end">
    <div class="flex-1 min-w-[200px]">
      <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Cari Pengguna</label>
      <input type="text" name="q" value="<?= esc($search ?? '') ?>"
             placeholder="Nama, email, atau role..."
             class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
    </div>
    <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-md shadow-sm transition-colors">Cari</button>
    <?php if (!empty($search)): ?>
    <a href="/ipsrs/pengguna" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">Reset</a>
    <?php endif; ?>
  </form>
</div>

<p class="text-sm text-slate-500 mb-4">Menampilkan <span class="font-semibold text-slate-900"><?= $total ?></span> pengguna</p>

<!-- Table -->
<div class="card overflow-hidden">
  <?php if (empty($pengguna)): ?>
  <div class="text-center py-16">
    <p class="text-sm text-slate-500">Belum ada data pengguna.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto p-0">
    <table class="w-full text-sm text-left border-collapse">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Nama</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Role</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Unit</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
          <th class="py-3 px-4 text-xs font-medium text-slate-500 uppercase tracking-wider text-right"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php foreach ($pengguna as $u): ?>
        <tr class="hover:bg-slate-50 transition-colors group">
          <td class="px-4 py-3 font-medium text-slate-900"><?= esc($u['nama_lengkap'] ?? '-') ?></td>
          <td class="px-4 py-3 text-slate-600"><?= esc($u['email'] ?? '-') ?></td>
          <td class="px-4 py-3">
            <?php
              $roleColors = [
                'Admin'     => 'bg-purple-50 text-purple-700 border border-purple-200',
                'Teknisi'   => 'bg-blue-50 text-blue-700 border border-blue-200',
                'Pelapor'   => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                'Manajemen' => 'bg-amber-50 text-amber-700 border border-amber-200',
              ];
              $role = $u['role'] ?? 'Pelapor';
              $color = $roleColors[$role] ?? 'bg-slate-50 text-slate-700 border border-slate-200';
            ?>
            <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium <?= $color ?>"><?= esc($role) ?></span>
          </td>
          <td class="px-4 py-3 text-slate-600"><?= esc($u['unit'] ?? '-') ?></td>
          <td class="px-4 py-3">
            <?php if (!empty($u['aktif'])): ?>
            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
            </span>
            <?php else: ?>
            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500">
              <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Nonaktif
            </span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3 text-right">
            <button type="button"
                    onclick="editPengguna(this)"
                    data-id="<?= esc($u['id'] ?? '') ?>"
                    data-nama="<?= esc($u['nama_lengkap'] ?? '') ?>"
                    data-role="<?= esc($u['role'] ?? '') ?>"
                    data-unit="<?= esc($u['unit'] ?? '') ?>"
                    data-aktif="<?= !empty($u['aktif']) ? '1' : '0' ?>"
                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium px-3 py-1.5 rounded-md border border-slate-200 bg-white hover:bg-slate-50 transition-colors">Edit</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
  <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeEdit()"></div>
  <div class="relative bg-white rounded-lg shadow-xl border border-slate-200 w-full max-w-md p-6">
    <h3 class="text-base font-bold text-slate-800 mb-4">Edit Pengguna</h3>
    <form id="edit-form" method="POST">
      <?= csrf_field() ?>
      <div class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nama Lengkap <span class="text-red-500">*</span></label>
          <input type="text" name="nama_lengkap" id="edit-nama" required
                 class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Role <span class="text-red-500">*</span></label>
          <select name="role" id="edit-role" required
                  class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
            <?php foreach (['Admin', 'Teknisi', 'Pelapor', 'Manajemen'] as $r): ?>
            <option value="<?= $r ?>"><?= $r ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Unit <span class="text-red-500">*</span></label>
          <select name="unit" id="edit-unit" required
                  class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
            <option value="">-- Pilih Unit --</option>
            <?php foreach (getStandardUnits() as $u): ?>
              <option value="<?= esc($u) ?>"><?= esc($u) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Status</label>
          <select name="aktif" id="edit-aktif"
                  class="w-full px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm transition-colors">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
        </div>
      </div>
      <div class="mt-6 flex items-center justify-between">
        <button type="button" onclick="hapusPengguna()" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors">Hapus</button>
        <div class="flex items-center gap-3">
          <button type="button" onclick="closeEdit()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Batal</button>
          <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">Simpan</button>
        </div>
      </div>
    </form>
    <form id="delete-form" method="POST" class="hidden">
      <?= csrf_field() ?>
    </form>
  </div>
</div>

<script>
  function editPengguna(btn) {
    document.getElementById('edit-form').action = '/ipsrs/pengguna/' + btn.dataset.id + '/edit';
    document.getElementById('edit-nama').value   = btn.dataset.nama || '';
    document.getElementById('edit-role').value   = btn.dataset.role || 'Pelapor';
    document.getElementById('edit-unit').value   = btn.dataset.unit || '';
    document.getElementById('edit-aktif').value  = btn.dataset.aktif || '1';
    document.getElementById('edit-modal').classList.remove('hidden');
  }
  function closeEdit() {
    document.getElementById('edit-modal').classList.add('hidden');
  }
  function hapusPengguna() {
    if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
      const actionUrl = document.getElementById('edit-form').action.replace('/edit', '/delete');
      document.getElementById('delete-form').action = actionUrl;
      document.getElementById('delete-form').submit();
    }
  }
</script>
