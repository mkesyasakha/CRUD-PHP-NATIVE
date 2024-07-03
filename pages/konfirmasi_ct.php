<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mendapatkan informasi customer untuk konfirmasi
    $sql = "SELECT name FROM categories WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($namaCategories);
    $stmt->fetch();
    $stmt->close();

    if (empty($namaCategories)) {
        echo "<script>
            alert('ID categories tidak ditemukan.');
            window.location.href = 'view_customer.php';
            </script>";
        exit();
    }

    // Menampilkan konfirmasi penghapusan menggunakan JavaScript
    echo "<script>
            var tanya = confirm('Apakah Anda yakin ingin menghapus data categories \"" . $namaCategories . "\"?');
            if (tanya) {
                window.location.href = 'delete_categories.php?id={$id}';
            } else {
                window.location.href = 'view_categories.php';
            }
        </script>";
} else {
    echo "<script>
        alert('ID tidak ditemukan.');
        window.location.href = 'view_categories.php';
        </script>";
}
?>
