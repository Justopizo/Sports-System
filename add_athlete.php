<?php
// Start session and check if the admin is logged in
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.html");
    exit();
}

// Handle new athlete registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'config.php';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $category = $_POST['category'];

    // Database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert new athlete
    $stmt = $conn->prepare("INSERT INTO athletes (name, email, category) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $category);

    if ($stmt->execute()) {
        echo "Athlete successfully registered!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<h2>Register New Athlete</h2>
<form method="POST">
    <input type="text" name="name" placeholder="Athlete Name" required>
    <input type="email" name="email" placeholder="Athlete Email" required>
    <select name="category" required>
        <option value="">Select Category</option>
        <option value="Football">Football</option>
        <option value="Netball">Netball</option>
        <option value="Others">Others</option>
    </select>
    <button type="submit">Register Athlete</button>
</form>
