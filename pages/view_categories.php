<?php
include '../db.php';
include 'index.php';

// Menampilkan data pelanggan
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
?>
<div class="container">
    <h2>Categories</h2>
    <a href="create_categories.php" class="btn btn-primary mb-2">Add Categories</a>
    <table class="table table-striped table-hover table-dark" style="border-radius: 12px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
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
                    echo "<td>
                        <a href='update_categories.php?id=" . $row["id"] . "' class='btn btn-warning'>Edit</a>
                        <a href='delete_categories.php?id=" . $row["id"] . "' class='btn btn-danger'>Delete</a>
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
</body>
</html>
