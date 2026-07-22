<?php

namespace App\Controllers;

use App\Config\IPSRS;
use App\Models\LKModel;
use App\Models\StokModel;
use App\Models\JadwalModel;

class Dashboard extends BaseController
{
    public function index()
    {
        if (session('user_role') === 'pelapor') {
            return redirect()->to('/ipsrs/lk');
        }

        $lkModel     = new LKModel();
        $stokModel   = new StokModel();
        $jadwalModel = new JadwalModel();

        $allLK     = $lkModel->getAll();
        $allStok   = $stokModel->getAll();
        $allJadwal = $jadwalModel->getAll();

        $today     = date('Y-m-d');
        $thisMonth = date('Y-m');

        // Status rail counts
        $belumDisurvei = count(array_filter($allLK, fn($l) =>
            in_array($l['status'], IPSRS::STATUS_LK_BELUM_DISURVEI)));
        $mnggSC = count(array_filter($allLK, fn($l) =>
            in_array($l['status'], IPSRS::STATUS_LK_MENUNGGU)));
        $pmTerlambat = count(array_filter($allJadwal, fn($j) =>
            $j['tanggal'] < $today && $j['status'] !== IPSRS::STATUS_JADWAL[1]));
        $stokMenipis = count(array_filter($allStok, fn($s) =>
            $s['stok_tersedia'] <= $s['minimum_stok']));
        $selesaiHariIni = count(array_filter($allLK, fn($l) =>
            $l['status'] === IPSRS::STATUS_LK[6] && $l['tanggal_selesai'] === $today));

        // KPI
        $lkWithRT = array_filter($allLK, fn($l) => $l['response_time'] !== null);
        $slaPct   = count($lkWithRT) > 0
            ? round(count(array_filter($lkWithRT, fn($l) => $l['response_time'] <= IPSRS::SLA_RESPONSE_TIME)) / count($lkWithRT) * 100)
            : 0;
        $avgRespon = count($lkWithRT) > 0
            ? round(array_sum(array_column(array_values($lkWithRT), 'response_time')) / count($lkWithRT))
            : 0;
        $activeLK = count(array_filter($allLK, fn($l) => $l['status'] !== IPSRS::STATUS_LK[6]));

        // PM bulan ini
        $jadwalBulan  = array_filter($allJadwal, fn($j) => str_starts_with($j['tanggal'], $thisMonth));
        $jadwalSelesai = count(array_filter($jadwalBulan, fn($j) => $j['status'] === IPSRS::STATUS_JADWAL[1]));
        $jadwalTotal  = count($jadwalBulan);
        $pmPct        = $jadwalTotal > 0 ? round($jadwalSelesai / $jadwalTotal * 100) : 0;

        // Pipeline
        $pipeline = [
            ['label' => 'Lap. Masuk',    'status' => [IPSRS::STATUS_LK[0]],                       'color' => 'bg-slate-300'],
            ['label' => 'Disposisi',     'status' => [IPSRS::STATUS_LK[1]],                       'color' => 'bg-slate-400'],
            ['label' => 'Survei',        'status' => [IPSRS::STATUS_LK[2]],                       'color' => 'bg-indigo-400'],
            ['label' => 'Perbaikan',     'status' => [IPSRS::STATUS_LK[3]],                       'color' => 'bg-indigo-500'],
            ['label' => 'Mngg SC/Vendor','status' => [IPSRS::STATUS_LK[4], IPSRS::STATUS_LK[5]], 'color' => 'bg-amber-400'],
            ['label' => 'Selesai',       'status' => [IPSRS::STATUS_LK[6]],                       'color' => 'bg-emerald-400'],
        ];
        foreach ($pipeline as &$stage) {
            $stage['count'] = count(array_filter($allLK, fn($l) => in_array($l['status'], $stage['status'])));
        }
        unset($stage);
        $pipelineMax = max(array_column($pipeline, 'count') ?: [1]);

        // Recent LK (5 terbaru)
        usort($allLK, fn($a, $b) =>
            strcmp($b['tanggal'], $a['tanggal']) ?: strcmp($b['jam_laporan'], $a['jam_laporan']));
        $recentLK = array_slice($allLK, 0, 5);

        // Upcoming jadwal
        $upcoming = array_filter($allJadwal, fn($j) => $j['tanggal'] >= $today);
        usort($upcoming, fn($a, $b) => strcmp($a['tanggal'], $b['tanggal']));
        $upcoming = array_slice(array_values($upcoming), 0, 4);

        // Priority items
        $priority = [];
        $unresponded = array_filter($allLK, fn($l) =>
            in_array($l['status'], IPSRS::STATUS_LK_BELUM_DISURVEI));
        usort($unresponded, fn($a, $b) =>
            strcmp($a['tanggal'], $b['tanggal']) ?: strcmp($a['jam_laporan'], $b['jam_laporan']));
        if (!empty($unresponded)) {
            $o = array_values($unresponded)[0];
            $priority[] = ['level' => 'critical', 'title' => $o['no_order'] . ' belum disurvei',
                'desc' => $o['keluhan'] . ' · ' . $o['lokasi'], 'action' => 'Tangani', 'path' => '/ipsrs/lk/' . $o['id']];
        }
        if ($pmTerlambat > 0) {
            $priority[] = ['level' => 'warning', 'title' => $pmTerlambat . ' jadwal PM melewati batas',
                'desc' => 'Perlu dijadwalkan ulang', 'action' => 'Lihat', 'path' => '/ipsrs/preventif'];
        }
        $kritisStok = array_filter($allStok, fn($s) => $s['stok_tersedia'] <= $s['minimum_stok']);
        if (!empty($kritisStok)) {
            usort($kritisStok, fn($a, $b) => $a['stok_tersedia'] <=> $b['stok_tersedia']);
            $w = array_values($kritisStok)[0];
            $priority[] = ['level' => 'warning',
                'title' => ($w['stok_tersedia'] == 0 ? 'Stok habis' : 'Stok menipis') . ': ' . $w['nama'],
                'desc' => $w['stok_tersedia'] . ' ' . $w['satuan'] . ' tersisa · min ' . $w['minimum_stok'],
                'action' => 'Restok', 'path' => '/ipsrs/stok'];
        }

        return $this->render('pages/dashboard', compact(
            'belumDisurvei', 'mnggSC', 'pmTerlambat', 'stokMenipis', 'selesaiHariIni',
            'slaPct', 'avgRespon', 'activeLK',
            'jadwalSelesai', 'jadwalTotal', 'pmPct',
            'pipeline', 'pipelineMax',
            'recentLK', 'upcoming', 'priority',
            'today'
        ));
    }
}
