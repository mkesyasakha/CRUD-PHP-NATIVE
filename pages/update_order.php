<?php
include '../db.php'; // Include your database connection file
include 'index.php'; // Include other necessary files

$id = $_GET['id'];

// Validate ID parameter to prevent SQL injection
if (!is_numeric($id)) {
    die("ID tidak valid");
}

try {
    // Fetch order and order detail data for editing
    $sql_select_orders = "SELECT o.id, o.customer_id, o.order_date, od.id as detail_id, od.product_id, od.quantity, od.price 
                          FROM orders o 
                          LEFT JOIN order_details od ON o.id = od.order_id 
                          WHERE od.id=?";
    $stmt_select_orders = $conn->prepare($sql_select_orders);
    $stmt_select_orders->bind_param("i", $id);
    $stmt_select_orders->execute();
    $result_orders = $stmt_select_orders->get_result();
    $row_orders = $result_orders->fetch_assoc();

    if (!$row_orders) {
        throw new Exception("Data order tidak ditemukan untuk ID: " . $id);
    }

    // Fetch customers for dropdown
    $sql_select_customers = "SELECT id, name FROM customers";
    $result_customers = $conn->query($sql_select_customers);

    // Fetch products for dropdown
    $sql_select_products = "SELECT id, name FROM products";
    $result_products = $conn->query($sql_select_products);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $customer_id = $_POST['customer_id'];
        $order_date = $_POST['order_date'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // Validate quantity
        if (!is_numeric($quantity)) {
            echo "<script>alert('Quantity harus diisi, dan berupa angka');</script>";
        } elseif ($quantity < 0) {
            echo "<script>alert('Quantity tidak boleh kurang dari 0');</script>";
        } else {
            // Update order
            $update_order_query = "UPDATE orders SET customer_id=?, order_date=? WHERE id=?";
            $stmt_update_order = $conn->prepare($update_order_query);
            $stmt_update_order->bind_param("isi", $customer_id, $order_date, $row_orders['id']);
            $stmt_update_order->execute();

            // Update order_details
            $price_query = "SELECT price FROM products WHERE id=?";
            $stmt_price = $conn->prepare($price_query);
            $stmt_price->bind_param("i", $product_id);
            $stmt_price->execute();
            $result_price = $stmt_price->get_result();
            $row_price = $result_price->fetch_assoc();
            
            // Calculate total price if both quantity and price are valid
            if ($row_price && is_numeric($quantity)) {
                $total_price = $row_price['price'] * $quantity;

                $update_detail_query = "UPDATE order_details SET product_id=?, quantity=?, price=? WHERE id=?";
                $stmt_update_detail = $conn->prepare($update_detail_query);
                $stmt_update_detail->bind_param("iiii", $product_id, $quantity, $total_price, $row_orders['detail_id']);
                $stmt_update_detail->execute();

                echo "<script>
                        alert('Data berhasil diupdate.');
                        window.location.href = 'view_orders.php';
                      </script>";
                exit();
            } else {
                echo "<script>alert('Product ID tidak ditemukan atau Quantity harus berupa angka.');</script>";
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
    <title>Edit Order and Order Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <script>
        // JavaScript function to validate quantity on form submit
        function validateQuantity() {
            var quantity = document.getElementById('quantity').value;
            if (isNaN(quantity) || quantity < 0) {
                alert('Quantity harus diisi dan tidak boleh kurang dari 0.');
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<div class="container">
    <h2>Edit Order and Order Details</h2>
    <form method="POST" action="" onsubmit="return validateQuantity();">
        <div class="form-group">
            <label for="customer_id">Customer:</label>
            <select class="form-control" id="customer_id" name="customer_id">
                <?php while ($row_customer = $result_customers->fetch_assoc()): ?>
                    <option value="<?php echo $row_customer['id']; ?>" <?php echo ($row_customer['id'] == $row_orders['customer_id']) ? 'selected' : ''; ?>>
                        <?php echo $row_customer['id'] . ' - ' . $row_customer['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="order_date">Order Date:</label>
            <input type="datetime-local" class="form-control" id="order_date" name="order_date" value="<?php echo date('Y-m-d\TH:i', strtotime($row_orders['order_date'])); ?>">
        </div>
        <div class="form-group">
            <label for="product_id">Product:</label>
            <select class="form-control" id="product_id" name="product_id">
                <?php while ($row_product = $result_products->fetch_assoc()): ?>
                    <option value="<?php echo $row_product['id']; ?>" <?php echo ($row_product['id'] == $row_orders['product_id']) ? 'selected' : ''; ?>>
                        <?php echo $row_product['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $row_orders['quantity']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
</body>
</html>
