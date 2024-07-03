<?php
include '../db.php';
include 'index.php';

try {
    // Ambil data jumlah pesanan dan total harga dari setiap customer
    $query = "
        SELECT customers.id, customers.name, 
               IFNULL(SUM(order_details.quantity), 0) AS total_quantity, 
               IFNULL(SUM(products.price * order_details.quantity), 0) AS total_price
        FROM customers
        LEFT JOIN orders ON customers.id = orders.customer_id
        LEFT JOIN order_details ON orders.id = order_details.order_id
        LEFT JOIN products ON order_details.product_id = products.id
        GROUP BY customers.id, customers.name
    ";
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Error fetching order and price totals: " . $conn->error);
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger' role='alert'>" . $e->getMessage() . "</div>";
} finally {
    $conn->close();
}
?>

<div class="container mt-4">
    <h2 class="text-center">Total Pesanan dan Harga dari Setiap Customer</h2>
    <table class="table table-striped table-dark">
        <thead>
            <tr>
                <th>No</th>
                <th>Customer Name</th>
                <th>Total Quantity</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $nomor = 0;
                while ($row = $result->fetch_assoc()) {
                    $nomor++;
                    echo "<tr>";
                    echo "<td>" . $nomor . "</td>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['total_quantity'] . "</td>";
                    echo "<td>" . number_format($row['total_price'], 2) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No customers found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
