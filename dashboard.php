<?php
// dashboard.php - Enhanced user dashboard with validation
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

// Get user information
try {
    $stmt = $conn->prepare("SELECT fullname, email FROM usertb WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user_info = $user_result->fetch_assoc();
    $stmt->close();
} catch (Exception $e) {
    error_log("Dashboard user query error: " . $e->getMessage());
    $user_info = ['fullname' => 'User', 'email' => $user_email];
}

// Get user's bookings
try {
    $stmt = $conn->prepare("
        SELECT booking_reference, room_type, checkin, checkout, total_price, status, created_at, special_requests
        FROM bookings 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $bookings_result = $stmt->get_result();
    $bookings = $bookings_result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    error_log("Dashboard bookings query error: " . $e->getMessage());
    $bookings = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - hotel bougainvillea</title>
    <link rel="stylesheet" href="style1.css">
    <style>
        .welcome-image {
            margin-top: 20px;
            text-align: center;
        }
        
        .welcome-image img {
            max-width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        
        .welcome-image img:hover {
            transform: scale(1.02);
        }
        
        .welcome-section {
            margin-bottom: 30px;
        }
        
        .image-caption {
            margin-top: 10px;
            font-style: italic;
            color: #000;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .welcome-image img {
                height: 200px;
            }
        }
        /* Contact Bar */
.contact-bar {
    background-color: rgba(11, 11, 12, 0.6);
    color: #fff;
    text-align: center;
    padding: 15px 10px;
    font-size: 14px;
    position: relative;
    bottom: 0;
    width: 100%;
}
    </style>
</head>
<body>
    <header>
        <div class="navbar">
            <div class="logo">Hotel Bougainvillea</div>
            <ul>
                <li><a href="dashboard.php">Home</a></li>
                <li><a href="rooms.html">Rooms</a></li>
                <li><a href="facilities.html">Facilities</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </header>

    <main>
        <section class="dashboard">
            <div class="welcome-section">
                <h2>Welcome, <?php echo htmlspecialchars($user_info['fullname']); ?>!</h2>
                <p>Manage your bookings and account information from your dashboard.</p>
                
                <!-- Welcome Image -->
                <div class="welcome-image">
                    <img src="image/Hotel2.avif" alt="ABC Hotel Luxury Lobby" >
                    <div class="image-caption">Experience luxury and comfort at hotel bougainvillea</div>
                </div>
            </div>

            <div class="quick-actions">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <a href="Booking_Room.php" class="action-btn">
                        <span class="icon">🏨</span>
                        <span class="text">New Booking</span>
                    </a>
                    <a href="contact.html" class="action-btn">
                        <span class="icon">📞</span>
                        <span class="text">Contact Us</span>
                    </a>
                    <a href="profile.php" class="action-btn">
                        <span class="icon">👤</span>
                        <span class="text">Update Profile</span>
                    </a>
                </div>
            </div><br>
            <div class="contact-bar">
        <p>Contact Us: +94 112345678 | Email: hotel.bougainvillea@gmail.com | Location:Sri Pathi rd,Aluthgama 80500</p>
    </div>

        </section>
    </main>

    <script>
        function cancelBooking(bookingReference) {
            if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
                // Send AJAX request to cancel booking
                fetch('cancel_booking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'booking_reference=' + encodeURIComponent(bookingReference)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Booking cancelled successfully');
                        location.reload();
                    } else {
                        alert('Error cancelling booking: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while cancelling the booking');
                });
            }
        }
    </script>
</body>
</html>