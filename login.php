<?php
// Database connection
$host = 'localhost';
$dbname = 'viit';
$username = 'root'; // Adjust your DB username
$password = ''; // Adjust your DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Handle POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data['username'] || !$data['password']) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Check user credentials
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $data['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($data['password'], $user['password_hash'])) {
        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
