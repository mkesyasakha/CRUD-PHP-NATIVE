<?php
include '../db.php';
include 'index.php';

$id = $_GET['id'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    if(empty($name) || empty($email)){
        echo "<script>alert ('Isi Semua Form!')</script>";
         // To prevent further execution.
    } else {

        $sql = "UPDATE customers SET name='$name', email='$email' WHERE id=$id";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: view_customer.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$sql = "SELECT * FROM customers WHERE id=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>
<div class="container">
<h2>Edit Customer</h2>
<form method="POST" action="">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $row['name']; ?>" >
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['email']; ?>" >
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
</form>
</div>
</div>
</body>
</html>
