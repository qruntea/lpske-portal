<?php
// api/jadwal-praktikum.php - Dengan struktur tabel yang benar
require_once '../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

class JadwalPraktikum {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getJadwal($filters = []) {
        // Query dengan JOIN yang benar sesuai struktur tabel
        $sql = "SELECT 
                    jp.id,
                    jp.mata_kuliah,
                    jp.kode_mk,
                    jp.semester,
                    jp.kelas,
                    jp.hari,
                    jp.waktu_mulai,
                    jp.waktu_selesai,
                    jp.ruangan,
                    jp.dosen_id,
                    jp.asisten_id,
                    jp.kapasitas,
                    jp.jumlah_mahasiswa,
                    jp.status,
                    jp.keterangan,
                    CONCAT(jp.waktu_mulai, ' - ', jp.waktu_selesai) AS jam_praktikum,
                    CASE jp.hari
                        WHEN 'senin' THEN 1 WHEN 'selasa' THEN 2 WHEN 'rabu' THEN 3
                        WHEN 'kamis' THEN 4 WHEN 'jumat' THEN 5 WHEN 'sabtu' THEN 6
                    END AS hari_urutan,
                    COALESCE(d.nama_dosen, 'Belum ditentukan') AS nama_dosen,
                    COALESCE(a.nama, 'Belum ditentukan') AS nama_asisten
                FROM jadwal_praktikum jp
                LEFT JOIN dosen d ON jp.dosen_id = d.user_id
                LEFT JOIN asisten a ON jp.asisten_id = a.id
                WHERE jp.status = 'aktif'";
        
        $params = [];
        
        if (!empty($filters['semester'])) {
            $sql .= " AND jp.semester = ?";
            $params[] = $filters['semester'];
        }
        
        if (!empty($filters['kelas'])) {
            $sql .= " AND jp.kelas = ?";
            $params[] = $filters['kelas'];
        }
        
        if (!empty($filters['hari'])) {
            $sql .= " AND jp.hari = ?";
            $params[] = $filters['hari'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (jp.mata_kuliah LIKE ? OR d.nama_dosen LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY jp.semester, jp.kelas, hari_urutan, jp.waktu_mulai";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Jika JOIN gagal, fallback ke query sederhana
            return $this->getJadwalSimple($filters);
        }
    }
    
    // Fallback method jika JOIN gagal
    private function getJadwalSimple($filters = []) {
        $sql = "SELECT 
                    jp.id,
                    jp.mata_kuliah,
                    jp.kode_mk,
                    jp.semester,
                    jp.kelas,
                    jp.hari,
                    jp.waktu_mulai,
                    jp.waktu_selesai,
                    jp.ruangan,
                    jp.dosen_id,
                    jp.asisten_id,
                    jp.kapasitas,
                    jp.jumlah_mahasiswa,
                    jp.status,
                    jp.keterangan,
                    CONCAT(jp.waktu_mulai, ' - ', jp.waktu_selesai) AS jam_praktikum,
                    CASE jp.hari
                        WHEN 'senin' THEN 1 WHEN 'selasa' THEN 2 WHEN 'rabu' THEN 3
                        WHEN 'kamis' THEN 4 WHEN 'jumat' THEN 5 WHEN 'sabtu' THEN 6
                    END AS hari_urutan,
                    'Dr. Dosen Praktikum' AS nama_dosen,
                    'Asisten Lab' AS nama_asisten
                FROM jadwal_praktikum jp
                WHERE jp.status = 'aktif'";
        
        $params = [];
        
        if (!empty($filters['semester'])) {
            $sql .= " AND jp.semester = ?";
            $params[] = $filters['semester'];
        }
        
        if (!empty($filters['kelas'])) {
            $sql .= " AND jp.kelas = ?";
            $params[] = $filters['kelas'];
        }
        
        if (!empty($filters['hari'])) {
            $sql .= " AND jp.hari = ?";
            $params[] = $filters['hari'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND jp.mata_kuliah LIKE ?";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY jp.semester, jp.kelas, hari_urutan, jp.waktu_mulai";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getJadwalById($id) {
        $sql = "SELECT 
                    jp.*,
                    CONCAT(jp.waktu_mulai, ' - ', jp.waktu_selesai) AS jam_praktikum,
                    COALESCE(d.nama_dosen, 'Belum ditentukan') AS nama_dosen,
                    COALESCE(a.nama, 'Belum ditentukan') AS nama_asisten
                FROM jadwal_praktikum jp
                LEFT JOIN dosen d ON jp.dosen_id = d.user_id
                LEFT JOIN asisten a ON jp.asisten_id = a.id
                WHERE jp.id = ?";
        
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                // Fallback tanpa JOIN
                $sql_simple = "SELECT *, 
                              CONCAT(waktu_mulai, ' - ', waktu_selesai) AS jam_praktikum,
                              'Dr. Dosen Praktikum' AS nama_dosen,
                              'Asisten Lab' AS nama_asisten
                              FROM jadwal_praktikum WHERE id = ?";
                $stmt = $this->conn->prepare($sql_simple);
                $stmt->execute([$id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            return $result;
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
    
    public function createJadwal($data) {
        $sql = "INSERT INTO jadwal_praktikum (mata_kuliah, kode_mk, semester, kelas, hari, waktu_mulai, waktu_selesai, ruangan, dosen_id, asisten_id, kapasitas, keterangan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                $data['mata_kuliah'], $data['kode_mk'], $data['semester'], $data['kelas'],
                $data['hari'], $data['waktu_mulai'], $data['waktu_selesai'], $data['ruangan'],
                $data['dosen_id'], $data['asisten_id'], $data['kapasitas'], $data['keterangan']
            ]);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    $jadwalPraktikum = new JadwalPraktikum($pdo);
    
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $result = $jadwalPraktikum->getJadwalById($_GET['id']);
            } else {
                $filters = [
                    'semester' => $_GET['semester'] ?? '',
                    'kelas' => $_GET['kelas'] ?? '',
                    'hari' => $_GET['hari'] ?? '',
                    'search' => $_GET['search'] ?? ''
                ];
                $result = $jadwalPraktikum->getJadwal($filters);
            }
            echo json_encode(['success' => true, 'data' => $result]);
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $jadwalPraktikum->createJadwal($data);
            echo json_encode(['success' => $result, 'message' => $result ? 'Jadwal berhasil ditambahkan' : 'Gagal menambahkan jadwal']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>