<?php
header('Location: home.php');
exit;
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>ET Store - Portal Cuti Karyawan</title>

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
padding:40px 20px;

background:linear-gradient(-45deg,#0f172a,#1e3a8a,#2563eb,#38bdf8);
background-size:400% 400%;
animation:bgMove 12s ease infinite;
position:relative;
overflow-x:hidden;
color:white;
}

@keyframes bgMove{
0%{background-position:0% 50%;}
50%{background-position:100% 50%;}
100%{background-position:0% 50%;}
}

/* FLOATING GLOW */
body::before,
body::after{
content:"";
position:absolute;
border-radius:50%;
filter:blur(100px);
opacity:.35;
animation:float 8s ease-in-out infinite;
}

body::before{
width:350px;
height:350px;
background:#60a5fa;
top:-120px;
left:-100px;
}

body::after{
width:420px;
height:420px;
background:#a855f7;
bottom:-150px;
right:-120px;
animation-delay:3s;
}

@keyframes float{
0%,100%{transform:translateY(0);}
50%{transform:translateY(-40px);}
}

/* CARD */
.container{
position:relative;
z-index:2;

width:100%;
max-width:900px;

padding:45px;

border-radius:30px;

background:linear-gradient(145deg,rgba(255,255,255,.16),rgba(255,255,255,.10));
backdrop-filter:blur(30px);
border:1px solid rgba(255,255,255,.18);

box-shadow:0 30px 80px rgba(0,0,0,.35);

animation:fade .7s ease;
}

@keyframes fade{
from{opacity:0;transform:translateY(25px);}
to{opacity:1;transform:translateY(0);}
}

/* HEADER */
.header{
text-align:center;
margin-bottom:25px;
}

.header h1{
font-size:40px;
font-weight:700;
}

.header p{
opacity:.9;
margin-top:6px;
}

/* HERO */
.hero{
background:linear-gradient(135deg,rgba(255,255,255,.18),rgba(255,255,255,.05));
border:1px solid rgba(255,255,255,.2);

padding:25px;
border-radius:20px;

text-align:center;
margin-bottom:25px;
}

/* ALERT */
.alert{
padding:15px;
border-radius:12px;
margin-bottom:20px;
font-weight:500;
animation:fade .5s ease;
}

.alert.success{
background:rgba(34,197,94,.2);
border:1px solid rgba(34,197,94,.3);
}

.alert.error{
background:rgba(239,68,68,.2);
border:1px solid rgba(239,68,68,.3);
}

/* FORM */
form{
display:grid;
gap:18px;
}

.grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:15px;
}

label{
font-weight:600;
font-size:14px;
margin-bottom:6px;
display:block;
}

input,select,textarea{
width:100%;
padding:14px;

border-radius:12px;
border:1px solid rgba(255,255,255,.25);

background:rgba(255,255,255,.15);
color:white;

outline:none;
transition:.3s;
}

input::placeholder,
textarea::placeholder{
color:rgba(255,255,255,.7);
}

input:focus,
select:focus,
textarea:focus{
border-color:#93c5fd;
box-shadow:0 0 0 4px rgba(59,130,246,.25);
background:rgba(255,255,255,.25);
}

/* TEXTAREA */
textarea{
resize:none;
}

