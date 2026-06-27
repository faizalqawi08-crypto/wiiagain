<?php
session_start();
require_once __DIR__ . '/../config.php';

$message = '';

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

try {
    $conn = getConnection();
    ensureDefaultAdmin($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $message = 'Isi username dan password.';
        } else {
            $stmt = $conn->prepare('SELECT id, username, password FROM admin_users WHERE username = ?');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $admin = $result->fetch_assoc();

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_user'] = $admin['username'];
                header('Location: dashboard.php');
                exit;
            }

            $message = 'Username atau password salah.';
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
<title>Login Admin</title>

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
overflow:hidden;
padding:25px;

background:linear-gradient(-45deg,#0f172a,#1d4ed8,#2563eb,#38bdf8,#0ea5e9);
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
opacity:.4;
animation:float 8s ease-in-out infinite;
}

body::before{
width:350px;height:350px;
background:#3b82f6;
top:-120px;left:-100px;
}

body::after{
width:400px;height:400px;
background:#a855f7;
bottom:-150px;right:-120px;
animation-delay:2s;
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
max-width:440px;

padding:40px;

border-radius:28px;

background:linear-gradient(145deg,rgba(255,255,255,.16),rgba(255,255,255,.10));
backdrop-filter:blur(25px);
border:1px solid rgba(255,255,255,.2);

box-shadow:0 30px 70px rgba(0,0,0,.35);

color:white;

animation:fade .7s ease;
}

@keyframes fade{
from{opacity:0;transform:translateY(20px);}
to{opacity:1;transform:translateY(0);}
}

.badge{
display:inline-block;
padding:6px 12px;
font-size:12px;
border-radius:50px;
background:rgba(255,255,255,.15);
margin-bottom:15px;
}

.icon{
font-size:60px;
text-align:center;
margin-bottom:10px;
}

h1{
text-align:center;
font-size:30px;
margin-bottom:6px;
}

.subtitle{
text-align:center;
opacity:.85;
margin-bottom:25px;
font-size:14px;
}

.message{
background:rgba(239,68,68,.2);
border:1px solid rgba(239,68,68,.4);
padding:12px;
border-radius:12px;
margin-bottom:15px;
color:#fee2e2;
font-size:14px;
}

/* INPUT */
label{
font-size:13px;
display:block;
margin-bottom:6px;
}

.input-group{
position:relative;
margin-bottom:15px;
}

input{
width:100%;
padding:13px 14px;
border-radius:12px;
border:1px solid rgba(255,255,255,.2);
background:rgba(255,255,255,.15);
color:white;
outline:none;
transition:.3s;
font-size:14px;
}

input::placeholder{
color:rgba(255,255,255,.65);
}

input:focus{
border-color:#93c5fd;
background:rgba(255,255,255,.22);
box-shadow:0 0 0 4px rgba(59,130,246,.25);
}

/* password toggle */
.toggle{
position:absolute;
right:12px;
top:50%;
transform:translateY(-50%);
cursor:pointer;
font-size:13px;
opacity:.8;
}

/* BUTTON */
button{
width:100%;
padding:14px;
border:none;
border-radius:12px;
cursor:pointer;

font-weight:600;
font-size:15px;
color:white;

background:linear-gradient(135deg,#2563eb,#3b82f6);
box-shadow:0 15px 30px rgba(37,99,235,.35);

transition:.3s;
}

button:hover{
transform:translateY(-3px);
box-shadow:0 20px 40px rgba(37,99,235,.5);
}

/* footer */
.footer{
text-align:center;
margin-top:18px;
font-size:13px;
opacity:.85;
}

.footer a{
color:#bfdbfe;
text-decoration:none;
}

.footer a:hover{
color:white;
}

/* responsive */
@media(max-width:500px){
.card{padding:30px;}
h1{font-size:26px;}
}
</style>
</head>

<body>

<div class="card">

<div class="badge">🔐 Administrator Panel</div>

<div class="icon">👨‍💼</div>

<h1>Login Admin</h1>

<p class="subtitle">
Masuk untuk mengelola seluruh pengajuan cuti secara aman dan cepat.
</p>

<?php if ($message !== ''): ?>
<div class="message">
<?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
</div>
<?php endif; ?>

<form method="post">

<label>Username</label>
<div class="input-group">
<input type="text" name="username" placeholder="Masukkan username" required>
</div>

<label>Password</label>
<div class="input-group">
<input type="password" name="password" id="password" placeholder="Masukkan password" required>
<span class="toggle" onclick="togglePassword()">👁️</span>
</div>

<button type="submit">Masuk</button>

</form>

<div class="footer">
<a href="../home.php">← Kembali ke Beranda</a>
</div>

</div>

<script>
function togglePassword(){
const pass = document.getElementById('password');
pass.type = pass.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>