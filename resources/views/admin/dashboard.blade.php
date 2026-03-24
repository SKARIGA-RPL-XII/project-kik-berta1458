@include('layout.header')

<section class="admin-dashboard">
    <div class="container">
        <div class="row">

            <div class="col-md-4">
                <div class="admin-card">
                    <i class="fa-solid fa-user-tie"></i>
                    <h4>Total Konselor</h4>
                    <h1>{{ $totalKonselor }}</h1>
                </div>
            </div>

            <div class="col-md-4">
                <div class="admin-card">
                    <i class="fa-solid fa-users"></i>
                    <h4>Total Siswa</h4>
                    <h1>{{ $totalSiswa }}</h1>
                </div>
            </div>

            <div class="col-md-4">
                <div class="admin-card">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <h4>Total Pengajuan</h4>
                    <h1>{{ $totalPengajuan }}</h1>
                </div>
            </div>

        </div>
    </div>
</section>

@include('layout.footer')
