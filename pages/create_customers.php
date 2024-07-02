<?php
include '../db.php';
include 'index.php';

$errors = []; // Array untuk menyimpan pesan error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Validasi nama
    if (empty($name)) {
        $errors[] = "Nama harus diisi";
    } elseif (!preg_match("/^[a-zA-Z ]*$/", $name)) {
        $errors[] = "Nama hanya boleh diisi dengan huruf";
    }

    // Validasi email
    if (empty($email)) {
        $errors[] = "Email harus diisi";
    }

    // Jika tidak ada error, lanjutkan dengan proses INSERT
    if (empty($errors)) {
        // Cek apakah email sudah ada di database
        $sql_check_email = "SELECT * FROM customers WHERE email = ?";
        $stmt_email = $conn->prepare($sql_check_email);
        $stmt_email->bind_param("s", $email);
        $stmt_email->execute();
        $result_email = $stmt_email->get_result();

        // Cek apakah nama sudah ada di database
        $sql_check_name = "SELECT * FROM customers WHERE name = ?";
        $stmt_name = $conn->prepare($sql_check_name);
        $stmt_name->bind_param("s", $name);
        $stmt_name->execute();
        $result_name = $stmt_name->get_result();

        if ($result_email->num_rows > 0 || $result_name->num_rows > 0) {
            // Jika email atau nama sudah ada, tampilkan alert
            echo "<script>alert('Email atau Nama sudah terdaftar!'); window.location.href = 'create_customers.php';</script>";
        } else {
            // Jika email belum ada, lakukan INSERT
            $sql = "INSERT INTO customers (name, email) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql);
            $stmt_insert->bind_param("ss", $name, $email);

            if ($stmt_insert->execute()) {
                header("Location: view_customer.php");
                exit();
            } else {
                echo "<div class='alert alert-danger' role='alert'>Error: " . $stmt_insert->error . "</div>";
            }
        }
    } else {
        // Jika terdapat error, tampilkan pesan error
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger' role='alert'>$error</div>";
        }
    }
}
?>
<div class="container">
    <h2>Add Customer</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" >
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
</body>
</html>
