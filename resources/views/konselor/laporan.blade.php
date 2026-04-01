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
                        <select name="date" id="">
                            <option value="" selected disabled>Pilih Tanggal</option>
                        </select>
                        <select name="kategori" id="">
                            <option value="" selected disabled>Pilih Kategori</option>
                            <option value="">Akademik</option>
                            <option value="">Peribadi</option>
                            <option value="">Sosial</option>
                            <option value="">Karir</option>
                        </select>
                        <button>Terapkan</button>
                        <button>Reset</button>
                    </div>
                    <!-- <div class="search"> -->
                    <input class="search" type="text" placeholder="Cari...">
                    <!-- </div> -->
                </div>
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
                                        data-foto="{{ $lap->laporan->bukti_foto ?? '' }}"
                                        data-catatan="{{ urlencode($lap->laporan->hasil_catatan ?? '') }}"
                                        data-pesan="{{ urlencode($lap->laporan->pesan_siswa ?? '') }}">
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
            let btnSimpan = document.getElementById('submitIsiLap');
            let btnBatal = document.getElementById('closeIsiLap');
            let btnTutup = document.getElementById('btnTutupDetailLap');
            let title = document.querySelector('.modal-header-laporan h2');

            let img = document.getElementById('hasilFoto');
            let video = document.getElementById('cameraPreview');
            let btnStart = document.getElementById('btnStartCamera');
            let btnAmbil = document.getElementById('btnAmbilFoto');

            let pesanEl = document.getElementById('pesanSiswa');

            if (btn.dataset.pesan && btn.dataset.pesan.trim() !== "") {
                pesanEl.innerHTML = decodeURIComponent(btn.dataset.pesan);
                pesanEl.contentEditable = "false";
            } else {
                pesanEl.innerHTML = "";
                pesanEl.contentEditable = "true";
            }

            // reset
            img.style.display = "none";
            video.style.display = "block";
            btnStart.style.display = "inline-block";
            btnAmbil.style.display = "inline-block";

            textarea.contentEditable = "true";
            btnSimpan.style.display = "inline-block";
            btnBatal.style.display = "inline-block";
            btnTutup.style.display = "none";

            // detail
            if (btn.dataset.catatan && btn.dataset.catatan.trim() !== "") {

                textarea.innerHTML = decodeURIComponent(btn.dataset.catatan);
                title.textContent = "Detail Laporan";

                textarea.contentEditable = "false";

                btnSimpan.style.display = "none";
                btnBatal.style.display = "none";
                btnTutup.style.display = "block";

                if (btn.dataset.foto) {
                    img.src = btn.dataset.foto;
                    img.style.display = "block";

                    video.style.display = "none";
                    btnStart.style.display = "none";
                    btnAmbil.style.display = "none";
                }

            } else {
                textarea.innerHTML = "";
                title.textContent = "Laporan Bimbingan Konseling";
            }

            btnSimpan.setAttribute("data-current-id", btn.dataset.id);

            document.getElementById('modalIsiLap').style.display = 'flex';
        });
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
        let foto = document.getElementById('hasilFoto').src || null;

        let pesan = document.getElementById('pesanSiswa') ?
            document.getElementById('pesanSiswa').innerHTML :
            '';

        if (!catatan) {
            alert("Catatan wajib diisi");
            return;
        }

        if (!foto || !foto.startsWith("data:image")) {
            alert("Foto wajib diambil");
            return;
        }

        let formData = new FormData();
        formData.append('hasil_catatan', catatan);
        formData.append('bukti_foto', foto);
        formData.append('pesan_siswa', pesan);

        formData.append('_token', '{{ csrf_token() }}');

        fetch(`/konselor/laporan/${id}/simpan`, {
                method: 'POST',
                body: formData
            })
            .then(async res => {
                let data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(result => {
                alert(result.message);
                location.reload();
            })
            .catch(err => {
                console.error("ERROR:", err);
                alert(err.message || "Terjadi error!");
            });
    });


    // kamera
    let stream = null;

    document.getElementById('btnStartCamera').addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: true
            });
            document.getElementById('cameraPreview').srcObject = stream;
        } catch (err) {
            alert("Tidak bisa akses kamera");
        }
    });

    document.getElementById('btnAmbilFoto').addEventListener('click', () => {
        let video = document.getElementById('cameraPreview');
        let canvas = document.getElementById('cameraCanvas');
        let img = document.getElementById('hasilFoto');
        let btnStart = document.getElementById('btnStartCamera');
        let btnAmbil = document.getElementById('btnAmbilFoto');

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        let ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);

        let dataURL = canvas.toDataURL("image/png");

        img.src = dataURL;
        img.style.display = "block";

        video.style.display = "none";
        btnStart.style.display = "none";
        btnAmbil.style.display = "none";

        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    });
</script>