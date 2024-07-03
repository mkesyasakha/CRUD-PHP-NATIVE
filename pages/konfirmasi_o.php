<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mendapatkan informasi customer untuk konfirmasi
    $sql = "SELECT id FROM order_details WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($namaCategories);
    $stmt->fetch();
    $stmt->close();

    if (empty($namaCategories)) {
        echo "<script>
            alert('ID orders tidak ditemukan.');
            window.location.href = 'view_orders.php';
            </script>";
        exit();
    }

    // Menampilkan konfirmasi penghapusan menggunakan JavaScript
    echo "<script>
            var tanya = confirm('Apakah Anda yakin ingin menghapus data Orders');
            if (tanya) {
                window.location.href = 'delete_order.php?id={$id}';
            } else {
                window.location.href = 'view_orders.php';
            }
        </script>";
} else {
    echo "<script>
        alert('ID tidak ditemukan.');
        window.location.href = 'view_orders.php';
        </script>";
}
?>
