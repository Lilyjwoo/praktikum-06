<?php
declare(strict_types=1);

require_once __DIR__ . '/PegawaiTetap.php';
require_once __DIR__ . '/PegawaiKontrak.php';

$pegawaiTetap = new PegawaiTetap(
    id: 1,
    nama: "Fadhilah Raihannisa",
    gajiPokok: 6500000
);

$pegawaiKontrak = new PegawaiKontrak(
    id: 2,
    nama: "Raihannisa Fadhilah",
    upahPerJam: 50000
);

$jamKerja = 160;

$gajiTetap = $pegawaiTetap->hitungGaji();
$gajiKontrak = $pegawaiKontrak->hitungGaji($jamKerja);

function rupiah(int $nilai): string
{
    return 'Rp' . number_format($nilai, 0, ',', '.');
}

$validasiBerhasil = false;

try {
    new PegawaiKontrak(
        id: 3,
        nama: "Anonim",
        upahPerJam: -50000
    );
} catch (InvalidArgumentException $e) {
    $validasiBerhasil = true;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tugas 2 - Inheritance Pegawai</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #eef1f6;
            color: #1f2937;
        }

        .container {
            width: 92%;
            max-width: 1050px;
            margin: 40px auto;
        }

        /* HEADER */
        .header {
            background: white;
            border-radius: 18px;
            padding: 32px;
            margin-bottom: 25px;
            border: 1px solid #e1e5ec;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.06);
        }

        .label {
            color: #5b6475;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header h1 {
            margin: 8px 0;
            font-size: 32px;
        }

        .header p {
            margin: 0;
            color: #667085;
            line-height: 1.7;
        }

        /* INFORMASI */
        .info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .info-box {
            background: white;
            padding: 18px;
            border-radius: 14px;
            border: 1px solid #e1e5ec;
        }

        .info-box span {
            display: block;
            font-size: 12px;
            color: #7b8494;
            margin-bottom: 6px;
        }

        .info-box strong {
            font-size: 16px;
        }

        /* PEGAWAI */
        .pegawai-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .pegawai {
            background: white;
            border-radius: 18px;
            padding: 25px;
            border: 1px solid #e1e5ec;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.05);
        }

        .pegawai-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .pegawai-header h2 {
            margin: 0;
            font-size: 18px;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .tetap {
            background: #e8edff;
            color: #4054a5;
        }

        .kontrak {
            background: #e5f7ef;
            color: #18794e;
        }

        .nama {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .id {
            color: #7b8494;
            font-size: 13px;
            margin-bottom: 22px;
        }

        .detail {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #edf0f4;
            font-size: 14px;
        }

        .detail span {
            color: #667085;
        }

        .detail strong {
            text-align: right;
        }

        .total {
            margin-top: 20px;
            padding: 15px;
            background: #f5f7fa;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total span {
            font-size: 13px;
            color: #667085;
        }

        .total strong {
            font-size: 20px;
        }

        /* VALIDASI */
        .validation {
            background: #1f2937;
            color: white;
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .validation h2 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .validation p {
            color: #cbd1dc;
            line-height: 1.7;
            margin-bottom: 15px;
        }

        .status {
            display: inline-block;
            background: #d9f7e8;
            color: #176b45;
            padding: 8px 13px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: bold;
        }

        footer {
            text-align: center;
            color: #8a93a3;
            font-size: 13px;
            padding: 10px;
        }

        @media (max-width: 750px) {

            .info,
            .pegawai-grid {
                grid-template-columns: 1fr;
            }

            .container {
                width: 94%;
            }

            .header h1 {
                font-size: 27px;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- HEADER -->
        <div class="header">

            <div class="label">
                PBO • Praktikum 06
            </div>

            <h1>Sistem Penggajian Pegawai</h1>

            <p>
                Implementasi inheritance menggunakan parent class
                <b>Pegawai</b> dengan turunan
                <b>PegawaiTetap</b> dan <b>PegawaiKontrak</b>.
            </p>

        </div>


        <!-- INFORMASI -->
        <div class="info">

            <div class="info-box">
                <span>Parent Class</span>
                <strong>Pegawai</strong>
            </div>

            <div class="info-box">
                <span>Child Class</span>
                <strong>2 Class</strong>
            </div>

            <div class="info-box">
                <span>Konsep</span>
                <strong>Inheritance</strong>
            </div>

        </div>


        <!-- DATA PEGAWAI -->
        <div class="pegawai-grid">

            <!-- PEGAWAI TETAP -->
            <div class="pegawai">

                <div class="pegawai-header">

                    <h2>Pegawai Tetap</h2>

                    <span class="badge tetap">
                        PEGAWAI TETAP
                    </span>

                </div>

                <div class="nama">
                    <?= htmlspecialchars($pegawaiTetap->getNama()) ?>
                </div>

                <div class="id">
                    ID Pegawai: PGW-001
                </div>

                <div class="detail">
                    <span>Gaji Pokok</span>
                    <strong><?= rupiah($gajiTetap) ?></strong>
                </div>

                <div class="total">
                    <span>Total Gaji</span>
                    <strong><?= rupiah($gajiTetap) ?></strong>
                </div>

            </div>


            <!-- PEGAWAI KONTRAK -->
            <div class="pegawai">

                <div class="pegawai-header">

                    <h2>Pegawai Kontrak</h2>

                    <span class="badge kontrak">
                        PEGAWAI KONTRAK
                    </span>

                </div>

                <div class="nama">
                    <?= htmlspecialchars($pegawaiKontrak->getNama()) ?>
                </div>

                <div class="id">
                    ID Pegawai: PGW-002
                </div>

                <div class="detail">
                    <span>Upah Per Jam</span>
                    <strong><?= rupiah(50000) ?></strong>
                </div>

                <div class="detail">
                    <span>Jumlah Jam</span>
                    <strong><?= $jamKerja ?> jam</strong>
                </div>

                <div class="total">
                    <span>Total Gaji</span>
                    <strong><?= rupiah($gajiKontrak) ?></strong>
                </div>

            </div>

        </div>


        <!-- VALIDASI -->
        <div class="validation">

            <h2>Pengujian Validasi</h2>

            <p>
                Dilakukan pengujian dengan memasukkan nilai upah per jam
                negatif sebesar <b>-Rp50.000</b>. Sistem seharusnya menolak
                nilai tersebut melalui <b>InvalidArgumentException</b>.
            </p>

            <?php if ($validasiBerhasil): ?>

                <span class="status">
                    ✓ Validasi berhasil — nilai negatif ditolak
                </span>

            <?php endif; ?>

        </div>


        <footer>
            Praktikum Pemrograman Berorientasi Objek • PHP 8+
        </footer>

    </div>

</body>

</html>