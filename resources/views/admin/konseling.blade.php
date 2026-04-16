@include('layout.header')

<section class="admin-konseling">
    <div class="container">

        <div class="title-body">
            <h3>Kelola Konseling</h3>
            <p>Tambah dan kelola laporan konseling</p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="search-tambah">
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
                        <select id="filterKonselor">
                            <option value="">Pilih Konselor</option>
                            @foreach($konselor as $k)
                            <option value="{{ $k->id }}"
                                {{ request('konselor') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama }}
                            </option>
                            @endforeach
                        </select>

                        <button id="btnFilter">Terapkan</button>
                        <button id="reset">Reset</button>
                    </div>
                    <div class="right-sec">
                        <button class="btn-tambah-admin" id="openModalKonseling">
                            + Tambah Konseling
                        </button>
                        <input class="search" type="text" id="searchInput" placeholder="Cari...">
                    </div>
                </div>
                <div class="selected-filter" id="selectedFilter"></div>
            </div>
        </div>

        <div class="admin-table">
            <table>
                <thead>
                    <tr>
                        <th>Tanggal Konseling</th>
                        <th>Nama Siswa</th>
                        <th>Konselor</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($laporan as $lap)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($lap->tanggal_pengajuan)->translatedFormat('d F Y')}}</td>
                        <td>{{ $lap->siswa->nama }}</td>
                        <td>{{ $lap->konselor->nama }}</td>
                        <td>{{ $lap->kategori->nama_kategori }}</td>
                        <td>@if ($lap->status == 'menunggu')
                            <span class="menunggu">Menunggu</span>

                            @elseif ($lap->status == 'ditolak')
                            <span class="ditolak">Ditolak</span>

                            @elseif ($lap->status == 'dijadwalkan')
                            <span class="dijadwalkan">Dijadwalkan</span>

                            @elseif ($lap->status == 'berlangsung')
                            <span class="berlangsung">Berlangsung</span>

                            @elseif ($lap->status == 'selesai')
                            <span class="selesai">Selesai</span>
                            @endif
                        </td>
                        <td>
                            <button class="detail"
                                data-deskripsi="{{ $lap->deskripsi_masalah }}">
                                <i class="fa-solid fa-folder"></i>
                            </button>
                            <button class="pesan"
                                data-id="{{ $lap->id }}"
                                data-nama="{{ $lap->siswa->nama }}">
                                <i class="fa-solid fa-message"></i>
                            </button>
                            @if($lap->status === 'menunggu')
                            <button class="edit-konselor"
                                data-id="{{ $lap->id }}"
                                data-konselor="{{ $lap->id_konselor }}">
                                <i class="fa-solid fa-user"></i>
                            </button>
                            @endif

                            @if($lap->status === 'ditolak')
                            <button class="catatan"
                                data-alasan="{{ $lap->alasan_penolakan }}">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </button>
                            @endif

                            @if(in_array($lap->status, ['dijadwalkan', 'berlangsung', 'selesai']))
                            <button class="isi-lap"
                                data-id="{{ $lap->id }}"
                                data-nama="{{ $lap->siswa->nama }}"
                                data-kelas="{{ $lap->siswa->kelas }}"
                                data-konselor="{{ $lap->konselor->nama }}"
                                data-kategori="{{ $lap->kategori->nama_kategori }}"
                                data-tanggal="{{ $lap->tanggal_pengajuan }}"
                                data-permasalahan="{{ $lap->deskripsi_masalah }}"
                                data-hasil="{{ $lap->laporan->hasil_catatan ?? '' }}"
                                data-foto="{{ $lap->laporan->bukti_file ?? '' }}" data-pesan="{{ $lap->laporan->pesan_siswa ?? '' }}">
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
            <button onclick="prevPage()">
                <p>Kembali</p>
            </button>
            <span class="number" id="pageInfo"></span>
            <button onclick="nextPage()">
                <p>Berikutnya</p>
            </button>
        </div>
    </div>
</section>

<!-- modal edit konselor -->
<div id="modalEditKonselor" class="modal-overlay" style="display:none;">
    <div class="modal-box modal-admin">
        <div class="modal-header-laporan">
            <h2>Edit Konselor</h2>
        </div>
        <form id="formEditKonselor">
            @csrf

            <input type="hidden" id="editId" name="id_pengajuan">

            <label>Konselor</label><br>
            <select name="id_konselor" required>
                <option value="">Pilih Konselor</option>
                @foreach($konselor as $k)
                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                @endforeach
            </select>

            <br><br>

            <div class="modal-actions" style="display:flex; justify-content: center; gap:10px;">
                <button type="button" id="batalKonselor">Batal</button>
                <button type="button" id="simpanKonselor">Simpan</button>
            </div>
        </form>

    </div>
