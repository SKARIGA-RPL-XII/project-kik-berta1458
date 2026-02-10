<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>eCounsel</title>
</head>

<body class="login">
    <section class="form-login">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('login.proses') }}" method="POST">
                        @csrf
                        <img class="logo" src="{{asset('image/logo.png')}}" alt="">
                        <h3>Selamat Datang di</h3>
                        <h1>eCounsel - Skariga </h1>
                        <p>Sistem Bimbingan Konseling Online Sekolah.</p>
                        <label for="">Username</label><br>
                        <input type="text" name="username" id="username"><br><br>
                        <label for="">Password</label><br>
                        <input type="password" name="password" id="password"><br>
                        <ul>
                            <li class="ingat-saya"><input type="checkbox" name="" id="rememberMe"><span>Simpan Data Pengguna</span></li>
                        </ul><br>
                        <button type="submit">Masuk</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>

</html>

<script>
    window.onload = function() {
        const savedUsername = localStorage.getItem('saved_username');
        const savedPassword = localStorage.getItem('saved_password');

        if (savedUsername) {
            document.getElementById('username').value = savedUsername;
        }

        if (savedPassword) {
            document.getElementById('password').value = savedPassword;
        }
    };

    document.querySelector('form').addEventListener('submit', function() {
        const remember = document.getElementById('rememberMe').checked;
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        if (remember) {
            localStorage.setItem('saved_username', username);
            localStorage.setItem('saved_password', password);
        } else {
            localStorage.removeItem('saved_username');
            localStorage.removeItem('saved_password');
        }
    });
</script>