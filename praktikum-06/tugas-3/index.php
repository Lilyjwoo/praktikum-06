<?php
declare(strict_types=1);

require_once __DIR__ . '/DataMahasiswa.php';

session_start();

$repo = new DataMahasiswa($_SESSION['mahasiswa'] ?? []);

$pesan = $_SESSION['pesan'] ?? '';
unset($_SESSION['pesan']);

$error = '';
$edit = null;


/**
 * Mengamankan teks sebelum ditampilkan ke halaman HTML
 */
function aman(string $teks): string
{
    return htmlspecialchars(
        $teks,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}


/*
|--------------------------------------------------------------------------
| PROSES CRUD
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $aksi = (string) ($_POST['aksi'] ?? '');

    $nimLama = trim(
        (string) ($_POST['nim_lama'] ?? '')
    );

    $nim = trim(
        (string) ($_POST['nim'] ?? '')
    );

    try {

        /*
        |--------------------------------------------------------------------------
        | HAPUS DATA
        |--------------------------------------------------------------------------
        */

        if ($aksi === 'hapus') {

            $berhasil = $repo->hapus($nim);

            $pesan = $berhasil
                ? 'Data mahasiswa berhasil dihapus.'
                : 'NIM tidak ditemukan.';


        /*
        |--------------------------------------------------------------------------
        | TAMBAH / UBAH DATA
        |--------------------------------------------------------------------------
        */

        } elseif (in_array($aksi, ['tambah', 'ubah'], true)) {

            $nama = trim(
                (string) ($_POST['nama'] ?? '')
            );

            $prodi = trim(
                (string) ($_POST['prodi'] ?? '')
            );

            $semester = filter_var(
                $_POST['semester'] ?? null,
                FILTER_VALIDATE_INT
            );

            $jenis = trim(
                (string) ($_POST['jenis'] ?? 'Reguler')
            );

            $ukt = filter_var(
                $_POST['ukt'] ?? null,
                FILTER_VALIDATE_INT
            );

            $potonganPersen = filter_var(
                $_POST['potongan_persen'] ?? 0,
                FILTER_VALIDATE_INT
            );


            /*
            |--------------------------------------------------------------------------
            | VALIDASI DATA UMUM
            |--------------------------------------------------------------------------
            */

            if (
                $nim === '' ||
                $nama === '' ||
                $prodi === '' ||
                $semester === false ||
                $semester === null ||
                $semester < 1 ||
                $semester > 14
            ) {
                throw new InvalidArgumentException(
                    'Lengkapi data NIM, nama, program studi, dan semester 1–14.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDASI UKT
            |--------------------------------------------------------------------------
            */

            if (
                $ukt === false ||
                $ukt === null ||
                $ukt < 0
            ) {
                throw new InvalidArgumentException(
                    'Biaya UKT tidak boleh bernilai negatif.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | MEMBUAT OBJEK SESUAI JENIS MAHASISWA
            |--------------------------------------------------------------------------
            */

            if ($jenis === 'Beasiswa') {

                if (
                    $potonganPersen === false ||
                    $potonganPersen === null ||
                    $potonganPersen < 0 ||
                    $potonganPersen > 100
                ) {
                    throw new InvalidArgumentException(
                        'Potongan beasiswa harus berada antara 0 sampai 100 persen.'
                    );
                }

                $mhs = new MahasiswaBeasiswa(
                    $nim,
                    $nama,
                    $prodi,
                    $semester,
                    $ukt,
                    $potonganPersen
                );

            } else {

                $mhs = new MahasiswaReguler(
                    $nim,
                    $nama,
                    $prodi,
                    $semester,
                    $ukt
                );
            }


            /*
            |--------------------------------------------------------------------------
            | TAMBAH / UBAH
            |--------------------------------------------------------------------------
            */

            if ($aksi === 'tambah') {

                $berhasil = $repo->tambah($mhs);

            } else {

                if ($nimLama === '') {
                    throw new InvalidArgumentException(
                        'NIM lama tidak ditemukan.'
                    );
                }

                $berhasil = $repo->ubah(
                    $nimLama,
                    $mhs
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CEK HASIL OPERASI
            |--------------------------------------------------------------------------
            */

            if (!$berhasil) {

                throw new InvalidArgumentException(
                    'Operasi gagal. Pastikan NIM tidak duplikat dan NIM saat edit tidak berubah.'
                );
            }

            $pesan = ($aksi === 'tambah')
                ? 'Data mahasiswa berhasil ditambahkan.'
                : 'Data mahasiswa berhasil diubah.';
        }

        /*
        |--------------------------------------------------------------------------
        | AKSI TIDAK DIKENAL
        |--------------------------------------------------------------------------
        */

        else {

            throw new InvalidArgumentException(
                'Aksi tidak dikenal.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN DATA KE SESSION
        |--------------------------------------------------------------------------
        */

        $_SESSION['mahasiswa'] = $repo->semua();
        $_SESSION['pesan'] = $pesan;

        header('Location: index.php');
        exit;


    } catch (InvalidArgumentException $e) {

        $error = $e->getMessage();
        $pesan = '';
    }
}


/*
|--------------------------------------------------------------------------
| DATA UNTUK EDIT
|--------------------------------------------------------------------------
*/

if (isset($_GET['edit'])) {

    $edit = $repo->cari(
        (string) $_GET['edit']
    );
}


/*
|--------------------------------------------------------------------------
| FITUR CARI NAMA
|--------------------------------------------------------------------------
*/

$cariNama = trim(
    (string) ($_GET['cari_nama'] ?? '')
);

$dataMahasiswa = $repo->semua();

if ($cariNama !== '') {

    $dataMahasiswa = array_filter(
        $dataMahasiswa,
        function (Mahasiswa $m) use ($cariNama): bool {

            return stripos(
                $m->getNama(),
                $cariNama
            ) !== false;
        }
    );
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Praktikum PBO - CRUD Mahasiswa</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f2f6fb;
            color: #17324d;
        }

        .wrap {
            max-width: 1150px;
            margin: 30px auto;
            padding: 0 18px;
        }

        h1 {
            margin-bottom: 6px;
        }

        .sub {
            color: #61778c;
        }

        .panel {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin: 22px 0;
            box-shadow: 0 4px 18px #1233;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        label {
            display: block;
            font-weight: 600;
            font-size: 14px;
        }

        input,
        select {
            display: block;
            width: 100%;
            padding: 11px;
            margin-top: 7px;
            border: 1px solid #cbd7e2;
            border-radius: 7px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #17324d;
            background: white;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #195d96;
            box-shadow: 0 0 0 3px #195d9620;
        }

        button,
        .btn {
            display: inline-block;
            border: 0;
            border-radius: 7px;
            padding: 10px 15px;
            background: #195d96;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover,
        .btn:hover {
            opacity: 0.88;
        }

        .secondary {
            background: #6b7886;
        }

        .danger {
            background: #b73f45;
        }

        .alert {
            padding: 12px;
            border-radius: 7px;
            background: #e5f5e9;
            margin: 15px 0;
        }

        .error {
            background: #ffe6e7;
        }

        .tablewrap {
            overflow: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th,
        td {
            text-align: left;
            border-bottom: 1px solid #e3eaf0;
            padding: 11px;
            white-space: nowrap;
        }

        th {
            background: #eaf3fb;
        }

        td form {
            display: inline;
        }

        td .btn,
        td button {
            margin: 2px;
        }

        small {
            color: #61778c;
        }

        .jenis-info {
            color: #61778c;
            font-size: 12px;
            margin-top: 5px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 18px;
        }

        .search-form input {
            margin-top: 0;
            flex: 1;
        }

        @media (max-width: 680px) {

            .grid {
                grid-template-columns: 1fr;
            }

            .search-form {
                flex-direction: column;
            }

        }

    </style>

</head>


<body>

<main class="wrap">


    <h1>Data Mahasiswa</h1>

    <p class="sub">
        Praktikum PBO: Inheritance dan CRUD Data Mahasiswa
        menggunakan Array di Session.
    </p>


    <?php if ($pesan): ?>

        <div class="alert">
            <?= aman($pesan) ?>
        </div>

    <?php endif; ?>


    <?php if ($error): ?>

        <div class="alert error">
            <?= aman($error) ?>
        </div>

    <?php endif; ?>


    <!--
    ==================================================================
    FORM TAMBAH / UBAH
    ==================================================================
    -->

    <section class="panel">

        <h2>
            <?= $edit
                ? 'Ubah Data Mahasiswa'
                : 'Tambah Data Mahasiswa'
            ?>
        </h2>


        <form method="post">

            <input
                type="hidden"
                name="aksi"
                value="<?= $edit ? 'ubah' : 'tambah' ?>"
            >


            <?php if ($edit): ?>

                <input
                    type="hidden"
                    name="nim_lama"
                    value="<?= aman($edit->getNim()) ?>"
                >

            <?php endif; ?>


            <div class="grid">


                <!-- NIM -->

                <label>

                    NIM

                    <input
                        type="text"
                        name="nim"
                        value="<?= aman(
                            $_POST['nim']
                            ?? $edit?->getNim()
                            ?? ''
                        ) ?>"
                        <?= $edit ? 'readonly' : '' ?>
                        required
                    >

                </label>


                <!-- NAMA -->

                <label>

                    Nama

                    <input
                        type="text"
                        name="nama"
                        value="<?= aman(
                            $_POST['nama']
                            ?? $edit?->getNama()
                            ?? ''
                        ) ?>"
                        required
                    >

                </label>


                <!-- PRODI -->

                <label>

                    Program Studi

                    <input
                        type="text"
                        name="prodi"
                        value="<?= aman(
                            $_POST['prodi']
                            ?? $edit?->getProdi()
                            ?? ''
                        ) ?>"
                        required
                    >

                </label>


                <!-- SEMESTER -->

                <label>

                    Semester

                    <input
                        type="number"
                        name="semester"
                        min="1"
                        max="14"
                        value="<?= aman(
                            (string) (
                                $_POST['semester']
                                ?? $edit?->getSemester()
                                ?? ''
                            )
                        ) ?>"
                        required
                    >

                </label>


                <!-- JENIS -->

                <label>

                    Jenis Mahasiswa

                    <select
                        name="jenis"
                        required
                    >

                        <?php
                        $jenisSaatIni =
                            $_POST['jenis']
                            ?? $edit?->getJenis()
                            ?? 'Reguler';
                        ?>

                        <option
                            value="Reguler"
                            <?= $jenisSaatIni === 'Reguler'
                                ? 'selected'
                                : '' ?>
                        >
                            Reguler
                        </option>

                        <option
                            value="Beasiswa"
                            <?= $jenisSaatIni === 'Beasiswa'
                                ? 'selected'
                                : '' ?>
                        >
                            Beasiswa
                        </option>

                    </select>

                    <div class="jenis-info">
                        Pilih jenis mahasiswa sesuai kategori.
                    </div>

                </label>


                <!-- UKT -->

                <label>

                    UKT

                    <input
                        type="number"
                        name="ukt"
                        min="0"
                        value="<?= aman(
                            (string) (
                                $_POST['ukt']
                                ?? (
                                    $edit instanceof MahasiswaReguler
                                    || $edit instanceof MahasiswaBeasiswa
                                    ? $edit->getUkt()
                                    : ''
                                )
                                ?? ''
                            )
                        ) ?>"
                        required
                    >

                </label>


                <!-- POTONGAN BEASISWA -->

                <label>

                    Potongan Beasiswa (%)

                    <input
                        type="number"
                        name="potongan_persen"
                        min="0"
                        max="100"
                        value="<?= aman(
                            (string) (
                                $_POST['potongan_persen']
                                ?? (
                                    $edit instanceof MahasiswaBeasiswa
                                    ? $edit->getPotonganPersen()
                                    : 0
                                )
                            )
                        ) ?>"
                    >

                    <div class="jenis-info">
                        Isi 0–100% jika mahasiswa mendapatkan beasiswa.
                    </div>

                </label>


            </div>


            <p>

                <button type="submit">

                    <?= $edit
                        ? 'Simpan Perubahan'
                        : 'Tambah Mahasiswa'
                    ?>

                </button>


                <?php if ($edit): ?>

                    <a
                        class="btn secondary"
                        href="index.php"
                    >
                        Batal
                    </a>

                <?php endif; ?>

            </p>


        </form>

    </section>


    <!--
    ==================================================================
    DAFTAR MAHASISWA
    ==================================================================
    -->

    <section class="panel">

        <h2>
            Daftar Mahasiswa
            (<?= count($dataMahasiswa) ?>)
        </h2>


        <!--
        ==================================================================
        FORM PENCARIAN NAMA
        ==================================================================
        -->

        <form
            method="get"
            class="search-form"
        >

            <input
                type="text"
                name="cari_nama"
                placeholder="Cari nama mahasiswa..."
                value="<?= aman($cariNama) ?>"
            >

            <button type="submit">
                Cari
            </button>

            <?php if ($cariNama !== ''): ?>

                <a
                    class="btn secondary"
                    href="index.php"
                >
                    Reset
                </a>

            <?php endif; ?>

        </form>


        <div class="tablewrap">

            <table>

                <thead>

                    <tr>

                        <th>NIM</th>

                        <th>Nama</th>

                        <th>Prodi</th>

                        <th>Semester</th>

                        <th>Jenis</th>

                        <th>UKT</th>

                        <th>Potongan</th>

                        <th>Biaya Akhir</th>

                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach ($dataMahasiswa as $m): ?>

                        <tr>

                            <td>
                                <?= aman($m->getNim()) ?>
                            </td>

                            <td>
                                <?= aman($m->getNama()) ?>
                            </td>

                            <td>
                                <?= aman($m->getProdi()) ?>
                            </td>

                            <td>
                                <?= $m->getSemester() ?>
                            </td>

                            <td>
                                <?= aman($m->getJenis()) ?>
                            </td>


                            <td>
                                <?php
                                if ($m instanceof MahasiswaBeasiswa) {
                                    $ukt = $m->getUkt();
                                } elseif ($m instanceof MahasiswaReguler) {
                                    $ukt = $m->getUkt();
                                } else {
                                    $ukt = 0;
                                }
                                ?>
                                Rp<?= number_format($ukt, 0, ',', '.') ?>
                            </td>


                            <td>

                                <?php if (
                                    $m instanceof MahasiswaBeasiswa
                                ): ?>

                                    <?= $m->getPotonganPersen() ?>%

                                <?php else: ?>

                                    -

                                <?php endif; ?>

                            </td>


                            <td>

                                Rp<?= number_format(
                                    $m->hitungBiaya(),
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </td>


                            <td>

                                <a
                                    class="btn"
                                    href="?edit=<?= urlencode(
                                        $m->getNim()
                                    ) ?>"
                                >
                                    Edit
                                </a>


                                <form
                                    method="post"
                                    onsubmit="return confirm(
                                        'Hapus data mahasiswa ini?'
                                    )"
                                >

                                    <input
                                        type="hidden"
                                        name="aksi"
                                        value="hapus"
                                    >

                                    <input
                                        type="hidden"
                                        name="nim"
                                        value="<?= aman(
                                            $m->getNim()
                                        ) ?>"
                                    >


                                    <button
                                        class="danger"
                                        type="submit"
                                    >
                                        Hapus
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>


                    <?php if (!$dataMahasiswa): ?>

                        <tr>

                            <td colspan="9">

                                <?php if ($cariNama !== ''): ?>

                                    Nama
                                    "<strong><?= aman($cariNama) ?></strong>"
                                    tidak ditemukan.

                                <?php else: ?>

                                    Belum ada data mahasiswa.
                                    Tambahkan mahasiswa melalui form di atas.

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endif; ?>


                </tbody>

            </table>

        </div>


        <p>

            <small>
                Data disimpan menggunakan session PHP sehingga
                tidak menggunakan database.
            </small>

        </p>


    </section>


</main>

</body>

