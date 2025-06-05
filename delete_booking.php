<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "abc");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Security improvement
    
    // Get booking details before deletion for confirmation - only if it belongs to logged-in user
    $stmt = $conn->prepare("SELECT full_name FROM bookingtb WHERE id = ? AND email = ?");
    $stmt->bind_param("is", $id, $_SESSION['user_email']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        
        // Delete the booking (only if it belongs to the user)
        $delete_stmt = $conn->prepare("DELETE FROM bookingtb WHERE id = ? AND email = ?");
        $delete_stmt->bind_param("is", $id, $_SESSION['user_email']);
        $delete_stmt->execute();
        
        if ($delete_stmt->affected_rows > 0) {
            $message = "Your booking for " . htmlspecialchars($booking['full_name']) . " has been deleted successfully.";
            $success = true;
        } else {
            $message = "Unable to delete booking. Please try again.";
            $success = false;
        }
    } else {
        $message = "Booking not found or you do not have permission to delete it.";
        $success = false;
    }
} else {
    $message = "Invalid booking ID.";
    $success = false;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete My Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .success-icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .error-icon {
            font-size: 60px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .message {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            margin-bottom: 30px;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            margin-bottom: 30px;
        }
        
        .back-btn {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
            margin: 0 10px;
        }
        
        .back-btn:hover {
            background-color: #0056b3;
        }
        
        .user-info {
            background-color: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        
        .user-info p {
            margin: 0;
            color: #155724;
            font-weight: bold;
        }
        
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <script>
     
    </script>
</head>
<body>
    <div class="container">
        <div class="user-info">
            <p>Logged in as: <?= htmlspecialchars($_SESSION['user_email']) ?></p>
        </div>
        
        <?php if ($success) { ?>
            <div class="success-icon">✓</div>
            <h2>Booking Deleted</h2>
            <div class="success-message">
                <?= $message ?>
            </div>
        <?php } else { ?>
            <div class="error-icon">✗</div>
            <h2>Delete Error</h2>
            <div class="error-message">
                <?= $message ?>
            </div>
        <?php } ?>
        
        <a href="view_bookings.php" class="back-btn">Back to My Bookings</a>
        <a href="Booking_Room.php" class="back-btn">Make New Booking</a>
    </div>
</body>
</html>