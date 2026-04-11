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
                            <h1>{{ $total }} Kali</h1>
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
                            <h1>{{ $aktif }} Aktif</h1>
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
                            <h1>{{ $selesai }} Selesai</h1>
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
                    <h1>Konseling Pada Bulan {{ \Carbon\Carbon::now()->translatedFormat('F') }}</h1>
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
                            @forelse($bulanIni as $item)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->translatedFormat('d F Y') }}
                                </td>
                                <td>{{ $item->kategori->nama_kategori }}</td>
                                <td><span class="status {{$item->status}}">{{ ucfirst($item->status) }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" style="text-align:center;">Tidak ada data bulan ini</td>
                            </tr>
                            @endforelse
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
                    <li>
                        <a href="/pengajuan-konseling">
                            + Ajukan Konseling
                        </a>
                    </li>
                    <li>
                        <a href="/riwayat-konseling">
                            Lihat Riwayat Konseling
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

@include('layout/footer')