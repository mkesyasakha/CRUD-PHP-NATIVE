<?php
include '../db.php';
include 'index.php';

$errors = []; // Array untuk menyimpan pesan error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    // Validasi nama
    if (empty($name)) {
        $errors[] = "Nama Category harus diisi";
    } else {
        // Cek apakah nama category sudah ada di database
        $sql_check_name = "SELECT * FROM categories WHERE name = ?";
        $stmt = $conn->prepare($sql_check_name);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Nama Category sudah terdaftar";
        }
    }

    // Jika tidak ada error, lanjutkan dengan proses INSERT
    if (empty($errors)) {
        $sql = "INSERT INTO categories (name) VALUES (?)";
        $stmt_insert = $conn->prepare($sql);
        $stmt_insert->bind_param("s", $name);

        if ($stmt_insert->execute()) {
            echo "<script>alert('Category berhasil ditambahkan!'); window.location.href='view_categories.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error: " . $stmt_insert->error . "');</script>";
        }
    } else {
        // Jika terdapat error, tampilkan pesan error
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}
?>
<div class="container">
    <h2>Add Categories</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
</body>
</html>
