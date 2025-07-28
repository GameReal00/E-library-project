<?php
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
     if ($_SESSION['role'] == 'admin') {
          header("Location: admin_dashboard.php");
     } else {
          header("Location: user_dashboard.php");
     }
     exit();
}

// Handle form submissions
$login_error = "";
$register_error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
     // Database connection
     $host = 'localhost';
     $dbname = 'sot_e_library';
     $username = 'root'; // Change if needed
     $password = '';     // Change if needed

     try {
          $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     } catch (PDOException $e) {
          die("Database connection failed: " . $e->getMessage());
     }

     // Login logic
     if (isset($_POST['login'])) {
          $email = trim($_POST['login_email']);
          $password = $_POST['login_password'];

          $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
          $stmt->execute([$email]);
          $user = $stmt->fetch();

          if ($user && password_verify($password, $user['password'])) {
               $_SESSION['user_id'] = $user['user_id'];
               $_SESSION['fullname'] = $user['fullname'];
               $_SESSION['role'] = $user['role'];

               if ($user['role'] == 'admin') {
                    header("Location: admin_dashboard.php");
               } else {
                    header("Location: user_dashboard.php");
               }
               exit();
          } else {
               $login_error = "Invalid email or password.";
          }
     }

     // Registration logic
     if (isset($_POST['register'])) {
          $fullname = trim($_POST['register_fullname']);
          $email = trim($_POST['register_email']);
          $password = $_POST['register_password'];
          $confirm_password = $_POST['register_confirm_password'];

          // Validation
          if (empty($fullname) || empty($email) || empty($password)) {
               $register_error = "All fields are required.";
          } elseif ($password !== $confirm_password) {
               $register_error = "Passwords do not match.";
          } else {
               // Check if email exists
               $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
               $stmt->execute([$email]);
               if ($stmt->rowCount() > 0) {
                    $register_error = "Email already registered.";
               } else {
                    // Hash password and insert user
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
                    if ($stmt->execute([$fullname, $email, $hashed_password])) {
                         $register_success = "Registration successful! You can now login.";
                    } else {
                         $register_error = "Registration failed. Please try again.";
                    }
               }
          }
     }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>SOT E-Library - Login/Register</title>
     <style>
          * {
               margin: 0;
               padding: 0;
               box-sizing: border-box;
               font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          }

          body {
               background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
               min-height: 100vh;
               display: flex;
               justify-content: center;
               align-items: center;
               padding: 20px;
          }

          .container {
               display: flex;
               width: 900px;
               background: white;
               border-radius: 10px;
               box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
               overflow: hidden;
          }

          .form-container {
               flex: 1;
               padding: 40px;
          }

          .login-container {
               background: #f8f9fa;
          }

          h2 {
               color: #333;
               margin-bottom: 30px;
               text-align: center;
          }

          .form-group {
               margin-bottom: 20px;
          }

          label {
               display: block;
               margin-bottom: 5px;
               color: #555;
               font-weight: 500;
          }

          input[type="text"],
          input[type="email"],
          input[type="password"] {
               width: 100%;
               padding: 12px;
               border: 1px solid #ddd;
               border-radius: 5px;
               font-size: 16px;
               transition: border-color 0.3s;
          }

          input[type="text"]:focus,
          input[type="email"]:focus,
          input[type="password"]:focus {
               outline: none;
               border-color: #667eea;
          }

          .btn {
               width: 100%;
               padding: 12px;
               background: #667eea;
               color: white;
               border: none;
               border-radius: 5px;
               font-size: 16px;
               cursor: pointer;
               transition: background 0.3s;
          }

          .btn:hover {
               background: #5a6fd8;
          }

          .error {
               color: #e74c3c;
               font-size: 14px;
               margin-top: 5px;
          }

          .success {
               color: #27ae60;
               font-size: 14px;
               margin-top: 5px;
          }

          .welcome-section {
               flex: 1;
               background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
               color: white;
               padding: 40px;
               display: flex;
               flex-direction: column;
               justify-content: center;
               text-align: center;
          }

          .welcome-section h1 {
               font-size: 2.5em;
               margin-bottom: 20px;
          }

          .welcome-section p {
               font-size: 1.1em;
               line-height: 1.6;
               margin-bottom: 30px;
          }

          @media (max-width: 768px) {
               .container {
                    flex-direction: column;
                    width: 100%;
               }
          }
     </style>
</head>

<body>
     <div class="container">
          <div class="welcome-section">
               <h1>SOT E-Library</h1>
               <p>Welcome to the School of Technology Digital Library Management System. Access thousands of e-books,
                    journals, and articles anytime, anywhere.</p>
               <p>Join our community of learners and researchers today!</p>
          </div>

          <div class="form-container login-container">
               <h2>Login to Your Account</h2>
               <?php if (!empty($login_error)): ?>
                    <div class="error"><?php echo htmlspecialchars($login_error); ?></div>
               <?php endif; ?>

               <form method="POST">
                    <div class="form-group">
                         <label for="login_email">Email:</label>
                         <input type="email" id="login_email" name="login_email" required>
                    </div>

                    <div class="form-group">
                         <label for="login_password">Password:</label>
                         <input type="password" id="login_password" name="login_password" required>
                    </div>

                    <button type="submit" name="login" class="btn">Login</button>
               </form>

               <hr style="margin: 30px 0;">

               <h2>Register New Account</h2>
               <?php if (!empty($register_error)): ?>
                    <div class="error"><?php echo htmlspecialchars($register_error); ?></div>
               <?php endif; ?>
               <?php if (isset($register_success)): ?>
                    <div class="success"><?php echo htmlspecialchars($register_success); ?></div>
               <?php endif; ?>

               <form method="POST">
                    <div class="form-group">
                         <label for="register_fullname">Full Name:</label>
                         <input type="text" id="register_fullname" name="register_fullname" required>
                    </div>

                    <div class="form-group">
                         <label for="register_email">Email:</label>
                         <input type="email" id="register_email" name="register_email" required>
                    </div>

                    <div class="form-group">
                         <label for="register_password">Password:</label>
                         <input type="password" id="register_password" name="register_password" required>
                    </div>

                    <div class="form-group">
                         <label for="register_confirm_password">Confirm Password:</label>
                         <input type="password" id="register_confirm_password" name="register_confirm_password"
                              required>
                    </div>

                    <button type="submit" name="register" class="btn">Register</button>
               </form>
          </div>
     </div>
</body>

</html>