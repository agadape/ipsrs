
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Daftar Aset Dihapuskan</h1>
    <p class="text-sm font-medium text-slate-500 mt-1">Arsip dan Berita Acara (BA) penghapusan aset.</p>
  </div>
</div>

<div class="card p-0 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Aset</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Berita Acara</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Tindak Lanjut</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Tanggal BA</th>
          <th class="px-6 py-3 font-medium text-slate-500 text-xs uppercase tracking-wider">Dokumen</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if(empty($penghapusan)): ?>
          <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">Belum ada aset yang dihapuskan.</td></tr>
        <?php else: foreach($penghapusan as $p): ?>
          <tr class="hover:bg-slate-50 transition-colors">
            <td class="px-6 py-4">
              <a href="/ipsrs/aset/series/<?= esc($p["id_aset_series"]) ?>" class="font-medium text-slate-800 hover:text-red-600 transition-colors"><?= esc($p["nama_aset"] ?? "-") ?></a>
              <span class="block text-xs text-red-600 font-mono"><?= esc($p["nomor_aset"] ?? "-") ?></span>
            </td>
            <td class="px-6 py-4 text-slate-600 font-medium"><?= esc($p["no_ba"] ?? "-") ?></td>
            <td class="px-6 py-4">
              <span class="badge bg-slate-100 text-slate-700 border border-slate-200"><?= esc($p["tindak_lanjut"] ?? "-") ?></span>
            </td>
            <td class="px-6 py-4 text-slate-600"><?= tgl($p["tgl_ba"]) ?></td>
            <td class="px-6 py-4">
              <?php if(!empty($p["file_dokumen_ba"])): ?>
                <a href="/uploads/ba/<?= esc($p["file_dokumen_ba"]) ?>" target="_blank" class="text-xs font-medium text-blue-600 hover:underline inline-flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                  Lihat PDF
                </a>
              <?php else: ?>
                <span class="text-xs text-slate-400">Tidak ada file</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

