<?php
declare(strict_types=1);

// Child Class MahasiswaBeasiswa extends Mahasiswa
// Modul Praktikum PBO Pertemuan 6: Inheritance (Pewarisan Class)


require_once __DIR__ . '/Mahasiswa.php';

final class MahasiswaBeasiswa extends Mahasiswa
{
    private int $ukt;
    private int $potonganPersen;

    /**
     * Konstruktor child memanggil parent::__construct() lalu memvalidasi UKT dan persentase potongan
     */
    public function __construct(
        string $nim,
        string $nama,
        string $prodi,
        int $semester,
        int $ukt,
        int $potonganPersen
    ) {
        parent::__construct($nim, $nama, $prodi, $semester);

        if ($ukt < 0) {
            throw new InvalidArgumentException("Biaya UKT tidak boleh bernilai negatif.");
        }

        if ($potonganPersen < 0 || $potonganPersen > 100) {
            throw new InvalidArgumentException("Potongan beasiswa harus berupa persentase antara 0 sampai 100 persen.");
        }

        $this->ukt = $ukt;
        $this->potonganPersen = $potonganPersen;
    }

    public function getUkt(): int
    {
        return $this->ukt;
    }

    public function getPotonganPersen(): int
    {
        return $this->potonganPersen;
    }

    /**
     * Implementasi overriding perhitungan biaya untuk mahasiswa beasiswa
     * Menggunakan fungsi intdiv() untuk pembagian bilangan bulat rupiah
     */
    public function hitungBiaya(): int
    {
        return intdiv
            ($this->ukt * (100 - $this->potonganPersen), 100);
    }

    /**
     * Mengembalikankategori mahasiswa
     */
    public function getJenis(): string
    {
        return 'Beasiswa';
    }
}