<?php
// ===========================
// REGISTER PAGE
// ===========================
// File: pages/register.php
// Fungsi: Form registrasi akun mahasiswa baru

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php?page=dashboard");
    exit();
}

$error = '';
$success = '';

$flash = getFlash();
if ($flash) {
    if ($flash['type'] === 'error') {
        $error = $flash['message'];
    } else {
        $success = $flash['message'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // db.php sudah di-include oleh index.php
    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    // Validasi
    if (strlen($password) < 6) {
        $error = "❌ Password minimal 6 karakter!";
    } elseif ($password !== $password_confirm) {
        $error = "❌ Password tidak sesuai!";
    } else {
        // Check apakah email sudah terdaftar
        $check_email = "SELECT id FROM users WHERE email = '$email'";
        $result = $conn->query($check_email);
        
        if ($result->num_rows > 0) {
            $error = "❌ Email sudah terdaftar!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert user baru (default role: mahasiswa, menunggu approval admin)
            $insert_query = "INSERT INTO users (nama, email, password, role, account_status) 
                            VALUES ('$nama', '$email', '$hashed_password', 'mahasiswa', 'pending')";
            
            if ($conn->query($insert_query) === TRUE) {
                setFlash('success', '✅ Registrasi berhasil! Akun Anda sedang menunggu persetujuan admin sebelum bisa login.');
                header('Location: index.php?page=login');
                exit();
            } else {
                $error = "❌ Terjadi kesalahan saat registrasi!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/svg+xml" href="assets/images/logo-green.svg">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
        }
        
        .register-wrapper {
            min-height: 100vh;
            display: flex;
            overflow: hidden;
            position: relative;
        }
        
        .register-wrapper::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            width: 60px;
            height: 100%;
            transform: translateX(-50%);
            z-index: 5;
            background: 
                repeating-linear-gradient(
                    90deg,
                    transparent,
                    transparent 10px,
                    rgba(255,255,255,0.6) 10px,
                    rgba(255,255,255,0.6) 15px,
                    transparent 15px,
                    transparent 30px
                ),
                repeating-linear-gradient(
                    90deg,
                    transparent,
                    transparent 5px,
                    rgba(255,255,255,0.3) 5px,
                    rgba(255,255,255,0.3) 10px,
                    transparent 10px,
                    transparent 30px
                );
            animation: waveShift 4s linear infinite;
        }
        
        @keyframes waveShift {
            0% { transform: translateX(-50%) translateY(0); }
            100% { transform: translateX(-50%) translateY(20px); }
        }

        
        .register-left {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            background: white;
            z-index: 1;
        }
        
        .register-right {
            flex: 1;
            background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        /* Decorative clouds */
        .cloud {
            position: absolute;
            background: white;
            border-radius: 100px;
            opacity: 0.2;
        }
        
        .cloud-1 {
            width: 200px;
            height: 80px;
            top: 20%;
            right: 10%;
        }
        
        .cloud-2 {
            width: 150px;
            height: 60px;
            bottom: 30%;
            left: 5%;
        }
        
        .cloud-3 {
            width: 180px;
            height: 70px;
            top: 60%;
            right: -20%;
        }
        
        .illustration {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
        }
        
        .illustration-icon {
            font-size: 120px;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .register-form-box {
            width: 100%;
            max-width: 400px;
        }
        
        .register-header {
            margin-bottom: 2rem;
        }
        
        .register-header img {
            height: 50px;
            margin-bottom: 1rem;
        }
        
        .register-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }
        
        .register-header p {
            color: #999;
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        
        .btn-register {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3);
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.95rem;
        }
        
        .login-link a {
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .alert {
            margin-bottom: 1.5rem;
            padding: 1rem;
            border-radius: 8px;
            border: none;
        }
        
        .alert-danger {
            background-color: #fee;
            color: #c33;
        }
        
        .alert-success {
            background-color: #efe;
            color: #3c3;
        }
        
        @media (max-width: 768px) {
            .register-wrapper {
                flex-direction: column;
            }
            
            .register-right {
                min-height: 200px;
            }
            
            .register-left {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
<div class="register-wrapper">
    <!-- Left Section -->
    <div class="register-left">
        <div class="register-form-box">
            <div class="register-header">
                <img src="assets/images/logo-green.svg" alt="Campus Event Hub">
                <h2>Create Account</h2>
                <p>Join us to discover amazing events</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="color: #c33;"></button>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="color: #3c3;"></button>
                </div>
                <a href="index.php?page=login" class="btn-register" style="display: block; text-align: center; text-decoration: none; color: white;">
                    Go to Login
                </a>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <input type="text" name="nama" placeholder="Full Name" required>
                    </div>

                    <div class="form-group">
                        <input type="email" name="email" placeholder="your@email.com" required>
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" placeholder="Password (min. 6 characters)" required>
                    </div>

                    <div class="form-group">
                        <input type="password" name="password_confirm" placeholder="Confirm Password" required>
                    </div>

                    <button type="submit" class="btn-register">Create Account</button>
                </form>

                <div class="login-link">
                    Already have an account? <a href="index.php?page=login">Sign In</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Section -->
    <div class="register-right">
        <div class="cloud cloud-1"></div>
        <div class="cloud cloud-2"></div>
        <div class="cloud cloud-3"></div>
        
        <div class="illustration">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 400" width="280" height="370">
                <!-- Phone -->
                <rect x="80" y="80" width="120" height="200" fill="#f97316" rx="15" stroke="#5a2d0c" stroke-width="3"/>
                <rect x="85" y="90" width="110" height="170" fill="#fb923c" rx="10"/>
                
                <!-- Phone screen details -->
                <circle cx="130" cy="130" r="25" fill="none" stroke="#fed7aa" stroke-width="2"/>
                <path d="M 130 105 Q 145 130 130 155 Q 115 130 130 105" fill="#fed7aa" opacity="0.3"/>
                <circle cx="130" cy="155" r="3" fill="#fed7aa"/>
                <line x1="110" y1="175" x2="150" y2="175" stroke="#fed7aa" stroke-width="1.5"/>
                <line x1="110" y1="182" x2="150" y2="182" stroke="#fed7aa" stroke-width="1.5"/>
                
                <!-- Lock icon on phone -->
                <g transform="translate(130, 220)">
                    <rect x="-12" y="-8" width="24" height="20" fill="white" rx="2" stroke="#ea580c" stroke-width="1.5"/>
                    <circle cx="0" cy="-3" r="5" fill="none" stroke="#ea580c" stroke-width="1.5"/>
                </g>
                
                <!-- Person (yellow jacket) -->
                <g transform="translate(60, 150)">
                    <!-- Head -->
                    <circle cx="25" cy="20" r="12" fill="#e8b86b"/>
                    
                    <!-- Hair -->
                    <path d="M 13 18 Q 13 8 25 8 Q 37 8 37 18" fill="#3d2817"/>
                    
                    <!-- Body (yellow jacket) -->
                    <rect x="10" y="35" width="30" height="40" fill="#fbbf24" rx="3"/>
                    
                    <!-- Jacket details -->
                    <line x1="25" y1="35" x2="25" y2="75" stroke="#f59e0b" stroke-width="1"/>
                    <circle cx="15" cy="50" r="2" fill="#f59e0b"/>
                    <circle cx="35" cy="50" r="2" fill="#f59e0b"/>
                    
                    <!-- Arms -->
                    <rect x="-5" y="40" width="15" height="25" fill="#e8b86b" rx="7" transform="rotate(-25 7.5 52)"/>
                    <rect x="40" y="40" width="15" height="25" fill="#e8b86b" rx="7" transform="rotate(25 47.5 52)"/>
                    
                    <!-- Hand pointing at phone -->
                    <circle cx="70" cy="45" r="4" fill="#e8b86b"/>
                    
                    <!-- Legs -->
                    <rect x="15" y="75" width="8" height="30" fill="#f5f5f5"/>
                    <rect x="27" y="75" width="8" height="30" fill="#f5f5f5"/>
                    
                    <!-- Shoes -->
                    <ellipse cx="19" cy="108" rx="6" ry="4" fill="#1a1a1a"/>
                    <ellipse cx="31" cy="108" rx="6" ry="4" fill="#1a1a1a"/>
                </g>
                
                <!-- Checkmark bubble -->
                <g transform="translate(160, 70)">
                    <circle cx="0" cy="0" r="28" fill="white" stroke="#5a2d0c" stroke-width="2"/>
                    <path d="M -8 0 L -2 6 L 8 -4" stroke="#ea580c" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                
                <!-- Lock icon (decorative) -->
                <g transform="translate(200, 160)">
                    <rect x="-15" y="-10" width="30" height="25" fill="white" rx="3" stroke="#5a2d0c" stroke-width="2"/>
                    <circle cx="0" cy="-5" r="8" fill="none" stroke="#ea580c" stroke-width="2"/>
                    <circle cx="0" cy="5" r="2" fill="#ea580c"/>
                </g>
                
                <!-- Bottom clouds -->
                <ellipse cx="40" cy="320" rx="35" ry="15" fill="white" opacity="0.25"/>
                <ellipse cx="240" cy="340" rx="40" ry="18" fill="white" opacity="0.2"/>
            </svg>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
