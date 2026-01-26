@include('layout/header')

<section class="pengajuan">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title-body">
                    <h3>Konseling</h3>
                    <h1>Konseling Pada Bulan Januari</h1>
                </div>
            </div>
        </div>
        <div class="row form-pengajuan">
            <div class="col-md-6">
                <form action="">
                    <label for="">Kategori</label><br>
                    <select name="" id="">
                        <option value="">Akademik</option>
                        <option value="">Peribadi</option>
                        <option value="">Sosial</option>
                        <option value="">Karir</option>
                    </select><br>
                    <label for="">Deskripsi</label>
                    <input type="text" placeholder="Ringkasan masalah">
                </form>
            </div>
            <div class="col-md-6">
                <label for="">Tanggal Konseling</label>
                <input type="text" id="tanggal_konsultasi" class="form-control">
            </div>
        </div>
    </div>
</section>

<script>
    flatpickr("#tanggal_konsultasi", {
        inline: true,
        dateFormat: "Y-m-d"
    });
</script>