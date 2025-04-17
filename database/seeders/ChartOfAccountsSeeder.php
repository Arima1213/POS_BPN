<?php

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [

            // ===== ASET LANCAR =====
            ['kode' => '1000', 'nama' => 'Kas Kecil', 'kelompok' => 'aset', 'tipe' => 'lancar', 'deskripsi' => 'Kas tunai usaha'],
            ['kode' => '1010', 'nama' => 'Rekening Bank', 'kelompok' => 'aset', 'tipe' => 'lancar', 'deskripsi' => 'Saldo bank perusahaan'],
            ['kode' => '1020', 'nama' => 'Piutang Usaha Produk', 'kelompok' => 'aset', 'tipe' => 'lancar', 'deskripsi' => 'Piutang dari pelanggan produk'],
            ['kode' => '1021', 'nama' => 'Piutang Jasa Giling', 'kelompok' => 'aset', 'tipe' => 'lancar', 'deskripsi' => 'Piutang jasa penggilingan padi'],
            ['kode' => '1030', 'nama' => 'Persediaan Pupuk dan Benih', 'kelompok' => 'aset', 'tipe' => 'lancar', 'deskripsi' => 'Persediaan barang dagang pertanian'],
            ['kode' => '1031', 'nama' => 'Persediaan Produk Jadi (Beras)', 'kelompok' => 'aset', 'tipe' => 'lancar', 'deskripsi' => 'Stok hasil penggilingan padi'],

            // ===== ASET TETAP =====
            ['kode' => '1100', 'nama' => 'Mesin Penggiling Padi', 'kelompok' => 'aset', 'tipe' => 'tetap', 'deskripsi' => 'Mesin utama jasa giling'],
            ['kode' => '1101', 'nama' => 'Alat Pertanian (Traktor, Pompa)', 'kelompok' => 'aset', 'tipe' => 'tetap', 'deskripsi' => 'Aset tetap pertanian'],
            ['kode' => '1110', 'nama' => 'Bangunan Gudang', 'kelompok' => 'aset', 'tipe' => 'tetap', 'deskripsi' => 'Gudang penyimpanan hasil'],

            // ===== KEWAJIBAN =====
            ['kode' => '2000', 'nama' => 'Hutang Usaha', 'kelompok' => 'kewajiban', 'tipe' => 'jangka_pendek', 'deskripsi' => 'Hutang pembelian produk'],
            ['kode' => '2010', 'nama' => 'Hutang Leasing Alat Berat', 'kelompok' => 'kewajiban', 'tipe' => 'jangka_panjang', 'deskripsi' => 'Sewa alat secara cicilan'],

            // ===== EKUITAS =====
            ['kode' => '3000', 'nama' => 'Modal Pemilik', 'kelompok' => 'ekuitas', 'tipe' => 'modal', 'deskripsi' => 'Investasi pemilik usaha'],
            ['kode' => '3010', 'nama' => 'Prive Pemilik', 'kelompok' => 'ekuitas', 'tipe' => 'modal', 'deskripsi' => 'Pengambilan pribadi'],

            // ===== PENDAPATAN =====
            ['kode' => '4000', 'nama' => 'Pendapatan Penjualan Produk', 'kelompok' => 'pendapatan', 'tipe' => 'operasional', 'deskripsi' => 'Pendapatan dari pupuk, benih, alat pertanian'],
            ['kode' => '4010', 'nama' => 'Pendapatan Jasa Giling Padi', 'kelompok' => 'pendapatan', 'tipe' => 'operasional', 'deskripsi' => 'Jasa penggilingan padi menjadi beras'],
            ['kode' => '4020', 'nama' => 'Pendapatan Lainnya', 'kelompok' => 'pendapatan', 'tipe' => 'non_operasional', 'deskripsi' => 'Misalnya sewa lahan, jasa tambahan'],

            // ===== BEBAN-BEBAN =====
            ['kode' => '5000', 'nama' => 'Beban Gaji Karyawan', 'kelompok' => 'beban', 'tipe' => 'operasional', 'jenis_beban' => 'beban_kas', 'deskripsi' => 'Biaya tenaga kerja'],
            ['kode' => '5010', 'nama' => 'Beban Solar & Listrik Mesin', 'kelompok' => 'beban', 'tipe' => 'operasional', 'jenis_beban' => 'beban_kas', 'deskripsi' => 'BBM dan listrik operasional'],
            ['kode' => '5020', 'nama' => 'Beban Penyusutan Mesin', 'kelompok' => 'beban', 'tipe' => 'non_operasional', 'jenis_beban' => 'beban_non_kas', 'deskripsi' => 'Penyusutan mesin dan alat'],
            ['kode' => '5030', 'nama' => 'Beban Perawatan Alat', 'kelompok' => 'beban', 'tipe' => 'operasional', 'jenis_beban' => 'beban_usaha', 'deskripsi' => 'Perbaikan mesin, traktor, dll'],
            ['kode' => '5040', 'nama' => 'Beban Operasional Toko', 'kelompok' => 'beban', 'tipe' => 'operasional', 'jenis_beban' => 'beban_usaha', 'deskripsi' => 'Biaya air, telepon, internet, dll'],

        ];

        foreach ($accounts as $account) {
            ChartOfAccount::create($account);
        }
    }
}