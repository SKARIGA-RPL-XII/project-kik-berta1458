@include('layout.header')

<section class="permintaan">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-body">
                    <h3>Permintaan Konseling</h3>
                    <p>Kelola dan tinjau permintaan konseling yang diajukan oleh siswa sebagai dasar pemberian persetujuan</p>
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>20 Desember 2026</td>
                                <td>Berta Yuanita</td>
                                <td>Karir</td>
                                <td><a class="detail" href="#"><i class="fa-solid fa-folder"></i></a>
                                    <a class="tolak" href="#"><i class="fa-solid fa-circle-xmark"></i></a>
                                    <a class="terima" href="#"><i class="fa-solid fa-circle-check"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>07 Desember 2026</td>
                                <td>Candy Cantika</td>
                                <td>Akademik</td>
                                <td><a class="detail" href="#"><i class="fa-solid fa-folder"></i></a>
                                    <a class="tolak" href="#"><i class="fa-solid fa-circle-xmark"></i></a>
                                    <a class="terima" href="#"><i class="fa-solid fa-circle-check"></i></a>
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