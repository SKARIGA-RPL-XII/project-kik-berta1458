<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <title>eCounsel</title>
</head>

<body>
    <section class="main-header">
        <div class="container">
            <div class="row">
                <div class="col-md-3 d-flex align-items-center">
                    <img src="/image/logo-putih.png" alt="eCounsel" class="logo">
                </div>
                <div class="col-md-9">
                    <div class="nav-menu">
                        <ul class="menu">
                            @if(session('role') === 'siswa')
                                <li><a href="/dashboard-siswa"
                                        class="{{ request()->is('dashboard-siswa') ? 'active' : '' }}">Dashboard</a></li>
                                <li><a href="/pengajuan-konseling"
                                        class="{{ request()->is('pengajuan-konseling') ? 'active' : '' }}">Pengajuan</a>
                                </li>
                                <li><a href="/riwayat-konseling"
                                        class="{{ request()->is('riwayat-konseling') ? 'active' : '' }}">Riwayat</a></li>
                                <li><a class="profil" href="/profil-siswa"><i class="fa-solid fa-user"></i></a></li>
                            @endif

                            @if(session('role') === 'konselor')
                                <li>
                                    <a href="/dashboard-konselor"
                                        class="{{ request()->is('dashboard-konselor') ? 'active' : '' }}">
                                        Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="/permintaan-konseling"
                                        class="{{ request()->is('permintaan-konseling') ? 'active' : '' }}">
                                        Permintaan
                                    </a>
                                </li>
                                <li>
                                    <a href="/jadwal-konseling"
                                        class="{{ request()->is('jadwal-konseling') ? 'active' : '' }}">
                                        Jadwal
                                    </a>
                                </li>
                                <li>
                                    <a href="/laporan-konseling"
                                        class="{{ request()->is('laporan-konseling') ? 'active' : '' }}">
                                        Laporan
                                    </a>
                                </li>
                                <li><button class="logout" id="btnLogout" type="button"><i
                                            class="fa-solid fa-arrow-right-from-bracket"></i></a></li>
                            @endif

                            @if(session('role') === 'admin')
                                <li><a href="/dashboard-admin"
                                        class="{{ request()->is('dashboard-admin') ? 'active' : '' }}">Dashboard</a></li>
                                <li><a href="/siswa" class="{{ request()->is('siswa') ? 'active' : '' }}">Kelola Siswa</a>
                                </li>
                                <li><a href="/konselor" class="{{ request()->is('konselor') ? 'active' : '' }}">Kelola
                                        Konselor</a></li>
                                <li><a href="/konseling" class="{{ request()->is('konseling') ? 'active' : '' }}">Kelola
                                        Konseling</a></li>
                                <li><button class="logout" id="btnLogout" type="button"><i
                                            class="fa-solid fa-arrow-right-from-bracket"></i></a></li>
                            @endif
                        </ul>
                    </div>

                    <div id="modalLogout" class="modal-overlay show" style="display:none;">
                        <div class="modal-box modal-logout">

                            <div class="logout-icon">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            </div>

                            <h2>Konfirmasi Keluar</h2>
                            <p>Apakah Anda yakin ingin keluar dari sistem?</p>

                            <div class="logout-actions">
                                <button id="batalLogout" class="btn-batal-logout">Batal</button>

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-keluar">Keluar</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>

<script>
    document.getElementById('btnLogout').addEventListener('click', function () {
        document.getElementById('modalLogout').style.display = 'flex';
    });
    document.getElementById('batalLogout').addEventListener('click', function () {
        document.getElementById('modalLogout').style.display = 'none';
    });
</script>