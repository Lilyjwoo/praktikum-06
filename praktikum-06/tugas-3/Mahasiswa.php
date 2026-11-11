<?php
declare(strict_types=1);
// Parent Abstract Class Mahasiswa
// Modul Praktikum PBO Pertemuan 6: Inheritance (Pewarisan Class)
abstract class Mahasiswa
{
    // Property private tetap menjaga enkapsulasi agar tidak diakses langsung dari luar
    private string $nim;
    private string $nama;
    private string $prodi;
    private int $semester;

    /**
     * Konstruktor parent menginisialisasi dan memvalidasi data identitas umum
     */
    public function __construct(
        string $nim, 
        string $nama,
        string $prodi,
        int $semester
    ) {
        $nimInput = trim($nim);
        $namaInput = trim($nama);
        $prodiInput = trim($prodi);

        if ($nimInput === '' || $namaInput === '' || $prodiInput === '') {
            throw new InvalidArgumentException("Identitas mahasiswa (NIM, Nama, dan Program Studi) wajib diisi.");
        }

        if (!ctype_digit($nimInput)) {
            throw new InvalidArgumentException("NIM harus berupa digit angka numerik.");
        }

        if ($semester < 1 || $semester > 14) {
            throw new InvalidArgumentException("Semester harus berupa bilangan bulat antara 1 sampai 14.");
        }

        $this->nim = $nimInput;
        $this->nama = $namaInput;
        $this->prodi = $prodiInput;
        $this->semester = $semester;
    }

    public function getNim(): string
    {
        return $this->nim;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function getProdi(): string
    {
        return $this->prodi;
    }

    public function getSemester(): int
    {
        return $this->semester;
    }

    /**
     * Method abstrak yang mewajibkan seluruh class turunan mendefinisikan perhitungan biaya
     */
    abstract public function hitungBiaya(): int;

    /**
     * Method abstrak yang mengembalikan label kategori mahasiswa
     */
    abstract public function getJenis(): string;
}