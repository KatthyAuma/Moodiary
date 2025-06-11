<?php
require_once 'connection.php'; // 🔁 Use shared DB connection

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
    echo "Invalid email or password (must be at least 6 characters).";
    exit;
}

$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "Email already registered. <a href='index.html'>Try again</a>";
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$insert = $conn->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
$insert->bind_param("ss", $email, $hashedPassword);

if ($insert->execute()) {
    header("Location: signin.html");
    exit;
} else {
    echo "Registration failed. Try again later.";
}
?>
