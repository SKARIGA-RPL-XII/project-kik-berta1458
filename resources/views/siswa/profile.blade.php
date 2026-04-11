@include('layout/header')

<section class="info-profile">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="card-profile">

                    <div class="profile text-center">
                        <img src="{{ asset('image/user.png') }}" alt="">
                        <h2>{{ $siswa->nama ?? '-' }}</h2>
                    </div>

                    <ul class="data-siswa">
                        <li>
                            <label>Nama</label>
                            <input type="text" readonly value="{{ $siswa->nama ?? '-'}}">
                        </li>

                        <li>
                            <label>NIS</label>
                            <input type="text" readonly value="{{ $siswa->nis ?? '-' }}">
                        </li>

                        <li>
                            <label>Jurusan</label>
                            <input type="text" readonly value="{{ $siswa->jurusan ?? '-'}}">
                        </li>

                        <li>
                            <label>Kelas</label>
                            <input type="text" readonly value="{{ $siswa->kelas ?? '-'}}">
                        </li>
                    </ul>
                    <button id="openLogout"><i class="fa-solid fa-arrow-right-from-bracket"></i>Keluar</button>
                </div>

            </div>
        </div>
    </div>
</section>

<div id="modalLogout" class="modal-overlay show" style="display:none;">
    <div class="modal-box modal-logout">
        <div class="logout-icon"> <i class="fa-solid fa-arrow-right-from-bracket"></i> </div>
        <h2>Konfirmasi Keluar</h2>
        <p>Apakah Anda yakin ingin keluar dari sistem?</p>
        <div class="logout-actions"> <button id="batalLogout" class="btn-batal-logout">Batal</button>
            <form action="{{ route('logout') }}" method="POST"> @csrf <button type="submit" class="btn-keluar">Keluar</button> </form>
        </div>
    </div>
</div>
@include('layout/footer')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const modal = document.getElementById('modalLogout');
        const openBtn = document.getElementById('openLogout');
        const closeBtn = document.getElementById('batalLogout');

        // buka modal
        openBtn.onclick = () => {
            modal.style.display = 'flex';
        };

        // tutup modal
        closeBtn.onclick = () => {
            modal.style.display = 'none';
        };

        // klik luar modal = tutup
        modal.onclick = (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        };

    });
</script>