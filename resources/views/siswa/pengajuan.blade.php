@include('layout/header')

<section class="pengajuan">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-body">
                    <h3>Pengajuan Konseling</h3>
                    <p>Ajukan permohonan konseling sesuai permasalahan yang sedang kamu hadapi. Data yang kamu kirim bersifat rahasia.</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 ">
                <div class="filter-wrap">
                    <div class="filter">
                        <input type="date" id="filterTanggal" value="{{ request('tanggal') }}">
                        <select id="filterKategori">
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
                    <div class="filter-right">
                        <input class="search" type="text" placeholder="Cari...">
                        <button class="btn-tambah-ajuan">+ Ajukan Konseling</button>
                    </div>
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
                                <th>Kategori</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuan as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y') }}</td>
                                <td>{{ $item->kategori->nama_kategori }}</td>
                                <td> <span class="{{ $item->status }}">
                                        {{ ucfirst($item->status) }}
                                    </span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" style="text-align:center;">Belum ada pengajuan</td>
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
        <div class="overlay" id="popupPengajuan">
            <div class="popup-content">
                <div class="row form-pengajuan">
                    <div class="col-md-6">
                        <form id="formPengajuan">

                            @csrf

                            <label>Kategori Masalah</label>
                            <select name="id_kategori" required>
                                <option value="">Pilih kategori</option>
                                @foreach($kategori as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                                @endforeach
                            </select>

                            <br><br>
                            <input type="hidden" name="tanggal_pengajuan" id="tanggal_hidden">

                            <label>Deskripsi Masalah</label>
                            <textarea name="deskripsi_masalah" placeholder="Ceritakan masalahmu..."></textarea>

                        </form>
                    </div>
                    <div class="col-md-6">
                        <label>Pilih Konselor</label><br>
                        <select name="id_konselor" required>
                            <option value="">-- Pilih Konselor --</option>
                            @foreach($konselor as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                        <br><br>
                        <label for="">Tanggal Konseling</label>
                        <input type="text" id="tanggal_konsultasi" class="form-control kal-jadwal">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 btn-ajuan text-center">
                        <button id="btnBatal">Batal</button>
                        <button type="submit" class="submit-btn">Kirim</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('layout/footer')

<script>
    flatpickr("#tanggal_konsultasi", {
        inline: true,
        dateFormat: "Y-m-d",

        onChange: function(selectedDates, dateStr) {
            document.getElementById('tanggal_hidden').value = dateStr;
        }
    });

    document.getElementById('btnFilter').onclick = function() {
        const tanggal = document.getElementById('filterTanggal').value;
        const kategori = document.getElementById('filterKategori').value;

        let url = new URL(window.location.href);

        if (tanggal) url.searchParams.set('tanggal', tanggal);
        if (kategori) url.searchParams.set('kategori', kategori);

        window.location.href = url.toString();
    };
    const btnTambah = document.querySelector('.btn-tambah-ajuan');
    const popup = document.getElementById('popupPengajuan');
    const btnBatal = document.getElementById('btnBatal');

    btnTambah.addEventListener('click', () => {
        popup.classList.add('show');
    });

    btnBatal.addEventListener('click', () => {
        popup.classList.remove('show');
    });

    document.querySelector('.submit-btn').addEventListener('click', function() {
        document.getElementById('formPengajuan').requestSubmit();
    });
    const tanggal = document.getElementById('filterTanggal');
    const kategori = document.getElementById('filterKategori');
    const container = document.getElementById('selectedFilter');
    const resetBtn = document.getElementById('reset');

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

    tanggal.addEventListener('change', () => {
        const date = new Date(tanggal.value);
        const formatted = date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
        createChip(formatted, 'tanggal');
    });

    kategori.addEventListener('change', () => {
        createChip(kategori.value, 'kategori');
    });

    resetBtn.addEventListener('click', () => {
        window.location.href = window.location.pathname;
    });

    document.getElementById('formPengajuan').onsubmit = function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        const tanggal = document.getElementById('tanggal_hidden').value;
        const idKonselor = document.querySelector('[name="id_konselor"]').value;

        if (!tanggal) {
            alert('Pilih tanggal dulu ya!');
            return;
        }

        if (!idKonselor) {
            alert('Pilih konselor dulu!');
            return;
        }

        // ⬇️ ini yang penting (ambil dari luar form)
        formData.append('id_konselor', idKonselor);

        fetch('/siswa/pengajuan/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                alert(res.message);
                location.reload();
            })
            .catch(() => {
                alert('Gagal mengajukan');
            });
    };
</script>