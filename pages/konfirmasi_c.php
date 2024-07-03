<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mendapatkan informasi customer untuk konfirmasi
    $sql = "SELECT name FROM customers WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($namaCustomer);
    $stmt->fetch();
    $stmt->close();

    if (empty($namaCustomer)) {
        echo "<script>
            alert('ID customer tidak ditemukan.');
            window.location.href = 'view_customer.php';
            </script>";
        exit();
    }

    // Menampilkan konfirmasi penghapusan menggunakan JavaScript
    echo "<script>
            var tanya = confirm('Apakah Anda yakin ingin menghapus data customer \"" . $namaCustomer . "\"?');
            if (tanya) {
                window.location.href = 'delete_customer.php?id={$id}';
            } else {
                window.location.href = 'view_customer.php';
            }
        </script>";
} else {
    echo "<script>
        alert('ID tidak ditemukan.');
        window.location.href = 'view_customer.php';
        </script>";
}
?>
