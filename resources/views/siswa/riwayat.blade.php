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
                        <input type="date" id="filterTanggal" class="date-picker" required>

                        <select id="filterKategori">
                            <option value="" selected disabled>Pilih Kategori</option>
                            <option value="Akademik">Akademik</option>
                            <option value="Peribadi">Peribadi</option>
                            <option value="Sosial">Sosial</option>
                            <option value="Karir">Karir</option>
                        </select>

                        <button>Terapkan</button>
                        <button id="reset">Reset</button>
                    </div>
                    <!-- <div class="search"> -->
                    <input class="search" type="text" placeholder="Cari...">
                    <!-- </div> -->
                </div>
                <div class="selected-filter" id="selectedFilter"></div>
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
                            @forelse($pengajuan as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y') }}</td>
                                <td>{{ $item->kategori->nama_kategori }}</td>
                                <td> <span class="{{ $item->status }}">
                                        {{ ucfirst($item->status) }}
                                    </span></td>
                                <td><a class="detail" data-nama="{{ $user->siswa->nama ?? '-' }}"
                                        data-kelas="{{ $user->siswa->kelas ?? '-' }}"
                                        data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y') }}"
                                        data-catatan="{{ $item->laporan->pesan_siswa ?? 'Belum ada catatan' }}"
                                        href="#"><i class="fa-solid fa-folder"></i></a></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" style="text-align:center;">Belum ada riwayat konseling</td>
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

<div class="overlay" id="popupDetail">
    <div class="popup-content-detail">
        <h2 class="modal-title">Hasil Bimbingan Konseling</h2>

        <table class="table-report">
            <tr>
                <th>Nama</th>
                <td id="modalNama"></td>
            </tr>
            <tr>
                <th>Kelas</th>
                <td id="modalKelas">

                </td>
            </tr>
            <tr>
                <th>Tgl, Konseling</th>
                <td id="modalTanggal">

                </td>
            </tr>
            <tr>
                <th>Catatan</th>
                <td id="modalCatatan">
                </td>
            </tr>
        </table>

        <button class="tutup" id="tutup">Tutup</button>
    </div>
</div>

@include('layout/footer')

<script>
    const btnDetails = document.querySelectorAll('.detail');
    const popup = document.getElementById('popupDetail');
    const btnClose = document.getElementById('tutup');

    btnDetails.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            // ambil data dari tombol
            const nama = this.dataset.nama;
            const kelas = this.dataset.kelas;
            const tanggal = this.dataset.tanggal;
            const catatan = this.dataset.catatan;

            // inject ke modal
            document.getElementById('modalNama').innerText = nama;
            document.getElementById('modalKelas').innerText = kelas;
            document.getElementById('modalTanggal').innerText = tanggal;
            document.getElementById('modalCatatan').innerText = catatan;

            popup.classList.add('show');
        });
    });

    btnClose.addEventListener('click', () => {
        popup.classList.remove('show');
    });

    const tanggal = document.getElementById('filterTanggal');
    const kategori = document.getElementById('filterKategori');
    const container = document.getElementById('selectedFilter');
    const resetBtn = document.getElementById('reset');

    function createChip(label, type) {
        container.querySelectorAll(`.chip[data-type="${type}"]`).forEach(el => el.remove());

        const chip = document.createElement('div');
        chip.className = 'chip';
        chip.dataset.type = type;
        chip.innerHTML = `
            ${label}
            <span class="close">&times;</span>
        `;

        chip.querySelector('.close').onclick = () => {
            chip.remove();
            if (type === 'tanggal') tanggal.value = '';
            if (type === 'kategori') kategori.value = '';
        };

        container.appendChild(chip);
    }

    tanggal.addEventListener('change', () => {
        const date = new Date(tanggal.value);
        const formatted = date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
        createChip(formatted, 'tanggal');
    });

    kategori.addEventListener('change', () => {
        createChip(kategori.value, 'kategori');
    });

    resetBtn.addEventListener('click', () => {
        container.innerHTML = '';
        tanggal.value = '';
        kategori.value = '';
    });
</script>