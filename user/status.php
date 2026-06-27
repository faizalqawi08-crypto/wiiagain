<?php
session_start();
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $conn = getConnection();

    $stmt = $conn->prepare("
        SELECT id, nama, email, jenis_cuti, tanggal_mulai, tanggal_selesai, alasan, surat_path, status, created_at
        FROM cuti_pengajuan
        WHERE id = ?
    ");

    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();

    $result = $stmt->get_result();
    $request = $result->fetch_assoc();

} catch (Exception $e) {
    $request = null;
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Status Cuti</title>

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
background:linear-gradient(-45deg,#07111f,#0f2d52,#1f6feb);
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

body::before,
body::after{
content:"";
position:absolute;
border-radius:50%;
filter:blur(110px);
opacity:.24;
animation:float 10s ease-in-out infinite;
}

body::before{
width:320px;height:320px;
background:#f59e0b;
top:-100px;left:-90px;
}

body::after{
width:380px;height:380px;
background:#22c55e;
bottom:-120px;right:-100px;
animation-delay:3s;
}

@keyframes float{
0%,100%{transform:translateY(0);}
50%{transform:translateY(-35px);}
}

.wrapper{
position:relative;
z-index:2;
display:flex;
min-height:100vh;
}

.sidebar{
width:260px;
padding:28px 20px;
background:linear-gradient(180deg,rgba(2,6,23,.60),rgba(2,6,23,.38));
backdrop-filter:blur(20px);
border-right:1px solid rgba(255,255,255,.12);
display:flex;
flex-direction:column;
gap:18px;
}

.sidebar .brand{
font-size:24px;
font-weight:800;
margin-bottom:6px;
}

.sidebar .brand small{
display:block;
font-size:12px;
opacity:.75;
margin-top:4px;
}

.sidebar .menu a{
display:block;
padding:10px 12px;
margin-bottom:8px;
color:#e2e8f0;
text-decoration:none;
border-radius:10px;
transition:.2s;
font-weight:600;
}

.sidebar .menu a:hover,
.sidebar .menu a.active{
background:rgba(255,255,255,.12);
color:white;
}

.main{
flex:1;
padding:24px;
}

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:24px;
padding:18px 22px;
border-radius:20px;
background:rgba(255,255,255,.12);
backdrop-filter:blur(20px);
border:1px solid rgba(255,255,255,.16);
box-shadow:0 10px 30px rgba(0,0,0,.16);
}

.topbar h1{
font-size:24px;
font-weight:700;
}

.topbar p{
font-size:13px;
opacity:.8;
margin-top:3px;
}

.user-pill{
display:flex;
align-items:center;
gap:10px;
}

.user-pill .avatar{
width:40px;
height:40px;
border-radius:50%;
background:linear-gradient(135deg,#f59e0b,#fbbf24);
color:#111827;
display:flex;
align-items:center;
justify-content:center;
font-weight:800;
}

.user-pill .status{
font-size:12px;
padding:4px 8px;
border-radius:999px;
background:rgba(34,197,94,.2);
color:#bbf7d0;
}

.hero{
background:linear-gradient(135deg,rgba(255,255,255,.16),rgba(255,255,255,.08));
padding:18px 20px;
border-radius:18px;
border:1px solid rgba(255,255,255,.16);
margin-bottom:18px;
box-shadow:0 8px 24px rgba(0,0,0,.12);
}

.hero .welcome{
font-size:16px;
font-weight:600;
margin-bottom:6px;
}

.hero .sub{
font-size:13px;
opacity:.85;
}

/* HEADER */
.header{
text-align:center;
padding:30px;
background:linear-gradient(135deg,rgba(255,255,255,.15),rgba(255,255,255,.05));
}

.header h1{
font-size:32px;
}

.header p{
opacity:.85;
font-size:14px;
}

/* CONTENT */
.content{
padding:30px;
}

/* WELCOME */
.welcome{
background:rgba(255,255,255,.10);
padding:12px 15px;
border-radius:12px;
margin-bottom:20px;
border:1px solid rgba(255,255,255,.15);
}

/* GRID */
.grid{
display:grid;
grid-template-columns:repeat(2,1fr);
gap:15px;
margin-bottom:20px;
}

.card{
background:rgba(255,255,255,.10);
border:1px solid rgba(255,255,255,.15);
padding:15px;
border-radius:15px;
transition:.3s;
}

.card:hover{
transform:translateY(-5px);
}

.label{
font-size:12px;
opacity:.7;
margin-bottom:5px;
}

.value{
font-weight:600;
}

/* REASON */
.reason{
background:rgba(255,255,255,.10);
padding:15px;
border-radius:15px;
margin-bottom:20px;
border:1px solid rgba(255,255,255,.15);
}

/* STATUS */
.status{
text-align:center;
margin-bottom:25px;
}

.badge{
display:inline-block;
padding:10px 18px;
border-radius:999px;
font-weight:600;
font-size:14px;
}

.pending{
background:#facc15;
color:#000;
}

.approved{
background:#22c55e;
color:#000;
}

.rejected{
background:#ef4444;
color:#fff;
}

/* BUTTONS */
.actions{
display:flex;
justify-content:center;
gap:10px;
flex-wrap:wrap;
}

.btn{
padding:12px 18px;
border-radius:12px;
text-decoration:none;
font-weight:600;
font-size:13px;
color:white;
transition:.3s;
}

.btn:hover{
transform:translateY(-3px);
}

.primary{background:#2563eb;}
.success{background:#16a34a;}
.danger{background:#dc2626;}

/* EMPTY */
.empty{
text-align:center;
padding:40px;
opacity:.8;
}

/* ERROR */
.error{
background:rgba(239,68,68,.2);
border:1px solid rgba(239,68,68,.3);
padding:12px;
border-radius:12px;
margin-bottom:15px;
}

/* RESPONSIVE */
@media(max-width:900px){
.wrapper{flex-direction:column;}
.sidebar{width:100%;border-right:0;border-bottom:1px solid rgba(255,255,255,.12);}
.grid{grid-template-columns:1fr;}
}

</style>
</head>

<body>

<div class="wrapper">

<aside class="sidebar">
<div class="brand">ET Store <small>Employee Portal</small></div>
<div class="menu">
<a href="status.php" class="active">Status Cuti</a>
<a href="../form.php">Ajukan Cuti</a>
<a href="../uploads/upload_surat.php">Upload Surat</a>
<a href="logout.php">Logout</a>
</div>
</aside>

<main class="main">
<div class="topbar">
<div>
<h1>Status Pengajuan Cuti</h1>
<p>Panel pribadi karyawan ET Store</p>
</div>
<div class="user-pill">
<div class="avatar"><?php echo strtoupper(substr(htmlspecialchars($_SESSION['user_name'] ?? 'U'), 0, 1)); ?></div>
<div>
<strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Karyawan'); ?></strong><br>
<span class="status">Aktif</span>
</div>
</div>
</div>

<div class="hero">
<div class="welcome">Halo, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
<div class="sub">Pantau proses persetujuan cuti Anda dari panel yang lebih terstruktur dan modern.</div>
</div>

<?php if(isset($error)): ?>
<div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if(!empty($request)): ?>

<div class="grid">

<div class="card">
<div class="label">Nama</div>
<div class="value"><?php echo htmlspecialchars($request['nama']); ?></div>
</div>

<div class="card">
<div class="label">Email</div>
<div class="value"><?php echo htmlspecialchars($request['email']); ?></div>
</div>

<div class="card">
<div class="label">Jenis Cuti</div>
<div class="value"><?php echo htmlspecialchars($request['jenis_cuti']); ?></div>
</div>

<div class="card">
<div class="label">Tanggal Pengajuan</div>
<div class="value"><?php echo date('d M Y', strtotime($request['created_at'])); ?></div>
</div>

<div class="card">
<div class="label">Mulai</div>
<div class="value"><?php echo $request['tanggal_mulai']; ?></div>
</div>

<div class="card">
<div class="label">Selesai</div>
<div class="value"><?php echo $request['tanggal_selesai']; ?></div>
</div>

</div>

<div class="detail-box">
<div class="label">Alasan</div>
<div class="value"><?php echo nl2br(htmlspecialchars($request['alasan'])); ?></div>
</div>

<?php if (!empty($request['surat_path'])): ?>
<div class="detail-box">
<div class="label">Lampiran Surat</div>
<div class="value">
<a href="../uploads/<?php echo rawurlencode($request['surat_path']); ?>" target="_blank" style="color:#bfdbfe;">Lihat file lampiran</a>
</div>
</div>
<?php endif; ?>

<div class="status">
<?php
$status = strtolower($request['status'] ?? 'pending');
$text = [
'pending' => '⏳ Menunggu Persetujuan',
'approved' => '✅ Disetujui',
'rejected' => '❌ Ditolak'
];
?>
<span class="badge <?php echo $status; ?>"><?php echo $text[$status] ?? '⏳ Menunggu Persetujuan'; ?></span>
</div>

<?php else: ?>
<div class="empty">
<h3>📭 Data Tidak Ditemukan</h3>
<p>Belum ada pengajuan cuti untuk akun ini.</p>
</div>
<?php endif; ?>

<div class="actions">
<a href="login.php" class="btn primary">🔍 Cek Lagi</a>
<a href="../form.php" class="btn success">➕ Ajukan Cuti</a>
<a href="logout.php" class="btn danger">🚪 Logout</a>
</div>

</main>
</div>

</body>
</html>