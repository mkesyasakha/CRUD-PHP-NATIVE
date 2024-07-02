<?php
include '../db.php';
include 'index.php';

try {
    // Ambil data customer dari tabel customers
    $customer_query = "SELECT id, name FROM customers";
    $customer_result = $conn->query($customer_query);

    if (!$customer_result) {
        throw new Exception("Error fetching customers: " . $conn->error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $customer_id = $_POST['customer_id'];
        $order_date = $_POST['order_date'];

        $sql = "INSERT INTO orders (customer_id, order_date) VALUES ('$customer_id', '$order_date')";

        if ($conn->query($sql) === TRUE) {
            header("Location: view_orders.php");
        } else {
            throw new Exception("Error inserting order: " . $conn->error);
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
    <form method="POST" action="">
        <div class="form-group">
            <label for="customer_id">Customer ID:</label>
            <select class="form-control" id="customer_id" name="customer_id" required>
                <?php
                if (isset($customer_result) && $customer_result->num_rows > 0) {
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
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
