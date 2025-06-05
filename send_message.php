<?php
// DB connection
$host = 'localhost';
$db = 'abc';
$user = 'root'; // Change this if your DB username is different
$pass = '';     // Change this if your DB has a password

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and validate inputs
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$subject = trim($_POST['subject']);
$message = trim($_POST['message']);

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo "Please fill in all required fields.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format.";
    exit;
}

$stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

if ($stmt->execute()) {
    echo "<script>alert('Your message has been sent successfully!'); window.location.href='contact.html';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
