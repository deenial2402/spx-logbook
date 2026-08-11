<?php
header('Content-Type: application/json');
require_once 'config.php';

$action = $_GET['action'] ?? '';

// Helper Upload Image
function uploadBase64Image($base64_string, $prefix) {
    if (empty($base64_string)) return null;
    $folderPath = "uploads/";
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true);
    }
    $image_parts = explode(";base64,", $base64_string);
    if(count($image_parts) < 2) return null;
    
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1] ?? 'jpeg';
    $image_base64 = base64_decode($image_parts[1]);
    $file_name = $prefix . '_' . uniqid() . '.' . $image_type;
    $file = $folderPath . $file_name;
    
    file_put_contents($file, $image_base64);
    return $file;
}

switch($action) {
    
    case 'login':
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $conn->real_escape_string($data['username'] ?? '');
        $password = $data['password'] ?? '';

        $query = $conn->query("SELECT * FROM users WHERE username = '$username'");
        if ($query->num_rows > 0) {
            $user = $query->fetch_assoc();
            // password_verify atau plain fallback (untuk migrasi cepat)
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['username'] = $user['username'];
                echo json_encode(["status" => true, "role" => $user['role'], "message" => "Login Berhasil"]);
            } else {
                echo json_encode(["status" => false, "message" => "Password Salah!"]);
            }
        } else {
            echo json_encode(["status" => false, "message" => "Username Tidak Ditemukan!"]);
        }
        break;

    case 'logout':
        session_destroy();
        echo json_encode(["status" => true]);
        break;

    case 'get_assets':
        $result = $conn->query("SELECT * FROM asset_master ORDER BY id DESC");
        $assets = [];
        while($row = $result->fetch_assoc()) {
            $assets[] = [
                'assetId' => $row['asset_id'],
                'sn' => $row['sn'],
                'name' => $row['name']
            ];
        }
        echo json_encode(["status" => true, "data" => $assets]);
        break;

    case 'add_asset':
        if (($_SESSION['user_role'] ?? '') !== 'Super Admin') {
            echo json_encode(["status" => false, "message" => "Akses Ditolak!"]);
            exit();
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $assetId = strtoupper($conn->real_escape_string($data['assetId']));
        $sn = strtoupper($conn->real_escape_string($data['sn']));
        $name = $conn->real_escape_string($data['name']);

        $sql = "INSERT INTO asset_master (asset_id, sn, name) VALUES ('$assetId', '$sn', '$name')";
        if ($conn->query($sql)) {
            echo json_encode(["status" => true, "message" => "Asset berhasil ditambahkan"]);
        } else {
            echo json_encode(["status" => false, "message" => "Asset ID atau SN sudah terdaftar"]);
        }
        break;

    case 'delete_asset':
        if (($_SESSION['user_role'] ?? '') !== 'Super Admin') {
            echo json_encode(["status" => false, "message" => "Akses Ditolak!"]);
            exit();
        }
        $assetId = $conn->real_escape_string($_GET['id'] ?? '');
        $conn->query("DELETE FROM asset_master WHERE asset_id = '$assetId'");
        echo json_encode(["status" => true, "message" => "Asset berhasil dihapus"]);
        break;

    case 'get_logs':
        $result = $conn->query("SELECT * FROM asset_logs ORDER BY id DESC");
        $logs = [];
        while($row = $result->fetch_assoc()) {
            $logs[] = [
                'id' => (int)$row['id'],
                'nama' => $row['nama'],
                'opsId' => $row['ops_id'],
                'snAsset' => $row['sn_asset'],
                'status' => $row['status'],
                'isoDate' => $row['date_pinjam'],
                'photo' => $row['photo_pinjam'],
                'returnIsoDate' => $row['date_kembali'],
                'returnPhoto' => $row['photo_kembali']
            ];
        }
        echo json_encode(["status" => true, "data" => $logs]);
        break;

    case 'submit_transaction':
        $data = json_decode(file_get_contents('php://input'), true);
        $nama = $conn->real_escape_string($data['nama']);
        $opsId = strtoupper($conn->real_escape_string($data['opsId']));
        $snInput = strtoupper($conn->real_escape_string($data['snAsset']));
        $status = $data['status'];
        $photoData = $data['photo'];

        // Cek Keberadaan Asset
        $checkAsset = $conn->query("SELECT sn FROM asset_master WHERE RIGHT(sn, 5) = '$snInput' OR UPPER(sn) = '$snInput'");
        if ($checkAsset->num_rows == 0) {
            echo json_encode(["status" => false, "message" => "Serial Number tidak ditemukan di master asset!"]);
            exit();
        }
        $fullSN = $checkAsset->fetch_assoc()['sn'];

        // Cek Transaksi Aktif
        $activeCheck = $conn->query("SELECT * FROM asset_logs WHERE status = 'Pinjam' AND (UPPER(ops_id) = '$opsId' OR UPPER(sn_asset) = '$fullSN')");
        
        if ($status === 'Pinjam') {
            if ($activeCheck->num_rows > 0) {
                echo json_encode(["status" => false, "message" => "Asset sedang dipinjam atau Ops ID masih memiliki transaksi aktif!"]);
                exit();
            }
            $filePath = uploadBase64Image($photoData, 'borrow');
            $stmt = $conn->prepare("INSERT INTO asset_logs (nama, ops_id, sn_asset, status, photo_pinjam) VALUES (?, ?, ?, 'Pinjam', ?)");
            $stmt->bind_param("ssss", $nama, $opsId, $fullSN, $filePath);
            $stmt->execute();
            echo json_encode(["status" => true, "message" => "Peminjaman berhasil dicatat!"]);

        } else if ($status === 'Kembali') {
            if ($activeCheck->num_rows == 0) {
                echo json_encode(["status" => false, "message" => "Tidak ada catatan peminjaman aktif!"]);
                exit();
            }
            $activeLog = $activeCheck->fetch_assoc();
            $filePath = uploadBase64Image($photoData, 'return');
            $now = date('Y-m-d H:i:s');
            
            $stmt = $conn->prepare("UPDATE asset_logs SET status = 'Kembali', photo_kembali = ?, date_kembali = ? WHERE id = ?");
            $stmt->bind_param("ssi", $filePath, $now, $activeLog['id']);
            $stmt->execute();
            echo json_encode(["status" => true, "message" => "Pengembalian berhasil diperbarui!"]);
        }
        break;

    case 'delete_log':
        if (($_SESSION['user_role'] ?? '') !== 'Super Admin') {
            echo json_encode(["status" => false, "message" => "Akses Ditolak!"]);
            exit();
        }
        $logId = (int)($_GET['id'] ?? 0);
        $conn->query("DELETE FROM asset_logs WHERE id = $logId");
        echo json_encode(["status" => true, "message" => "Log berhasil dihapus"]);
        break;

    default:
        echo json_encode(["status" => false, "message" => "Invalid Action"]);
        break;
}
?>