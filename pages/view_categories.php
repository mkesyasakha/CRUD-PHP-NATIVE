<?php
include '../db.php';
include 'index.php';

// Query untuk menampilkan data kategori
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
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
            if ($result->num_rows > 0) {
                $nomor = 1; // Inisialisasi nomor urut
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $nomor . "</td>"; // Menampilkan nomor urut
                    echo "<td>" . htmlspecialchars($row["name"]) . "</td>"; // Menghindari XSS dengan htmlspecialchars
                    echo "<td>
                        <a href='update_categories.php?id=" . $row["id"] . "' class='btn btn-warning'>Edit</a>
                        <a href='konfirmasi_ct.php?id=" . $row["id"] . "' class='btn btn-danger'>Delete</a>
                        </td>";
                    echo "</tr>";
                    $nomor++; // Increment nomor urut
                }
            } else {
                echo "<tr><td colspan='3'>No categories found</td></tr>";
            }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
