<?php
declare(strict_types=1);

// Modul Praktikum PBO Pertemuan 6: Inheritance (Pewarisan Class)

require_once __DIR__ . '/Mahasiswa.php';
require_once __DIR__ . '/MahasiswaReguler.php';
require_once __DIR__ . '/MahasiswaBeasiswa.php';

class DataMahasiswa
{
    /**
     * Repositori polimorfis menyimpan objek bertipe Mahasiswa (Reguler maupun Beasiswa)
     * @var array<string, Mahasiswa>
     */
    private array $data = [];

    public function __construct(?array $awal = null)
    {
        if ($awal !== null) {
            foreach ($awal as $item) {
                if ($item instanceof Mahasiswa) {
                    $this->data[$item->getNim()] = $item;
                }
            }
        } else {
            $this->muatDataAwal();
        }
    }

    /**
     * Menambahkan objek mahasiswa (baik Reguler maupun Beasiswa)
     * Menolak penambahan jika NIM telah terdaftar sebelumnya
     */
    public function tambah(Mahasiswa $mahasiswa): bool
    {
        $nim = $mahasiswa->getNim();
        if (isset($this->data[$nim])) {
            return false;
        }
        $this->data[$nim] = $mahasiswa;
        return true;
    }

    /**
     * Mencari objek mahasiswa berdasarkan kunci NIM
     */
    public function cari(string $nim): ?Mahasiswa
    {
        $nimBersih = trim($nim);
        return $this->data[$nimBersih] ?? null;
    }

    /**
     * Mengembalikan seluruh koleksi mahasiswa aktif
     * @return array<string, Mahasiswa>
     */
    public function semua(): array
    {
        return $this->data;
    }

    /**
     * Memperbarui data objek mahasiswa pada kunci NIM yang sama
     * Mengizinkan pergantian jenis (Reguler ke Beasiswa atau sebaliknya)
     * Menolak jika NIM berbeda atau NIM lama tidak ditemukan
     */
    public function ubah(string $nimLama, Mahasiswa $mahasiswaBaru): bool
    {
        $nimLamaBersih = trim($nimLama);
        if (!isset($this->data[$nimLamaBersih])) {
            return false;
        }

        if ($mahasiswaBaru->getNim() !== $nimLamaBersih) {
            return false;
        }

        $this->data[$nimLamaBersih] = $mahasiswaBaru;
        return true;
    }

    /**
     * Menghapus record mahasiswa berdasarkan NIM
     */
    public function hapus(string $nim): bool
    {
        $nimBersih = trim($nim);
        if (!isset($this->data[$nimBersih])) {
            return false;
        }
        unset($this->data[$nimBersih]);
        return true;
    }

    public function jumlah(): int
    {
        return count($this->data);
    }

    public function reset(): void
    {
        $this->data = [];
        $this->muatDataAwal();
    }

    private function muatDataAwal(): void
    {
        $dataDefault = [
            new MahasiswaReguler(
                nim: "2530521082",
                nama: "Muhamad Mulqi Adiyat Abdilah Ruswandi",
                prodi: "Teknik Informatika",
                semester: 3,
                ukt: 5000000
            ),
            new MahasiswaBeasiswa(
                nim: "2530521015",
                nama: "Siti Nurhaliza",
                prodi: "Teknik Informatika",
                semester: 4,
                ukt: 5000000,
                potonganPersen: 60
            ),
            new MahasiswaReguler(
                nim: "2530521040",
                nama: "Rian Hidayatulloh",
                prodi: "Teknik Informatika",
                semester: 2,
                ukt: 4500000
            ),
            new MahasiswaBeasiswa(
                nim: "2530521099",
                nama: "Aulia Rahma",
                prodi: "Sistem Informasi",
                semester: 5,
                ukt: 4800000,
                potonganPersen: 100
            ),
        ];

        foreach ($dataDefault as $mhs) {
            $this->data[$mhs->getNim()] = $mhs;
        }
    }
}