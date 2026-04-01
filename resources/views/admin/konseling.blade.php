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
                    <button class="btn-tambah-admin" id="openModalKonseling">
                        + Tambah Konseling
                    </button>
                    <input class="search" type="text" placeholder="Cari...">
                </div>
            </div>
        </div>

        <div class="admin-table">
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
                <tbody>
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
                                data-hasil="{{ $lap->laporan->hasil_catatan ?? '' }}"
                                data-foto="{{ $lap->laporan->foto ?? '' }}"
                                data-pesan="{{ $lap->laporan->pesan_siswa ?? '' }}">
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
            <p>kembali</p><span class="number">1</span>
            <p>Berikutnya</p>
        </div>
    </div>
</section>

<!-- modal laporan -->
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
                    <th>Kategori</th>
                    <td id="lapKategori"></td>
                </tr>
                <tr>
                    <th>Permasalahan</th>
                    <td id="lapPermasalahan"></td>
                </tr>

                <!-- KAMERA -->
                <tr>
                    <th>Bukti Konseling</th>
                    <td>
                        <div class="kamera-wrap">

                            <video id="cameraPreview" autoplay></video>

                            <canvas id="cameraCanvas" style="display:none;"></canvas>
                            <img id="hasilFoto" style="display:none; width:100%; margin-top:10px;" />

                            <div class="kamera-btn">
                                <button type="button" id="btnStartCamera">Buka Kamera</button>
                                <button type="button" id="btnAmbilFoto">Ambil Foto</button>
                            </div>

                        </div>
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
                            <button type="button" onclick="formatTextPesan('bold')"><b>B</b></button>
                            <button type="button" onclick="formatTextPesan('italic')"><i>I</i></button>
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


@include('layout.footer')


<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('btnAmbilFoto').onclick = () => {
            let video = document.getElementById('cameraPreview');
            let canvas = document.getElementById('cameraCanvas');
            let img = document.getElementById('hasilFoto');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            let ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);

            let dataUrl = canvas.toDataURL('image/png');

            img.src = dataUrl;
            img.style.display = 'block';

            foto = dataUrl;
        };
        // =========================
        // MODAL TAMBAH KONSELING
        // =========================
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


        // =========================
        // MODAL DESKRIPSI
        // =========================
        document.querySelectorAll('.detail').forEach(btn => {
            btn.onclick = () => {
                document.getElementById('modalDeskripsi').textContent = btn.dataset.deskripsi;
                document.getElementById('modalDetail').style.display = 'flex';
            };
        });

        document.getElementById('closeModalDetail').onclick = () => {
            document.getElementById('modalDetail').style.display = 'none';
        };


        // =========================
        // MODAL LAPORAN
        // =========================
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
                document.getElementById('lapKategori').textContent = btn.dataset.kategori;
                document.getElementById('lapPermasalahan').textContent = btn.dataset.permasalahan;
                document.getElementById('lapTanggal').textContent = btn.dataset.tanggal;

                // reset kamera
                foto = null;

                // mode
                let hasil = btn.dataset.hasil;
                let fotoLama = btn.dataset.foto;
                let pesanLama = btn.dataset.pesan;

                // =====================
                // MODE VIEW / CREATE
                // =====================
                if (hasil && hasil.trim() !== '') {
                    mode = 'view';

                    catatan.innerHTML = hasil;
                    catatan.contentEditable = false;

                    pesan.innerHTML = pesanLama || '';
                    pesan.contentEditable = false;

                    btnSubmit.textContent = 'Edit';

                } else {
                    mode = 'create';

                    catatan.innerHTML = '';
                    catatan.contentEditable = true;

                    pesan.innerHTML = '';
                    pesan.contentEditable = true;

                    btnSubmit.textContent = 'Simpan';
                }

                // =====================
                // FOTO LAMA
                // =====================
                let img = document.getElementById('hasilFoto');
                let video = document.getElementById('cameraPreview');
                let btnCam = document.getElementById('btnStartCamera');
                let btnSnap = document.getElementById('btnAmbilFoto');

                if (fotoLama) {
                    img.src = '/storage/' + fotoLama; // sesuaikan path
                    img.style.display = 'block';
                } else {
                    img.style.display = 'none';
                }

                // default: kamera disembunyikan
                video.style.display = 'none';
                btnCam.style.display = 'none';
                btnSnap.style.display = 'none';

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

            // dari VIEW → EDIT
            if (mode === 'view') {
                mode = 'edit';

                catatan.contentEditable = true;
                pesan.contentEditable = true;

                this.textContent = 'Update';

                // munculin kamera
                document.getElementById('cameraPreview').style.display = 'block';
                document.getElementById('btnStartCamera').style.display = 'inline-block';
                document.getElementById('btnAmbilFoto').style.display = 'inline-block';

                catatan.focus();
                return;
            }
            // SIMPAN / UPDATE
            let formData = new FormData();
            formData.append('id_pengajuan', document.getElementById('laporanId').value);
            formData.append('hasil_catatan', catatan.innerHTML);
            formData.append('pesan_siswa', pesan.innerHTML);

            if (foto) {
                formData.append('foto', foto);
            }

            fetch('/admin/laporan/simpan', {
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
                .catch(() => alert('Gagal simpan'));
        };


        // =========================
        // KAMERA
        // =========================
        document.getElementById('btnStartCamera').onclick = async () => {
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });
                document.getElementById('cameraPreview').srcObject = stream;
            } catch {
                alert('Kamera tidak bisa diakses');
            }
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
</script>