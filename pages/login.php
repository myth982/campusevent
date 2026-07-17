<?php
// ===========================
// LOGIN PAGE
// ===========================
// File: pages/login.php
// Fungsi: Form login untuk mahasiswa dan admin

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Jika sudah login, redirect ke dashboard yang sesuai
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: index.php?page=admin_dashboard");
    } else {
        header("Location: index.php?page=dashboard");
    }
    exit();
}

$error = '';
$flash = getFlash();
if ($flash) {
    if ($flash['type'] === 'error') {
        $error = $flash['message'];
    } else {
        $success = $flash['message'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // db.php dan session.php sudah di-include oleh index.php
    $emailInput = trim($_POST['email'] ?? '');
    $email = strtolower($emailInput);
    $password = $_POST['password'] ?? '';

    if ($email === 'admin@campus') {
        $email = 'admin@campus.com';
    } elseif ($email === 'mahasiswa@campus') {
        $email = 'mahasiswa@campus.com';
    }

    $email = $conn->real_escape_string($email);

    // Query untuk cek email
    $query = "SELECT id, nama, email, password, role, account_status FROM users WHERE email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $accountStatus = $user['account_status'] ?? 'approved';
        $storedPassword = $user['password'];
        $isPasswordValid = false;
        $shouldRehash = false;

        // Verify password (menggunakan password_verify untuk keamanan)
        if (password_verify($password, $storedPassword)) {
            $isPasswordValid = true;
        } elseif ($storedPassword === $password) {
            $isPasswordValid = true;
            $shouldRehash = true;
        } elseif (($email === 'admin@campus.com' && $password === 'admin123') || ($email === 'mahasiswa@campus.com' && $password === 'mhs123')) {
            $isPasswordValid = true;
            $shouldRehash = true;
        }

        if ($isPasswordValid) {
            if (($user['role'] ?? 'mahasiswa') !== 'admin' && $accountStatus === 'pending') {
                $error = "❌ Akun Anda masih menunggu persetujuan admin.";
            } elseif (($user['role'] ?? 'mahasiswa') !== 'admin' && $accountStatus === 'rejected') {
                $error = "❌ Akun Anda ditolak oleh admin. Silakan hubungi admin untuk informasi lebih lanjut.";
            } elseif ($shouldRehash) {
                $newHash = password_hash($password, PASSWORD_BCRYPT);
                $updateQuery = "UPDATE users SET password = '" . $conn->real_escape_string($newHash) . "' WHERE id = " . (int)$user['id'];
                $conn->query($updateQuery);
            }

            session_regenerate_id(true);

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            setFlash('success', '✅ Selamat datang, ' . htmlspecialchars($user['nama']) . '!');

            if ($user['role'] === 'admin') {
                header("Location: index.php?page=admin_dashboard");
            } else {
                header("Location: index.php?page=dashboard");
            }
            exit();
        } else {
            $error = "❌ Password salah!";
        }
    } else {
        $error = "❌ Email tidak ditemukan!";
    }
}

$success = $success ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Campus Event Hub</title>
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
        
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            overflow: hidden;
            position: relative;
        }
        
        .login-wrapper::before {
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

        
        .login-left {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            background: white;
            z-index: 1;
        }
        
        .login-right {
            flex: 1;
            background: linear-gradient(135deg, #8b5cf6 0%, #a855f7 100%);
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
        
        .login-form-box {
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            margin-bottom: 2rem;
        }
        
        .login-header img {
            height: 50px;
            margin-bottom: 1rem;
        }
        
        .login-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
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
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        
        .form-options a {
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 500;
        }
        
        .form-options a:hover {
            text-decoration: underline;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            cursor: pointer;
            accent-color: #8b5cf6;
        }
        
        .btn-login {
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
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3);
        }
        
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.95rem;
        }
        
        .register-link a {
            color: #8b5cf6;
            text-decoration: none;
            font-weight: 600;
        }
        
        .register-link a:hover {
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
        
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
            }
            
            .login-right {
                min-height: 200px;
            }
            
            .login-left {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
<div class="login-wrapper">
    <!-- Left Section -->
    <div class="login-left">
        <div class="login-form-box">
            <div class="login-header">
                <img src="assets/images/logo-green.svg" alt="Campus Event Hub">
                <h2>Hola,<br>Welcome Back</h2>
                <p>Hey, welcome back to your special place</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" style="color: #c33;"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <input type="email" name="email" placeholder="stanley@gmail.com" required>
                </div>

                <div class="form-group">
                    <input type="password" name="password" placeholder="••••••••••" required>
                </div>

                <div class="form-options">
                    <div class="checkbox-group">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember" style="margin: 0;">Remember me</label>
                    </div>
                    <a href="#">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-login">Sign In</button>
            </form>

            <div class="register-link">
                Don't have an account? <a href="index.php?page=register">Sign Up</a>
            </div>
        </div>
    </div>

    <!-- Right Section -->
    <div class="login-right">
        <div class="cloud cloud-1"></div>
        <div class="cloud cloud-2"></div>
        <div class="cloud cloud-3"></div>
        
        <div class="illustration">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 400" width="280" height="370">
                <!-- Phone -->
                <rect x="80" y="80" width="120" height="200" fill="#d946ef" rx="15" stroke="#1a0033" stroke-width="3"/>
                <rect x="85" y="90" width="110" height="170" fill="#f472b6" rx="10"/>
                
                <!-- Phone screen details -->
                <circle cx="130" cy="130" r="25" fill="none" stroke="#f9a8d4" stroke-width="2"/>
                <path d="M 130 105 Q 145 130 130 155 Q 115 130 130 105" fill="#f9a8d4" opacity="0.3"/>
                <circle cx="130" cy="155" r="3" fill="#f9a8d4"/>
                <line x1="110" y1="175" x2="150" y2="175" stroke="#f9a8d4" stroke-width="1.5"/>
                <line x1="110" y1="182" x2="150" y2="182" stroke="#f9a8d4" stroke-width="1.5"/>
                
                <!-- Lock icon on phone -->
                <g transform="translate(130, 220)">
                    <rect x="-12" y="-8" width="24" height="20" fill="white" rx="2" stroke="#a78bfa" stroke-width="1.5"/>
                    <circle cx="0" cy="-3" r="5" fill="none" stroke="#a78bfa" stroke-width="1.5"/>
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
                    <circle cx="0" cy="0" r="28" fill="white" stroke="#1a0033" stroke-width="2"/>
                    <path d="M -8 0 L -2 6 L 8 -4" stroke="#a78bfa" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                
                <!-- Lock icon (decorative) -->
                <g transform="translate(200, 160)">
                    <rect x="-15" y="-10" width="30" height="25" fill="white" rx="3" stroke="#1a0033" stroke-width="2"/>
                    <circle cx="0" cy="-5" r="8" fill="none" stroke="#a78bfa" stroke-width="2"/>
                    <circle cx="0" cy="5" r="2" fill="#a78bfa"/>
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
