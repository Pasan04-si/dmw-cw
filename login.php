
<?php
// login.php - Enhanced security
session_start();
include 'db.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Rate limiting (simple implementation)
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt'] = time();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check rate limiting
    if ($_SESSION['login_attempts'] >= 5 && (time() - $_SESSION['last_attempt']) < 900) { // 15 minutes
        echo "<script>alert('Too many login attempts. Please try again in 15 minutes.'); window.location.href='login.php';</script>";
        exit();
    }
    
    
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $errors = [];
    
    // Validate inputs
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    if (empty($errors)) {
        // Use prepared statement
        $stmt = $conn->prepare("SELECT id, email, password FROM usertb WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Reset login attempts on successful login
                $_SESSION['login_attempts'] = 0;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_id'] = $user['id'];
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                header("Location: dashboard.php");
                exit();
            } else {
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt'] = time();
                $errors[] = "Invalid email or password";
            }
        } else {
            $_SESSION['login_attempts']++;
            $_SESSION['last_attempt'] = time();
            $errors[] = "Invalid email or password";
        }
        $stmt->close();
    }
    
    if (!empty($errors)) {
        $error_message = implode("\\n", $errors);
        echo "<script>alert('$error_message'); window.history.back();</script>";
        exit();
    }
}
?>


