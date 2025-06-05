
<?php
// register.php - Enhanced validation
include 'db.php';

// CSRF Token generation


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
   
    
    // Input validation
    $errors = [];
    
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validate full name
    if (empty($fullname)) {
        $errors[] = "Full name is required";
    } elseif (strlen($fullname) < 2 || strlen($fullname) > 50) {
        $errors[] = "Full name must be between 2 and 50 characters";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $fullname)) {
        $errors[] = "Full name can only contain letters and spaces";
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } elseif (strlen($email) > 100) {
        $errors[] = "Email is too long";
    }
    
    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    } 
    
    if (empty($errors)) {
        // Check if email already exists using prepared statement
        $stmt = $conn->prepare("SELECT id FROM usertb WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Email already registered";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usertb (fullname, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullname, $email, $hashed_password);
            
            if ($stmt->execute()) {
                echo "<script>alert('Registration successful! Please login.'); window.location.href='log.html';</script>";
                exit();
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
    }
    
    // Display errors
    if (!empty($errors)) {
        $error_message = implode("\\n", $errors);
        echo "<script>alert('$error_message'); window.history.back();</script>";
        exit();
    }
}
?>


