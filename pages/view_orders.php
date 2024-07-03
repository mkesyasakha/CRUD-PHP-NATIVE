<?php
include '../db.php';
include 'index.php';

// Query untuk mendapatkan data dari tabel order_details dengan join ke tabel orders dan products
$sql = "SELECT od.id, od.order_id, od.product_id, od.quantity, od.price, o.customer_id, o.order_date, p.name AS product_name, c.name as customer_name
        FROM order_details od
        JOIN products p ON od.product_id = p.id
        JOIN orders o ON od.order_id = o.id
        JOIN customers c ON o.customer_id = c.id";
$result = $conn->query($sql);
?>

<div class="container">
    <h2>Order Details</h2>
    <a href="create_orders.php" class="btn btn-primary mb-2">Add Order Details</a>
    <table class="table table-striped table-hover table-dark" style="border-radius: 12px;">
        <thead>
            <tr>
                <th>No</th>
                <th>Customer</th>
                <th>Product Name</th>
                <th>Order Date</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Actions</th>
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
                    echo "<td>" . $row['customer_name'] . "</td>";
                    echo "<td>" . $row['product_name'] . "</td>";
                    echo "<td>" . $row['order_date'] . "</td>";
                    echo "<td>" . $row['quantity'] . "</td>";
                    echo "<td> Rp." . $row['price'] . "</td>";
                    echo "<td>
                        <a href='update_order.php?id=" . $row["id"] . "' class='btn btn-warning'>Edit</a>
                        <a href='konfirmasi_o.php?id=" . $row["id"] . "' class='btn btn-danger'>Delete</a>
                        </td>";
                        
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No records found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
$conn->close();
?>

