<?php
include '../db.php';
include 'index.php';

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    
    $sql = "UPDATE products SET name='$name', price='$price', category_id='$category_id' WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: view_products.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT * FROM products WHERE id=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>
<div class="container">
<h2>Edit Product</h2>
<form method="POST" action="">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $row['name']; ?>" required>
    </div>
    <div class="form-group">
        <label for="price">Price:</label>
        <input type="price" class="form-control" id="price" name="price" value="<?php echo $row['price']; ?>" required>
    </div>
    <div class="form-group">
        <label for="category_id">Category ID:</label>
        <input type="int" class="form-control" id="category_id" name="category_id" value="<?php echo $row['category_id']; ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
</form>
</div>
</div>
</body>
</html>
