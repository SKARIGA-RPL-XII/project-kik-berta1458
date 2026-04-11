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
                        <input type="date" id="filterTanggal" value="{{ request('tanggal') }}">

                        <select id="filterKategori">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategori as $k)
                            <option value="{{ $k->nama_kategori }}"
                                {{ request('kategori') == $k->nama_kategori ? 'selected' : '' }}>
                                {{ $k->nama_kategori }}
                            </option>
                            @endforeach
                        </select>

                        <button id="btnFilter">Terapkan</button>
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
                                <th>Konselor</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Hasil</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuan as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y') }}</td>
                                <td>{{ $item->konselor->nama ?? '-' }}</td>
                                <td>{{ $item->kategori->nama_kategori }}</td>
                                <td> <span class="{{ $item->status }}">
                                        {{ ucfirst($item->status) }}
                                    </span></td>
                                <td>
                                    <a class="detail" data-nama="{{ $user->siswa->nama ?? '-' }}"
                                        data-kelas="{{ $user->siswa->kelas ?? '-' }}"
                                        data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y') }}"
                                        data-catatan="{{ $item->laporan->pesan_siswa ?? 'Belum ada catatan' }}"
                                        href="#"><i class="fa-solid fa-folder"></i></a>
                                    @if($item->status === 'ditolak')
                                    <button class="catatan"
                                        data-alasan="{{ $item->alasan_penolakan }}">
                                        <i class="fa-solid fa-clipboard-list"></i>
                                    </button>
                                    @endif
                                </td>
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
<!-- modal penolakan -->
<div id="modalCatatan" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Alasan Penolakan</h2>
        </div>

        <div class="modal-content">
            <p id="isiCatatan"></p>
        </div>

        <div class="modal-actions">
            <button id="closeModalCatatan">Tutup</button>
        </div>
    </div>
</div>

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
    document.querySelector('.search').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();

        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ?
                '' :
                'none';
        });
    });
    document.getElementById('btnFilter').onclick = function() {
        const tanggal = document.getElementById('filterTanggal').value;
        const kategori = document.getElementById('filterKategori').value;
        const search = document.querySelector('.search').value;

        let url = new URL(window.location.href);

        if (tanggal) url.searchParams.set('tanggal', tanggal);
        if (kategori) url.searchParams.set('kategori', kategori);
        if (search) url.searchParams.set('search', search);

        window.location.href = url.toString();
    };

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
        window.location.href = window.location.pathname;
    });
    document.querySelectorAll('.catatan').forEach(btn => {
        btn.onclick = () => {
            document.getElementById('modalCatatan').style.display = 'flex';
            document.getElementById('isiCatatan').textContent = btn.dataset.alasan || 'Tidak ada alasan';
        };
    });

    // tombol tutup
    document.getElementById('closeModalCatatan').onclick = () => {
        document.getElementById('modalCatatan').style.display = 'none';
    };
</script>