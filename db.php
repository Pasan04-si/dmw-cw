
<?php
// db.php - Enhanced with error handling
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'abc';

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        die('Database connection failed. Please try again later.');
    }
    $conn->set_charset("utf8");
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    die('Database error occurred.');
}
?>
