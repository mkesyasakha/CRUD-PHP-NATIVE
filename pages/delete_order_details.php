<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus semua orders terkait dengan customer terlebih dahulu
    // $sql = "DELETE FROM customers WHERE id=?";
    // $stmt = $conn->prepare($sql);
    // $stmt->bind_param("i", $id);
    // $stmt->execute();

    // Setelah menghapus orders terkait, hapus customer
    $sql = "DELETE FROM order_details WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: view_order_details.php");
} else {
    echo "Error: ";
}
?>
