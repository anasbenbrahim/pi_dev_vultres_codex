<?php
// Database connection details
$host = 'localhost'; // Replace with your MariaDB host
$dbname = 'parking'; // Replace with your database name
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get POST data
    $userId = $_POST['user_id'];
    $cardNumber = $_POST['card_number'];
    $expiryDate = $_POST['expiry_date'];
    $cardHolderName = $_POST['card_holder_name'];

    // Validate input (basic validation)
    if (empty($userId) || empty($cardNumber) || empty($expiryDate) || empty($cardHolderName)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    // Prepare and execute the SQL query
    $stmt = $pdo->prepare("INSERT INTO payments (user_id, card_number, expiry_date, card_holder_name) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $cardNumber, $expiryDate, $cardHolderName]);

    // Return success response
    echo json_encode(['success' => true, 'message' => 'Payment added successfully']);
} catch (Exception $e) {
    // Return error response
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>