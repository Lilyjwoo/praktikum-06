<?php
declare(strict_types=1);

require_once __DIR__ . '/Pegawai.php';

class PegawaiTetap extends Pegawai
{
    private int $gajiPokok;

    public function __construct(
        int $id,
        string $nama,
        int $gajiPokok
    ) {
        parent::__construct($id, $nama);

        if ($gajiPokok < 0) {
            throw new InvalidArgumentException(
                "Gaji pokok tidak boleh bernilai negatif."
            );
        }

        $this->gajiPokok = $gajiPokok;
    }

    public function hitungGaji(): int
    {
        return $this->gajiPokok;
    }
}