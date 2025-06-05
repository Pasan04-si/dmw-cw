<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Include DB connection
$conn = new mysqli("localhost", "root", "", "abc");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['full_name']);
    $email = $_SESSION['user_email']; // Use logged-in user's email
    $phone = trim($_POST['phone']);
    $checkin = $_POST['check_in'];
    $checkout = $_POST['check_out'];
    $room_type = $_POST['room_type'];
    $guests = $_POST['guests'];

    if (!empty($name) && !empty($phone) && !empty($checkin) && !empty($checkout) && !empty($room_type) && $guests > 0) {
        $stmt = $conn->prepare("INSERT INTO bookingtb (full_name, email, phone, check_in, check_out, room_type, guests) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssi", $name, $email, $phone, $checkin, $checkout, $room_type, $guests);
        $stmt->execute();

        // ✅ Redirect after successful booking
        header("Location: view_bookings.php");
        exit();
    } else {
        echo "<script>alert('Please fill all fields correctly.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Room Booking</title>
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
            border-bottom: 3px solid #007bff;
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
            border-color: #007bff;
        }
        
        .submit-btn {
            background-color: #007bff;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        .submit-btn:hover {
            background-color: #0056b3;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
         .custom-btn {
    display: inline-block;
    background-color:rgb(34, 105, 47); /* same blue as your image */
    color: white;
    text-align: center;
    padding: 14px 0;
    width: 100%;
    border-radius: 8px;
    text-decoration: none;
    font-size: 18px;
    font-family: sans-serif;
    margin: 20px 0;
  }

  .custom-btn:hover {
    background-color:rgb(29, 219, 83);
  }
  
  .user-info {
      background-color: #e3f2fd;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      border-left: 4px solid #007bff;
  }
  
  .user-info p {
      margin: 0;
      color: #1565c0;
      font-weight: bold;
  }
    </style>
    <script>
        function validateForm() {
            const name = document.forms["bookingForm"]["full_name"].value.trim();
            const phone = document.forms["bookingForm"]["phone"].value.trim();
            const guests = document.forms["bookingForm"]["guests"].value;

            if (name == "" || phone == "" || guests <= 0) {
                alert("All fields are required and guests must be > 0.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Book a Room</h2>
        
        <div class="user-info">
            <p>Booking for: <?= htmlspecialchars($_SESSION['user_email']) ?></p>
        </div>
        
        <form name="bookingForm" method="POST" onsubmit="return validateForm();">
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" readonly style="background-color: #f8f9fa;">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            
            <div class="form-group">
                <label for="check_in">Check-in Date:</label>
                <input type="date" id="check_in" name="check_in" required>
            </div>
            
            <div class="form-group">
                <label for="check_out">Check-out Date:</label>
                <input type="date" id="check_out" name="check_out" required>
            </div>
            
            <div class="form-group">
                <label for="room_type">Room Type:</label>
                <select id="room_type" name="room_type">
                    <option value="Single">Premium King Room</option>
                    <option value="Double">Deluxe Room</option>
                    <option value="Deluxe">Double Room</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="guests">Number of Guests:</label>
                <input type="number" id="guests" name="guests" min="1" required>
            </div>
            
            <input type="submit" class="submit-btn" value="Book Now"><br>
        <a href="dashboard.php" class="custom-btn">← Back</a>

        </form>
    </div>
</body>
</html>