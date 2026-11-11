<?php
declare(strict_types=1);

class Pegawai
{
    protected int $id;
    protected string $nama;

    public function __construct(int $id, string $nama)
    {
        if ($id < 0) {
            throw new InvalidArgumentException(
                "ID pegawai tidak boleh bernilai negatif."
            );
        }

        $namaInput = trim($nama);

        if ($namaInput === '') {
            throw new InvalidArgumentException(
                "Nama pegawai wajib diisi."
            );
        }

        $this->id = $id;
        $this->nama = $namaInput;
    }

    public function getNama(): string
    {
        return $this->nama;
    }
}