<?php
include '../db.php';
include 'index.php';

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    try {
        // Validasi input
        if (empty($name)) {
            throw new Exception("Nama kategori harus diisi.");
        }

        // Periksa ketersediaan nama sebelum update
        $checkSql = "SELECT * FROM categories WHERE name=? AND id!=?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<script>alert("Data Sudah ada")</script>';
        }

        // Lakukan update jika validasi berhasil
        $updateSql = "UPDATE categories SET name=? WHERE id=?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("si", $name, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Data berhasil diupdate.'); window.location.href='view_categories.php';</script>";
            exit();
        } else {
            throw new Exception("Gagal melakukan update kategori: " . $stmt->error);
        }
    } catch (mysqli_sql_exception $e) {
        echo "<script>alert('" . $e->getMessage() . "'); window.location.href='view_categories.php';</script>";
    }
}

// Ambil data kategori berdasarkan ID
$sql = "SELECT * FROM categories WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
?>
<div class="container">
    <h2>Edit Categories</h2>
    <form method="POST" action="" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
<script>
    function validateForm() {
        var name = document.getElementById('name').value;
        if (name.trim() === '') {
            alert('Nama kategori harus diisi.');
            return false;
        }
        return true;
    }
</script>
</body>
</html>
