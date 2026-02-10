@include('layout.header')

<section class="info-konselor">
    <div class="container">
        <div class="row">
            <div class="col-md-12 summary-card">
                <ul>
                    <li>
                        <div class="isi">
                            <div class="title"><i class="fa-solid fa-clipboard-list"></i>
                                <h4>Jumlah Permintaan Baru</h4>
                            </div>
                            <span>2026</span>
                        </div>
                        <div class="isi">
                            <h1>{{ $permintaanBaru }} Sesi</h1>
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
                            <h1>{{ $konselingAktif}} aktif</h1>
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
                            <h1>{{ $konselingSelesai}} Sesi</h1>
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
                            @forelse ($jadwalHariIni as $j)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($j->tanggal_konseling)->translatedFormat('d F Y')}}</td>
                                <td>{{ $j->pengajuan->siswa->nama }}</td>
                                <td>{{ $j->pengajuan->kategori->nama_kategori }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">Tidak ada jadwal konseling hari ini</td>
                            </tr>
                            @endforelse
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