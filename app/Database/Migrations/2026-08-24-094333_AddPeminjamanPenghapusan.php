<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPeminjamanPenghapusan extends Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS peminjaman_aset (
            id CHAR(36) PRIMARY KEY,
            id_aset_series CHAR(36) NOT NULL,
            nama_peminjam VARCHAR(150) NOT NULL,
            unit_peminjam VARCHAR(100) NOT NULL,
            tgl_pinjam DATE NOT NULL,
            tgl_kembali_rencana DATE NOT NULL,
            tgl_kembali_aktual DATE NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'Dipinjam',
            keterangan TEXT NULL,
            id_admin CHAR(36) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        
        $this->db->query("CREATE TABLE IF NOT EXISTS penghapusan_aset (
            id CHAR(36) PRIMARY KEY,
            id_aset_series CHAR(36) NOT NULL,
            no_ba VARCHAR(100) NOT NULL,
            tgl_ba DATE NOT NULL,
            tindak_lanjut VARCHAR(50) NOT NULL,
            file_dokumen_ba VARCHAR(255) NULL,
            keterangan TEXT NULL,
            id_admin CHAR(36) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function down()
    {
        $this->forge->dropTable('peminjaman_aset', true);
        $this->forge->dropTable('penghapusan_aset', true);
    }
}
