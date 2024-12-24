<?php
// Start session and check if the admin is logged in
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.html"); // Redirect to login page if not logged in
    exit();
}

echo "<h1>Welcome, " . $_SESSION['admin'] . "</h1>";
?>

<div class="dashboard-container">
    <h2>Admin Dashboard</h2>
    <p>Manage Athlete Registrations</p>
    <a href="view_athletes.php">View All Athletes</a> |
    <a href="add_athlete.php">Register New Athlete</a> |
    <a href="admin_actions.php?action=logout">Logout</a>
</div>

<?php
// Logout functionality
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.html"); // Redirect to login page
    exit();
}
?>
