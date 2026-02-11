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

<section class="kalender kal-jadwal">
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
                        <tbody id="tabelJadwal">

                            <tr id="rowKosong" style="display:none;">
                                <td colspan="4" style="text-align:center;">Tidak ada jadwal pada bulan ini</td>
                            </tr>

                            @foreach ($jadwal as $j)
                            <tr class="jadwal-row"
                                data-bulan="{{ \Carbon\Carbon::parse($j->tanggal_konseling)->format('m') }}"
                                data-tahun="{{ \Carbon\Carbon::parse($j->tanggal_konseling)->format('Y') }}">
                                <td>{{ \Carbon\Carbon::parse($j->tanggal_konseling)->translatedFormat('d F Y') }}</td>
                                <td>{{ $j->pengajuan->siswa->nama }}</td>
                                <td>{{ $j->pengajuan->kategori->nama_kategori }}</td>
                                <td><button class="detail" data-deskripsi="{{$j->pengajuan->deskripsi_masalah}}"><i class="fa-solid fa-folder"></i></button></td>
                            </tr>
                            @endforeach

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
    let semuaJadwal = @json($jadwal);
    let tanggalAdaJadwal = semuaJadwal.map(j => j.tanggal_konseling);

    document.querySelectorAll('.detail').forEach(btn => {
        btn.addEventListener('click', function() {
            let deskripsi = this.getAttribute('data-deskripsi');
            document.getElementById('modalDeskripsi').textContent = deskripsi;
            document.getElementById('modalDetail').style.display = 'flex';
        });
    });

    document.getElementById('closeModalDetail').addEventListener('click', function() {
        document.getElementById('modalDetail').style.display = 'none';
    });
</script>
<script>
    let kalender = flatpickr("#kalender", {
        inline: true,
        dateFormat: "Y-m-d",
        disableMobile: true,

        onReady: function(selectedDates, dateStr, instance) {
            highlightDates(instance);
            updateTabel(instance.currentMonth + 1, instance.currentYear);
        },

        onMonthChange: function(selectedDates, dateStr, instance) {
            highlightDates(instance);
            updateTabel(instance.currentMonth + 1, instance.currentYear);
        }
    });

    function highlightDates(instance) {
        let days = instance.days.childNodes;

        days.forEach(day => {

            let tgl = day.dateObj.getFullYear() + '-' +
                String(day.dateObj.getMonth() + 1).padStart(2, '0') + '-' +
                String(day.dateObj.getDate()).padStart(2, '0');

            if (tanggalAdaJadwal.includes(tgl)) {
                day.classList.add("punya-jadwal");
            } else {
                day.classList.remove("punya-jadwal");
            }
        });
    }

    function updateTabel(bulan, tahun) {
        let rows = document.querySelectorAll('#tabelJadwal .jadwal-row');
        let kosongRow = document.getElementById('rowKosong');
        let ditemukan = false;

        rows.forEach(row => {
            let rowBulan = row.getAttribute('data-bulan');
            let rowTahun = row.getAttribute('data-tahun');

            if (rowBulan == bulan.toString().padStart(2, '0') && rowTahun == tahun) {
                row.style.display = "table-row";
                ditemukan = true;
            } else {
                row.style.display = "none";
            }
        });

        kosongRow.style.display = ditemukan ? "none" : "table-row";
    }
</script>