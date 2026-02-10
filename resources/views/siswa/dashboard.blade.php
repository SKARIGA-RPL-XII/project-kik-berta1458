@include('layout/header')

<section class="banner-siswa">
<div class="container">
    <div class="row">
        <div class="col-md-12">
        </div>
    </div>
</div>
</section>
<section class="info">
    <div class="container">
        <div class="row">
            <div class="col-md-12 summary-card">
                <ul>
                    <li>
                        <div class="isi">
                            <div class="title"><i class="fa-solid fa-arrow-up-from-bracket"></i>
                                <h4>Jumlah Pengajuan</h4>
                            </div>
                            <span>2026</span>
                        </div>
                        <div class="isi">
                            <h1>10 Kali</h1>
                            <p>Pada tahun ini</p>
                        </div>
                    </li>
                    <li>
                        <div class="isi">
                            <div class="title"><i class="fa-solid fa-user-clock"></i>
                                <h4>Jumlah Konseling Aktif</h4>
                            </div>
                            <span>2026</span>
                        </div>
                        <div class="isi">
                            <h1>10 Aktif</h1>
                            <p>Pada tahun ini</p>
                        </div>
                    </li>
                    <li>
                        <div class="isi">
                            <div class="title"><i class="fa-solid fa-circle-check"></i>
                                <h4>Jumlah Terselesaikan</h4>
                            </div>
                            <span>2026</span>
                        </div>
                        <div class="isi">
                            <h1>10 Sesi</h1>
                            <p>Pada tahun ini</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="konseling-bulanan">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-body">
                    <h3>Konseling</h3>
                    <h1>Konseling Pada Bulan Januari</h1>
                </div>
                <div class="tabel">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal Konseling</th>
                                <th>Kategori</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>20 Desember 2026</td>
                                <td>Karir</td>
                                <td><span class="menunggu">Menunggu</span></td>
                            </tr>
                            <tr>
                                <td>07 Desember 2026</td>
                                <td>Akademik</td>
                                <td><span class="selesai">Selesai</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="slide">
                    <p>kembali</p><span class="number">1</span>
                    <p>Berikutnya</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="aksi-cepat">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul>
                    <a href="#">
                        <li>+ Ajukan Konseling
                    </a></li>
                    <a href="#">
                        <li>Lihat Riwayat Konseling
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

@include('layout/footer')