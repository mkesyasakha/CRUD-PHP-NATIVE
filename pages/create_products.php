<?php
include '../db.php';
include 'index.php';

$name = $price = $category_id = '';
$nameErr = $priceErr = $categoryErr = '';
$formValid = true; // Variabel untuk melacak validasi form

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
            $formValid = false; // Form tidak valid
        } else {
            $name = $_POST['name'];
        }

        // Validasi harga
        if (empty($_POST['price'])) {
            $priceErr = "Price is required";
            $formValid = false; // Form tidak valid
        } else {
            $price = $_POST['price'];
            if ($price < 0) {
                $priceErr = "Price cannot be negative";
                $formValid = false; // Form tidak valid
            }
        }

        // Validasi category_id
        if (empty($_POST['category_id'])) {
            $categoryErr = "Category ID is required";
            $formValid = false; // Form tidak valid
        } else {
            $category_id = $_POST['category_id'];
        }

        // Jika form valid, lanjutkan dengan proses insert
        if ($formValid) {
            // Cek duplikasi nama produk
            $checkSql = "SELECT * FROM products WHERE name=?";
            $stmt = $conn->prepare($checkSql);
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<script>alert('Product dengan nama ini sudah ada');</script>";
            } else {
                // Insert data produk
                $sql = "INSERT INTO products (name, price, category_id) VALUES ('$name', '$price', '$category_id')";

                if ($conn->query($sql) === TRUE) {
                    $success_message = "Product added successfully!";
                    echo "<script>alert('$success_message'); window.location.href='view_products.php';</script>";
                } else {
                    throw new Exception("Error inserting product: " . $conn->error);
                }
            }
        } else {
            echo "<script>alert('Isi Data Dengan Valid');</script>";
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
            <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>">
            
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" class="form-control" id="price" name="price" value="<?php echo isset($_POST['price']) ? $_POST['price'] : ''; ?>">
            
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
           
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<script>
    // Menampilkan alert jika form tidak diisi
    if (document.querySelector('.alert-danger')) {
        alert('Please fill in all required fields');
    }
    function validateForm() {
        var name = document.getElementById('name').value.trim();
        var price = document.getElementById('price').value.trim();

        if (name === '' || price === '') {
            alert('Name dan Price harus diisi');
            return false;
        }

        if( price < 0){
            alert('Price cannot be negative');
            return false;
        }

        return true;
    }
</script>
</body>
</html>
