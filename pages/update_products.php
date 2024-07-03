<?php
include '../db.php';
include 'index.php';

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    
    // Validasi harga untuk memastikan tidak negatif
    // if ($price < 0) {
    //     echo "<script>alert('Price cannot be negative');</script>";
    
        // Validasi nama produk untuk memastikan tidak ada duplikat
        $checkDuplicateSql = "SELECT id FROM products WHERE name=? AND id != ?";
        $stmtCheck = $conn->prepare($checkDuplicateSql);
        $stmtCheck->bind_param("si", $name, $id);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            echo "<script>alert('Nama product sudah ada. Isi nama product yang lain');</script>";
        } else {
            // Lanjutkan dengan query UPDATE jika tidak ada duplikat dan harga valid
            $updateSql = "UPDATE products SET name=?, price=?, category_id=? WHERE id=?";
            $stmtUpdate = $conn->prepare($updateSql);
            $stmtUpdate->bind_param("siii", $name, $price, $category_id, $id);

            if ($stmtUpdate->execute()) {
                echo '<script>alert("Product successfully updated!"); window.location.href = "view_products.php";</script>';
                exit();
            } else {
                echo "Error: " . $stmtUpdate->error;
            }

            $stmtUpdate->close();
        }

        $stmtCheck->close();
    
}

$sql = "SELECT * FROM products WHERE id=?";
$stmtSelect = $conn->prepare($sql);
$stmtSelect->bind_param("i", $id);
$stmtSelect->execute();
$result = $stmtSelect->get_result();
$row = $result->fetch_assoc();
$stmtSelect->close();

// Query untuk mengambil data kategori
$category_query = "SELECT id, name FROM categories";
$category_result = $conn->query($category_query);
?>
<div class="container">
    <h2>Edit Product</h2>
    <form method="POST" action="" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $row['name']; ?>">
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="text" class="form-control" id="price" name="price" value="<?php echo $row['price']; ?>">
        </div>
        <div class="form-group">
            <label for="category_id">Category ID:</label>
            <select class="form-control" id="category_id" name="category_id">
                <?php
                if ($category_result->num_rows > 0) {
                    while ($category = $category_result->fetch_assoc()) {
                        $selected = $category['id'] == $row['category_id'] ? 'selected' : '';
                        echo "<option value='{$category['id']}' $selected>{$category['id']} - {$category['name']}</option>";
                    }
                } else {
                    echo "<option value=''>No categories found</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<script>
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
