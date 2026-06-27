<?php
session_start();
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';

try {
    $conn = getConnection();
    ensureDefaultAdmin($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            if ($_POST['action'] === 'delete') {
                $stmt = $conn->prepare("DELETE FROM cuti_pengajuan WHERE id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $message = 'Data berhasil dihapus.';
            } else {
                $status = $_POST['status'] ?? 'pending';
                $stmt = $conn->prepare("UPDATE cuti_pengajuan SET status = ? WHERE id = ?");
                $stmt->bind_param('si', $status, $id);
                $stmt->execute();
                $message = 'Status berhasil diperbarui.';
            }
        }
    }

    $result = $conn->query("SELECT * FROM cuti_pengajuan ORDER BY created_at DESC");
    $requests = $result->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $message = $e->getMessage();
    $requests = [];
}

function countStatus($data, $status){
    return count(array_filter($data, fn($x) => $x['status'] === $status));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin</title>

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
padding:0;
color:white;
position:relative;
overflow-x:hidden;
}

body::before,
body::after{
content:"";
position:absolute;
border-radius:50%;
filter:blur(120px);
opacity:.25;
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
0%,100%{transform:translateY(0);}50%{transform:translateY(-30px);}
}

@keyframes bg{
0%{background-position:0% 50%;}
50%{background-position:100% 50%;}
100%{background-position:0% 50%;}
}

/* LAYOUT */
.wrapper{
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
margin-bottom:8px;
}

.sidebar .brand small{
font-size:12px;
opacity:.75;
display:block;
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
position:relative;
z-index:2;
}

