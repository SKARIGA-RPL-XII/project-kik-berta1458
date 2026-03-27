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
                                data-permasalahan="{{ $lap->deskripsi_masalah }}">
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


<!-- modal edit konseling -->
<div class="modal-overlay" id="modalEditKonseling" style="display:none;">
    <div class="modal-box modal-admin">

        <div class="title-body-tambah">
            <h3>Edit Konseling</h3>
        </div>

        <div class="form-tambah">

            <form id="formEditKonseling" method="POST">
                @csrf
                @method('PUT')

                <label>Tanggal Konseling</label><br>
                <input type="text" name="nama" id="editNama" required>
                <br><br>

                <label>NIP</label><br>
                <input type="text" name="nip" id="editNip" required>
                <br><br>

                <label>Password Baru (opsional)</label><br>
                <input type="password" name="password">
                <br><br>

                <div class="modal-actions">
                    <button type="button" id="closeEditModal" class="btn-batal">
                        Batal
                    </button>
                    <button type="submit" class="btn-kirim">
                        Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<div class="modal-overlay" id="modalKonselor">
    <div class="modal-box modal-admin">

        <div class="title-body-tambah">
            <h3>Tambah Konseling</h3>
        </div>

        <div class="form-tambah">

            <form action="{{ route('admin.konselor.store') }}" method="POST">
                @csrf

                <label>Nama Konselor</label><br>
                <input type="text" name="nama" required>
                <br><br>
                <label>NIP</label><br>
                <input type="text" name="nip" required>
                <br><br>
                <label>Password</label><br>
                <input type="password" name="password" required>

                <div class="modal-actions">
                    <button type="button" id="closeModal" class="btn-batal">
                        Batal
                    </button>
                    <button type="submit" class="btn-kirim">
                        Simpan
                    </button>
                </div>

            </form>

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
    // modal tambah konseling
    document.getElementById('openModalKonseling').addEventListener('click', () => {
        document.getElementById('modalTambahKonseling').style.display = 'flex';
    });

    document.getElementById('closeTambahKonseling').addEventListener('click', () => {
        document.getElementById('modalTambahKonseling').style.display = 'none';
    });

    // submit form
    document.getElementById('formTambahKonseling').addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        fetch('/admin/konseling/store', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(result => {
                alert(result.message);
                location.reload();
            })
            .catch(err => {
                console.error(err);
                alert("Gagal tambah konseling");
            });
    });

    // modal deskripsi
    document.addEventListener('DOMContentLoaded', function() {
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
    });
</script>