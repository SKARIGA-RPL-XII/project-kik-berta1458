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
                        <input type="date" id="filterTanggal" value="{{ request('tanggal') }}">
                        <select id="filterKategori">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategori as $k)
                            <option value="{{ $k->nama_kategori }}" {{ request('kategori') == $k->nama_kategori ? 'selected' : '' }}>
                                {{ $k->nama_kategori }}
                            </option>
                            @endforeach
                        </select>
                        <select id="filterStatus">
                            <option value="">Pilih Status</option>
                            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="dijadwalkan" {{ request('status') == 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                            <option value="berlangsung" {{ request('status') == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
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
                                <th>Nama Siswa</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @forelse($laporan as $lap)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($lap->tanggal_pengajuan)->translatedFormat('d F Y') }}</td>
                                <td>{{ $lap->siswa->nama }}</td>
                                <td>{{ $lap->kategori->nama_kategori }}</td>
                                <td>
                                    @if($lap->status == 'menunggu') <span class="menunggu">Menunggu</span>
                                    @elseif($lap->status == 'ditolak') <span class="ditolak">Ditolak</span>
                                    @elseif($lap->status == 'dijadwalkan') <span class="dijadwalkan">Dijadwalkan</span>
                                    @elseif($lap->status == 'berlangsung') <span class="berlangsung">Berlangsung</span>
                                    @elseif($lap->status == 'selesai') <span class="selesai">Selesai</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Tombol deskripsi --}}
                                    <button class="detail" data-deskripsi="{{ $lap->deskripsi_masalah }}">
                                        <i class="fa-solid fa-folder"></i>
                                    </button>

                                    <button class="pesan"
                                        data-id="{{ $lap->id }}"
                                        data-nama="{{ $lap->siswa->nama }}">
                                        <i class="fa-solid fa-message"></i>
                                    </button>

                                    {{-- Tombol alasan penolakan --}}
                                    @if($lap->status === 'ditolak')
                                    <button class="catatan" data-alasan="{{ $lap->alasan_penolakan }}">
                                        <i class="fa-solid fa-clipboard-list"></i>
                                    </button>
                                    @endif

                                    {{-- Tombol isi laporan --}}
                                    @if(in_array($lap->status, ['dijadwalkan', 'berlangsung', 'selesai']))
                                    <button class="isi-lap"
                                        data-id="{{ $lap->id }}"
                                        data-nama="{{ $lap->siswa->nama }}"
                                        data-kelas="{{ $lap->siswa->kelas }}"
                                        data-kategori="{{ $lap->kategori->nama_kategori }}"
                                        data-tanggal="{{ $lap->tanggal_pengajuan }}"
                                        data-permasalahan="{{ $lap->deskripsi_masalah }}"
                                        data-foto="{{ optional($lap->laporan)->bukti_file }}"
                                        data-catatan="{{ urlencode(optional($lap->laporan)->hasil_catatan) }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align:center;">Tidak ada data laporan</td>
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

{{-- Modal deskripsi --}}
<div id="modalDetail" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header"><h2>Deskripsi Singkat Permasalahan</h2></div>
        <div class="modal-content"><p id="modalDeskripsi"></p></div>
        <div class="modal-actions"><button id="closeModalDetail">Tutup</button></div>
    </div>
</div>

