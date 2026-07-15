<?php $total = count($pengguna ?? []); ?>

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-xl font-bold text-gray-100">Data Pengguna</h1>
    <p class="text-sm text-gray-400 mt-0.5">Kelola akun pengguna sistem IPSRS</p>
  </div>
</div>

<!-- Tambah Pengguna -->
<div class="card p-6 mb-6">
  <div class="flex items-center gap-2 mb-5">
    <div class="w-8 h-8 rounded-lg bg-[#CCFF00]/10 flex items-center justify-center">
      <svg class="w-4 h-4 text-[#CCFF00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14M5 12h14"/>
      </svg>
    </div>
    <h2 class="text-sm font-semibold text-gray-200">Tambah Pengguna</h2>
  </div>
  <form method="POST" action="/ipsrs/pengguna/tambah">
    <?= csrf_field() ?>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Email <span class="text-red-500">*</span></label>
        <input type="email" name="email" value="<?= esc(old('email') ?? '') ?>" required
               placeholder="user@rsud jogja.go.id"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
        <input type="text" name="nama_lengkap" value="<?= esc(old('nama_lengkap') ?? '') ?>" required
               placeholder="Nama lengkap"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Role <span class="text-red-500">*</span></label>
        <select name="role" required
                class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
          <option value="">-- Pilih Role --</option>
          <?php foreach (['Admin', 'Teknisi', 'Pelapor', 'Manajemen'] as $r): ?>
          <option value="<?= $r ?>" <?= old('role') === $r ? 'selected' : '' ?>><?= $r ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-300 mb-1.5">Unit <span class="text-red-500">*</span></label>
        <input type="text" name="unit" value="<?= esc(old('unit') ?? '') ?>" required
               placeholder="Contoh: IGD, ICU,ipsrs"
               class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
      </div>
    </div>
    <div class="mt-4 flex justify-end">
      <button type="submit"
              class="px-8 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/30 hover:-translate-y-0.5 text-white text-[14px] font-bold rounded-2xl transition-all duration-300">
        Simpan Pengguna
      </button>
    </div>
  </form>
</div>

<!-- Search -->
<div class="card p-4 mb-5">
  <form method="GET" action="/ipsrs/pengguna" class="flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[200px]">
      <label class="block text-xs font-medium text-gray-400 mb-1.5">Cari Pengguna</label>
      <input type="text" name="q" value="<?= esc($search ?? '') ?>"
             placeholder="Nama, email, atau role..."
             class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
    </div>
    <button type="submit" class="px-6 py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all duration-300">Cari</button>
    <?php if (!empty($search)): ?>
    <a href="/ipsrs/pengguna" class="px-5 py-2.5 text-sm font-semibold text-gray-400 hover:text-gray-700 rounded-xl bg-[#202532] hover:bg-white/15 transition-colors">Reset</a>
    <?php endif; ?>
  </form>
</div>

<p class="text-sm text-gray-400 mb-3">Menampilkan <span class="font-semibold text-gray-200"><?= $total ?></span> pengguna</p>

<!-- Table -->
<div class="card overflow-hidden">
  <?php if (empty($pengguna)): ?>
  <div class="text-center py-16">
    <p class="text-sm text-gray-400">Belum ada data pengguna.</p>
  </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50/80 border-b border-gray-200/60">
        <tr>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Nama</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Email</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Role</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Unit</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
          <th class="text-left px-5 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php foreach ($pengguna as $u): ?>
        <tr class="hover:bg-indigo-50/40 transition-colors group">
          <td class="px-5 py-3.5 font-medium text-gray-100"><?= esc($u['nama_lengkap'] ?? '-') ?></td>
          <td class="px-5 py-3.5 text-gray-300"><?= esc($u['email'] ?? '-') ?></td>
          <td class="px-5 py-3.5">
            <?php
              $roleColors = [
                'Admin'     => 'bg-purple-100 text-purple-700',
                'Teknisi'   => 'bg-blue-100 text-blue-700',
                'Pelapor'   => 'bg-green-100 text-green-700',
                'Manajemen' => 'bg-amber-100 text-amber-700',
              ];
              $role = $u['role'] ?? 'Pelapor';
              $color = $roleColors[$role] ?? 'bg-[#202532] text-gray-200';
            ?>
            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold <?= $color ?>"><?= esc($role) ?></span>
          </td>
          <td class="px-5 py-3.5 text-gray-300"><?= esc($u['unit'] ?? '-') ?></td>
          <td class="px-5 py-3.5">
            <?php if (!empty($u['aktif'])): ?>
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
            </span>
            <?php else: ?>
            <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-400">
              <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span> Nonaktif
            </span>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3.5 text-right">
            <button type="button"
                    onclick="editPengguna(this)"
                    data-id="<?= esc($u['id'] ?? '') ?>"
                    data-nama="<?= esc($u['nama_lengkap'] ?? '') ?>"
                    data-role="<?= esc($u['role'] ?? '') ?>"
                    data-unit="<?= esc($u['unit'] ?? '') ?>"
                    data-aktif="<?= !empty($u['aktif']) ? '1' : '0' ?>"
                    class="text-xs text-[#CCFF00] hover:text-indigo-800 font-medium hover:underline">Edit</button>
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
  <div class="absolute inset-0 bg-black/50" onclick="closeEdit()"></div>
  <div class="relative bg-[#121620]/60 rounded-2xl shadow-xl w-full max-w-md p-6">
    <h3 class="text-sm font-semibold text-gray-100 mb-4">Edit Pengguna</h3>
    <form id="edit-form" method="POST">
      <?= csrf_field() ?>
      <div class="space-y-3">
        <div>
          <label class="block text-xs font-semibold text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
          <input type="text" name="nama_lengkap" id="edit-nama" required
                 class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-300 mb-1.5">Role <span class="text-red-500">*</span></label>
          <select name="role" id="edit-role" required
                  class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
            <?php foreach (['Admin', 'Teknisi', 'Pelapor', 'Manajemen'] as $r): ?>
            <option value="<?= $r ?>"><?= $r ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-300 mb-1.5">Unit <span class="text-red-500">*</span></label>
          <input type="text" name="unit" id="edit-unit" required
                 class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-300 mb-1.5">Status</label>
          <select name="aktif" id="edit-aktif"
                  class="w-full px-3 py-2.5 text-sm bg-[#181C25]/80 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 appearance-none">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
        </div>
      </div>
      <div class="mt-5 flex items-center justify-end gap-3">
        <button type="button" onclick="closeEdit()" class="px-4 py-2 text-sm text-gray-400 hover:text-gray-700">Batal</button>
        <button type="submit" class="px-5 py-2 bg-[#CCFF00] text-black border-none hover:bg-[#B3E600] text-black text-white text-sm font-semibold rounded-xl transition-colors">Simpan</button>
      </div>
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
</script>
