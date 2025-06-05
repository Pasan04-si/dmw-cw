<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "abc");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Only allow editing bookings that belong to the logged-in user
    $stmt = $conn->prepare("SELECT * FROM bookingtb WHERE id = ? AND email = ?");
    $stmt->bind_param("is", $id, $_SESSION['user_email']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = trim($_POST['full_name']);
            $email = $_SESSION['user_email']; // Keep original user email
            $phone = trim($_POST['phone']);
            $checkin = $_POST['check_in'];
            $checkout = $_POST['check_out'];
            $room_type = $_POST['room_type'];
            $guests = intval($_POST['guests']);

            $stmt = $conn->prepare("UPDATE bookingtb SET full_name=?, phone=?, check_in=?, check_out=?, room_type=?, guests=? WHERE id=? AND email=?");
            $stmt->bind_param("sssssiis", $name, $phone, $checkin, $checkout, $room_type, $guests, $id, $_SESSION['user_email']);
            $stmt->execute();
            
            echo "<script>alert('Booking updated successfully.'); window.location='view_bookings.php';</script>";
        }
    } else {
        // Booking not found or doesn't belong to user
        echo "<script>alert('Booking not found or you do not have permission to edit it.'); window.location='view_bookings.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit My Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 3px solid #ffc107;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        
        input[type="text"], 
        input[type="email"], 
        input[type="date"], 
        input[type="number"], 
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #ffc107;
        }
        
        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .update-btn {
            background-color: #ffc107;
            color: #333;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            flex: 1;
            transition: background-color 0.3s;
        }
        
        .update-btn:hover {
            background-color: #e0a800;
        }
        
        .cancel-btn {
            background-color: #6c757d;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            flex: 1;
            transition: background-color 0.3s;
        }
        
        .cancel-btn:hover {
            background-color: #545b62;
        }
        
        .form-title {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            border-left: 4px solid #ffc107;
        }
        
        .form-title p {
            margin: 0;
            color: #856404;
            font-weight: bold;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit My Booking</h2>
        
        <div class="user-info">
            <p>Logged in as: <?= htmlspecialchars($_SESSION['user_email']) ?></p>
        </div>
        
        <?php if(isset($data)) { ?>
        <div class="form-title">
            <p>Editing booking for: <?= htmlspecialchars($data['full_name']) ?></p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($data['full_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email (Cannot be changed):</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" readonly style="background-color: #f8f9fa;">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($data['phone']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="check_in">Check-in Date:</label>
                <input type="date" id="check_in" name="check_in" value="<?= $data['check_in'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="check_out">Check-out Date:</label>
                <input type="date" id="check_out" name="check_out" value="<?= $data['check_out'] ?>" required>
            </div>
            
            <div class="form-group">
                <label for="room_type">Room Type:</label>
                <select id="room_type" name="room_type" required>
                    <option value="Single" <?= $data['room_type'] == 'Single' ? 'selected' : '' ?>>Single</option>
                    <option value="Double" <?= $data['room_type'] == 'Double' ? 'selected' : '' ?>>Double</option>
                    <option value="Deluxe" <?= $data['room_type'] == 'Deluxe' ? 'selected' : '' ?>>Deluxe</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="guests">Number of Guests:</label>
                <input type="number" id="guests" name="guests" value="<?= $data['guests'] ?>" min="1" required>
            </div>
            
            <div class="btn-container">
                <input type="submit" class="update-btn" value="Update Booking">
                <a href="view_bookings.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
        
        <?php } else { ?>
        <div class="form-title">
            <p>Booking not found or you do not have permission to edit it.</p>
        </div>
        <div class="btn-container">
            <a href="view_bookings.php" class="cancel-btn">Back to My Bookings</a>
        </div>
        <?php } ?>
    </div>
</body>
</html>