{{-- Modal laporan --}}
<div id="modalIsiLap" class="modal-overlay" style="display:none;">
    <div class="modal-box modal-laporan">
        <div class="modal-header-laporan"><h2>Laporan Bimbingan Konseling</h2></div>
        <div class="modal-content-laporan">
            <div class="identitas-lap">
                <p><span>Nama</span> : <span id="lapNama"></span></p>
                <p><span>Kelas</span> : <span id="lapKelas"></span></p>
                <p><span>Tgl. Konseling</span> : <span id="lapTanggal"></span></p>
            </div>
            <table class="table-laporan">
                <tr><th>Kategori</th><td id="lapKategori"></td></tr>
                <tr><th>Permasalahan</th><td id="lapPermasalahan"></td></tr>
                <tr>
                    <th>Bukti Konseling</th>
                    <td>
                        <small style="color:red;">*File tidak dapat diubah setelah disimpan</small>
                        <input style="margin-top:10px;" type="file" id="buktiFile" name="bukti_file" accept="image/*,.pdf">
                        <img id="previewImage" style="display:none; width:100%; margin-top:10px;" />
                        <iframe id="previewPDF" style="display:none; width:100%; height:300px; margin-top:10px;"></iframe>
                    </td>
                </tr>
                <tr>
                    <th>Hasil Konseling</th>
                    <td>
                        <div class="editor-toolbar">
                            <button type="button" onclick="formatText('bold')"><b>B</b></button>
                            <button type="button" onclick="formatText('italic')"><i>I</i></button>
                            <button type="button" onclick="formatText('underline')"><u>U</u></button>
                            <button type="button" onclick="formatText('insertUnorderedList')"><i class="fa-solid fa-list"></i></button>
                            <button type="button" onclick="formatText('insertOrderedList')"><i class="fa-solid fa-list-ol"></i></button>
                        </div>
                        <div id="lapCatatan" class="textarea-laporan editor" contenteditable="true" placeholder="Tuliskan hasil dan catatan..."></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="modal-actions-laporan">
            <button id="closeIsiLap" class="btn-batal-lap">Batal</button>
            <button id="submitIsiLap" class="btn-simpan-lap">Simpan</button>
            <button id="btnTutupDetailLap" class="btn-tutup-lap" style="display:none;">Tutup</button>
        </div>
    </div>
</div>

{{-- Modal pesan (chat history) --}}
<div id="modalPesan" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-header">
            <h2>Pesan ke <span id="pesanNamaSiswa"></span></h2>
        </div>
        <div class="modal-content">
            {{-- Area chat riwayat pesan --}}
            <div class="chat-bubble" id="chatDisplay" style="min-height:80px; max-height:300px; overflow-y:auto; margin-bottom:12px;">
                <p style="color:#aaa; font-size:13px; text-align:center;" id="chatKosong">Belum ada pesan</p>
            </div>

            {{-- Input pesan baru --}}
            <div class="wrap-input-text">
                <div class="editor-toolbar">
                    <button type="button" onclick="formatTextPesan('bold')"><b>B</b></button>
                    <button type="button" onclick="formatTextPesan('italic')"><i>I</i></button>
                    <button type="button" onclick="formatTextPesan('underline')"><u>U</u></button>
                    <button type="button" onclick="formatTextPesan('insertUnorderedList')"><i class="fa-solid fa-list"></i></button>
                    <button type="button" onclick="formatTextPesan('insertOrderedList')"><i class="fa-solid fa-list-ol"></i></button>
                </div>
                <div id="inputPesan" class="textarea-laporan editor" contenteditable="true" placeholder="Tulis pesan ke siswa..."></div>
            </div>
        </div>
        <div class="modal-actions">
            <button id="closeModalPesan">Tutup</button>
            <button id="kirimPesan">Kirim</button>
        </div>
    </div>
</div>

{{-- Modal penolakan --}}
<div id="modalCatatan" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header"><h2>Alasan Penolakan</h2></div>
        <div class="modal-content"><p id="isiCatatan"></p></div>
        <div class="modal-actions"><button id="closeModalCatatan">Tutup</button></div>
    </div>
</div>

@include('layout.footer')

