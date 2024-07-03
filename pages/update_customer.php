<?php
include '../db.php';
include 'index.php';

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    if (empty($name) || empty($email)) {
        echo "<script>alert('Isi Semua Form!');</script>";
    } else {
        // Periksa apakah email sudah ada di database kecuali untuk pengguna yang sedang diupdate
        $checkEmailSql = "SELECT * FROM customers WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($checkEmailSql);
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Email sudah digunakan oleh pelanggan lain.');</script>";
        } else {
            // Lakukan update jika email tidak duplikat
            $updateSql = "UPDATE customers SET name = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("ssi", $name, $email, $id);

            if ($stmt->execute()) {
                echo "<script>
                    alert('Data berhasil diedit.');
                    window.location.href = 'view_customer.php';
                </script>";
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
        }

        $stmt->close();
    }
}

$sql = "SELECT * FROM customers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<div class="container">
    <h2>Edit Customer</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
</body>
</html>
