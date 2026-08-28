
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Catatan Peminjaman Aset</h1>
    <p class="text-sm font-medium text-slate-500 mt-1">Daftar aset yang sedang dan pernah dipinjamkan.</p>
  </div>
</div>

<div class="card p-0 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Aset</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Peminjam</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Tgl Pinjam</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Jatuh Tempo</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if(empty($peminjaman)): ?>
          <tr><td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada catatan peminjaman.</td></tr>
        <?php else: foreach($peminjaman as $p): ?>
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-6 py-4">
              <span class="block font-medium text-slate-800"><?= esc($p["nama_aset"] ?? "-") ?></span>
              <span class="block text-xs text-red-600 font-mono"><?= esc($p["nomor_aset"] ?? "-") ?></span>
            </td>
            <td class="px-6 py-4">
              <span class="block font-medium text-slate-800"><?= esc($p["nama_peminjam"] ?? "-") ?></span>
              <span class="block text-xs text-slate-500"><?= esc($p["unit_peminjam"] ?? "-") ?></span>
            </td>
            <td class="px-6 py-4 text-slate-600"><?= tgl($p["tgl_pinjam"]) ?></td>
            <td class="px-6 py-4 text-slate-600"><?= tgl($p["tgl_kembali_rencana"]) ?></td>
            <td class="px-6 py-4">
              <?php if($p["status"] === "Dipinjam"): ?>
                <span class="badge bg-amber-50 text-amber-700 border border-amber-200">Dipinjam</span>
              <?php else: ?>
                <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200">Dikembalikan</span>
                <span class="block text-[10px] text-slate-400 mt-1">(<?= tgl($p["tgl_kembali_aktual"] ?? "") ?>)</span>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4">
              <a href="/ipsrs/aset/series/<?= esc($p["id_aset_series"]) ?>" class="text-xs font-medium text-blue-600 hover:underline">Lihat Aset</a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

