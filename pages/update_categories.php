<?php
include '../db.php';
include 'index.php';

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $sql = "UPDATE categories SET name='$name' WHERE id=$id";

    if(empty($name)){
        echo"<script>alert('Isi Semua Form')</script>";
    } else {
        if ($conn->query($sql) === TRUE) {
            header("Location: view_categories.php");
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    
}

$sql = "SELECT * FROM categories WHERE id=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>
<div class="container">
<h2>Edit Categories</h2>
<form method="POST" action="">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo $row['name']; ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
</form>
</div>
</div>
</body>
</html>
