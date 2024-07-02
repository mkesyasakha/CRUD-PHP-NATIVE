<?php
include '../db.php';
include 'index.php';

try {
// Menampilkan data pelanggan
$sql = "SELECT products.id, products.name, products.price, categories.name as category_name
        FROM products
        JOIN categories ON products.category_id = categories.id";
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
    <h2>Product</h2>
    <a href="create_products.php" class="btn btn-primary mb-2">Add Product</a>
    <table class="table table-striped table-hover table-dark" style="border-radius: 12px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Category Name</th>
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
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td> Rp." . $row["price"] . "</td>";
                    echo "<td>" . $row["category_name"] . "</td>";
                    echo "<td>
                        <a href='update_products.php?id=" . $row["id"] . "' class='btn btn-warning'>Edit</a>
                        <a href='delete_products.php?id=" . $row["id"] . "' class='btn btn-danger'>Delete</a>
                        </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No products found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
