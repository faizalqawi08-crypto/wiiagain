<?php
session_start();
require_once __DIR__ . '/../config.php';

$message = '';
$loggedIn = !empty($_SESSION['user_id']);
$currentName = $_SESSION['user_name'] ?? '';

try {
    $conn = getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $nama = trim($_POST['nama'] ?? '');

        if ($email === '' || $nama === '') {
            $message = 'Email dan nama wajib diisi.';
        } else {
            $stmt = $conn->prepare("SELECT id, nama, email FROM cuti_pengajuan WHERE email = ? AND nama = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->bind_param('ss', $email, $nama);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nama'];
                $_SESSION['user_email'] = $user['email'];
                header('Location: status.php');
                exit;
            }

            $message = 'Data tidak ditemukan. Pastikan Anda sudah mengajukan cuti.';
        }
    }
} catch (Exception $e) {
    $message = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Pemohon Cuti</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
padding:25px;
overflow:hidden;

background:linear-gradient(-45deg,#0f172a,#1e3a8a,#2563eb,#38bdf8);
background-size:400% 400%;
animation:bg 12s ease infinite;
position:relative;
}

@keyframes bg{
0%{background-position:0% 50%;}
50%{background-position:100% 50%;}
100%{background-position:0% 50%;}
}

/* floating glow */
body::before,
body::after{
content:"";
position:absolute;
border-radius:50%;
filter:blur(90px);
opacity:.35;
animation:float 8s ease-in-out infinite;
}

body::before{
width:320px;height:320px;
background:#60a5fa;
top:-100px;left:-80px;
}

body::after{
width:380px;height:380px;
background:#a855f7;
bottom:-120px;right:-100px;
animation-delay:3s;
}

@keyframes float{
0%,100%{transform:translateY(0);}
50%{transform:translateY(-35px);}
}

/* CARD */
.card{
position:relative;
z-index:2;

width:100%;
max-width:460px;

padding:40px;

border-radius:28px;

background:linear-gradient(145deg,rgba(255,255,255,.16),rgba(255,255,255,.10));
backdrop-filter:blur(25px);
border:1px solid rgba(255,255,255,.2);

box-shadow:0 25px 60px rgba(0,0,0,.3);

color:white;

animation:fade .7s ease;
}

@keyframes fade{
from{opacity:0;transform:translateY(20px);}
to{opacity:1;transform:translateY(0);}
}

h1{
text-align:center;
font-size:28px;
margin-bottom:8px;
}

.subtitle{
text-align:center;
font-size:14px;
opacity:.85;
margin-bottom:20px;
}

.message{
padding:12px;
border-radius:12px;
margin-bottom:12px;
font-size:14px;
background:rgba(239,68,68,.2);
border:1px solid rgba(239,68,68,.3);
color:#fee2e2;
}

.success{
background:rgba(34,197,94,.2);
border:1px solid rgba(34,197,94,.3);
color:#dcfce7;
}

.info{
padding:12px 14px;
border-radius:12px;
margin-bottom:14px;
font-size:13px;
background:rgba(255,255,255,.14);
border:1px solid rgba(255,255,255,.2);
color:#eff6ff;
line-height:1.5;
}

input{
width:100%;
padding:13px;
margin-top:10px;
border-radius:12px;
border:1px solid rgba(255,255,255,.2);
background:rgba(255,255,255,.15);
color:white;
outline:none;
transition:.3s;
}

input::placeholder{
color:rgba(255,255,255,.7);
}

input:focus{
box-shadow:0 0 0 4px rgba(59,130,246,.25);
border-color:#93c5fd;
}

button, .btn-link{
width:100%;
display:inline-block;
margin-top:15px;
padding:14px;
border:none;
border-radius:12px;
cursor:pointer;
text-align:center;
text-decoration:none;
font-size:15px;
font-weight:600;
background:linear-gradient(135deg,#2563eb,#3b82f6);
color:white;
transition:.3s;
}

button:hover, .btn-link:hover{
transform:translateY(-3px);
box-shadow:0 15px 30px rgba(37,99,235,.4);
}

.link{
text-align:center;
margin-top:15px;
font-size:13px;
}

.link a{
color:#bfdbfe;
text-decoration:none;
}

.link a:hover{
color:white;
}

.card small{
display:block;
text-align:center;
opacity:.8;
margin-bottom:10px;
}

</style>
</head>

<body>

<div class="card">

<h1>📄 Cek Status Cuti</h1>
<p class="subtitle">
Masukkan nama dan email yang sama saat Anda mengajukan cuti untuk melihat status pengajuan.
</p>

<?php if ($message !== ''): ?>
<div class="message"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
<?php endif; ?>

<?php if ($loggedIn): ?>
<div class="message success">
✔ Anda sedang login sebagai <b><?php echo htmlspecialchars($currentName, ENT_QUOTES, 'UTF-8'); ?></b>
</div>

<a href="status.php" class="btn-link">Lihat Status Cuti</a>
<a href="../index.php" class="btn-link" style="background:linear-gradient(135deg,#059669,#10b981); margin-top:10px;">Ajukan Cuti Baru</a>

<?php else: ?>

<div class="info">
<strong>Langkah cepat:</strong><br>
1. Ajukan cuti terlebih dahulu<br>
2. Masukkan nama dan email yang sama<br>
3. Lihat status pengajuan Anda
</div>

<form method="post">
<label style="display:block; margin-top:8px; font-size:13px; opacity:.9;">Nama Lengkap</label>
<input type="text" name="nama" placeholder="Contoh: Budi Santoso" required>

<label style="display:block; margin-top:10px; font-size:13px; opacity:.9;">Email</label>
<input type="email" name="email" placeholder="Contoh: budi@email.com" required>

<button type="submit">Cek Status</button>
</form>

<?php endif; ?>

<div class="link">
<a href="../home.php">← Kembali ke menu</a>
</div>

</div>

</body>
</html>