// This is the PHP backend code to handle the athlete registration system. .

<?php
// Include necessary files for database connection and email sending
require 'config.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_admin'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo "Admin registered successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Admin login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_admin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        session_start();
        $_SESSION['admin'] = $username;
        echo "Login successful.";
    } else {
        echo "Invalid credentials.";
    }
    $stmt->close();
}

// Forgot password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgot_password'])) {
    $username = $_POST['username'];
    $new_password = bin2hex(random_bytes(4));
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $hashed_password, $username);

    if ($stmt->execute()) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;

            $mail->setFrom(SMTP_USER, 'Athlete Registration System');
            $mail->addAddress($username);
            $mail->isHTML(true);
            $mail->Subject = "Password Reset";
            $mail->Body = "Your new password is: $new_password";

            $mail->send();
            echo "Password reset email sent.";
        } catch (Exception $e) {
            echo "Error: " . $mail->ErrorInfo;
        }
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Register athlete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_athlete'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("INSERT INTO athletes (name, email, category) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $category);

    if ($stmt->execute()) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;

            $mail->setFrom(SMTP_USER, 'Athlete Registration System');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Registration Confirmation";
            $mail->Body = "Hello $name, you have been successfully registered for $category.";

            $mail->send();
            echo "Athlete registered successfully and email sent.";
        } catch (Exception $e) {
            echo "Error: " . $mail->ErrorInfo;
        }
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
