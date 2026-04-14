@include('layout.header')

<section class="admin-dashboard">
    <div class="container">
        <div class="row">
            <div class="col-md-12 summary-card">
                <ul>
                    <li>
                        <div class="isi">
                            <div class="title"> <i class="fa-solid fa-users"></i>
                                <h4>Jumlah Konselor</h4>
                            </div>
                            <a href="/konselor"><span><i class="fa-solid fa-magnifying-glass"></i></span></a>
                        </div>
                        <div class="isi">
                            <h1>{{ $totalKonselor }} Guru</h1>
                        </div>
                    </li>
                    <li>
                        <div class="isi">
                            <div class="title"><i class="fa-solid fa-user"></i>
                                <h4>Jumlah Siswa</h4>
                            </div>
                            <a href="/siswa"><span><i class="fa-solid fa-magnifying-glass"></i></span></a>
                        </div>
                        <div class="isi">
                            <h1>{{ $totalSiswa }} Anak</h1>
                        </div>
                    </li>
                    <li>
                        <div class="isi">
                            <div class="title"><i class="fa-solid fa-clipboard-list"></i>
                                <h4>Jumlah Konseling </4>
                            </div>
                            <a href="/konseling"><span><i class="fa-solid fa-magnifying-glass"></i></span></a>
                        </div>
                        <div class="isi">
                            <h1>{{ $totalPengajuan }} Sesi</h1>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="kinerja-box">
                    <div class="title-body">
                        <h3>Top 5 Konselor Teraktif</h3>
                    </div>
                    <table class="table-kinerja">
                        <thead>
                            <tr>
                                <th>Ranking</th>
                                <th>Nama Konselor</th>
                                <th>Jumlah Konseling</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($konselorTerbaik as $index => $k)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $k->nama }}</td>
                                <td>{{ $k->pengajuan_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-box">
                    <div class="title-body">
                        <h3>Grafik Konseling Siswa</h3>
                    </div>
                    <canvas id="chartKonseling"></canvas>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
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
                                        <tbody id="tableBody">
                                            @forelse ($jadwalHariIni as $j)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($j->tanggal_pengajuan)->translatedFormat('d F Y')}}</td>
                                                <td>{{ $j->siswa->nama }}</td>
                                                <td>{{ $j->kategori->nama_kategori }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="kosong">Tidak ada jadwal konseling hari ini</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="slide">
                                    <button onclick="prevPage()">
                                        <p>Kembali</p>
                                    </button>
                                    <span class="number" id="pageInfo"></span>
                                    <button onclick="nextPage()">
                                        <p>Berikutnya</p>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</section>

@include('layout.footer')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartKonseling');

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
            ],
            datasets: [{
                label: 'Jumlah Konseling',
                data: @json($chartData),
                borderWidth: 2,
                fill: false,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    //slide
    let currentPage = 1;
    let rowsPerPage = 10;

    function showTablePage() {
        const table = document.getElementById("tableBody");
        const rows = table.getElementsByTagName("tr");

        let totalRows = rows.length;
        let totalPages = Math.ceil(totalRows / rowsPerPage);

        let start = (currentPage - 1) * rowsPerPage;
        let end = start + rowsPerPage;

        for (let i = 0; i < totalRows; i++) {
            if (i >= start && i < end) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }

        document.getElementById("pageInfo").innerText = currentPage;
    }

    function nextPage() {
        const rows = document.getElementById("tableBody").getElementsByTagName("tr");
        let totalPages = Math.ceil(rows.length / rowsPerPage);

        if (currentPage < totalPages) {
            currentPage++;
            showTablePage();
        }
    }

    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            showTablePage();
        }
    }

    // jalankan pertama kali
    window.onload = function() {
        showTablePage();
    };
</script>