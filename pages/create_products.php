<?php
include '../db.php';
include 'index.php';

$name = $price = $category_id = '';
$nameErr = $priceErr = $categoryErr = '';

try {
    // Ambil data kategori
    $category_query = "SELECT id, name FROM categories";
    $category_result = $conn->query($category_query);

    if (!$category_result) {
        throw new Exception("Error fetching categories: " . $conn->error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validasi nama
        if (empty($_POST['name'])) {
            $nameErr = "Name is required";
        } else {
            $name = $_POST['name'];
        }

        // Validasi harga
        if (empty($_POST['price'])) {
            $priceErr = "Price is required";
        } else {
            $price = $_POST['price'];
        }

        // Validasi category_id
        if (empty($_POST['category_id'])) {
            $categoryErr = "Category ID is required";
        } else {
            $category_id = $_POST['category_id'];
        }

        // Jika tidak ada error validasi, lanjutkan dengan proses insert
        if (empty($nameErr) && empty($priceErr) && empty($categoryErr)) {
            $sql = "INSERT INTO products (name, price, category_id) VALUES ('$name', '$price', '$category_id')";

            if ($conn->query($sql) === TRUE) {
                header("Location: view_products.php");
            } else {
                throw new Exception("Error inserting product: " . $conn->error);
            }
        }
    }
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger' role='alert'>" . $e->getMessage() . "</div>";
} finally {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Products</title>
</head>
<body>
<div class="container">
    <h2>Add Products</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name">
            <?php if (!empty($nameErr)): ?>
                <div class="alert alert-danger"><?php echo $nameErr; ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" class="form-control" id="price" name="price">
            <?php if (!empty($priceErr)): ?>
                <div class="alert alert-danger"><?php echo $priceErr; ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="category_id">Category ID:</label>
            <select class="form-control" id="category_id" name="category_id">
                <option value="">Select category</option>
                <?php
                if (isset($category_result) && $category_result->num_rows > 0) {
                    while ($row = $category_result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['id'] . " - " . $row['name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No categories found</option>";
                }
                ?>
            </select>
            <?php if (!empty($categoryErr)): ?>
                <div class="alert alert-danger"><?php echo $categoryErr; ?></div>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
</body>
</html>
