<?php
include '../db.php';
include 'index.php';

try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Hapus data produk
        $sql = "DELETE FROM products WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Product successfully deleted.'); window.location.href='view_products.php';</script>";
            exit(); // Exit agar script berhenti di sini setelah alert ditampilkan
        } else {
            throw new Exception("Failed to delete product: " );
        }

        $stmt->close();
    } else {
        throw new Exception("ID not found.");
    }
} catch (Exception $e) {
    echo "<script>alert('Failed to delete product'); window.location.href='view_products.php';</script>";
} finally {
    $conn->close();
}
?>
