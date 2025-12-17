<?php 
session_start();
include("connection.php");
include("functions.php");

$message = '';
$status = ''; // 'success' or 'error'
$show_form = true; // Controls whether to show the login form or success message

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    // Something was posted
    $user_name = trim($_POST['user_name']);
    $password = $_POST['password'];

    if(!empty($user_name) && !empty($password) && !is_numeric($user_name))
    {
        // Read from database
        $query = "SELECT * FROM users WHERE user_name = '$user_name' LIMIT 1";
        $result = mysqli_query($con, $query);

        if($result && mysqli_num_rows($result) > 0)
        {
            $user_data = mysqli_fetch_assoc($result);
            
            if($user_data['password'] === $password)
            {
                // Login successful
                $_SESSION['user_id'] = $user_data['user_id'];
                $_SESSION['username'] = $user_name;

                $message = 'Login successful! Redirecting to dashboard...';
                $status = 'success';
                $show_form = false;

                // Auto redirect after 5 seconds
                header("Refresh: 5; url=index.php");

                // === PERSONALIZATION: Pass user data to frontend via localStorage ===
                // Escape data for safe JavaScript injection
                $js_user_name = htmlspecialchars($user_data['user_name'], ENT_QUOTES);
                $js_full_name = htmlspecialchars($user_data['full_name'] ?? 'Not set', ENT_QUOTES);
                $js_referral_code = htmlspecialchars($user_data['referral_code'] ?? 'MP' . strtoupper(substr(md5($user_data['user_id']), 0, 6)), ENT_QUOTES);
                $js_total_earnings = number_format((float)($user_data['total_earnings'] ?? 0), 2);
                $js_referrals = (int)($user_data['referrals_count'] ?? 0);
                $js_join_date = !empty($user_data['date_joined']) 
                    ? date('F Y', strtotime($user_data['date_joined'])) 
                    : 'December 2025';
            }
            else
            {
                $message = 'Wrong username or password!';
                $status = 'error';
            }
        }
        else
        {
            $message = 'Wrong username or password!';
            $status = 'error';
        }
    }
    else
    {
        $message = 'Please enter valid information!';
        $status = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Material Symbols & Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
            background: url('https://images.unsplash.com/photo-1501785888041-af3ef285b470?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') no-repeat center center/cover;
            background-attachment: fixed;
        }

        /* Floating Particles */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(100, 200, 255, 0.3);
            border-radius: 50%;
            animation: float linear infinite;
        }

        @keyframes float {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) scale(1); opacity: 0; }
        }

        .container {
            width: 90%;
            max-width: 440px;
            padding: 1.5em;
            border-radius: 2.2em;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(100, 200, 255, 0.3);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
            position: relative;
            z-index: 2;
            overflow: hidden;
        }

        .inner-card {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 2em;
            padding: 2.5em;
            text-align: center;
            box-shadow: inset 0 0 25px rgba(100, 220, 255, 0.15);
        }

        .header h2 {
            color: #005f8f;
            font-size: 32px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .header .material-symbols-rounded {
            font-size: 36px;
            color: #005f8f;
        }

        .subtitle {
            color: #005f8f;
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 2em;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 18px 18px 18px 56px;
            border: none;
            border-bottom: 3px solid #005f8f;
            border-left: 3px solid #005f8f;
            border-radius: 0 0 0 16px;
            background: rgba(255, 255, 255, 0.78);
            font-size: 16px;
            color: #005f8f;
            transition: all 0.4s ease;
        }

        input::placeholder {
            color: #005f8f;
            opacity: 0.8;
            letter-spacing: 1px;
        }

        input:focus {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 25px rgba(0, 95, 143, 0.3);
            transform: translateY(-2px);
        }

        .input-icon, .toggle-password {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            color: #005f8f;
        }

        .input-icon { left: 18px; }
        .toggle-password { right: 18px; cursor: pointer; opacity: 0.7; }
        .toggle-password:hover { opacity: 1; }

        #login-btn {
            width: 100%;
            padding: 18px;
            margin: 20px 0;
            background: linear-gradient(270deg, #0073d7, #00a0e0);
            color: white;
            font-size: 19px;
            font-weight: 600;
            border: none;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.8s ease;
            box-shadow: 0 6px 20px rgba(0, 160, 224, 0.4);
            position: relative;
            overflow: hidden;
        }

        #login-btn:hover {
            background: linear-gradient(45deg, #0073d7, #00a0e0);
            transform: translateY(-4px);
        }

        #login-btn.loading {
            background: #005f8f;
            cursor: not-allowed;
        }

        #login-btn.loading span { visibility: hidden; }

        #login-btn.loading::after {
            content: '';
            position: absolute;
            width: 24px;
            height: 24px;
            border: 3px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .success-message {
            color: #007f5f;
            font-size: 20px;
            font-weight: 600;
            margin: 20px 0;
        }

        .loader {
            border: 6px solid #f3f3f3;
            border-top: 6px solid #005f8f;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 30px auto;
        }

        .error {
            color: #c0392b;
            font-weight: 600;
            margin: 15px 0;
            font-size: 16px;
        }

        p a {
            color: #a0e8ff;
            text-decoration: none;
            font-weight: 600;
        }

        p a:hover {
            color: white;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .container { padding: 1em; }
            .header h2 { font-size: 28px; }
        }
    </style>
