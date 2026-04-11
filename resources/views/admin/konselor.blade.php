@include('layout.header')

<section class="admin-dashboard">
    <div class="container">

        <div class="title-body">
            <h3>Kelola Konselor</h3>
            <p>Tambah dan kelola akun konselor</p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="search-tambah">
                    <div class="filter">
                        <input type="date" id="filterTanggal" class="date-picker" required>

                        <select id="filterKategori">
                            <option value="" selected disabled>Pilih Kategori</option>
                            <option value="Akademik">Akademik</option>
                            <option value="Peribadi">Peribadi</option>
                            <option value="Sosial">Sosial</option>
                            <option value="Karir">Karir</option>
                        </select>

                        <select name="filterKonselor" id="filterKonselor">
                            <option value="" selected disabled>Pilih Konselor</option>
                            @foreach($konselor as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>

                        <button>Terapkan</button>
                        <button id="reset">Reset</button>
                    </div>
                    <div class="right-sec">

                        <button class="btn-tambah-konselor" id="openModal">
                            + Tambah Konselor
                        </button>
                        <input class="search" type="text" placeholder="Cari...">
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-table">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($konselor as $k)
                    <tr>
                        <td>{{ $k->nama }}</td>
                        <td>{{ $k->nip }}</td>
                        <td>
                            <button
                                class="aksi-admin edit"
                                data-id="{{ $k->id }}"
                                data-nama="{{ $k->nama }}"
                                data-nip="{{ $k->nip }}">
                                <i class="fa-solid fa-pen"></i>
                            </button>

                            <form action="{{ route('admin.konselor.delete', $k->id) }}"
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
                            Belum ada data konselor
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


{{-- MODAL EDIT KONSELOR --}}
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
            <h3>Tambah Konselor</h3>
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

    const modalEdit = document.getElementById('modalEditKonselor');
    const closeEdit = document.getElementById('closeEditModal');

    document.querySelectorAll('.aksi-admin.edit').forEach(btn => {
        btn.addEventListener('click', function() {

            let id = this.dataset.id;
            let nama = this.dataset.nama;
            let nip = this.dataset.nip;

            document.getElementById('editNama').value = nama;
            document.getElementById('editNip').value = nip;

            document.getElementById('formEditKonselor').action = `/admin/konselor/${id}`;

            modalEdit.style.display = 'flex';
        });
    });

    closeEdit.addEventListener('click', function() {
        modalEdit.style.display = 'none';
    });
</script>