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

                        <select name="filterKonselor" id="filterKonselor">
                            <option value="" selected disabled>Pilih Konselor</option>
                            @foreach($konselor as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>

                        <button id="btnFilter">Terapkan</button> <button id="reset">Reset</button>
                    </div>
                    <div class="right-sec">

                        <button class="btn-tambah-konselor" id="openModal">
                            + Tambah Konselor
                        </button>
                        <input class="search" id="searchInput" type="text" placeholder="Cari...">
                    </div>
                </div>
                <div class="selected-filter" id="selectedFilter"></div>
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
                <tbody id="tableBody">
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
            if (type === 'konselor') konselor.value = '';
        };

        container.appendChild(chip);
    }

    // EVENT
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