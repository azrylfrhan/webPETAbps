<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | PETA BPS</title>
    <link rel="stylesheet" href="style.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <style>
        .auth-group { position: relative; margin-bottom: 20px; }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 38px;
            cursor: pointer;
            color: #666;
            z-index: 10;
        }
        .error-msg {
            color: #721c24;
            background-color: #f8d7da;
            padding: 12px;
            border-radius: 5px;
            font-size: 0.85em;
            margin-bottom: 15px;
            text-align: center;
            border: 1px solid #f5c6cb;
        }
        .info-msg {
            margin-top: 20px;
            font-size: 0.8em;
            color: #666;
            text-align: center;
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<header class="header">
    <div class="logo">
        <img src="images/logobps.png" alt="Logo BPS">
        <span>PETA — Pemetaan Potensi Pegawai</span>
    </div>
</header>

<div class="auth-container">
    <div class="auth-card">
        <h2>Login Peserta</h2>

        <?php if(isset($_GET['error'])): ?>
            <div class="error-msg">
                <?php 
                    if($_GET['error'] == 'wrong') echo "NIP atau Password salah!";
                    elseif($_GET['error'] == 'akses_ditolak') echo "Maaf, akun Anda belum diaktifkan oleh Admin untuk jadwal hari ini.";
                    elseif($_GET['error'] == 'empty') echo "Harap isi NIP dan Password!";
                ?>
            </div>
        <?php endif; ?>

        <form action="backend/login_process.php" method="POST">
            <div class="auth-group">
                <label>NIP</label>
                <input type="text" name="nip" placeholder="Masukkan NIP Anda" required>
            </div>

            <div class="auth-group">
                <label>Password</label>
                <input type="password" name="password" id="pass_login" placeholder="Masukkan Password" required>
                <i class="fi fi-rr-eye toggle-password" id="btn_toggle_login"></i>
            </div>

            <div class="action">
                <button type="submit" class="btn-primary">Login</button>
            </div>
        </form>
        
    </div>
</div>

<script>
    const btnToggle = document.querySelector('#btn_toggle_login');
    const inputPass = document.querySelector('#pass_login');

    btnToggle.addEventListener('click', function() {
        if (inputPass.type === 'password') {
            inputPass.type = 'text';
            this.classList.replace('fi-rr-eye', 'fi-rr-eye-crossed');
        } else {
            inputPass.type = 'password';
            this.classList.replace('fi-rr-eye-crossed', 'fi-rr-eye');
        }
    });
</script>

</body>
</html>