@include('layout.header')

<section class="jadwal">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-body">
                    <h3>Jadwal Konseling</h3>
                    <p>Daftar jadwal konseling yang belum terlaksanakan pada tahun 2026</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="kalender kal-jadwal">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="kalender" id="kalender"></div>
            </div>
        </div>
    </div>
</section>

<section class="tabel-perbulan">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="tabel">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal Konseling</th>
                                <th>Nama Siswa</th>
                                <th>Kategori</th>
                                <th>Hasil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>20 Desember 2026</td>
                                <td>Berta Yuanita</td>
                                <td>Karir</td>
                                <td><a class="detail" href="#"><i class="fa-solid fa-folder"></i></a></td>
                            </tr>
                            <tr>
                                <td>07 Desember 2026</td>
                                <td>Candy Cantika</td>
                                <td>Akademik</td>
                                <td><a class="detail" href="#"><i class="fa-solid fa-folder"></i></a></td>
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

@include('layout.footer')

<script>
    flatpickr("#kalender", {
        inline: true,
        dateFormat: "Y-m-d"
    });
</script>