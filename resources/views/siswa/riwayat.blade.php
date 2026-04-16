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
            <div class="col-md-12">
                <div class="filter-wrap">
                    <div class="filter">
                        <input type="date" id="filterTanggal" value="{{ request('tanggal') }}">
                        <select id="filterKategori">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategori as $k)
                            <option value="{{ $k->nama_kategori }}" {{ request('kategori') == $k->nama_kategori ? 'selected' : '' }}>
                                {{ $k->nama_kategori }}
                            </option>
                            @endforeach
                        </select>
                        <button id="btnFilter">Terapkan</button>
                        <button id="reset">Reset</button>
                    </div>
                    <input class="search" type="text" placeholder="Cari...">
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @forelse($pengajuan as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y') }}</td>
                                <td>{{ $item->konselor->nama ?? '-' }}</td>
                                <td>{{ $item->kategori->nama_kategori }}</td>
                                <td><span class="{{ $item->status }}">{{ ucfirst($item->status) }}</span></td>
                                <td>
                                    <a class="detail"
                                        data-nama="{{ $user->siswa->nama ?? '-' }}"
                                        data-kelas="{{ $user->siswa->kelas ?? '-' }}"
                                        data-tanggal="{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y') }}"
                                        href="#">
                                        <i class="fa-solid fa-folder"></i>
                                    </a>

                                    @if($item->pesan->count() > 0)
                                    <button class="pesan" data-id="{{ $item->id }}">
                                        <i class="fa-solid fa-message"></i>
                                    </button>
                                    @endif

                                    @if($item->status === 'ditolak')
                                    <button class="catatan" data-alasan="{{ $item->alasan_penolakan }}">
                                        <i class="fa-solid fa-clipboard-list"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align:center;">Belum ada riwayat konseling</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="slide">
                    <button onclick="prevPage()"><p>Kembali</p></button>
                    <span class="number" id="pageInfo"></span>
                    <button onclick="nextPage()"><p>Berikutnya</p></button>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="modalCatatan" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header"><h2>Alasan Penolakan</h2></div>
        <div class="modal-content"><p id="isiCatatan"></p></div>
        <div class="modal-actions"><button id="closeModalCatatan">Tutup</button></div>
    </div>
</div>

<div class="overlay" id="popupDetail">
    <div class="popup-content-detail">
        <h2 class="modal-title">Detail Konseling</h2>
        <table class="table-report">
            <tr><th>Nama</th><td id="modalNama"></td></tr>
            <tr><th>Kelas</th><td id="modalKelas"></td></tr>
            <tr><th>Tgl. Konseling</th><td id="modalTanggal"></td></tr>
        </table>
        <button class="tutup" id="tutup">Tutup</button>
    </div>
</div>

<div id="modalPesan" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-header"><h2>Pesan dari Konselor</h2></div>
        <div class="modal-content">
            <div class="chat-bubble" id="chatDisplay" style="min-height:80px; max-height:300px; overflow-y:auto; margin-bottom:12px;">
                <p style="color:#aaa; font-size:13px; text-align:center;">Memuat pesan...</p>
            </div>
            <div style="padding:10px 12px; background:#f9f9f9; border-radius:6px; border:1px solid #eee;">
                <p style="font-size:12px; color:#999; margin:0;">
                    Kamu tidak dapat membalas pesan ini. Jika ingin berkonsultasi lebih lanjut, silakan ajukan konseling.
                </p>
            </div>
        </div>
        <div class="modal-actions">
            <button id="closeModalPesan">Tutup</button>
        </div>
    </div>
</div>

@include('layout/footer')

<script>
    document.querySelector('.search').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    document.getElementById('btnFilter').onclick = function() {
        const tanggal  = document.getElementById('filterTanggal').value;
        const kategori = document.getElementById('filterKategori').value;
        const search   = document.querySelector('.search').value;
        let url = new URL(window.location.href);
        if (tanggal)  url.searchParams.set('tanggal', tanggal);
        if (kategori) url.searchParams.set('kategori', kategori);
        if (search)   url.searchParams.set('search', search);
        window.location.href = url.toString();
    };

    document.querySelectorAll('.detail').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('modalNama').innerText    = this.dataset.nama;
            document.getElementById('modalKelas').innerText   = this.dataset.kelas;
            document.getElementById('modalTanggal').innerText = this.dataset.tanggal;
            document.getElementById('popupDetail').classList.add('show');
        });
    });
    document.getElementById('tutup').addEventListener('click', () => {
        document.getElementById('popupDetail').classList.remove('show');
    });

    document.querySelectorAll('.catatan').forEach(btn => {
        btn.onclick = () => {
            document.getElementById('isiCatatan').textContent = btn.dataset.alasan || 'Tidak ada alasan';
            document.getElementById('modalCatatan').style.display = 'flex';
        };
    });
    document.getElementById('closeModalCatatan').onclick = () => {
        document.getElementById('modalCatatan').style.display = 'none';
    };

    document.querySelectorAll('.pesan').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const chatDisplay = document.getElementById('chatDisplay');
            chatDisplay.innerHTML = '<p style="color:#aaa;font-size:13px;text-align:center;">Memuat pesan...</p>';
            document.getElementById('modalPesan').style.display = 'flex';

            fetch(`/siswa/pesan/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        chatDisplay.innerHTML = '<p style="color:#aaa;font-size:13px;text-align:center;">Belum ada pesan</p>';
                    } else {
                        let html = '';
                        data.forEach(p => {
                            html += `<div class="bubble kiri"><div>${p.isi_pesan}</div><span class="bubble-waktu">${p.waktu}</span></div>`;
                        });
                        chatDisplay.innerHTML = html;
                        chatDisplay.scrollTop = chatDisplay.scrollHeight;
                    }
                })
                .catch(() => {
                    chatDisplay.innerHTML = '<p style="color:red;font-size:13px;">Gagal memuat pesan</p>';
                });
        });
    });

    document.getElementById('closeModalPesan').addEventListener('click', () => {
        document.getElementById('modalPesan').style.display = 'none';
    });

    const tanggal   = document.getElementById('filterTanggal');
    const kategori  = document.getElementById('filterKategori');
    const container = document.getElementById('selectedFilter');

    function createChip(label, type) {
        container.querySelectorAll(`.chip[data-type="${type}"]`).forEach(el => el.remove());
        const chip = document.createElement('div');
        chip.className = 'chip';
        chip.dataset.type = type;
        chip.innerHTML = `${label}<span class="close">&times;</span>`;
        chip.querySelector('.close').onclick = () => {
            chip.remove();
            if (type === 'tanggal') tanggal.value = '';
            if (type === 'kategori') kategori.value = '';
        };
        container.appendChild(chip);
    }

    tanggal.addEventListener('change', () => {
        const date = new Date(tanggal.value);
        createChip(date.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' }), 'tanggal');
    });
    kategori.addEventListener('change', () => { createChip(kategori.value, 'kategori'); });
    document.getElementById('reset').addEventListener('click', () => { window.location.href = window.location.pathname; });

    let currentPage = 1;
    const rowsPerPage = 10;

    function showTablePage() {
        const rows = document.getElementById('tableBody').getElementsByTagName('tr');
        const start = (currentPage - 1) * rowsPerPage;
        const end   = start + rowsPerPage;
        for (let i = 0; i < rows.length; i++) {
            rows[i].style.display = (i >= start && i < end) ? '' : 'none';
        }
        document.getElementById('pageInfo').innerText = currentPage;
    }

    function nextPage() {
        const rows = document.getElementById('tableBody').getElementsByTagName('tr');
        if (currentPage < Math.ceil(rows.length / rowsPerPage)) { currentPage++; showTablePage(); }
    }

    function prevPage() {
        if (currentPage > 1) { currentPage--; showTablePage(); }
    }

    window.onload = function() { showTablePage(); };
</script>
