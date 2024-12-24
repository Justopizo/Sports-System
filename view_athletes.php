<?php
// Include necessary files
require 'config.php';

// Start session and check if the admin is logged in
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.html"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch athletes
$sql = "SELECT * FROM athletes";
$result = $conn->query($sql);

echo "<h2>Registered Athletes</h2>";
echo "<table border='1'>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Category</th>
        <th>Action</th>
    </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>" . $row['id'] . "</td>
            <td>" . $row['name'] . "</td>
            <td>" . $row['email'] . "</td>
            <td>" . $row['category'] . "</td>
            <td>
                <a href='edit_athlete.php?id=" . $row['id'] . "'>Edit</a> |
                <a href='delete_athlete.php?id=" . $row['id'] . "'>Delete</a>
            </td>
          </tr>";
}
echo "</table>";

$conn->close();
?>
