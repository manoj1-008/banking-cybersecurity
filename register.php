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

// Hash the password
$passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

// Save user data to the database
try {
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, security_answers) VALUES (:username, :password_hash, :security_answers)");
    $securityAnswers = json_encode(array_slice($data, 2)); // Save security questions as JSON

    $stmt->execute([
        ':username' => $data['username'],
        ':password_hash' => $passwordHash,
        ':security_answers' => $securityAnswers
    ]);

    echo json_encode(['success' => true, 'message' => 'Registration successful']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Registration failed: Username already exists']);
}
?>
