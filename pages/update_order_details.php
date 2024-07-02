<?php
include '../db.php';
include 'index.php';

try {
    $id = $_GET['id'];

    // Ambil data orders dan nama customer terkait
    $order_query = "SELECT orders.id, customers.name 
                    FROM orders 
                    INNER JOIN customers ON orders.customer_id = customers.id";
    $order_result = $conn->query($order_query);

    if (!$order_result) {
        throw new Exception("Error fetching orders: " . $conn->error);
    }

    // Ambil data products
    $product_query = "SELECT id, name, price FROM products";
    $product_result = $conn->query($product_query);

    if (!$product_result) {
        throw new Exception("Error fetching products: " . $conn->error);
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $order_id = $_POST['order_id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        

        // Cek apakah product_id ada di tabel products dan dapatkan harga produk
        $product_check_query = "SELECT id, price FROM products WHERE id = ?";
        $stmt = $conn->prepare($product_check_query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            echo "<script>alert('Product ID tidak ditemukan. Silakan periksa Product ID yang dimasukkan.'); window.location.href='view_order_details.php';</script>";
            exit();
        }

        $product = $result->fetch_assoc();
        $price_per_unit = $product['price'];
        $total_price = $price_per_unit * $quantity;
        
        $sql = "UPDATE order_details SET order_id='$order_id', product_id='$product_id', quantity='$quantity', price='$total_price' WHERE id=$id";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: view_order_details.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    
    $sql = "SELECT * FROM order_details WHERE id=$id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
} catch (Exception $e) {
    echo "<div class='alert alert-danger' role='alert'>" . $e->getMessage() . "</div>";
} finally {
    $conn->close();
}
?>
<div class="container">
    <h2>Edit Order Details</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="order_id">Order ID:</label>
            <select class="form-control" id="order_id" name="order_id" required>
                <?php
                $order_result->data_seek(0); // Reset pointer ke awal
                while ($order = $order_result->fetch_assoc()) {
                    $selected = $order['id'] == $row['order_id'] ? 'selected' : '';
                    echo "<option value='{$order['id']}' $selected>{$order['name']} -{$order['id']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="product_id">Product ID:</label>
            <select class="form-control" id="product_id" name="product_id" required>
                <?php
                $product_result->data_seek(0); // Reset pointer ke awal
                while ($product = $product_result->fetch_assoc()) {
                    $selected = $product['id'] == $row['product_id'] ? 'selected' : '';
                    echo "<option value='{$product['id']}' $selected>{$product['name']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $row['quantity']; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
