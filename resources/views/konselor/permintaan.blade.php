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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
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
</script>