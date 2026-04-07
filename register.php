<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" href="/images/logobps.png">
    <meta charset="UTF-8">
    <title>Registrasi | Tes Psikologi Pegawai</title>
    <link rel="stylesheet" href="style.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <style>
        .auth-group { position: relative; margin-bottom: 15px; }
        .toggle-password { position: absolute; right: 15px; top: 38px; cursor: pointer; color: #666; font-size: 1.1rem; }
        .auth-group input { padding-right: 45px; }
        #match_msg { font-size: 0.75em; display: block; margin-top: 5px; }
    </style>
</head>
<body>

<header class="header">
    <div class="logo"><img src="images/logobps.png"> <span>Tes Psikologi Pegawai</span></div>
</header>

<div class="auth-container">
    <div class="auth-card">
        <h2>Registrasi Akun</h2>

        <?php if(isset($_GET['error'])): ?>
            <p style="color: red; font-size: 0.8em; margin-bottom: 15px; text-align: center;">
                <?php 
                    if($_GET['error'] == 'nip_ada') echo "NIP sudah terdaftar!";
                    if($_GET['error'] == 'password_mismatch') echo "Konfirmasi password tidak cocok!";
                    if($_GET['error'] == 'gagal') echo "Terjadi kesalahan sistem.";
                ?>
            </p>
        <?php endif; ?>

        <form action="backend/register_process.php" method="POST">
            <div class="auth-group"><label>Nama Lengkap</label><input type="text" name="nama" required></div>
            <div class="auth-group"><label>NIP</label><input type="text" name="nip" required></div>
            <div class="auth-group"><label>Jabatan</label><input type="text" name="jabatan" required></div>
            <div class="auth-group"><label>Satuan Kerja</label><input type="text" name="satuan_kerja" required></div>
            
            <div class="auth-group">
                <label>Password</label>
                <input type="password" name="password" id="reg_pass" required>
                <i class="fi fi-rr-eye toggle-password" id="btn_toggle_reg"></i>
            </div>

            <div class="auth-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" id="confirm_pass" required>
                <span id="match_msg"></span>
            </div>

            <div class="action"><button type="submit" name="register" class="btn-primary">Daftar</button></div>
        </form>
        <p style="margin-top: 15px; text-align: center; font-size: 0.9em;">Sudah punya akun? <a href="login.php">Login</a></p>
    </div>
</div>

<script>
    const btnToggleReg = document.querySelector('#btn_toggle_reg');
    const regPass = document.querySelector('#reg_pass');
    const confirmPass = document.querySelector('#confirm_pass');
    const matchMsg = document.querySelector('#match_msg');

    btnToggleReg.addEventListener('click', function() {
        if (regPass.type === 'password') {
            regPass.type = 'text';
            this.classList.replace('fi-rr-eye', 'fi-rr-eye-crossed');
        } else {
            regPass.type = 'password';
            this.classList.replace('fi-rr-eye-crossed', 'fi-rr-eye');
        }
    });

    confirmPass.addEventListener('keyup', () => {
        if (confirmPass.value === "") {
            matchMsg.textContent = '';
        } else if (regPass.value === confirmPass.value) {
            matchMsg.style.color = 'green';
            matchMsg.textContent = '✅ Password cocok';
        } else {
            matchMsg.style.color = 'red';
            matchMsg.textContent = '❌ Password tidak sama';
        }
    });
</script>
</body>
</html>