<script>
    // ── Filter ───────────────────────────────────────────
    document.getElementById('btnFilter').onclick = function() {
        const tanggal  = document.getElementById('filterTanggal').value;
        const kategori = document.getElementById('filterKategori').value;
        const status   = document.getElementById('filterStatus').value;
        const search   = document.querySelector('.search').value;
        let url = new URL(window.location.href);
        if (tanggal)  url.searchParams.set('tanggal', tanggal);
        if (kategori) url.searchParams.set('kategori', kategori);
        if (status)   url.searchParams.set('status', status);
        if (search)   url.searchParams.set('search', search);
        window.location.href = url.toString();
    };

    document.getElementById('reset').onclick = function() {
        window.location.href = window.location.pathname;
    };

    document.querySelector('.search').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });

    const tanggal  = document.getElementById('filterTanggal');
    const kategori = document.getElementById('filterKategori');
    const status   = document.getElementById('filterStatus');
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
            if (type === 'status') status.value = '';
        };
        container.appendChild(chip);
    }

    tanggal.addEventListener('change', () => {
        if (!tanggal.value) return;
        const date = new Date(tanggal.value);
        createChip(date.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' }), 'tanggal');
    });
    kategori.addEventListener('change', () => { if (kategori.value) createChip(kategori.value, 'kategori'); });
    status.addEventListener('change', () => {
        if (status.value) createChip(status.options[status.selectedIndex].text, 'status');
    });

    // ── Text editor ──────────────────────────────────────
    function formatText(command)      { document.execCommand(command, false, null); }
    function formatTextPesan(command) { document.execCommand(command, false, null); }

    // ── Modal deskripsi ──────────────────────────────────
    document.querySelectorAll('.detail').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('modalDeskripsi').textContent = btn.dataset.deskripsi;
            document.getElementById('modalDetail').style.display = 'flex';
        });
    });
    document.getElementById('closeModalDetail').addEventListener('click', () => {
        document.getElementById('modalDetail').style.display = 'none';
    });

    // ── Modal penolakan
    document.querySelectorAll('.catatan').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('isiCatatan').textContent = btn.dataset.alasan;
            document.getElementById('modalCatatan').style.display = 'flex';
        });
    });
    document.getElementById('closeModalCatatan').addEventListener('click', () => {
        document.getElementById('modalCatatan').style.display = 'none';
    });

    // ── Modal pesan (chat) ───────────────────────────────
    let currentPesanId = null;

    document.querySelectorAll('.pesan').forEach(btn => {
        btn.addEventListener('click', () => {
            currentPesanId = btn.dataset.id;
            document.getElementById('pesanNamaSiswa').textContent = btn.dataset.nama;
            document.getElementById('inputPesan').innerHTML = '';

            // Kosongkan dulu, tampilkan loading
            const chatDisplay = document.getElementById('chatDisplay');
            chatDisplay.innerHTML = '<p style="color:#aaa;font-size:13px;text-align:center;">Memuat pesan...</p>';

            // Fetch riwayat pesan dari backend
            fetch(`/konselor/laporan/${currentPesanId}/ambil-pesan`)
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        chatDisplay.innerHTML = '<p style="color:#aaa;font-size:13px;text-align:center;">Belum ada pesan</p>';
                    } else {
                        let html = '';
                        data.forEach(p => {
                            html += `
                                <div class="bubble kanan">
                                    <div>${p.isi_pesan}</div>
                                    <span class="bubble-waktu">${p.waktu}</span>
                                </div>`;
                        });
                        chatDisplay.innerHTML = html;
                        // Scroll ke bawah
                        chatDisplay.scrollTop = chatDisplay.scrollHeight;
                    }
                })
                .catch(() => {
                    chatDisplay.innerHTML = '<p style="color:red;font-size:13px;">Gagal memuat pesan</p>';
                });

            document.getElementById('modalPesan').style.display = 'flex';
        });
    });

    document.getElementById('closeModalPesan').addEventListener('click', () => {
        document.getElementById('modalPesan').style.display = 'none';
    });

    // ── Kirim pesan ──────────────────────────────────────
    document.getElementById('kirimPesan').addEventListener('click', function() {
        const pesan = document.getElementById('inputPesan').innerHTML.trim();
        if (!pesan) { alert('Pesan tidak boleh kosong'); return; }

        const formData = new FormData();
        formData.append('pesan_siswa', pesan);
        formData.append('_token', '{{ csrf_token() }}');

        fetch(`/konselor/laporan/${currentPesanId}/simpan-pesan`, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Langsung tambahkan bubble baru tanpa reload
                const chatDisplay = document.getElementById('chatDisplay');
                const kosong = chatDisplay.querySelector('p');
                if (kosong) kosong.remove();

                const now = new Date().toLocaleString('id-ID', {
                    day:'2-digit', month:'short', year:'numeric',
                    hour:'2-digit', minute:'2-digit'
                });

                chatDisplay.innerHTML += `
                    <div class="bubble kanan">
                        <div>${pesan}</div>
                        <span class="bubble-waktu">${now}</span>
                    </div>`;
                chatDisplay.scrollTop = chatDisplay.scrollHeight;
                document.getElementById('inputPesan').innerHTML = '';
            }
        })
        .catch(() => alert('Gagal kirim pesan'));
    });

    // ── Modal isi laporan ────────────────────────────────
    document.querySelectorAll('.isi-lap').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('lapNama').textContent       = btn.dataset.nama;
            document.getElementById('lapKelas').textContent      = btn.dataset.kelas;
            document.getElementById('lapTanggal').textContent    = btn.dataset.tanggal;
            document.getElementById('lapKategori').textContent   = btn.dataset.kategori;
            document.getElementById('lapPermasalahan').textContent = btn.dataset.permasalahan;

            const textarea     = document.getElementById('lapCatatan');
            const previewImage = document.getElementById('previewImage');
            const previewPDF   = document.getElementById('previewPDF');
            const fileInput    = document.getElementById('buktiFile');
            const btnSimpan    = document.getElementById('submitIsiLap');
            const btnBatal     = document.getElementById('closeIsiLap');
            const btnTutup     = document.getElementById('btnTutupDetailLap');

            previewImage.style.display = 'none';
            previewPDF.style.display   = 'none';
            fileInput.value = '';

            textarea.innerHTML = btn.dataset.catatan ? decodeURIComponent(btn.dataset.catatan) : '';
            const sudahAda = btn.dataset.catatan && btn.dataset.catatan.trim() !== '';

            if (sudahAda) {
                textarea.contentEditable = 'false';
                fileInput.style.display  = 'none';
                btnSimpan.style.display  = 'none';
                btnBatal.style.display   = 'none';
                btnTutup.style.display   = 'inline-block';

                if (btn.dataset.foto) {
                    const url = '/storage/' + btn.dataset.foto;
                    if (btn.dataset.foto.endsWith('.pdf')) {
                        previewPDF.src = url; previewPDF.style.display = 'block';
                    } else {
                        previewImage.src = url; previewImage.style.display = 'block';
                    }
                }
            } else {
                textarea.contentEditable = 'true';
                fileInput.style.display  = 'block';
                btnSimpan.style.display  = 'inline-block';
                btnBatal.style.display   = 'inline-block';
                btnTutup.style.display   = 'none';
            }

            btnSimpan.setAttribute('data-current-id', btn.dataset.id);
            document.getElementById('modalIsiLap').style.display = 'flex';
        });
    });

    document.getElementById('buktiFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const previewImage = document.getElementById('previewImage');
        const previewPDF   = document.getElementById('previewPDF');
        previewImage.style.display = 'none';
        previewPDF.style.display   = 'none';
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => { previewImage.src = e.target.result; previewImage.style.display = 'block'; };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            previewPDF.src = URL.createObjectURL(file); previewPDF.style.display = 'block';
        }
    });

    document.getElementById('btnTutupDetailLap').addEventListener('click', () => {
        document.getElementById('modalIsiLap').style.display = 'none';
    });
    document.getElementById('closeIsiLap').addEventListener('click', () => {
        document.getElementById('modalIsiLap').style.display = 'none';
    });

    document.getElementById('submitIsiLap').addEventListener('click', function() {
        const id      = this.getAttribute('data-current-id');
        const catatan = document.getElementById('lapCatatan').innerHTML.trim();
        const fileInput = document.getElementById('buktiFile');

        if (!catatan) { alert('Catatan wajib diisi'); return; }

        const formData = new FormData();
        formData.append('hasil_catatan', catatan);
        if (fileInput.files[0]) formData.append('bukti_file', fileInput.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        fetch(`/konselor/laporan/${id}/simpan`, { method: 'POST', body: formData })
            .then(async res => {
                const text = await res.text();
                try {
                    const data = JSON.parse(text);
                    if (!res.ok) throw data;
                    alert(data.message);
                    location.reload();
                } catch (e) {
                    alert('Server error, cek console!');
                    console.error(text);
                }
            });
    });

    // ── Pagination ───────────────────────────────────────
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