</div>

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

<!-- modal laporan -->
<div id="modalIsiLap" class="modal-overlay" style="display:none;">
    <div class="modal-box modal-laporan">

        <div class="modal-header-laporan">
            <h2>Laporan Bimbingan Konseling</h2>
        </div>

        <!-- WAJIB -->
        <input type="hidden" id="laporanId">

        <div class="modal-content-laporan">

            <div class="identitas-lap">
                <p><span>Nama</span> : <span id="lapNama"></span></p>
                <p><span>Kelas</span> : <span id="lapKelas"></span></p>
                <p><span>Tgl. Konseling</span> : <span id="lapTanggal"></span></p>
            </div>

            <table class="table-laporan">
                <tr>
                    <th>Konselor</th>
                    <td id="lapKonselor"></td>
                </tr>
                <tr>
                    <th>Kategori</th>
                    <td id="lapKategori"></td>
                </tr>
                <tr>
                    <th>Permasalahan</th>
                    <td id="lapPermasalahan"></td>
                </tr>

                <tr>
                    <th>Bukti Konseling</th>
                    <td>
                        <input style="margin-top:10px;" type="file" id="buktiFile" name="bukti_file" accept="image/*,.pdf">

                        <!-- Preview Image -->
                        <img id="previewImage" style="display:none; width:100%; margin-top:10px;" />

                        <!-- Preview PDF -->
                        <iframe id="previewPDF" style="display:none; width:100%; height:300px; margin-top:10px;"></iframe>
                    </td>
                </tr>

                <!-- HASIL -->
                <tr>
                    <th>Hasil Konseling</th>
                    <td>
                        <div class="editor-toolbar">
                            <button type="button" onclick="formatText('bold')"><b>B</b></button>
                            <button type="button" onclick="formatText('italic')"><i>I</i></button>
                            <button type="button" onclick="formatText('underline')"><u>U</u></button>
                            <button type="button" onclick="formatText('insertUnorderedList')">
                                <i class="fa-solid fa-list"></i>
                            </button>
                            <button type="button" onclick="formatText('insertOrderedList')">
                                <i class="fa-solid fa-list-ol"></i>
                            </button>
                        </div>

                        <div id="lapCatatan" class="textarea-laporan editor"
                            contenteditable="true"
                            data-placeholder="Tuliskan hasil konseling..."></div>
                    </td>
                </tr>

                <!-- CATATAN -->
                <tr>
                    <th>Catatan</th>
                    <td>
                        <div class="editor-toolbar">
                            <button type="button" onclick="formatText('bold')"><b>B</b></button>
                            <button type="button" onclick="formatText('italic')"><i>I</i></button>
                            <button type="button" onclick="formatText('underline')"><u>U</u></button>
                            <button type="button" onclick="formatText('insertUnorderedList')">
                                <i class="fa-solid fa-list"></i>
                            </button>
                            <button type="button" onclick="formatText('insertOrderedList')">
                                <i class="fa-solid fa-list-ol"></i>
                            </button>
                        </div>

                        <div id="pesanSiswa" class="textarea-laporan editor"
                            contenteditable="true"
                            data-placeholder="Catatan tambahan..."></div>
                    </td>
                </tr>
            </table>

        </div>

        <!-- BUTTON FIX -->
        <div class="modal-actions-laporan">
            <button type="button" id="closeIsiLap">Batal</button>
            <button type="button" id="submitIsiLap">Simpan</button>
        </div>

    </div>
</div>

<!-- modal deskripsi -->
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

<!-- modal tambah konseling -->
<div class="modal-overlay" id="modalTambahKonseling" style="display:none;">
    <div class="modal-box modal-admin">

        <h3>Tambah Konseling</h3>

        <form id="formTambahKonseling">
            @csrf

            <label>Siswa</label><br>
            <select name="id_siswa" required>
                @foreach($siswa as $s)
                <option value="{{ $s->id }}">{{ $s->nama }}</option>
                @endforeach
            </select>
            <br><br>
            <label>Kategori</label><br>
            <select name="id_kategori" required>
                @foreach($kategori as $k)
                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                @endforeach
            </select>
            <br><br>

            <label>Tanggal</label><br>
            <input type="date" name="tanggal_pengajuan" required>
            <br><br>
            <label>Deskripsi (opsional)</label><br>
            <textarea name="deskripsi_masalah"></textarea>
            <br><br>
            <div class="modal-actions">
                <button type="button" id="closeTambahKonseling" class="btn-batal">Batal</button>
                <button type="submit" class="btn-kirim">Simpan</button>
            </div>

        </form>

    </div>
</div>
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

@include('layout.footer')


