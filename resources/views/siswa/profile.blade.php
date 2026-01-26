@include('layout/header')

<section class="info-profile">
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="card-profile">

                    <div class="profile text-center">
                        <img src="{{ asset('image/user.png') }}" alt="">
                        <h2>Berta Yuanita Putri Maryani</h2>
                    </div>

                    <ul class="data-siswa">
                        <li>
                            <label>Nama</label>
                            <input type="text" readonly value="Berta Yuanita Putri Maryani">
                        </li>

                        <li>
                            <label>NIS</label>
                            <input type="text" readonly value="0082507161">
                        </li>

                        <li>
                            <label>Jurusan</label>
                            <input type="text" readonly value="Rekayasa Perangkat Lunak">
                        </li>

                        <li>
                            <label>Kelas</label>
                            <input type="text" readonly value="XII-RPA">
                        </li>
                    </ul>
                    <button><i class="fa-solid fa-arrow-right-from-bracket"></i>Keluar</button>
                </div>

            </div>
        </div>
    </div>
</section>


@include('layout/footer')