<?php
include '../db.php';
include 'index.php';

try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Hapus data kategori
        $sql = "DELETE FROM categories WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo "<script>
                alert('Data berhasil dihapus.');
                window.location.href = 'view_categories.php';
            </script>";
            exit();
        } else {
            throw new Exception("Gagal menghapus kategori: " . $stmt->error);
        }

        $stmt->close();
    } else {
        throw new Exception("ID tidak ditemukan.");
    }
} catch (Exception $e) {
    echo "<script>
        alert('Gagal Menghapus Kategori ');
        window.location.href = 'view_categories.php';
    </script>";
} finally {
    $conn->close();
}
?>
