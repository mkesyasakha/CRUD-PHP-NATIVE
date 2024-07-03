<?php
include '../db.php';

try {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        echo "<script>
                var tanya = confirm('Apakah anda ingin Menghapus Data ini?');
                if(!tanya) {
                window.location.href = 'view_customer.php';
            </script>";

        // Hapus data customer
        $sql = "DELETE FROM customers WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo "<script>
                alert('Data berhasil dihapus.');
                window.location.href = 'view_customer.php';
            </script>";
            exit();
        } else {
            throw new Exception("Gagal menghapus customer");
        }

        $stmt->close();
    } else {
        throw new Exception("ID tidak ditemukan.");
    }
} catch (Exception $e) {
    echo "<script>
        alert('Gagal menghapus customer');
        window.location.href = 'view_customer.php';
    </script>";
} finally {
    $conn->close();
}
?>
