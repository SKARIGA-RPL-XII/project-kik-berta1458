@include('layout.header')

<section class="laporan">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-body">
                    <h3>Laporan Konseling</h3>
                    <p>Rekap hasil konsultasi permasalahan, dan tentukan tindak lanjut, sehingga semua laporan dapat ditinjau dan dipantau dengan mudah.</p>
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
                                <th>Nama Siswa</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Hasil</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>20 Desember 2026</td>
                                <td>Berta Yuanita</td>
                                <td>Karir</td>
                                <td><span class="menunggu">Menunggu</span></td>
                                <td><a class="detail" href="#"><i class="fa-solid fa-folder"></i></a>
                                    <a class="isi-lap" href="#"><i class="fa-solid fa-pen-to-square"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>07 Desember 2026</td>
                                <td>Candy Cantika</td>
                                <td>Akademik</td>
                                <td><span class="ditolak">Ditolak</span></td>
                                <td><a class="detail" href="#"><i class="fa-solid fa-folder"></i></a>
                                    <a class="catatan" href="#"><i class="fa-solid fa-clipboard-list"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>07 November 2026</td>
                                <td>Candy Cantika</td>
                                <td>Akademik</td>
                                <td><span class="selesai">Selesai</span></td>
                                <td><a class="detail" href="#"><i class="fa-solid fa-folder"></i></a>
                                    <a class="isi-lap-done" href="#"><i class="fa-solid fa-pen-to-square"></i></a>
                                </td>
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