<script>
    // APPLY FILTER
    document.getElementById('btnFilter').onclick = function() {
        let url = new URL(window.location.href);

        const tanggal = document.getElementById('filterTanggal').value;
        const kategori = document.getElementById('filterKategori').value;
        const konselor = document.getElementById('filterKonselor').value;
        const search = document.getElementById('searchInput').value;

        if (tanggal) url.searchParams.set('tanggal', tanggal);
        if (kategori) url.searchParams.set('kategori', kategori);
        if (konselor) url.searchParams.set('konselor', konselor);
        if (search) url.searchParams.set('search', search);

        window.location.href = url.toString();
    };
    // CHIP FILTER
    const tanggal = document.getElementById('filterTanggal');
    const kategori = document.getElementById('filterKategori');
    const konselor = document.getElementById('filterKonselor');
    const container = document.getElementById('selectedFilter');

    function createChip(label, type) {
        container.querySelectorAll(`.chip[data-type="${type}"]`)
            .forEach(el => el.remove());

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
            if (type === 'konselor') konselor.value = '';
        };

        container.appendChild(chip);
    }

    // EVENT
    tanggal.addEventListener('change', () => {
        if (!tanggal.value) return;

        const date = new Date(tanggal.value);
        const formatted = date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        createChip(formatted, 'tanggal');
    });

    kategori.addEventListener('change', () => {
        if (!kategori.value) return;
        createChip(kategori.value, 'kategori');
    });

    konselor.addEventListener('change', () => {
        if (!konselor.value) return;

        const label = konselor.options[konselor.selectedIndex].text;
        createChip(label, 'konselor');
    });
    // RESET
    document.getElementById('reset').onclick = function() {
        window.location.href = window.location.pathname;
    };

    // SEARCH REALTIME
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();

        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ?
                '' :
                'none';
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
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

        // MODAL TAMBAH KONSELING
        const modalTambah = document.getElementById('modalTambahKonseling');

        document.getElementById('openModalKonseling').onclick = () => {
            modalTambah.style.display = 'flex';
        };

        document.getElementById('closeTambahKonseling').onclick = () => {
            modalTambah.style.display = 'none';
        };

        document.getElementById('formTambahKonseling').onsubmit = function(e) {
            e.preventDefault();

            fetch('/admin/konseling/store', {
                    method: 'POST',
                    body: new FormData(this)
                })
                .then(res => res.json())
                .then(res => {
                    alert(res.message);
                    location.reload();
                })
                .catch(() => alert('Gagal tambah'));
        };

        // MODAL EDIT KONSELING


        const modalEdit = document.getElementById('modalEditKonselor');
        const selectKonselor = document.querySelector('select[name="id_konselor"]');
        const inputId = document.getElementById('editId');

        document.querySelectorAll('.edit-konselor').forEach(btn => {
            btn.onclick = () => {
                modalEdit.style.display = 'flex';

                inputId.value = btn.dataset.id;
                selectKonselor.value = btn.dataset.konselor || '';
            };
        });
        document.getElementById('batalKonselor').onclick = function() {
            document.getElementById('modalEditKonselor').style.display = 'none';
        };

        // MODAL DESKRIPSI
        document.querySelectorAll('.detail').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('modalDeskripsi').textContent = btn.dataset.deskripsi;
                document.getElementById('modalDetail').style.display = 'flex';
            };
        });


        document.getElementById('closeModalDetail').onclick = () => {
            document.getElementById('modalDetail').style.display = 'none';
        };

        document.getElementById('simpanKonselor').onclick = function() {

            let formData = new FormData(document.getElementById('formEditKonselor'));

            fetch('/admin/konseling/update-konselor', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    alert(res.message);
                    location.reload();
                })
                .catch(() => alert('Gagal update konselor'));
        };

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
                fetch(`/admin/laporan/${currentPesanId}/ambil-pesan`)
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
            if (!pesan) {
                alert('Pesan tidak boleh kosong');
                return;
            }

            const formData = new FormData();
            formData.append('pesan_siswa', pesan);
            formData.append('_token', '{{ csrf_token() }}');

            fetch(`/admin/laporan/${currentPesanId}/simpan-pesan`, {
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
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
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

        // MODAL LAPORAN
        let mode = 'create';
        let foto = null;
        let stream = null;

        const modalLap = document.getElementById('modalIsiLap');
        const btnSubmit = document.getElementById('submitIsiLap');
        const btnClose = document.getElementById('closeIsiLap');
        const catatan = document.getElementById('lapCatatan');
        const pesan = document.getElementById('pesanSiswa');

        document.querySelectorAll('.isi-lap').forEach(btn => {
            btn.onclick = () => {

                modalLap.style.display = 'flex';

                // isi data
                document.getElementById('laporanId').value = btn.dataset.id;
                document.getElementById('lapNama').textContent = btn.dataset.nama;
                document.getElementById('lapKelas').textContent = btn.dataset.kelas;
                document.getElementById('lapKonselor').textContent = btn.dataset.konselor;
                document.getElementById('lapKategori').textContent = btn.dataset.kategori;
                document.getElementById('lapPermasalahan').textContent = btn.dataset.permasalahan;
                document.getElementById('lapTanggal').textContent = btn.dataset.tanggal;

                // reset kamera
                foto = null;

                // mode
                let hasil = btn.dataset.hasil;
                let fotoLama = btn.dataset.foto;
                let pesanLama = btn.dataset.pesan;
                let fileInput = document.getElementById('buktiFile');

                // =====================
                // MODE VIEW / CREATE
                // =====================
                if (hasil && hasil.trim() !== '') {
                    mode = 'view';

                    catatan.innerHTML = hasil;
                    pesan.innerHTML = pesanLama || '';

                    catatan.contentEditable = false;
                    pesan.contentEditable = false;

                    document.querySelectorAll('.editor-toolbar').forEach(el => el.style.display = 'none');

                    btnSubmit.textContent = 'Edit';
                    fileInput.style.display = 'none';
                } else {
                    mode = 'create';

                    catatan.innerHTML = '';
                    pesan.innerHTML = '';

                    catatan.contentEditable = true;
                    pesan.contentEditable = true;

                    document.querySelectorAll('.editor-toolbar').forEach(el => el.style.display = 'flex');

                    btnSubmit.textContent = 'Simpan';
                    fileInput.style.display = 'block';
                }

                // =====================
                // FOTO LAMA
                // =====================

                let previewImage = document.getElementById('previewImage');
                let previewPDF = document.getElementById('previewPDF');

                previewImage.style.display = "none";
                previewPDF.style.display = "none";

                if (fotoLama) {
                    let url = "/storage/" + fotoLama;

                    if (fotoLama.endsWith('.pdf')) {
                        previewPDF.src = url;
                        previewPDF.style.display = "block";
                    } else {
                        previewImage.src = url;
                        previewImage.style.display = "block";
                    }
                }

            };
        });


        // CLOSE MODAL
        btnClose.onclick = () => {
            modalLap.style.display = 'none';

            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        };


        // =========================
        // BUTTON AKSI
        // =========================
        btnSubmit.onclick = function() {

            // MODE VIEW → EDIT
            if (mode === 'view') {
                mode = 'edit';

                catatan.contentEditable = true;
                pesan.contentEditable = true;

                document.querySelectorAll('.editor-toolbar').forEach(el => el.style.display = 'flex');

                this.textContent = 'Update';
                document.getElementById('buktiFile').style.display = 'block';
                return;
            }

            let formData = new FormData();
            let fileInput = document.getElementById('buktiFile');
            let isiCatatan = catatan.innerHTML.trim();

            if (!isiCatatan || isiCatatan === "<br>") {
                alert("Hasil konseling wajib diisi");
                return;
            }

            formData.append('hasil_catatan', isiCatatan);
            formData.append('id_pengajuan', document.getElementById('laporanId').value);
            formData.append('hasil_catatan', catatan.innerHTML);
            formData.append('pesan_siswa', pesan.innerHTML);

            // ✅ INI YANG PENTING
            if (fileInput.files[0]) {
                formData.append('bukti_file', fileInput.files[0]);
            }

            fetch('/admin/laporan/simpan', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(async res => {
                    let text = await res.text();

                    try {
                        let data = JSON.parse(text);

                        if (!res.ok) throw data;

                        return data;
                    } catch (e) {
                        console.error("RAW RESPONSE:", text);
                        throw {
                            message: "Response bukan JSON!"
                        };
                    }
                })
                .then(res => {
                    alert(res.message);
                    location.reload();
                })
                .catch(err => {
                    console.error("ERROR:", err);
                    alert(err.message || 'Gagal update');
                });
        };



    });
    // =========================
    // TEXT EDITOR
    // =========================
    function formatText(cmd) {
        document.execCommand(cmd, false, null);
    }

    function formatTextPesan(cmd) {
        document.execCommand(cmd, false, null);
    }

    // preview setelah disimpan
    document.getElementById('buktiFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const previewImage = document.getElementById('previewImage');
        const previewPDF = document.getElementById('previewPDF');

        previewImage.style.display = 'none';
        previewPDF.style.display = 'none';

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(event) {
                previewImage.src = event.target.result;
                previewImage.style.display = 'block';
            };
            reader.readAsDataURL(file);

        } else if (file.type === 'application/pdf') {
            const url = URL.createObjectURL(file);
            previewPDF.src = url;
            previewPDF.style.display = 'block';
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