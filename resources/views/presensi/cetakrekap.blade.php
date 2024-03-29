<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>LAPORAN ABSEN BATIK {{ date('d-m-Y', strtotime($rangetanggal[0])) }} s/d {{ date('d-m-Y', strtotime(end($rangetanggal))) }}</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @page {
            size: A4
        }

        #title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 18px;
            font-weight: bold;
        }

        .tabeldatakaryawan {
            margin-top: 40px;
        }

        .tabeldatakaryawan tr td {
            padding: 5px;
        }

        .tabelpresensi {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .tabelpresensi tr th {
            border: 1px solid #131212;
            padding: 8px;
            background-color: #dbdbdb;
            font-size: 10px
        }

        .tabelpresensi tr td {
            border: 1px solid #131212;
            padding: 5px;
            font-size: 12px;
        }

        .foto {
            width: 40px;
            height: 30px;

        }

    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A4">
    <?php
    function selisih($jam_masuk, $jam_keluar)
    {
        list($h, $m, $s) = explode(":", $jam_masuk);
        $dtAwal = mktime($h, $m, $s, "1", "1", "1");
        list($h, $m, $s) = explode(":", $jam_keluar);
        $dtAkhir = mktime($h, $m, $s, "1", "1", "1");
        $dtSelisih = $dtAkhir - $dtAwal;
        $totalmenit = $dtSelisih / 60;
        $jam = explode(".", $totalmenit / 60);
        $sisamenit = ($totalmenit / 60) - $jam[0];
        $sisamenit2 = $sisamenit * 60;
        $jml_jam = $jam[0];
        return $jml_jam . ":" . round($sisamenit2);
    }
    ?>
    <!-- Each sheet element should have the class "sheet" -->
    <!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
    <section class="sheet padding-10mm">

    <table style="width: 100%">
        <tr>
            <td style="width: 60px">
                <img src="{{ asset('assets/img/logopresensi.png') }}" width="80" height="70" alt="">
            </td>
            <td>
                <span id="title">
                    REKAP PRESENSI KARYAWAN<br>
                    PT.USSI BAHTERA TEKNIK INFORMATIKA<br>
                </span>

                <span class="location-info">Jl. Hos Cokroaminoto No.67, Landungsari, Kota Pekalongan, Jawa Tengah 51129</span>

                <span style="text-align: center; display: block; margin-top: 20px;"><b>Periode : </b>{{ date('d-m-Y', strtotime($rangetanggal[0])) }} s/d {{ date('d-m-Y', strtotime($rangetanggal[count($rangetanggal)-1])) }}</span>
            </td>
        </tr>
    </table>



        <table class="tabelpresensi">
            <tr>
                <th rowspan="2">Nik</th>
                <th rowspan="2">Nama Karyawan</th>
                <th colspan="{{ $jmlhari }}">Bulan {{ $namabulan[$bulan] }} {{ $tahun }}</th>
                <th rowspan="2">H</th>
                <th rowspan="2">I</th>
                <th rowspan="2">S</th>
                <th rowspan="2">C</th>
                <th rowspan="2">A</th>
                <th rowspan="2">Terlambat</th>
                <th rowspan="2">Uang Makan</th>
            </tr>
            <tr>
                @foreach ($rangetanggal as $d)
                    @if ($d != NULL)
                        <th>{{ date("d", strtotime($d)) }}</th>
                    @endif
                @endforeach
            </tr>
            @php
                // Menyiapkan array untuk menyimpan nilai uang makan setiap karyawan
                $uang_makan_karyawan = [];
            @endphp
            @foreach ($rekap as $r)

                <tr>
                    <td>{{ $r->nik }}</td>
                    <td>{{ $r->nama_lengkap }}</td>
                    <?php
                    $jml_hadir = 0;
                    $jml_izin = 0;
                    $jml_sakit = 0;
                    $jml_cuti = 0;
                    $jml_alpa = 0;
                    $jml_terlambat = 0;
                    $uang_makan = 0;
                    $total_uang_makan = 0;
                    $color = "";
                    for ($i = 1; $i <= $jmlhari; $i++) {
                        $tgl = "tgl_" . $i;
                        $datapresensi = explode("|", $r->$tgl);
                        if ($r->$tgl != NULL) {
                            $status = $datapresensi[2];
                            $jam_masuk = $datapresensi[4];
                        } else {
                            $status = "";
                            $jam_masuk = "";
                        }

                        if ($status == "h" && strtotime($jam_masuk) > strtotime("08:30:00")) {
                            // Mengurangkan nilai uang makan jika status "h" dan $jam_masuk lebih dari 09:30:00
                            $uang_makan -= 15000;
                            $jml_terlambat += 1;
                        }

                        if ($status == "h") {
                            $jml_hadir += 1;
                            $color = "white";
                        } elseif ($status == "i") {
                            $jml_izin += 1;
                            $color = "#ffbb00";
                        } elseif ($status == "s") {
                            $jml_sakit += 1;
                            $color = "#34a1eb";
                        } elseif ($status == "c") {
                            $jml_cuti += 1;
                            $color = "#a600ff";
                        } elseif (empty($status)) {
                            $jml_alpa += 1;
                            $color = "red";
                        }

                        $uang_makan += ($status == "h") ? 15000 : 0;
                    ?>
                        <td style="background-color: {{ $color }}">
                            {{ $status }}
                        </td>
                    <?php
                    }
                    ?>
                    <td>{{ !empty($jml_hadir) ? $jml_hadir : ""  }}</td>
                    <td>{{ !empty($jml_izin) ? $jml_izin : ""  }}</td>
                    <td>{{ !empty($jml_sakit) ? $jml_sakit : ""  }}</td>
                    <td>{{ !empty($jml_cuti) ? $jml_cuti : ""  }}</td>
                    <td>{{ !empty($jml_alpa) ? $jml_alpa : ""  }}</td>
                    <td>{{ $jml_terlambat  }}</td>
                    <td>Rp.{{ number_format($uang_makan, 0, ',', '.') }}</td>
                </tr>
                <?php
                    // Logika penjumlahan total uang makan per karyawan
                    $uang_makan_karyawan[] = ($jml_hadir - $jml_terlambat) * 15000;
                ?>
            @endforeach
            @php
                // Menghitung total uang makan dari seluruh karyawan
                $total_uang_makan = array_sum($uang_makan_karyawan);
            @endphp
            <tr>
                <td colspan="{{ $jmlhari + 8 }}"><b>Total Uang Makan</b></td>
                <td><b>Rp.{{ number_format($total_uang_makan, 0, ',', '.') }}</b></td>
            </tr>


        </table>


    </section>

</body>

</html>
