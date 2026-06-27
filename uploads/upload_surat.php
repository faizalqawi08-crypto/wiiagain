<?php
session_start();

$uploadDir = dirname(__DIR__) . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$message = '';
$uploadedFileUrl = '';
$uploadedFiles = [];

if (is_dir($uploadDir)) {
    foreach (scandir($uploadDir) as $fileName) {
        if ($fileName === '.' || $fileName === '..') continue;

        $fullPath = $uploadDir . $fileName;
        if (is_file($fullPath)) {
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (in_array($ext, ['pdf','doc','docx'])) {
                $uploadedFiles[] = $fileName;
            }
        }
    }
    sort($uploadedFiles);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['surat'])) {

    $file = $_FILES['surat'];

    $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $message = '❌ Upload gagal.';
    }
    elseif ($file['size'] > 2 * 1024 * 1024) {
        $message = '❌ Maksimal file 2MB.';
    }
    elseif (!in_array($ext, ['pdf','doc','docx'])) {
        $message = '❌ Format harus PDF/DOCX.';
    }
    else {

        $fileName = 'cuti-' . time() . '-' . preg_replace('/[^a-zA-Z0-9._-]/','-',$file['name']);
        $target = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            $message = '✅ Upload berhasil!';
            $uploadedFileUrl = './' . rawurlencode($fileName);
            $uploadedFiles[] = $fileName;
            sort($uploadedFiles);
        } else {
            $message = '❌ Gagal menyimpan file.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ET Store - Upload Surat Cuti</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

/* RESET */
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}

body{
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
padding:30px;

background:linear-gradient(-45deg,#0f172a,#1e3a8a,#2563eb,#38bdf8);
background-size:400% 400%;
animation:bg 12s ease infinite;

color:white;
position:relative;
overflow-x:hidden;
}

@keyframes bg{
0%{background-position:0% 50%;}
50%{background-position:100% 50%;}
100%{background-position:0% 50%;}
}

/* glow */
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
width:350px;height:350px;
background:#60a5fa;
top:-120px;left:-100px;
}

body::after{
width:420px;height:420px;
background:#a855f7;
bottom:-150px;right:-120px;
animation-delay:3s;
}

@keyframes float{
0%,100%{transform:translateY(0);}
50%{transform:translateY(-40px);}
}

/* CARD */
.card{
width:100%;
max-width:720px;

padding:40px;

border-radius:28px;

background:rgba(255,255,255,.12);
backdrop-filter:blur(30px);
border:1px solid rgba(255,255,255,.18);

box-shadow:0 30px 80px rgba(0,0,0,.35);

animation:fade .7s ease;
position:relative;
z-index:2;
}

@keyframes fade{
from{opacity:0;transform:translateY(25px);}
to{opacity:1;transform:translateY(0);}
}

/* HEADER */
h1{
font-size:34px;
text-align:center;
}

.subtitle{
text-align:center;
opacity:.85;
margin-top:5px;
margin-bottom:20px;
}

/* BADGE */
.badge{
display:inline-block;
padding:6px 12px;
border-radius:999px;
background:rgba(255,255,255,.15);
font-size:12px;
margin-bottom:12px;
}

/* BOX */
.box{
background:linear-gradient(135deg,rgba(255,255,255,.14),rgba(255,255,255,.08));
border:1px solid rgba(255,255,255,.15);
padding:15px;
border-radius:16px;
margin-bottom:15px;
box-shadow:0 10px 26px rgba(0,0,0,.12);
}

/* MESSAGE */
.message{
padding:12px;
border-radius:12px;
margin-bottom:15px;
background:rgba(34,197,94,.2);
border:1px solid rgba(34,197,94,.3);
}

.error{
background:rgba(239,68,68,.2);
border:1px solid rgba(239,68,68,.3);
}

/* INPUT */
input{
width:100%;
padding:13px;
border-radius:12px;
border:1px solid rgba(255,255,255,.2);
background:rgba(255,255,255,.15);
color:white;
outline:none;
margin-top:10px;
}

input::file-selector-button{
background:#2563eb;
border:none;
padding:8px 12px;
color:white;
border-radius:8px;
cursor:pointer;
}

/* BUTTON */
button{
width:100%;
margin-top:12px;
padding:14px;

border:none;
border-radius:12px;

background:linear-gradient(135deg,#2563eb,#3b82f6);
color:white;

font-weight:600;
cursor:pointer;

transition:.3s;
}

button:hover{
transform:translateY(-3px);
}

/* LIST */
ul{
margin-left:18px;
margin-top:10px;
}

a{
color:#bfdbfe;
text-decoration:none;
}

a:hover{
color:white;
}

.actions{
text-align:center;
margin-top:15px;
}

.actions a{
color:white;
opacity:.8;
}

.actions a:hover{
opacity:1;
}

</style>
</head>

<body>

<div class="card">

<div class="badge">📄 ET Store - Upload Surat Cuti</div>

<h1>Upload Dokumen Pendukung</h1>
<p class="subtitle">Unggah file PDF / DOCX untuk melengkapi pengajuan cuti Anda</p>

<div class="box">
<strong>Alur cepat:</strong><br>
• Siapkan file dokumen<br>
• Upload melalui form di bawah<br>
• File akan tersedia untuk dipakai dalam pengajuan Anda
</div>

<?php if($message): ?>
<div class="message <?= strpos($message,'❌')!==false?'error':'' ?>">
<?= $message ?>
</div>
<?php endif; ?>

<?php if($uploadedFileUrl): ?>
<div class="box">
<strong>File terbaru:</strong><br>
<a href="<?= $uploadedFileUrl ?>" target="_blank">📂 Buka file</a>
</div>
<?php endif; ?>

<div class="box">
<strong>Info:</strong><br>
• Max 2MB<br>
• Format: PDF, DOC, DOCX
</div>

<form method="post" enctype="multipart/form-data">
<input type="file" name="surat" required>
<button type="submit">📤 Upload</button>
</form>

<?php if(!empty($uploadedFiles)): ?>
<div class="box">
<strong>File terakhir:</strong>
<ul>
<?php foreach(array_slice($uploadedFiles,-5) as $f): ?>
<li><a href="./<?= rawurlencode($f) ?>" target="_blank"><?= $f ?></a></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<div class="actions">
<a href="../home.php">← Kembali ke Home ET Store</a>
</div>

</div>

</body>
</html>