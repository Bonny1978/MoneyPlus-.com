<?php 
session_start();

	include("connection.php");
	include("functions.php");


	if($_SERVER['REQUEST_METHOD'] == "POST")
	{
		//something was posted
		$user_name = $_POST['user_name'];
		$password = $_POST['password'];

		if(!empty($user_name) && !empty($password) && !is_numeric($user_name))
		{

			//save to database
			$user_id = random_num(20);
			$query = "insert into users (user_id,user_name,password) values ('$user_id','$user_name','$password')";

			mysqli_query($con, $query);

			header("Location: login.php");
			die;
		}else
		{
			echo "Please enter some valid information!";
		}
	}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>

    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Google reCAPTCHA v2 Script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --blue-ish: rgb(0, 71, 164);
            --light-blue: rgb(0, 115, 215);
            --accent-glow: rgba(0, 115, 215, 0.3);
            --soft-bg: rgba(255, 255, 255, 0.15);
        }

        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('https://images.unsplash.com/photo-1501785888041-af3ef285b470?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80') center/cover no-repeat fixed;
            padding: 20px;
        }

        main {
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 1.2rem;
            border-radius: 2.5rem;
            background: var(--soft-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            transition: all 0.4s ease;
        }

        main:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.35);
        }

        #box {
            border-radius: 2.3rem;
            overflow: hidden;
        }

        .header {
            padding: 2.5rem 1.5rem 1.8rem;
            text-align: center;
        }

        .header h2 {
            color: white;
            font-size: clamp(28px, 8vw, 36px);
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            text-shadow: 0 3px 8px rgba(0,0,0,0.4);
        }

        .header .material-symbols-rounded {
            font-size: clamp(32px, 9vw, 42px);
            color: white;
            text-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }

        form {
            padding: 2rem 1.8rem;
            background: var(--soft-bg);
            backdrop-filter: blur(15px);
            border-top: 2px dashed rgba(255, 255, 255, 0.4);
        }

        /* Horizontal Name Fields */
        .name-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.8rem;
        }

        .name-group {
            position: relative;
            flex: 1;
        }

        .input-group {
            position: relative;
            margin-bottom: 1.8rem;
        }

        #text {
            width: 100%;
            padding: clamp(16px, 4vw, 20px) clamp(16px, 4vw, 20px) clamp(16px, 4vw, 20px) clamp(50px, 12vw, 60px);
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-left: 5px solid var(--light-blue);
            border-bottom: 5px solid var(--light-blue);
            border-radius: 0 0 0 18px;
            font-size: clamp(15px, 4vw, 17px);
            color: white;
            transition: all 0.5s ease;
        }

        #text::placeholder {
            color: rgba(255, 255, 255, 0.8);
            letter-spacing: 1.5px;
            font-size: clamp(14px, 3.5vw, 16px);
        }

        #text:focus {
            background: rgba(255, 255, 255, 0.35);
            box-shadow: 0 8px 30px var(--accent-glow);
            transform: translateY(-4px);
        }

        .input-icon {
            position: absolute;
            left: clamp(16px, 4vw, 20px);
            top: 50%;
            transform: translateY(-50%);
            font-size: clamp(24px, 6vw, 28px);
            color: white;
            opacity: 0.9;
            transition: all 0.4s ease;
            pointer-events: none;
        }

        #text:focus + .input-icon {
            color: var(--light-blue);
            transform: translateY(-50%) scale(1.15);
        }

        .toggle-password {
            position: absolute;
            right: clamp(16px, 4vw, 20px);
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: clamp(22px, 5.5vw, 26px);
            color: white;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .toggle-password:hover {
            opacity: 1;
            color: #a0d8ff;
        }

        /* Agreements Section */
        .agreements {
            margin: 1.5rem 0 1.8rem;
        }

        .agreement-checkbox {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 1rem;
        }

        .agreement-checkbox input[type="checkbox"] {
            width: 22px;
            height: 22px;
            margin-top: 4px;
            accent-color: var(--light-blue);
            cursor: pointer;
            flex-shrink: 0;
        }

        .agreement-text {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.5;
        }

        .agreement-text a {
            color: #a0d8ff;
            text-decoration: underline;
        }

        .terms-scroll {
            max-height: 120px;
            overflow-y: auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 12px;
            border-radius: 12px;
            margin-top: 8px;
            font-size: 13px;
            line-height: 1.6;
        }

        /* reCAPTCHA */
        .recaptcha-container {
            margin: 1.5rem 0;
            display: flex;
            justify-content: center;
        }

        #button {
            width: 100%;
            padding: clamp(18px, 5vw, 22px);
            background: linear-gradient(45deg, var(--light-blue), #80c4ff);
            color: white;
            font-size: clamp(17px, 4.5vw, 20px);
            font-weight: 600;
            border: none;
            border-radius: 18px;
            cursor: pointer;
            transition: all 0.5s ease;
            box-shadow: 0 8px 25px rgba(0, 115, 215, 0.4);
            position: relative;
            overflow: hidden;
        }

        #button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -150%;
            width: 200%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.7s ease;
        }

        #button:hover::before {
            left: 150%;
        }

        #button:hover {
            background: linear-gradient(270deg, var(--light-blue), #80c4ff);
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 115, 215, 0.5);
        }

        #button:active {
            transform: translateY(0);
        }

        p {
            text-align: center;
            margin-top: 2rem;
            font-size: clamp(14px, 3.8vw, 16px);
            color: white;
            font-weight: 500;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        p a {
            color: #e0f7ff;
            text-decoration: none;
            font-weight: 700;
        }

        p a:hover {
            color: white;
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .name-row {
                flex-direction: column;
                gap: 1.2rem;
            }
            .terms-scroll {
                max-height: 100px;
            }
        }
    </style>
</head>
<body>

    <main>
        <div id="box">
            <div class="header">
                <h2>
                    <span class="material-symbols-rounded">account_circle</span>
                    Sign Up
                </h2>
            </div>

            <form method="post">
                <!-- First Name & Last Name - Horizontal -->
                <div class="name-row">
                    <div class="name-group input-group">
                        <span class="material-symbols-rounded input-icon">person</span>
                        <input id="text" type="text" name="first_name" placeholder="First Name" required>
                    </div>

                    <div class="name-group input-group">
                        <span class="material-symbols-rounded input-icon">person</span>
                        <input id="text" type="text" name="last_name" placeholder="Last Name" required>
                    </div>
                </div>

                <!-- Username -->
                <div class="input-group">
                    <span class="material-symbols-rounded input-icon">alternate_email</span>
                    <input id="text" type="text" name="user_name" placeholder="Username" required>
                </div>

                <!-- Password -->
                <div class="input-group">
                    <span class="material-symbols-rounded input-icon">lock</span>
                    <input id="text" type="password" name="password" placeholder="Password" required>
                    <span class="material-symbols-rounded toggle-password" onclick="togglePassword()">visibility</span>
                </div>

                <!-- Agreements with Checkbox -->
                <div class="agreements">
                    <div class="agreement-checkbox">
                        <input type="checkbox" id="agree" name="agree" required>
                        <label for="agree" class="agreement-text">
                            I agree to the <a href="#">Terms of Service</a>, <a href="#">Privacy Policy</a>, and <a href="#">Electronic Communications Agreement</a>.
                            <div class="terms-scroll">
                                <strong>Terms of Service (Full Text)</strong><br><br>
                                By creating an account, you agree to be bound by these Terms of Service ("Terms"). These Terms govern your access to and use of MoneyPlus+ services, including the website, mobile app, and all features ("Services").<br><br>
                                1. Eligibility: You must be at least 18 years old and capable of forming a binding contract.<br>
                                2. Account Responsibility: You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.<br>
                                3. Prohibited Activities: You agree not to use the Services for any unlawful purpose or in violation of these Terms, including but not limited to money laundering, fraud, or terrorism financing.<br>
                                4. Fees & Transactions: MoneyPlus+ may charge fees for certain services. All fees are non-refundable unless otherwise stated.<br>
                                5. Electronic Communications: You consent to receive all communications electronically.<br>
                                6. Termination: We may terminate or suspend your account at any time for violation of these Terms.<br>
                                7. Limitation of Liability: MoneyPlus+ shall not be liable for any indirect, incidental, or consequential damages.<br>
                                8. Governing Law: These Terms are governed by the laws of the State of Delaware.<br><br>
                                Last updated: December 2025.
                            </div>
                        </label>
                    </div>
                </div>

                <!-- reCAPTCHA -->
                <div class="recaptcha-container">
                    <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div>
                </div>

                <input id="button" type="submit" value="Signup">
            </form>

            <p>
                Have an account?
                <a href="login.php">Log in here</a>
            </p>
        </div>
    </main>

    <script>
        function togglePassword() {
            const passwordField = document.querySelector('input[name="password"]');
            const icon = document.querySelector('.toggle-password');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                passwordField.type = 'password';
                icon.textContent = 'visibility';
            }
        }
    </script>

</body>
</html>