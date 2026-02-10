<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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
                            @if(session('role')==='siswa')
                            <li><a href="/dashboard-siswa">Dashboard</a></li>
                            <li><a href="/pengajuan-konseling">Pengajuan</a></li>
                            <li><a href="/riwayat-konseling">Riwayat</a></li>
                            <li><a class="profil" href="/profil-siswa"><i class="fa-solid fa-user"></i></a></li>
                            @endif

                            @if(session('role')==='konselor')
                            <li><a href="/dashboard-konselor">Dashboard</a></li>
                            <li><a href="/permintaan-konseling">Permintaan</a></li>
                            <li><a href="/jadwal-konseling">Jadwal</a></li>
                            <li><a href="/laporan-konseling">Laporan</a></li>
                            <li><a class="logout" href="#"><i class="fa-solid fa-arrow-right-from-bracket"></i></a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>