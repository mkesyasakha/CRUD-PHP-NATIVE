<?php
include '../db.php';
include 'index.php';

$errors = []; // Inisialisasi array untuk menyimpan pesan kesalahan

try {
    // Ambil data customer dari tabel customers
    $customer_query = "SELECT id, name FROM customers";
    $customer_result = $conn->query($customer_query);

    // Ambil data products
    $product_query = "SELECT id, name, price FROM products";
    $product_result = $conn->query($product_query);

    if (!$customer_result) {
        throw new Exception("Error fetching customers: " . $conn->error);
    }

    if (!$product_result) {
        throw new Exception("Error fetching products: " . $conn->error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $customer_id = $_POST['customer_id'];
        $order_date = $_POST['order_date'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // Validasi Order ID
        $order_id_exists_query = "SELECT id FROM orders WHERE id = ?";
        $stmt_order_id_exists = $conn->prepare($order_id_exists_query);
        $stmt_order_id_exists->bind_param("i", $order_id);
        $order_id = $_POST['order_id'];

        $stmt_order_id_exists->execute();
        $stmt_order_id_exists->store_result();

        if ($stmt_order_id_exists->num_rows > 0) {
            $errors[] = "Order ID sudah ada. Silakan gunakan Order ID lain.";
        }

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

        if (empty($errors)) {
            // Insert data ke tabel orders
            $sql_order = "INSERT INTO orders (customer_id, order_date) VALUES (?, ?)";
            $stmt_order = $conn->prepare($sql_order);
            $stmt_order->bind_param("is", $customer_id, $order_date);

            if ($stmt_order->execute()) {
                $order_id = $stmt_order->insert_id;  // Mendapatkan ID order yang baru dimasukkan

                // Dapatkan harga produk
                $product = $result->fetch_assoc();
                $price_per_unit = $product['price'];
                $total_price = $price_per_unit * $quantity;

                // Insert data ke tabel order_details
                $sql_details = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
                $stmt_details = $conn->prepare($sql_details);
                $stmt_details->bind_param("iiid", $order_id, $product_id, $quantity, $total_price);

                if ($stmt_details->execute()) {
                    echo '<script>alert("Data berhasil ditambahkan!"); window.location.href = "view_orders.php";</script>';
                    exit();
                } else {
                    throw new Exception("Error inserting order details: " . $stmt_details->error);
                }
            } else {
                throw new Exception("Error inserting order: " . $stmt_order->error);
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
    <h2>Add Orders</h2>
    <form method="POST" action="" id="order_form">
        <div class="form-group">
            <label for="customer_id">Customer ID:</label>
            <select class="form-control" id="customer_id" name="customer_id">
                <?php
                if ($customer_result->num_rows > 0) {
                    while ($row = $customer_result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['id'] . " - " . $row['name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>No customers found</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="order_date">Order Date:</label>
            <input type="datetime-local" class="form-control" id="order_date" name="order_date">
        </div>
        <div class="form-group">
            <label for="product_id">Product ID:</label>
            <select class="form-control" id="product_id" name="product_id">
                <?php
                if ($product_result->num_rows > 0) {
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
            <input type="number" class="form-control" id="quantity" name="quantity">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script>
    // JavaScript untuk validasi form sebelum submit
    document.getElementById('order_form').addEventListener('submit', function(event) {
        const orderDate = document.getElementById('order_date').value.trim();
        const quantity = document.getElementById('quantity').value.trim();

        if (!orderDate || !quantity) {
            alert('Order Date dan Quantity harus diisi');
            event.preventDefault(); // Menghentikan pengiriman form jika ada kesalahan
        } else if (quantity < 0) {
            alert('Quantity tidak boleh diisi dengan nilai negatif');
            event.preventDefault(); // Menghentikan pengiriman form jika ada kesalahan
        }
    });

    // Tambahan validasi untuk quantity tidak boleh diisi dengan nilai di bawah 0
    function validateQuantity() {
            var quantity = document.getElementById('quantity').value;
            if (isNaN(quantity) || quantity < 0) {
                alert('Quantity harus diisi dan tidak boleh kurang dari 0.');
                return false;
            }
            return true;
        }
</script>