/* BUTTON */
button{
padding:15px;
border:none;
border-radius:12px;

font-size:16px;
font-weight:600;

cursor:pointer;
color:white;

background:linear-gradient(135deg,#2563eb,#3b82f6);

transition:.3s;
}

button:hover{
transform:translateY(-3px);
box-shadow:0 15px 30px rgba(37,99,235,.4);
}

/* FOOTER LINK */
.footer-link{
text-align:center;
margin-top:20px;
}

.footer-link a{
color:#bfdbfe;
text-decoration:none;
font-weight:600;
}

.footer-link a:hover{
color:white;
}

/* RESPONSIVE */
@media(max-width:768px){
.container{padding:25px;}
.header h1{font-size:28px;}
.grid{grid-template-columns:1fr;}
}

</style>
</head>

<body>

<?php
require_once __DIR__ . '/config.php';

$nama = '';
$email = '';
$jenis = '';
$mulai = '';
$selesai = '';
$alasan = '';
$status = '';
$uploadDir = __DIR__ . '/uploads/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

try {
    $conn = getConnection();
} catch (Exception $e) {
    $status = "<div class='alert error'>Koneksi database gagal: " . htmlspecialchars($e->getMessage()) . "</div>";
    $conn = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $conn !== null) {

    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $jenis = trim($_POST['jenis'] ?? '');
    $mulai = trim($_POST['mulai'] ?? '');
    $selesai = trim($_POST['selesai'] ?? '');
    $alasan = trim($_POST['alasan'] ?? '');

    $errors = [];
    $uploadedFileName = null;

    if (isset($_FILES['surat']) && $_FILES['surat']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['surat'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Upload surat gagal.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Ukuran file maksimal 2MB.';
        } else {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['pdf', 'doc', 'docx'])) {
                $errors[] = 'Format file harus PDF, DOC, atau DOCX.';
            } else {
                $safeName = 'cuti-' . time() . '-' . preg_replace('/[^a-zA-Z0-9._-]/', '-', $file['name']);
                $target = $uploadDir . $safeName;

                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $uploadedFileName = $safeName;
                } else {
                    $errors[] = 'Gagal menyimpan file surat.';
                }
            }
        }
    }

    if ($nama === '') $errors[] = 'Nama wajib diisi.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
    if ($jenis === '') $errors[] = 'Jenis cuti wajib dipilih.';
    if ($mulai === '' || $selesai === '') $errors[] = 'Tanggal wajib diisi.';
    if ($selesai < $mulai) $errors[] = 'Tanggal selesai tidak valid.';
    if ($alasan === '') $errors[] = 'Alasan wajib diisi.';

    if ($errors) {
        $status = "<div class='alert error'>" . implode('<br>', $errors) . "</div>";
    } else {

        if ($uploadedFileName === null) {
            $stmt = $conn->prepare("INSERT INTO cuti_pengajuan (nama,email,jenis_cuti,tanggal_mulai,tanggal_selesai,alasan) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('ssssss', $nama, $email, $jenis, $mulai, $selesai, $alasan);
        } else {
            $stmt = $conn->prepare("INSERT INTO cuti_pengajuan (nama,email,jenis_cuti,tanggal_mulai,tanggal_selesai,alasan,surat_path) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param('sssssss', $nama, $email, $jenis, $mulai, $selesai, $alasan, $uploadedFileName);
        }

        if ($stmt->execute()) {

            $hari = ((strtotime($selesai)-strtotime($mulai))/86400)+1;
            $attachmentText = $uploadedFileName ? ' <br><a href="uploads/' . rawurlencode($uploadedFileName) . '" target="_blank" style="color:#bfdbfe;">Lihat lampiran</a>' : '';

            $status = "<div class='alert success'>
            ✅ Pengajuan berhasil dikirim.<br><br>
            <b>$nama</b> mengajukan cuti <b>$jenis</b> selama <b>$hari hari</b>.{$attachmentText}
            </div>";

            $nama=$email=$jenis=$mulai=$selesai=$alasan='';
        }
    }
}
?>

<div class="container">

<div class="header">
<h1>📅 Portal Cuti</h1>
<p>Ajukan cuti dengan cepat & mudah</p>
</div>

<div class="hero">
<h2>Form Pengajuan Cuti</h2>
<p>Isi data berikut dengan benar untuk mengajukan cuti secara online.</p>
</div>

<?php echo $status; ?>

<form method="POST" enctype="multipart/form-data">

<div class="grid">

<div>
<label>Nama</label>
<input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" required>
</div>

<div>
<label>Email</label>
<input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
</div>

</div>

<label>Jenis Cuti</label>
<select name="jenis" required>
<option value="">Pilih jenis cuti</option>
<option value="Tahunan" <?= $jenis=='Tahunan'?'selected':'' ?>>Tahunan</option>
<option value="Sakit" <?= $jenis=='Sakit'?'selected':'' ?>>Sakit</option>
<option value="Melahirkan" <?= $jenis=='Melahirkan'?'selected':'' ?>>Melahirkan</option>
<option value="Alasan Penting" <?= $jenis=='Alasan Penting'?'selected':'' ?>>Alasan Penting</option>
</select>

<div class="grid">

<div>
<label>Mulai</label>
<input type="date" name="mulai" value="<?= htmlspecialchars($mulai) ?>" required>
</div>

<div>
<label>Selesai</label>
<input type="date" name="selesai" value="<?= htmlspecialchars($selesai) ?>" required>
</div>

</div>

<label>Alasan</label>
<textarea name="alasan" rows="5" required><?= htmlspecialchars($alasan) ?></textarea>

<label>Upload Surat Pendukung</label>
<input type="file" name="surat" accept=".pdf,.doc,.docx">
<p style="font-size:12px; opacity:.8; margin-top:6px;">Format yang diperbolehkan: PDF, DOC, DOCX. Maksimal 2 MB.</p>

<button type="submit">🚀 Kirim Pengajuan</button>

</form>

<div class="footer-link">
<a href="user/login.php">🔍 Cek Status Pengajuan</a>
</div>

</div>

</body>
</html>