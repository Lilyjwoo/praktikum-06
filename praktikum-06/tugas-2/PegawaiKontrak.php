<?php
declare(strict_types=1);

require_once __DIR__ . '/Pegawai.php';

class PegawaiKontrak extends Pegawai
{
    private int $upahPerJam;

    public function __construct(
        int $id,
        string $nama,
        int $upahPerJam
    ) {
        parent::__construct($id, $nama);

        if ($upahPerJam < 0) {
            throw new InvalidArgumentException(
                "Upah per jam tidak boleh bernilai negatif."
            );
        }

        $this->upahPerJam = $upahPerJam;
    }

    public function hitungGaji(int $jumlahJam = 0): int
    {
        if ($jumlahJam < 0) {
            throw new InvalidArgumentException(
                "Jumlah jam kerja tidak boleh bernilai negatif."
            );
        }

        return $this->upahPerJam * $jumlahJam;
    }
}