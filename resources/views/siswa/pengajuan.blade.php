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
                        <input type="date" id="filterTanggal" class="date-picker" required>
                        <select id="filterKategori">
                            <option value="" selected disabled>Pilih Kategori</option>
                            <option value="Akademik">Akademik</option>
                            <option value="Pribadi">Pribadi</option>
                            <option value="Sosial">Sosial</option>
                            <option value="Karir">Karir</option>
                        </select>
                        <button>Terapkan</button>
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
                        <label for="">Tanggal Konseling</label>
                        <input type="text" id="tanggal_konsultasi" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 btn-ajuan text-center">
                        <button id="btnBatal">Batal</button>
                        <button class="submit-btn">Kirim</button>
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
        container.innerHTML = '';
        tanggal.value = '';
        kategori.value = '';
    });

    document.getElementById('formPengajuan').onsubmit = function(e) {
        e.preventDefault();

        const tanggal = document.getElementById('tanggal_hidden').value;

        if (!tanggal) {
            alert('Pilih tanggal dulu ya!');
            return;
        }

        let formData = new FormData(this);

        fetch('/siswa/pengajuan/store', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(async res => {
                let data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(res => {
                alert(res.message);
                location.reload();
            })
            .catch(err => {
                alert(err.message || 'Gagal mengajukan');
            });
    };
</script>