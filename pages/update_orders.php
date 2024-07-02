<?php
include '../db.php'; // Periksa apakah path ini benar
include 'index.php'; // Periksa apakah inklusi ini diperlukan

$id = $_GET['id'];

// Validasi parameter ID untuk mencegah SQL injection
if (!is_numeric($id)) {
    die("ID tidak valid");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitasi dan validasi input
    $customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);
    $date = mysqli_real_escape_string($conn, $_POST['order_date']);

    
    
    // Update query SQL dengan prepared statement
    $sql = "UPDATE orders SET customer_id=?, order_date=?, total=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $customer_id, $date, $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        header("Location: view_orders.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Pilih data untuk form
$sql_select_orders = "SELECT * FROM orders WHERE id=?";
$stmt_select_orders = $conn->prepare($sql_select_orders);
$stmt_select_orders->bind_param("i", $id);
$stmt_select_orders->execute();
$result_orders = $stmt_select_orders->get_result();
$row_orders = $result_orders->fetch_assoc();
$stmt_select_orders->close();

// Pilih customers untuk dropdown
$sql_select_customers = "SELECT id, name FROM customers";
$result_customers = $conn->query($sql_select_customers);
?>

<div class="container">
    <h2>Edit Order</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="customer_id">Customer:</label>
            <select class="form-control" id="customer_id" name="customer_id" required>
                <?php while ($row_customer = $result_customers->fetch_assoc()): ?>
                    <option value="<?php echo $row_customer['id']; ?>" <?php echo ($row_customer['id'] == $row_orders['id']) ? 'selected' : ''; ?>>
                        <?php echo $row_customer['id'] . ' - ' . $row_customer['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="order_date">Tanggal Order:</label>
            <input type="datetime-local" class="form-control" id="order_date" name="order_date" value="<?php echo htmlspecialchars($row_orders['order_date']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
