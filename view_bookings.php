<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "abc");

// Only get bookings for the logged-in user
$stmt = $conn->prepare("SELECT * FROM bookingtb WHERE email = ?");
$stmt->bind_param("s", $_SESSION['user_email']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
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
            border-bottom: 3px solid #28a745;
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
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: white;
        }
        
        th {
            background-color: #28a745;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .action-links a {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 4px;
            margin-right: 5px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .edit-link {
            background-color: #007bff;
            color: white;
        }
        
        .edit-link:hover {
            background-color: #0056b3;
        }
        
        .delete-link {
            background-color: #dc3545;
            color: white;
        }
        
        .delete-link:hover {
            background-color: #c82333;
        }
        
        .home-btn {
            background-color: #6c757d;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
     
        .home-btn:hover {
            background-color: #545b62;
        }
       
        
        .btn-container {
            text-align: center;
            margin-top: 20px;
        }
        
        .no-bookings {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 30px;
        }
      .custom-btn {
    display: inline-block;
    background-color:rgb(45, 34, 105); /* same blue as your image */
    color: white;
    text-align: center;
    padding: 14px 0;
    width: 15%;
    border-radius: 8px;
    text-decoration: none;
    font-size: 18px;
    font-family: sans-serif;
    margin: 20px 0;
  }
   
    </style>
</head>
<body>
    <div class="container">
        <h2>My Bookings</h2>
        
        <div class="user-info">
            <p>Showing bookings for: <?= htmlspecialchars($_SESSION['user_email']) ?></p>
        </div>
        
        <?php if($result->num_rows > 0) { ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Phone</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Room Type</th>
                <th>Guests</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= $row['check_in'] ?></td>
                <td><?= $row['check_out'] ?></td>
                <td><?= htmlspecialchars($row['room_type']) ?></td>
                <td><?= $row['guests'] ?></td>
                <td class="action-links">
                    <a href="edit_booking.php?id=<?= $row['id'] ?>" class="edit-link">Edit</a>
                    <a href="delete_booking.php?id=<?= $row['id'] ?>" class="delete-link" onclick="return confirm('Delete this booking?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php } else { ?>
        <div class="no-bookings">
            <p>You haven't made any bookings yet.</p>
        </div>
        <?php } ?>

        <div class="btn-container">
            <form action="dashboard.php" method="get">
                <button type="submit" class="home-btn">Back to Home</button>
          <a href="Booking_Room.php" class="custom-btn">Back to Book</a>
        
            </form>
        </div>
    </div>
</body>
</html>