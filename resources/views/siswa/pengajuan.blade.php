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
        <div class="row form-pengajuan">
            <div class="col-md-6">
                <form action="">
                    <label for="">Kategori</label><br>
                    <select name="" id="">
                        <option value="">Akademik</option>
                        <option value="">Peribadi</option>
                        <option value="">Sosial</option>
                        <option value="">Karir</option>
                    </select><br><br>
                    <label for="">Deskripsi</label><br>
                    <textarea name="" placeholder="Ringkasan masalah" id=""></textarea>
                </form>
            </div>
            <div class="col-md-6">
                <label for="">Tanggal Konseling</label>
                <input type="text" id="tanggal_konsultasi" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 btn-ajuan text-center">
                <button>Batal</button>
                <button class="submit-btn">Kirim</button>
            </div>
        </div>
    </div>
</section>

@include('layout/footer')

<script>
    flatpickr("#tanggal_konsultasi", {
        inline: true,
        dateFormat: "Y-m-d"
    });
</script>