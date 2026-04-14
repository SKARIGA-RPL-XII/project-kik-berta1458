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
                        <input type="date" id="filterTanggal" value="{{ request('tanggal') }}"><select id="filterKategori">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategori as $k)
                            <option value="{{ $k->nama_kategori }}"
                                {{ request('kategori') == $k->nama_kategori ? 'selected' : '' }}>
                                {{ $k->nama_kategori }}
                            </option>
                            @endforeach
                        </select>
                        <select id="filterStatus">
                            <option value="">Pilih Status</option>

                            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>
                                Menunggu
                            </option>

                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                                Ditolak
                            </option>

                            <option value="dijadwalkan" {{ request('status') == 'dijadwalkan' ? 'selected' : '' }}>
                                Dijadwalkan
                            </option>

                            <option value="berlangsung" {{ request('status') == 'berlangsung' ? 'selected' : '' }}>
                                Berlangsung
                            </option>

                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>
                                Selesai
                            </option>
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
                                <th>Nama Siswa</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Hasil</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @forelse($laporan as $lap)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($lap->tanggal_pengajuan)->translatedFormat('d F Y')}}</td>
                                <td>{{ $lap->siswa->nama }}</td>
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
                                        data-kategori="{{ $lap->kategori->nama_kategori }}"
                                        data-tanggal="{{ $lap->tanggal_pengajuan }}"
                                        data-permasalahan="{{ $lap->deskripsi_masalah }}"
                                        data-foto="{{ optional($lap->laporan)->bukti_file }}"
                                        data-catatan="{{ urlencode(optional($lap->laporan)->hasil_catatan) }}"
                                        data-pesan="{{ urlencode(optional($lap->laporan)->pesan_siswa) }}"> <i class="fa-solid fa-pen-to-square"></i>
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
        </div>
    </div>
</section>

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

<!-- modal laporan -->
<div id="modalIsiLap" class="modal-overlay" style="display:none;">
    <div class="modal-box modal-laporan">

        <div class="modal-header-laporan">
            <h2>Laporan Bimbingan Konseling</h2>
        </div>

        <div class="modal-content-laporan">

            <div class="identitas-lap">
                <p><span>Nama</span> : <span id="lapNama"></span></p>
                <p><span>Kelas</span> : <span id="lapKelas"></span></p>
                <p><span>Tgl. Konseling</span> : <span id="lapTanggal"></span></p>
            </div>

            <table class="table-laporan">
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
                        <small style="color:red;">*File tidak dapat diubah setelah disimpan</small>
                        <input style="margin-top:10px;" type="file" id="buktiFile" name="bukti_file" accept="image/*,.pdf">

                        <!-- Preview Image -->
                        <img id="previewImage" style="display:none; width:100%; margin-top:10px;" />

                        <!-- Preview PDF -->
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
                            <button type="button" onclick="formatText('insertUnorderedList')">
                                <i class="fa-solid fa-list"></i>
                            </button>
                            <button type="button" onclick="formatText('insertOrderedList')">
                                <i class="fa-solid fa-list-ol"></i>
                            </button>
                        </div>

                        <div id="lapCatatan" class="textarea-laporan editor" contenteditable="true"
                            placeholder="Tuliskan hasil dan catatan..."></div>
                    </td>
                </tr>
                <tr>
                    <th>Catatan</th>
                    <td>
                        <div class="editor-toolbar">
                            <button type="button" onclick="formatTextPesan('bold')"><b>B</b></button>
                            <button type="button" onclick="formatTextPesan('italic')"><i>I</i></button>
                            <button type="button" onclick="formatTextPesan('underline')"><u>U</u></button>
                            <button type="button" onclick="formatTextPesan('insertUnorderedList')">
                                <i class="fa-solid fa-list"></i>
                            </button>
                            <button type="button" onclick="formatTextPesan('insertOrderedList')">
                                <i class="fa-solid fa-list-ol"></i>
                            </button>
                        </div>

                        <div id="pesanSiswa" class="textarea-laporan editor" contenteditable="true"
                            placeholder="Tuliskan hasil dan catatan..."></div>
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


@include('layout.footer')

