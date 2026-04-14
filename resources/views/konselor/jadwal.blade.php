@include('layout.header')

<section class="jadwal">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-body">
                    <h3>Jadwal Konseling</h3>
                    <p>Daftar jadwal konseling yang sudah diterima pada tahun 2026</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="kalender kal-jadwal-konselor">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div id="kalender"></div>
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
                        {{-- FIX #2: Ganti @forelse dengan @foreach biasa --}}
                        {{-- Row kosong selalu ada di DOM, dikontrol oleh JS --}}
                        <tbody id="tabelJadwal">
                            @foreach ($jadwal as $item)
                            <tr class="jadwal-row">
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_konseling)->translatedFormat('d F Y') }}</td>
                                <td>{{ $item->nama_siswa }}</td>
                                <td>{{ $item->nama_kategori }}</td>
                                <td>
                                    <button class="detail"
                                        onclick="showDetail(@json($item->deskripsi_masalah))">
                                        <i class="fa-solid fa-folder"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach

                            {{-- Selalu ada di DOM, JS yang mengatur display-nya --}}
                            <tr id="rowKosong" style="display:none;">
                                <td colspan="4" style="text-align:center;">
                                    Tidak ada jadwal pada bulan ini
                                </td>
                            </tr>
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

<div id="modalDetail" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Deskripsi Singkat Permasalahan</h2>
        </div>
        <div class="modal-content">
            <p id="modalDeskripsi"></p>
        </div>
        <div class="modal-actions">
            <button id="closeModalDetail">Tutup</button>
        </div>
    </div>
</div>

@include('layout.footer')

<script>
    let tanggalHighlight = @json($tanggalHighlight).map(t => t.substring(0, 10));
    let dataJadwal = @json($jadwal);

    // KALENDER
    let kalender = flatpickr("#kalender", {
        inline: true,
        dateFormat: "Y-m-d",
        disableMobile: true,

        onDayCreate: function(dObj, dStr, fp, dayElem) {
            // FIX #3: format dayElem juga ke Y-m-d agar cocok
            let d = dayElem.dateObj;
            let date = d.getFullYear() + '-' +
                String(d.getMonth() + 1).padStart(2, '0') + '-' +
                String(d.getDate()).padStart(2, '0');
            if (tanggalHighlight.includes(date)) {
                dayElem.classList.add("punya-jadwal");
            }
        },

        onMonthChange: function(selectedDates, dateStr, instance) {
            currentPage = 1; 
            filterByMonth(instance.currentMonth + 1, instance.currentYear);
        }
    });

    // FILTER TABEL BERDASARKAN BULAN
    function filterByMonth(bulan, tahun) {
        let tbody = document.getElementById("tabelJadwal");
        let rowKosong = document.getElementById("rowKosong");

        let existingRows = tbody.querySelectorAll("tr.jadwal-row");
        existingRows.forEach(row => row.remove());

        let filtered = dataJadwal.filter(item => {
            let tgl = new Date(item.tanggal_konseling);
            return (tgl.getMonth() + 1 == bulan) && (tgl.getFullYear() == tahun);
        });

        if (filtered.length === 0) {
            rowKosong.style.display = "";
            document.getElementById("pageInfo").innerText = "";
            return;
        }

        rowKosong.style.display = "none";

        filtered.forEach(item => {
            let tr = document.createElement('tr');
            tr.className = 'jadwal-row';
            tr.innerHTML = `
                <td>${formatTanggal(item.tanggal_konseling)}</td>
                <td>${item.nama_siswa}</td>
                <td>${item.nama_kategori}</td>
                <td>
                    <button class="detail">
                        <i class="fa-solid fa-folder"></i>
                    </button>
                </td>
            `;
            tr.querySelector('.detail').addEventListener('click', function() {
                showDetail(item.deskripsi_masalah);
            });
            tbody.insertBefore(tr, rowKosong);
        });

        showTablePage();
    }

    // FORMAT TANGGAL 
    function formatTanggal(tanggal) {
        let options = {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };
        return new Date(tanggal).toLocaleDateString('id-ID', options);
    }

    // MODAL DETAIL
    function showDetail(deskripsi) {
        document.getElementById("modalDeskripsi").innerText = deskripsi;
        document.getElementById("modalDetail").style.display = "flex";
    }

    document.getElementById("closeModalDetail").addEventListener('click', function() {
        document.getElementById("modalDetail").style.display = "none";
    });

    document.getElementById("modalDetail").addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = "none";
        }
    });

//slide
    let currentPage = 1;
    let rowsPerPage = 10;

    function showTablePage() {
        const tbody = document.getElementById("tabelJadwal");
        const rows = Array.from(tbody.querySelectorAll("tr.jadwal-row"));

        let totalRows = rows.length;
        let totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

        if (currentPage > totalPages) currentPage = totalPages;

        let start = (currentPage - 1) * rowsPerPage;
        let end = start + rowsPerPage;

        rows.forEach((row, i) => {
            row.style.display = (i >= start && i < end) ? "" : "none";
        });

        document.getElementById("pageInfo").innerText = `${currentPage} / ${totalPages}`;
    }

    function nextPage() {
        const rows = document.getElementById("tabelJadwal").querySelectorAll("tr.jadwal-row");
        let totalPages = Math.ceil(rows.length / rowsPerPage) || 1;
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

    window.addEventListener('load', function() {
        let now = new Date();
        filterByMonth(now.getMonth() + 1, now.getFullYear());
    });
</script>