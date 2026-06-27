<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>ET Store - Pengajuan Cuti Karyawan</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

/* ========== ROOT THEME ========== */
:root{
--bg1:#07111f;
--bg2:#0f2d52;
--bg3:#1f6feb;
--card:#ffffff;
--text:#0f172a;
--muted:#64748b;
--glass:rgba(255,255,255,.12);
--accent:#f59e0b;
--accent2:#22c55e;
}

/* DARK MODE */
body.dark{
--bg1:#020617;
--bg2:#0f172a;
--bg3:#1e293b;
--card:rgba(255,255,255,.08);
--text:#ffffff;
--muted:#cbd5e1;
--glass:rgba(255,255,255,.08);
}

/* RESET */
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
padding:40px;
color:var(--text);
overflow-x:hidden;
background:linear-gradient(-45deg,var(--bg1),var(--bg2),var(--bg3));
background-size:400% 400%;
animation:bgMove 14s ease infinite;
position:relative;
transition:.3s;
}

@keyframes bgMove{
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
filter:blur(110px);
opacity:.25;
animation:float 10s ease-in-out infinite;
}

body::before{
width:380px;height:380px;
background:rgba(245,158,11,.28);
top:-140px;left:-120px;
}

body::after{
width:450px;height:450px;
background:rgba(34,197,94,.24);
bottom:-180px;right:-140px;
animation-delay:3s;
}

@keyframes float{
0%,100%{transform:translateY(0);}
50%{transform:translateY(-45px);}
}

/* container */
.container{
position:relative;
z-index:2;
width:100%;
max-width:1260px;
padding:42px 44px;
border-radius:28px;
background:linear-gradient(145deg,rgba(255,255,255,.16),rgba(255,255,255,.10));
backdrop-filter:blur(32px);
border:1px solid rgba(255,255,255,.22);
box-shadow:0 30px 90px rgba(0,0,0,.28);
transition:.3s;
}

/* HEADER */
.company-header{
display:flex;
align-items:center;
justify-content:center;
gap:16px;
margin-bottom:24px;
padding:16px 22px;
border-radius:20px;
background:rgba(255,255,255,.12);
border:1px solid rgba(255,255,255,.16);
box-shadow:0 8px 24px rgba(0,0,0,.12);
}

