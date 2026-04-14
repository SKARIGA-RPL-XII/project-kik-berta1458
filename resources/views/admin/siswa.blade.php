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

                        <select id="filterJurusan">
                            <option value="">Pilih Jurusan</option>
                            @foreach($jurusanList as $j)
                            <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>

                        <button id="btnFilter">Terapkan</button>
                        <button id="reset">Reset</button>
                    </div>
                    <div class="right-sec">
                        <button class="btn-tambah-konselor" id="openModal">
                            + Tambah Siswa
                        </button>
                        <input class="search" id="searchInput" type="text" placeholder="Cari konselor...">
                    </div>
                </div>
                <div class="selected-filter" id="selectedFilter"></div>
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
                <tbody id="tableBody">
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
    // APPLY FILTER
    document.getElementById('btnFilter').onclick = function() {

        let url = new URL(window.location.href);

        const kelas = document.getElementById('filterKelas')?.value;
        const jurusan = document.getElementById('filterJurusan')?.value;
        const tanggal = document.getElementById('filterTanggal')?.value;
        const kategori = document.getElementById('filterKategori')?.value;
        const konselor = document.getElementById('filterKonselor')?.value;
        const search = document.getElementById('searchInput')?.value;

        if (kelas) url.searchParams.set('kelas', kelas);
        if (jurusan) url.searchParams.set('jurusan', jurusan);
        if (tanggal) url.searchParams.set('tanggal', tanggal);
        if (kategori) url.searchParams.set('kategori', kategori);
        if (konselor) url.searchParams.set('konselor', konselor);
        if (search) url.searchParams.set('search', search);

        window.location.href = url.toString();
    };

    // CHIP FILTER
    const kelas = document.getElementById('filterKelas');
    const jurusan = document.getElementById('filterJurusan');
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

            if (type === 'kelas') kelas.value = '';
            if (type === 'jurusan') jurusan.value = '';
        };

        container.appendChild(chip);
    }

    // EVENT
    kelas.addEventListener('change', () => {
        if (!kelas.value) return;
        createChip(kelas.value, 'kelas');
    });

    jurusan.addEventListener('change', () => {
        if (!jurusan.value) return;
        createChip(jurusan.value, 'jurusan');
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