<?php
declare(strict_types=1);


// Modul Praktikum PBO Pertemuan 6: Inheritance (Pewarisan Class)

require_once __DIR__ . '/Mahasiswa.php';

final class MahasiswaReguler extends Mahasiswa
{
    private int $ukt;

    /**
     * Konstruktor child memanggil parent::__construct() lalu memvalidasi UKT
     */
    public function __construct(
        string $nim,
        string $nama,
        string $prodi,
        int $semester,
        int $ukt
    ) {
        parent::__construct($nim, $nama, $prodi, $semester);

        if ($ukt < 0) {
            throw new InvalidArgumentException("Biaya UKT reguler tidak boleh bernilai negatif.");
        }
        $this->ukt = $ukt;
    }

    public function getUkt(): int
    {
        return $this->ukt;
    }

    /**
     * Mahasiswa reguler membayar UKT sesuai nominal yang ditentukan
     */
    public function hitungBiaya(): int
    {
        return $this->ukt;
    }

    /**
     *  Mengembalikan kategori mahasiswa
     */
    public function getJenis(): string
    {
        return 'Reguler';
    }
}