.company-badge{
background:linear-gradient(135deg,var(--accent),#fbbf24);
color:#111827;
padding:10px 16px;
border-radius:999px;
font-weight:700;
letter-spacing:.5px;
}

.company-title h1{
font-size:28px;
margin-bottom:4px;
}

.company-title p{
font-size:14px;
opacity:.9;
}

.header{
text-align:center;
margin-bottom:20px;
}

.header h2{
font-size:38px;
}

.header p{
opacity:.8;
margin-top:6px;
}

/* THEME BUTTON */
.theme-btn{
position:absolute;
top:20px;
right:20px;

padding:10px 14px;

border-radius:999px;
border:none;
cursor:pointer;

background:rgba(255,255,255,.15);
color:white;

backdrop-filter:blur(10px);

transition:.3s;
}

body.dark .theme-btn{
color:white;
}

.theme-btn:hover{
transform:scale(1.05);
}

/* HERO */
.hero{
background:linear-gradient(135deg,rgba(255,255,255,.20),rgba(255,255,255,.08));
padding:34px 30px;
border-radius:24px;
text-align:center;
margin-bottom:20px;
border:1px solid rgba(255,255,255,.18);
box-shadow:0 12px 36px rgba(0,0,0,.12);
}

.hero-badge{
display:inline-block;
margin-bottom:10px;
padding:7px 12px;
border-radius:999px;
background:rgba(245,158,11,.2);
color:#fde68a;
font-size:12px;
font-weight:700;
letter-spacing:.6px;
text-transform:uppercase;
}

.hero-actions{
display:flex;
justify-content:center;
gap:12px;
flex-wrap:wrap;
margin-top:16px;
}

.hero-actions .btn{
min-width:150px;
text-align:center;
}

.btn.primary{background:linear-gradient(135deg,#2563eb,#3b82f6);}
.btn.secondary{background:linear-gradient(135deg,#0f766e,#14b8a6);}

.info-panel{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:15px;
margin-bottom:22px;
}

.info-card{
background:rgba(255,255,255,.10);
padding:16px 18px;
border-radius:16px;
border:1px solid rgba(255,255,255,.15);
}

.info-label{
font-size:12px;
opacity:.75;
margin-bottom:6px;
text-transform:uppercase;
letter-spacing:.6px;
}

.info-value{
font-weight:700;
font-size:15px;
}

.info-value.active{
color:#bbf7d0;
}

/* STATS */
.stats{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:18px;
margin-bottom:30px;
}

.stat{
background:rgba(255,255,255,.10);
padding:20px;
border-radius:18px;
text-align:center;
transition:.3s;
border:1px solid rgba(255,255,255,.15);
box-shadow:0 6px 18px rgba(0,0,0,.08);
}

.stat:hover{
transform:translateY(-6px);
}

/* TIMELINE */
.timeline-card{
background:rgba(255,255,255,.10);
padding:20px;
border-radius:18px;
margin-bottom:24px;
border:1px solid rgba(255,255,255,.15);
box-shadow:0 6px 20px rgba(0,0,0,.08);
}

.notice-box{
margin-bottom:14px;
padding:12px 14px;
border-radius:12px;
background:rgba(255,255,255,.08);
border:1px solid rgba(255,255,255,.14);
font-size:13px;
opacity:.9;
}

.timeline-title{
font-size:18px;
font-weight:700;
margin-bottom:14px;
}

.timeline-item{
display:flex;
gap:12px;
align-items:flex-start;
margin-bottom:12px;
}

.timeline-dot{
width:10px;
height:10px;
border-radius:50%;
margin-top:7px;
flex-shrink:0;
}

.timeline-dot.approved{background:#22c55e;}
.timeline-dot.pending{background:#f59e0b;}

.timeline-item p{
font-size:13px;
opacity:.8;
margin-top:2px;
}

/* CARDS */
.cards{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:28px;
}

.card{
background:var(--card);
color:var(--text);

padding:32px;
border-radius:22px;

text-align:center;

transition:.3s;
box-shadow:0 20px 50px rgba(0,0,0,.15);
}

.card:hover{
transform:translateY(-10px);
}

/* ICON */
.icon{
width:75px;
height:75px;

margin:0 auto 16px;

border-radius:50%;

display:flex;
align-items:center;
justify-content:center;

font-size:34px;

color:white;
}

.adminIcon{background:linear-gradient(135deg,#ef4444,#dc2626);}
.userIcon{background:linear-gradient(135deg,#10b981,#059669);}
.cutiIcon{background:linear-gradient(135deg,#2563eb,#3b82f6);}

/* TEXT */
.card p{
color:var(--muted);
font-size:14px;
margin:10px 0 20px;
}

/* BUTTON */
.btn{
display:inline-block;
padding:12px 24px;
border-radius:999px;
text-decoration:none;
color:white;
font-weight:600;
transition:.2s;
box-shadow:0 10px 20px rgba(0,0,0,.12);
border:1px solid rgba(255,255,255,.12);
}

.btn:hover{
transform:translateY(-3px);
}

.admin{background:linear-gradient(135deg,#dc2626,#ef4444);}
.user{background:linear-gradient(135deg,#059669,#10b981);}
.cuti{background:linear-gradient(135deg,#2563eb,#3b82f6);}

/* FOOTER */
.footer{
text-align:center;
margin-top:35px;
opacity:.95;
font-size:13px;
display:flex;
justify-content:center;
gap:8px;
flex-wrap:wrap;
}

/* RESPONSIVE */
@media(max-width:992px){
.stats{grid-template-columns:repeat(2,1fr);}
.cards{grid-template-columns:1fr;}
.info-panel{grid-template-columns:1fr;}
.header h2{font-size:32px;}
}

</style>
</head>

<body>

<button class="theme-btn" onclick="toggleTheme()">
🌙 / ☀️
</button>

<div class="container">

<div class="company-header">
<div class="company-badge">ET Store</div>
<div class="company-title">
<h1>Portal Cuti Karyawan</h1>
<p>Pengajuan cuti yang cepat, aman, dan terintegrasi untuk seluruh karyawan ET Store</p>
</div>
</div>

<div class="header">
<h2>📅 ET Store</h2>
<p>Portal pengajuan cuti karyawan ET Store yang cepat, aman, dan terintegrasi</p>
</div>

<div class="hero">
<div class="hero-badge">ET Store Employee Portal</div>
<h2>Selamat Datang Karyawan 👋</h2>
<p>Pilih menu sesuai kebutuhan Anda untuk mengajukan atau memantau cuti</p>
<div class="hero-actions">
<a href="form.php" class="btn primary">Ajukan Cuti</a>
<a href="user/login.php" class="btn secondary">Cek Status</a>
</div>
</div>

<div class="info-panel">
<div class="info-card">
<div class="info-label">Nama Karyawan</div>
<div class="info-value">Karyawan ET Store</div>
</div>
<div class="info-card">
<div class="info-label">Departemen</div>
<div class="info-value">Operasional / Administrasi</div>
</div>
<div class="info-card">
<div class="info-label">Status</div>
<div class="info-value active">Aktif</div>
</div>
</div>

<div class="stats">
<div class="stat"><h2>100%</h2><p>Digital</p></div>
<div class="stat"><h2>24/7</h2><p>Akses</p></div>
<div class="stat"><h2>Fast</h2><p>Proses</p></div>
<div class="stat"><h2>Secure</h2><p>Aman</p></div>
</div>

<div class="timeline-card">
<div class="timeline-title">Status Cuti Terakhir</div>
<div class="notice-box">
Pengajuan cuti yang sudah disetujui akan tampil di sini untuk memudahkan pencatatan karyawan.
</div>
<div class="timeline-item">
<span class="timeline-dot approved"></span>
<div>
<strong>Pengajuan cuti tahunan</strong>
<p>Disetujui • 12 Jun 2026</p>
</div>
</div>
<div class="timeline-item">
<span class="timeline-dot pending"></span>
<div>
<strong>Pengajuan cuti penting</strong>
<p>Menunggu persetujuan • 20 Jun 2026</p>
</div>
</div>
</div>

<div class="cards">

<div class="card">
<div class="icon adminIcon">👨‍💼</div>
<h3>Admin</h3>
<p>Kelola pengajuan cuti dan proses persetujuan karyawan</p>
<a href="admin/login.php" class="btn admin">Login</a>
</div>

<div class="card">
<div class="icon userIcon">👤</div>
<h3>User</h3>
<p>Lihat status pengajuan cuti Anda secara real-time</p>
<a href="user/login.php" class="btn user">Login</a>
</div>

<div class="card">
<div class="icon cutiIcon">📝</div>
<h3>Pengajuan</h3>
<p>Ajukan cuti baru untuk kebutuhan pribadi atau penting</p>

<a href="form.php" class="btn cuti">Form</a>
<br><br>

<a href="uploads/upload_surat.php" class="btn" style="background:#0f766e;">
Upload
</a>

</div>

</div>

<div class="footer">
<div>© <?php echo date('Y'); ?> ET Store</div>
<div>Departemen HR & Administrasi</div>
</div>

</div>

<script>
function toggleTheme(){
document.body.classList.toggle('dark');

if(document.body.classList.contains('dark')){
localStorage.setItem('theme','dark');
}else{
localStorage.setItem('theme','light');
}
}

// load theme
if(localStorage.getItem('theme')==='dark'){
document.body.classList.add('dark');
}
</script>

</body>
</html>