@include('layout.header')

<section class="info-konselor">
    <div class="container">
        <div class="row">
            <div class="col-md-12 summary-card">
                <ul>
                    <li>
                        <div class="isi">
                            <div class="title"><i class="fa-solid fa-arrow-up-from-bracket"></i>
                                <h4>Jumlah Permintaan Baru</h4>
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
                            <div class="title"><i class="fa-solid fa-arrow-up-from-bracket"></i>
                                <h4>Jumlah Konseling Aktif</h4>
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
                            <div class="title"><i class="fa-solid fa-arrow-up-from-bracket"></i>
                                <h4>Jumlah Terselesaikan</h4>
                            </div>
                            <span>2026</span>
                        </div>
                        <div class="isi">
                            <h1>10 Kali</h1>
                            <p>Pada tahun ini</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="kalender">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="kalender" id="kalender"></div>
            </div>
        </div>
    </div>
</section>

<section class="konseling-now">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-body">
                    <h3>Konseling</h3>
                    <h1>Berlangsung Pada Hari ini</h1>
                </div>
                <div class="tabel">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal Konseling</th>
                                <th>Nama Siswa</th>
                                <th>Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>19 Desember 2026</td>
                                <td>Berta Yuanita</td>
                                <td>Karir</td>
                            </tr>
                            <tr>
                                <td>19 Desember 2026</td>
                                <td>Candy Cantika</td>
                                <td>Akademik</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

@include('layout.footer')

<script>
    flatpickr("#kalender", {
        inline: true,
        dateFormat: "Y-m-d"
    });
</script>