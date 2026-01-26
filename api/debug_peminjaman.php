<?php
// ===================================================================
// FILE: api/debug_peminjaman.php - UNTUK DEBUGGING
// ===================================================================

header('Content-Type: application/json');

try {
    require_once '../config/database.php';
    
    echo json_encode([
        'database_check' => 'OK',
        'tables' => [
            'peminjaman_structure' => $pdo->query("DESCRIBE peminjaman")->fetchAll(PDO::FETCH_ASSOC),
            'inventory_structure' => $pdo->query("DESCRIBE inventory")->fetchAll(PDO::FETCH_ASSOC),
            'users_structure' => $pdo->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC)
        ],
        'sample_data' => [
            'peminjaman_count' => $pdo->query("SELECT COUNT(*) FROM peminjaman")->fetchColumn(),
            'inventory_count' => $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn(),
            'users_count' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn()
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}

?>