.topbar h1{
font-size:26px;
font-weight:700;
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

.topbar a{
color:#bfdbfe;
text-decoration:none;
font-weight:600;
}

/* STATS */
.stats{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:15px;
margin-bottom:20px;
}

.stat{
background:rgba(255,255,255,.12);
backdrop-filter:blur(20px);
border:1px solid rgba(255,255,255,.16);
padding:16px;
border-radius:16px;
text-align:center;
transition:.3s;
box-shadow:0 8px 25px rgba(0,0,0,.10);
}

.stat:hover{
transform:translateY(-5px);
}

.stat h2{
font-size:24px;
}

/* SUMMARY */
.summary-grid{
display:grid;
grid-template-columns:repeat(2,1fr);
gap:15px;
margin-bottom:18px;
}

.summary-card{
background:rgba(255,255,255,.10);
padding:16px 18px;
border-radius:16px;
border:1px solid rgba(255,255,255,.14);
box-shadow:0 6px 18px rgba(0,0,0,.08);
}

.summary-title{
font-size:14px;
font-weight:700;
margin-bottom:6px;
}

.summary-text{
font-size:13px;
opacity:.9;
line-height:1.5;
}

/* SEARCH */
.searchBox{
margin-bottom:15px;
}

.searchBox input{
width:100%;
padding:13px 14px;
border-radius:12px;
border:1px solid rgba(255,255,255,.16);
outline:none;
background:rgba(255,255,255,.14);
color:white;
box-shadow:0 4px 16px rgba(0,0,0,.08);
}

.searchBox input::placeholder{
color:rgba(255,255,255,.7);
}

/* CARD */
.card{
background:linear-gradient(145deg,rgba(255,255,255,.14),rgba(255,255,255,.10));
backdrop-filter:blur(25px);
border:1px solid rgba(255,255,255,.16);
border-radius:20px;
padding:16px;
overflow:auto;
box-shadow:0 12px 35px rgba(0,0,0,.16);
position:relative;
z-index:2;
}

/* TABLE */
table{
width:100%;
border-collapse:collapse;
min-width:900px;
}

thead th{
position:sticky;
top:0;
background:rgba(0,0,0,.25);
backdrop-filter:blur(10px);
z-index:2;
}

th,td{
padding:12px;
text-align:left;
font-size:13px;
border-bottom:1px solid rgba(255,255,255,.1);
}

tr:hover{
background:rgba(255,255,255,.05);
}

/* BADGE */
.badge{
padding:5px 10px;
border-radius:999px;
font-size:12px;
font-weight:700;
letter-spacing:.3px;
}

.pending{background:#facc15;color:#000;}
.approved{background:#22c55e;color:#000;}
.rejected{background:#ef4444;color:#fff;}

/* BUTTON ICON STYLE */
.btn{
border:none;
padding:8px 10px;
border-radius:10px;
cursor:pointer;
font-size:12px;
font-weight:700;
margin-right:5px;
transition:.2s;
box-shadow:0 6px 12px rgba(0,0,0,.15);
}

.btn:hover{
transform:scale(1.05);
}

.approve{background:#22c55e;color:#000;}
.reject{background:#ef4444;color:#fff;}
.delete{background:#6b7280;color:#fff;}

/* MESSAGE */
.message{
margin-bottom:15px;
padding:12px 14px;
border-radius:12px;
background:rgba(34,197,94,.2);
border:1px solid rgba(34,197,94,.3);
box-shadow:0 6px 18px rgba(0,0,0,.10);
}

/* RESPONSIVE */
@media(max-width:900px){
.stats{
grid-template-columns:repeat(2,1fr);
}
}

@media(max-width:600px){
.stats{
grid-template-columns:1fr;
}
}
</style>
</head>

<body>
<div class="wrapper">
<div class="sidebar">
<div class="brand">ET Store<span><small>Admin Panel</small></span></div>
<div class="menu">
<a href="#" class="active">📊 Dashboard</a>

<a href="logout.php">🚪 Logout</a>
</div>
</div>

<div class="main">
<div class="topbar">
<h1>📊 Admin Dashboard</h1>
<div class="user-pill">
<div class="avatar">A</div>
<div>
<div><?php echo htmlspecialchars($_SESSION['admin_user']); ?></div>
<div class="status">Online</div>
</div>
</div>
</div>

<?php if($message): ?>
<div class="message"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<!-- STATS -->
<div class="stats">
<div class="stat"><h2><?php echo count($requests); ?></h2><p>Total</p></div>
<div class="stat"><h2><?php echo countStatus($requests,'pending'); ?></h2><p>Pending</p></div>
<div class="stat"><h2><?php echo countStatus($requests,'approved'); ?></h2><p>Approved</p></div>
<div class="stat"><h2><?php echo countStatus($requests,'rejected'); ?></h2><p>Rejected</p></div>
</div>

<div class="summary-grid">
<div class="summary-card">
<div class="summary-title">Notifikasi</div>
<div class="summary-text">Ada <?php echo countStatus($requests,'pending'); ?> pengajuan yang menunggu persetujuan.</div>
</div>
<div class="summary-card">
<div class="summary-title">Status Hari Ini</div>
<div class="summary-text">Admin dapat mengelola cuti karyawan secara cepat dan terkontrol.</div>
</div>
</div>

<!-- SEARCH -->
<div class="searchBox">
<input type="text" id="search" placeholder="Cari nama, email, jenis cuti...">
</div>

<div class="summary-card" id="lampiran" style="margin-bottom:15px;">
<div class="summary-title">📂 Lampiran Terbaru</div>
<?php $attachments = array_filter($requests, fn($item) => !empty($item['surat_path'])); ?>
<?php if (!empty($attachments)): ?>
<ul style="margin:8px 0 0 18px; line-height:1.7;">
<?php foreach (array_slice($attachments, 0, 5) as $attachment): ?>
<li>
<a href="../uploads/<?php echo rawurlencode($attachment['surat_path']); ?>" target="_blank" style="color:#bfdbfe;">
<?php echo htmlspecialchars($attachment['nama']); ?> — <?php echo htmlspecialchars($attachment['surat_path']); ?>
</a>
</li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<div class="summary-text">Belum ada lampiran yang diunggah.</div>
<?php endif; ?>
</div>

<div class="card" id="pengajuan">

<table id="table">
<thead>
<tr>
<th>Nama</th>
<th>Email</th>
<th>Jenis</th>
<th>Periode</th>
<th>Status</th>
<th>Lampiran</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

<?php foreach($requests as $r): ?>
<tr>
<td><?php echo htmlspecialchars($r['nama']); ?></td>
<td><?php echo htmlspecialchars($r['email']); ?></td>
<td><?php echo htmlspecialchars($r['jenis_cuti']); ?></td>
<td><?php echo $r['tanggal_mulai'].' - '.$r['tanggal_selesai']; ?></td>

<td>
<span class="badge <?php echo $r['status']; ?>">
<?php echo strtoupper($r['status']); ?>
</span>
</td>

<td>
<?php if (!empty($r['surat_path'])): ?>
<a href="../uploads/<?php echo rawurlencode($r['surat_path']); ?>" target="_blank" style="color:#bfdbfe;">Lihat</a>
<?php else: ?>-
<?php endif; ?>
</td>

<td>
<form method="post" style="display:inline;">
<input type="hidden" name="id" value="<?php echo $r['id']; ?>">
<input type="hidden" name="status" value="approved">
<input type="hidden" name="action" value="update">
<button class="btn approve">✔</button>
</form>

<form method="post" style="display:inline;">
<input type="hidden" name="id" value="<?php echo $r['id']; ?>">
<input type="hidden" name="status" value="rejected">
<input type="hidden" name="action" value="update">
<button class="btn reject">✖</button>
</form>

<form method="post" style="display:inline;" onsubmit="return confirm('Hapus data?')">
<input type="hidden" name="id" value="<?php echo $r['id']; ?>">
<input type="hidden" name="action" value="delete">
<button class="btn delete">🗑</button>
</form>

</td>
</tr>
<?php endforeach; ?>

</tbody>
</table>

</div>
</div>

<script>
// SEARCH FILTER
document.getElementById("search").addEventListener("keyup", function() {
let value = this.value.toLowerCase();
let rows = document.querySelectorAll("#table tbody tr");

rows.forEach(row => {
row.style.display = row.innerText.toLowerCase().includes(value)
? ""
: "none";
});
});
</script>

</body>
</html>