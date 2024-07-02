<?php
include '../db.php';
include 'index.php';

try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Hapus data customer
        $sql = "DELETE FROM customers WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header("Location: view_customer.php");
        } else {
            throw new Exception("Gagal menghapus customer: " . $stmt->error);
        }

        $stmt->close();
    } else {
        throw new Exception("ID tidak ditemukan.");
    }
} catch (Exception $e) {
    echo "<script>alert('Gagal Menghapus Customers'); window.location.href='view_customer.php';</script>";
} finally {
    $conn->close();
}
?>
