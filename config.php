<?php
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'cuti_db';

function getConnection()
{
    global $host, $user, $pass, $dbname;

    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        throw new Exception('Koneksi database gagal: ' . $conn->connect_error);
    }

    return $conn;
}

function ensureDefaultAdmin($conn)
{
    $result = $conn->query("SELECT COUNT(*) AS total FROM admin_users");
    $row = $result->fetch_assoc();

    if ((int) $row['total'] === 0) {
        $username = 'admin';
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $stmt->close();
    }
}
?>
