<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = $_SESSION['user_id'];
$message = "";

// Fetch user info
$stmt = $conn->prepare("SELECT fullname, email FROM usertb WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = htmlspecialchars(trim($_POST['fullname']));
  

    $stmt = $conn->prepare("UPDATE usertb SET fullname = ? WHERE id = ?");
    $stmt->bind_param("si",$fullname,$id);
    if ($stmt->execute()) {
        $message = "<p class='success-message'>Profile updated successfully!</p>";
        $user['fullname'] = $fullname;
        
    } else {
        $message = "<p class='error-message'>Error updating profile.</p>";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Hotel Bougainvilla</title>
    <link rel="stylesheet" href="style1.css">
</head>
<body>
<header>
    <div class="navbar">
        <div class="logo">Hotel Bougainvilla</div>
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</header>

<main class="form-container">
    <h2>My Profile</h2>
    <?= $message ?>
    <form method="POST" action="">
        <label for="fullname">Full Name:</label>
      
        <input type="text" name="fullname" id="fullname" required value="<?= htmlspecialchars($user['fullname']) ?>">


        <label for="email">Email (readonly):</label>
        <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>

        

        <button type="submit">Update Profile</button>
    </form>
</main>
</body>
</html>