<script>
    // =======================
    // FILTER (APPLY)
    // =======================
    document.getElementById('btnFilter').onclick = function() {
        const tanggal = document.getElementById('filterTanggal').value;
        const kategori = document.getElementById('filterKategori').value;
        const status = document.getElementById('filterStatus').value;
        const search = document.querySelector('.search').value;

        let url = new URL(window.location.href);

        if (tanggal) url.searchParams.set('tanggal', tanggal);
        if (kategori) url.searchParams.set('kategori', kategori);
        if (status) url.searchParams.set('status', status);
        if (search) url.searchParams.set('search', search);

        window.location.href = url.toString();
    };

    // =======================
    // RESET
    // =======================
    document.getElementById('reset').onclick = function() {
        window.location.href = window.location.pathname;
    };

    // =======================
    // SEARCH REALTIME
    // =======================
    document.querySelector('.search').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();

        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ?
                '' :
                'none';
        });
    });

    // =======================
    // CHIP FILTER
    // =======================
    const tanggal = document.getElementById('filterTanggal');
    const kategori = document.getElementById('filterKategori');
    const status = document.getElementById('filterStatus');
    const container = document.getElementById('selectedFilter');

    function createChip(label, type) {
        // hapus chip lama dengan type sama
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
            if (type === 'status') status.value = '';
        };

        container.appendChild(chip);
    }

    // =======================
    // EVENT TANGGAL
    // =======================
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

    // =======================
    // EVENT KATEGORI
    // =======================
    kategori.addEventListener('change', () => {
        if (!kategori.value) return;

        createChip(kategori.value, 'kategori');
    });

    // =======================
    // EVENT STATUS
    // =======================
    status.addEventListener('change', () => {
        if (!status.value) return;

        const label = status.options[status.selectedIndex].text;
        createChip(label, 'status');
    });
    // SEARCH realtime
    document.querySelector('.search').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();

        document.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ?
                '' :
                'none';
        });
    });
    let currentId = null;


    // text editor
    function formatText(command) {
        document.execCommand(command, false, null);
    }


    // modal deskripsi
    document.querySelectorAll('.detail').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('modalDeskripsi').textContent =
                btn.dataset.deskripsi;
            document.getElementById('modalDetail').style.display = 'flex';
        });
    });

    document.getElementById('closeModalDetail')
        .addEventListener('click', () => {
            document.getElementById('modalDetail').style.display = 'none';
        });


    // modal penolakan
    document.querySelectorAll('.catatan').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('isiCatatan').textContent =
                btn.dataset.alasan;
            document.getElementById('modalCatatan').style.display = 'flex';
        });
    });

    document.getElementById('closeModalCatatan')
        .addEventListener('click', () => {
            document.getElementById('modalCatatan').style.display = 'none';
        });


    // modal isi laporan
    document.querySelectorAll('.isi-lap').forEach(btn => {
        btn.addEventListener('click', () => {

            document.getElementById('lapNama').textContent = btn.dataset.nama;
            document.getElementById('lapKelas').textContent = btn.dataset.kelas;
            document.getElementById('lapTanggal').textContent = btn.dataset.tanggal;
            document.getElementById('lapKategori').textContent = btn.dataset.kategori;
            document.getElementById('lapPermasalahan').textContent = btn.dataset.permasalahan;

            let textarea = document.getElementById('lapCatatan');
            let pesanEl = document.getElementById('pesanSiswa');

            let previewImage = document.getElementById('previewImage');
            let previewPDF = document.getElementById('previewPDF');
            let fileInput = document.getElementById('buktiFile');

            let btnSimpan = document.getElementById('submitIsiLap');
            let btnBatal = document.getElementById('closeIsiLap');
            let btnTutup = document.getElementById('btnTutupDetailLap');

            // RESET
            previewImage.style.display = "none";
            previewPDF.style.display = "none";
            fileInput.value = "";

            // ISI DATA
            textarea.innerHTML = btn.dataset.catatan ? decodeURIComponent(btn.dataset.catatan) : "";
            pesanEl.innerHTML = btn.dataset.pesan ? decodeURIComponent(btn.dataset.pesan) : "";

            let sudahAdaLaporan = btn.dataset.catatan && btn.dataset.catatan.trim() !== "";

            // 🔥 MODE DETAIL (SUDAH DISIMPAN)
            if (sudahAdaLaporan) {

                textarea.contentEditable = "false";
                pesanEl.contentEditable = "false";

                fileInput.style.display = "none"; // ❌ tidak bisa upload lagi

                btnSimpan.style.display = "none";
                btnBatal.style.display = "none";
                btnTutup.style.display = "inline-block";

                // ✅ tampilkan preview file lama
                if (btn.dataset.foto) {
                    let url = "/storage/" + btn.dataset.foto;

                    if (btn.dataset.foto.endsWith('.pdf')) {
                        previewPDF.src = url;
                        previewPDF.style.display = "block";
                    } else {
                        previewImage.src = url;
                        previewImage.style.display = "block";
                    }
                }

            }
            // 🔥 MODE INPUT (BELUM ADA LAPORAN)
            else {

                textarea.contentEditable = "true";
                pesanEl.contentEditable = "true";

                fileInput.style.display = "block";

                btnSimpan.style.display = "inline-block";
                btnBatal.style.display = "inline-block";
                btnTutup.style.display = "none";
            }

            btnSimpan.setAttribute("data-current-id", btn.dataset.id);

            document.getElementById('modalIsiLap').style.display = 'flex';
        });
    });

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

    // tutup
    document.getElementById('btnTutupDetailLap').addEventListener('click', () => {
        document.getElementById('modalIsiLap').style.display = 'none';
    });

    document.getElementById('closeIsiLap').addEventListener('click', () => {
        document.getElementById('modalIsiLap').style.display = 'none';
    });


    // submit
    document.getElementById('submitIsiLap').addEventListener('click', function() {
        let id = this.getAttribute("data-current-id");
        let catatan = document.getElementById('lapCatatan').innerHTML.trim();
        let pesan = document.getElementById('pesanSiswa').innerHTML;
        let fileInput = document.getElementById('buktiFile');

        if (!catatan) {
            alert("Catatan wajib diisi");
            return;
        }

        let formData = new FormData();
        formData.append('hasil_catatan', catatan);
        formData.append('pesan_siswa', pesan);

        // Tambahkan file hanya kalau ada upload baru
        if (fileInput.files[0]) {
            formData.append('bukti_file', fileInput.files[0]);
        }

        formData.append('_token', '{{ csrf_token() }}');

        fetch(`/konselor/laporan/${id}/simpan`, {
                method: 'POST',
                body: formData
            })
            .then(async res => {
                let text = await res.text();
                console.log("RESPONSE:", text); // 🔥 DEBUG

                try {
                    let data = JSON.parse(text);

                    if (!res.ok) throw data;

                    alert(data.message);
                    location.reload();

                } catch (e) {
                    console.error("Bukan JSON:", text);
                    alert("Server error, cek console!");
                }
            });
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