</head>
<body>

    <!-- Floating Particles -->
    <div class="particles">
        <div class="particle" style="width:8px;height:8px;left:10%;animation-duration:20s;animation-delay:0s;"></div>
        <div class="particle" style="width:11px;height:11px;left:25%;animation-duration:18s;animation-delay:2s;"></div>
        <div class="particle" style="width:7px;height:7px;left:40%;animation-duration:22s;animation-delay:5s;"></div>
        <div class="particle" style="width:13px;height:13px;left:55%;animation-duration:16s;animation-delay:1s;"></div>
        <div class="particle" style="width:9px;height:9px;left:70%;animation-duration:24s;animation-delay:7s;"></div>
        <div class="particle" style="width:10px;height:10px;left:85%;animation-duration:19s;animation-delay:4s;"></div>
    </div>

    <div class="container">
        <div class="inner-card">

            <?php if ($show_form): ?>

                <div class="header">
                    <h2>
                        <span class="material-symbols-rounded">login</span>
                        Login
                    </h2>
                    <div class="subtitle">Welcome back! Glad to see you again.</div>
                </div>

                <?php if ($status === 'error'): ?>
                    <div class="error"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form method="post" onsubmit="handleLogin(event)">
                    <div class="input-group">
                        <span class="material-symbols-rounded input-icon">person</span>
                        <input type="text" name="user_name" placeholder="Username" required>
                    </div>

                    <div class="input-group">
                        <span class="material-symbols-rounded input-icon">lock</span>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <span class="material-symbols-rounded toggle-password" onclick="togglePass()">visibility</span>
                    </div>

                    <button type="submit" id="login-btn">
                        <span>Login</span>
                    </button>
                </form>

                <p>
                    Don't have an account?
                    <a href="signup.php">Sign up here</a>
                </p>

            <?php else: ?>

                <!-- Success Screen with Animation -->
                <div class="header">
                    <h2>
                        <span class="material-symbols-rounded" style="font-size:48px;color:#007f5f;">check_circle</span>
                        Success!
                    </h2>
                </div>

                <div class="loader"></div>
                <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
                <p style="color:#005f8f;font-size:15px;">
                    Verifying it's you...<br>
                </p>

                <!-- Inject user data into localStorage before redirect -->
                <script>
                    // Set personalized data for the dashboard
                    localStorage.setItem('user_name', '<?php echo $js_user_name; ?>');
                    localStorage.setItem('full_name', '<?php echo $js_full_name; ?>');
                    localStorage.setItem('referral_code', '<?php echo $js_referral_code; ?>');
                    localStorage.setItem('total_earnings', '<?php echo $js_total_earnings; ?>');
                    localStorage.setItem('referrals_count', '<?php echo $js_referrals; ?>');
                    localStorage.setItem('join_date', '<?php echo $js_join_date; ?>');
                </script>

            <?php endif; ?>

        </div>
    </div>

    <script>
        function togglePass() {
            const pass = document.getElementById('password');
            const icon = document.querySelector('.toggle-password');
            if (pass && icon) {
                if (pass.type === 'password') {
                    pass.type = 'text';
                    icon.textContent = 'visibility_off';
                } else {
                    pass.type = 'password';
                    icon.textContent = 'visibility';
                }
            }
        }

        function handleLogin(event) {
            const btn = document.getElementById('login-btn');
            if (btn) {
                btn.classList.add('loading');
                btn.querySelector('span').textContent = 'Logging in...';
            }
        }
    </script>

</body>
</html>