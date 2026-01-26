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
                <div class="col-md-6 text-center menu-tengah">
                    <a href="/pengajuan-konseling" class="menu-link">Pengajuan</a>

                    <div class="avatar-wrapper">
                        <a href="/profil-siswa" class="avatar-circle">
                            <img src="{{ asset('image/user.png') }}" alt="">
                        </a>
                    </div>

                    <a href="/riwayat-konseling" class="menu-link">Riwayat</a>
                </div>
                <div class="col-md-3 d-flex align-items-center justify-content-end">
                    <a href="/siswa-dashboard" class="home-btn">
                        <i class="fa-solid fa-house"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
</body>

</html>