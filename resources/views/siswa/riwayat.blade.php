@include('layout/header')

<section class="riwayat">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-body">
                    <h3>Riwayat Konseling</h3>
                    <p>Daftar sesi konseling yang telah selesai anda lakukan. Laporan hasil konseling dapat diakses langsung melalui tabel riwayat konseling.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 ">
                <div class="filter-wrap">
                    <div class="filter">
                        <select name="date" id="">
                            <option value="" selected disabled>Pilih Tanggal</option>
                        </select>
                        <select name="kategori" id="">
                            <option value="" selected disabled>Pilih Kategori</option>
                            <option value="">Akademik</option>
                            <option value="">Peribadi</option>
                            <option value="">Sosial</option>
                            <option value="">Karir</option>
                        </select>
                        <button>Terapkan</button>
                        <button>Reset</button>
                    </div>
                    <!-- <div class="search"> -->
                    <input class="search" type="text" placeholder="Cari...">
                    <!-- </div> -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="tabel">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal Konseling</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Hasil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>20 Desember 2026</td>
                                <td>Karir</td>
                                <td><span class="menunggu">Menunggu</span></td>
                                <td><a class="detail" href="#"><i class="fa-solid fa-folder"></i></a></td>
                            </tr>
                            <tr>
                                <td>07 Desember 2026</td>
                                <td>Akademik</td>
                                <td><span class="selesai">Selesai</span></td>
                                <td><a class="detail"  href="#"><i class="fa-solid fa-folder"></i></a></td>
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

@include('layout/footer')