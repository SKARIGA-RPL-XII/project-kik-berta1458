@include('layout.header')

<section class="admin-konseling">
    <div class="container">

        <div class="title-body">
            <h3>Kelola Konselor</h3>
            <p>Tambah dan kelola akun konselor</p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="search-tambah">
                    <button class="btn-tambah-konselor" id="openModal">
                        + Tambah Konselor
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


<!-- MODAL EDIT KONSELOR -->
<div class="modal-overlay" id="modalEditKonselor" style="display:none;">
    <div class="modal-box modal-admin">

        <div class="title-body-tambah">
            <h3>Edit Konselor</h3>
        </div>

        <div class="form-tambah">

            <form id="formEditKonselor" method="POST">
                @csrf
                @method('PUT')

                <label>Nama Konselor</label><br>
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


@include('layout.footer')