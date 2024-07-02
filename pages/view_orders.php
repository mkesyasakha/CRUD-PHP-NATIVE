<?php
include '../db.php';
include 'index.php';

try {
    // Query untuk mendapatkan data orders beserta nama customer
    $sql = "SELECT orders.id, customers.name as customer_name, orders.order_date
            FROM orders
            JOIN customers ON orders.customer_id = customers.id";
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Error fetching orders: " . $conn->error);
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger' role='alert'>" . $e->getMessage() . "</div>";
} finally {
    $conn->close();
}
?>

<div class="container">
    <h2>Order</h2>
    <a href="create_orders.php" class="btn btn-primary mb-2">Add Order</a>
    <table class="table table-striped table-hover table-dark" style="border-radius: 12px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($result) && $result->num_rows > 0) {
                $nomor = 0 ;
                while ($row = $result->fetch_assoc()) {
                    $nomor ++;
                    echo "<tr>";
                    echo "<td>" . $nomor . "</td>";
                    echo "<td>" . $row["customer_name"] . "</td>";
                    echo "<td>" . $row["order_date"] . "</td>";
                    echo "<td>
                        <a href='update_orders.php?id=" . $row["id"] . "' class='btn btn-warning'>Edit</a>
                        <a href='delete_orders.php?id=" . $row["id"] . "' class='btn btn-danger'>Delete</a>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No orders found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
