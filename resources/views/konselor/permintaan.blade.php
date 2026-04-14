@include('layout.header')

<section class="permintaan">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-body">
                    <h3>Permintaan Konseling</h3>
                    <p>Kelola dan tinjau permintaan konseling yang diajukan oleh siswa sebagai dasar pemberian persetujuan</p>
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @if($data->isEmpty())
                            <tr>
                                <td colspan="4" class="kosong">Tidak ada permintaan konseling</td>
                            </tr>
                            @else
                            @foreach ($data as $permintaan)
                            <tr>
                                <td> {{ \Carbon\Carbon::parse($permintaan->tanggal_pengajuan)->format('d M Y') }}</td>
                                <td>{{ $permintaan->siswa->nama }}</td>
                                <td>{{ $permintaan->kategori->nama_kategori }}</td>
                                <td><button class="detail" data-deskripsi="{{ $permintaan->deskripsi_masalah}}"><i class="fa-solid fa-folder"></i></button>
                                    <button class="tolak" data-id="{{ $permintaan->id }}"><i class="fa-solid fa-circle-xmark"></i></button>
                                    <button class="terima" data-id="{{$permintaan->id}}" data-tanggal="{{$permintaan->tanggal_pengajuan}}"><i class="fa-solid fa-circle-check"></i></button>
                                </td>
                            </tr>
                            @endforeach
                            @endif
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

<div id="modalTolak" class="modal-overlay">
    <div class="modal-box">

        <div class="modal-header">
            <h2>Alasan Penolakan</h2>
        </div>

        <div class="modal-content">
            <textarea id="inputAlasan" class="modal-textarea" placeholder="Tuliskan alasan penolakan..."></textarea>
        </div>

        <div class="modal-actions">
            <button id="btnBatalTolak" class="btn-batal">Batal</button>
            <button id="btnKirimTolak" class="btn-kirim">Kirim</button>
        </div>

    </div>
</div>

<div id="modalBerhasilTerima" class="modal-overlay">
    <div class="modal-box modal-success">

        <div class="success-icon">
            <i class="fa-regular fa-circle-check"></i>
        </div>

        <h2 class="success-title">Berhasil</h2>

        <p id="successMessage" class="success-text"></p>

        <div class="modal-actions">
            <button id="btnCloseBerhasil" class="btn-kirim">Tutup</button>
        </div>

    </div>
</div>

@include('layout.footer')

<script>
    document.getElementById('btnFilter').onclick = function() {
        const tanggal = document.getElementById('filterTanggal').value;
        const kategori = document.getElementById('filterKategori').value;
        const search = document.querySelector('.search').value;

        let url = new URL(window.location.href);

        if (tanggal) url.searchParams.set('tanggal', tanggal);
        if (kategori) url.searchParams.set('kategori', kategori);
        if (search) url.searchParams.set('search', search);

        window.location.href = url.toString();
    };
    const tanggal = document.getElementById('filterTanggal');
    const kategori = document.getElementById('filterKategori');
    const container = document.getElementById('selectedFilter');

    function createChip(label, type) {
        container.querySelectorAll(`.chip[data-type="${type}"]`).forEach(el => el.remove());

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
        };

        container.appendChild(chip);
    }

    // EVENT
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

    kategori.addEventListener('change', () => {
        if (!kategori.value) return;

        createChip(kategori.value, 'kategori');
    });
    // RESET
    document.getElementById('reset').onclick = function() {
        window.location.href = window.location.pathname;
    };

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

    document.querySelectorAll('.detail').forEach(btn => {
        btn.addEventListener('click', function() {
            let deskripsi = this.getAttribute('data-deskripsi');
            document.getElementById('modalDeskripsi').textContent = deskripsi;
            document.getElementById('modalDetail').style.display = 'flex';
        });
    });

    document.getElementById('closeModalDetail').addEventListener('click', function() {
        document.getElementById('modalDetail').style.display = 'none';
    });

    document.querySelectorAll('.tolak').forEach(btn => {
        btn.addEventListener('click', function() {
            currentId = this.getAttribute('data-id');
            document.getElementById('modalTolak').style.display = 'flex';
        });
    });

    document.getElementById('btnBatalTolak').addEventListener('click', function() {
        document.getElementById('modalTolak').style.display = 'none';
        document.getElementById('inputAlasan').value = "";
    });

    document.getElementById('btnKirimTolak').addEventListener('click', function() {
        let alasan = document.getElementById('inputAlasan').value.trim();

        if (!alasan) {
            alert("Alasan penolakan wajib diisi.");
            return;
        }

        fetch(`/konselor/permintaan/${currentId}/tolak`, {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    alasan_penolakan: alasan
                })
            })
            .then(res => res.json())
            .then(result => {
                alert("Permintaan berhasil ditolak!");
                location.reload();
            });
    });

    document.querySelectorAll('.terima').forEach(btn => {
        btn.addEventListener('click', function() {
            let id = this.getAttribute('data-id');
            let tanggal = this.getAttribute('data-tanggal');

            fetch(`/konselor/permintaan/${id}/terima`, {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(res => res.json())
                .then(result => {

                    document.getElementById('modalBerhasilTerima').style.display = 'flex';

                    document.getElementById('successMessage').textContent =
                        `Pengajuan pada ${tanggal} berhasil diterima, laksanakan konseling pada tanggal tersebut.`;
                });
        });
    });

    document.getElementById('btnCloseBerhasil').addEventListener('click', function() {
        document.getElementById('modalBerhasilTerima').style.display = 'none';
        location.reload();
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