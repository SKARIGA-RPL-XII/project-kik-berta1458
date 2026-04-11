@include('layout.header')

<section class="admin-dashboard">
    <div class="container">

        <div class="title-body">
            <h3>Kelola Siswa</h3>
            <p>Tambah dan kelola data siswa</p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="search-tambah">
                    <div class="filter">

                        <select id="filterKelas">
                            <option value="" selected disabled>Pilih Kelas</option>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select>

                        <select name="filterjurusan" id="filterJurusan">
                            <option value="" selected disabled>Pilih Jurusan</option>
                            <option value="IPA">IPA</option>
                            <option value="IPS">IPS</option>
                        </select>

                        <button>Terapkan</button>
                        <button id="reset">Reset</button>
                    </div>
                    <div class="right-sec">
                        <button class="btn-tambah-konselor" id="openModal">
                            + Tambah Siswa
                        </button>
                        <input class="search" type="text" placeholder="Cari konselor...">
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-table">
            <table>
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>NIS</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswa as $s)
                    <tr>
                        <td>{{ $s->nama }}</td>
                        <td>{{ $s->kelas }}</td>
                        <td>{{ $s->jurusan }}</td>
                        <td>{{ $s->nis }}</td>
                        <td>
                            <button
                                class="aksi-admin edit"
                                data-id="{{ $s->id }}"
                                data-nama="{{ $s->nama }}"
                                data-nis="{{ $s->nis }}"
                                data-kelas="{{ $s->kelas }}"
                                data-jurusan="{{ $s->jurusan }}">
                                <i class="fa-solid fa-pen"></i>
                            </button>

                            <form action="{{ route('admin.siswa.delete', $s->id) }}"
                                method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="aksi-admin delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center;">
                            Belum ada data siswa
                        </td>
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

{{-- MODAL EDIT SISWA --}}
<div class="modal-overlay" id="modalEditSiswa" style="display:none;">
    <div class="modal-box modal-admin">

        <div class="title-body-tambah">
            <h3>Edit Siswa</h3>
        </div>

        <div class="form-tambah">

            <form id="formEditSiswa" method="POST">
                @csrf
                @method('PUT')

                <label>Nama</label><br>
                <input type="text" name="nama" id="editNamaSiswa" required>
                <br><br>

                <label>NIS</label><br>
                <input type="text" name="nis" id="editNisSiswa" required>
                <br><br>

                <label>Kelas</label><br>
                <input type="text" name="kelas" id="editKelasSiswa" required>
                <br><br>

                <label>Jurusan</label><br>
                <input type="text" name="jurusan" id="editJurusanSiswa" required>
                <br><br>

                <label>Password Baru (Opsional)</label><br>
                <input type="password" name="password">
                <br><br>

                <div class="modal-actions">
                    <button type="button" id="closeEditSiswa" class="btn-batal">
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
            <h3>Tambah Siswa</h3>
        </div>

        <div class="form-tambah">

            <form action="{{ route('admin.siswa.store') }}" method="POST">
                @csrf

                <label>Nama Siswa</label><br>
                <input type="text" name="nama" required>
                <br><br>
                <label>NIS</label><br>
                <input type="text" name="nis" required>
                <br><br>
                <label>Kelas</label><br>
                <input type="text" name="kelas" required>
                <br><br>
                <label>Jurusan</label><br>
                <input type="text" name="jurusan" required>
                <br><br>
                <label>Password</label><br>
                <input type="password" name="password" required>
                <br>
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


<script>
    const modal = document.getElementById('modalKonselor');
    const openBtn = document.getElementById('openModal');
    const closeBtn = document.getElementById('closeModal');

    openBtn.addEventListener('click', () => {
        modal.style.display = 'flex';
    });

    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    const modalEditSiswa = document.getElementById('modalEditSiswa');
    const closeEditSiswa = document.getElementById('closeEditSiswa');

    document.querySelectorAll('.edit').forEach(btn => {
        btn.addEventListener('click', function() {

            let id = this.dataset.id;
            let nama = this.dataset.nama;
            let nis = this.dataset.nis;
            let kelas = this.dataset.kelas;
            let jurusan = this.dataset.jurusan;

            document.getElementById('editNamaSiswa').value = nama;
            document.getElementById('editNisSiswa').value = nis;
            document.getElementById('editKelasSiswa').value = kelas;
            document.getElementById('editJurusanSiswa').value = jurusan;

            document.getElementById('formEditSiswa').action = `/admin/siswa/${id}`;

            modalEditSiswa.style.display = 'flex';
        });
    });

    closeEditSiswa.addEventListener('click', function() {
        modalEditSiswa.style.display = 'none';
    });
</script>