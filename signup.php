<?php
require_once 'connection.php'; // ✅ Include DB connection

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

// Validate email and password
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
    exit;
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
    exit;
}

// Hash and insert new user
$hashed = password_hash($password, PASSWORD_DEFAULT);
$insert = $conn->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
$insert->bind_param("ss", $email, $hashed);

if ($insert->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Registration successful.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed.']);
}

$conn->close();
?>
