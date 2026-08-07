<?php
function getStandardUnits() {
    return [
        "IGD (Instalasi Gawat Darurat)",
        "Poliklinik / Rawat Jalan",
        "Rawat Inap Umum",
        "ICU",
        "ICVCU",
        "PICU",
        "NICU",
        "Bedah Sentral (IBS)",
        "Hemodialisis & CAPD",
        "Medical Check-Up (MCU)",
        "Kemoterapi",
        "Rehabilitasi Medik",
        "Radiologi",
        "Laboratorium Terpadu",
        "Instalasi Farmasi",
        "Instalasi Gizi",
        "Teknologi Informasi",
        "IPSRS",
        "Rekam Medis",
        "CSSD (Sterilisasi Sentral)",
        "Pemulasaraan Jenazah",
        "Manajemen & Administrasi",
        "Gudang Sentral",
        "Pendaftaran & Kasir",
        "Keamanan / Security",
        "Lainnya"
    ];
}

function format_aset_label(array $a): string {
    $code  = $a['nomor_aset'] ?? '';
    $nama  = $a['nama'] ?? '';
    $specs = array_filter([$a['merk'] ?? '', $a['model'] ?? '']);
    $specStr = !empty($specs) ? ' ' . implode(' ', $specs) : '';
    
    $locs = array_unique(array_filter([
        $a['unit'] ?? '',
        $a['ruangan'] ?? '',
        $a['gedung'] ?? '',
        $a['lokasi'] ?? ''
    ]));
    $locStr = !empty($locs) ? ' (' . implode(' • ', $locs) . ')' : '';

    return trim(($code ? $code . ' - ' : '') . $nama . $specStr . $locStr);
}
