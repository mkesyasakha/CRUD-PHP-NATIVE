<?php
include '../db.php';
include 'index.php';

$errors = []; // Array untuk menyimpan pesan error

try {
    // Ambil data orders dan nama customer terkait
    $order_query = "SELECT orders.id, customers.name 
                    FROM orders 
                    INNER JOIN customers ON orders.customer_id = customers.id";
    $order_result = $conn->query($order_query);

    if (!$order_result) {
        throw new Exception("Error fetching Orders: " . $conn->error);
    }
    
    // Ambil data products
    $product_query = "SELECT id, name, price FROM products";
    $product_result = $conn->query($product_query);

    if (!$product_result) {
        throw new Exception("Error fetching products: ". $conn->error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $order_id = $_POST['orders_id'];
        $product_id = $_POST['products_id'];
        $quantity = $_POST['quantity'];

        // Validasi product_id
        if (empty($product_id)) {
            $errors[] = "Product ID harus dipilih";
        } else {
            // Cek apakah product_id ada di tabel products dan dapatkan harga produk
            $product_check_query = "SELECT id, price FROM products WHERE id = ?";
            $stmt = $conn->prepare($product_check_query);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                $errors[] = "Product ID tidak ditemukan. Silakan periksa Product ID yang dimasukkan.";
            }
        }

        // Validasi quantity
        if (empty($quantity)) {
            $errors[] = "Quantity harus diisi";
        } elseif ($quantity <= 0) {
            $errors[] = "Quantity harus lebih besar dari 0";
        }

        // Jika tidak ada error, lanjutkan dengan proses INSERT
        if (empty($errors)) {
            $product = $result->fetch_assoc();
            $price_per_unit = $product['price'];
            $total_price = $price_per_unit * $quantity;

            // Lakukan INSERT ke order_details
            $sql = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql);
            $stmt_insert->bind_param("iiid", $order_id, $product_id, $quantity, $total_price);

            if ($stmt_insert->execute()) {
                header("Location: view_order_details.php");
                exit();
            } else {
                echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt_insert->error . "</div>";
            }
        }
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger' role='alert'>" . $e->getMessage() . "</div>";
} finally {
    $conn->close();
}
?>

<div class="container">
    <h2>Add Order Details</h2>
    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger' role='alert'>$error</div>";
        }
    }
    ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="orders_id">Orders ID:</label>
            <select class="form-control" id="orders_id" name="orders_id" required>
                <?php
                if (isset($order_result) && $order_result->num_rows > 0) {
                    while ($row = $order_result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No Orders found</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="products_id">Product ID:</label>
            <select class="form-control" id="products_id" name="products_id" required>
                <?php
                if (isset($product_result) && $product_result->num_rows > 0) {
                    while ($row = $product_result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['id'] . " - " . $row['name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No Products found</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
