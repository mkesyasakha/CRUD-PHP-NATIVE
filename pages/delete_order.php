<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // Dapatkan order_id terkait dari tabel order_details
        $sql_get_order_id = "SELECT order_id FROM order_details WHERE id=?";
        $stmt_get_order_id = $conn->prepare($sql_get_order_id);
        $stmt_get_order_id->bind_param("i", $id);
        $stmt_get_order_id->execute();
        $stmt_get_order_id->store_result();

        if ($stmt_get_order_id->num_rows > 0) {
            $stmt_get_order_id->bind_result($order_id);
            $stmt_get_order_id->fetch();

            // Hapus data dari tabel order_details
            $sql_order_details = "DELETE FROM order_details WHERE id=?";
            $stmt_order_details = $conn->prepare($sql_order_details);
            $stmt_order_details->bind_param("i", $id);
            $stmt_order_details->execute();

            // Hapus data dari tabel orders berdasarkan order_id
            if ($order_id) {
                $sql_orders = "DELETE FROM orders WHERE id=?";
                $stmt_orders = $conn->prepare($sql_orders);
                $stmt_orders->bind_param("i", $order_id);
                $stmt_orders->execute();
            }
        } else {
            throw new Exception("No order found for the given ID.");
        }

        // Commit transaksi
        $conn->commit();

        // Redirect ke halaman view_orders.php setelah penghapusan berhasil
        echo "<script>
                alert('Data berhasil dihapus.');
                window.location.href = 'view_orders.php';
            </script>";
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Error: No ID provided.";
}

$conn->close();
?>
