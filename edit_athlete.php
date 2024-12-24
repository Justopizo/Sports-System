<?php
// Start session and check if the admin is logged in
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.html");
    exit();
}

require 'config.php';

// Database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch athlete details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM athletes WHERE id = $id";
    $result = $conn->query($sql);
    $athlete = $result->fetch_assoc();
}

// Handle athlete update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("UPDATE athletes SET name = ?, email = ?, category = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $category, $id);

    if ($stmt->execute()) {
        echo "Athlete details updated!";
    } else {
        echo "Error: " . $stmt->error;
    }
}

$conn->close();
?>

<h2>Edit Athlete</h2>
<form method="POST">
    <input type="text" name="name" value="<?php echo $athlete['name']; ?>" required>
    <input type="email" name="email" value="<?php echo $athlete['email']; ?>" required>
    <select name="category" required>
        <option value="Football" <?php if ($athlete['category'] == 'Football') echo 'selected'; ?>>Football</option>
        <option value="Netball" <?php if ($athlete['category'] == 'Netball') echo 'selected'; ?>>Netball</option>
        <option value="Others" <?php if ($athlete['category'] == 'Others') echo 'selected'; ?>>Others</option>
    </select>
    <button type="submit">Update Athlete</button>
</form>
