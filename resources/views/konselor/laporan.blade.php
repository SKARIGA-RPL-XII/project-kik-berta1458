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
                                <td>@if ($lap->status == 'menunggu')<span class="menunggu">Menunggu</span>
                                    @elseif ($lap->status == 'diterima')<span class="diterima">Diterima</span>
                                    @elseif ($lap->status == 'ditolak')<span class="ditolak">Ditolak</span>
                                    @else <span class="selesai">Selesai</span>
                                    @endif</td>
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

                                    @if(in_array($lap->status, ['dijadwalkan', 'berlangsung']))
                                    <button class="isi-lap"
                                        data-id="{{ $lap->id }}"
                                        data-nama="{{ $lap->siswa->nama }}"
                                        data-kelas="{{ $lap->siswa->kelas }}"
                                        data-kategori="{{ $lap->kategori->nama_kategori }}"
                                        data-tanggal="{{ $lap->tanggal_pengajuan }}"
                                        data-permasalahan="{{ $lap->deskripsi_masalah }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    @endif


                                    @if($lap->status === 'selesai')
                                    <button class="isi-lap-done"
                                        data-id="{{ $lap->id }}">
                                        <i class="fa-solid fa-chart-bar"></i>
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
                    <th>Hasil & Catatan</th>
                    <td>
                        <textarea id="lapCatatan" class="textarea-laporan"
                            placeholder="Tuliskan hasil dan catatan..."></textarea>
                    </td>
                </tr>
            </table>

        </div>

        <div class="modal-actions-laporan">
            <button id="closeIsiLap" class="btn-batal-lap">Batal</button>
            <button id="submitIsiLap" class="btn-simpan-lap">Simpan</button>
        </div>

    </div>
</div>


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

    document.querySelectorAll('.isi-lap').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('lapNama').textContent = btn.dataset.nama;
            document.getElementById('lapKelas').textContent = btn.dataset.kelas;
            document.getElementById('lapTanggal').textContent = btn.dataset.tanggal;
            document.getElementById('lapKategori').textContent = btn.dataset.kategori;
            document.getElementById('lapPermasalahan').textContent = btn.dataset.permasalahan;

            document.getElementById('modalIsiLap').style.display = 'flex';
        });
    });

    document.getElementById('closeIsiLap').addEventListener('click', () => {
        document.getElementById('modalIsiLap').style.display = 'none';
    });

    document.getElementById('submitIsiLap').addEventListener('click', function() {
        let id = document.querySelector('.isi-lap').dataset.id;
        let catatan = document.getElementById('lapCatatan').value.trim();

        if (!catatan) {
            alert("Catatan wajib diisi");
            return;
        }

        fetch(`/konselor/laporan/${id}/simpan`, {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    hasil_catatan: catatan
                })
            })
            .then(res => res.json())
            .then(result => {
                alert(result.message);
                location.reload();
            });
    });
</script>