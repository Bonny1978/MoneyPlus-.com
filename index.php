<?php
session_start();
include("connection.php");
include("functions.php");

$user_data = null;
$is_logged_in = false;

if (isset($_SESSION['user_id'])) {
    $user_data = check_login($con);
    $is_logged_in = true;
}

// Handle password change
$change_password_message = '';
if ($is_logged_in && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($current_password !== $user_data['password']) {
        $change_password_message = "Current password is incorrect!";
    } elseif ($new_password !== $confirm_password) {
        $change_password_message = "New passwords do not match!";
    } elseif (strlen($new_password) < 6) {
        $change_password_message = "New password must be at least 6 characters!";
    } else {
        // Update password in database
        $user_id = $user_data['user_id'];
        $update_query = "UPDATE users SET password = '$new_password' WHERE user_id = '$user_id'";
        if (mysqli_query($con, $update_query)) {
            $change_password_message = "Password changed successfully!";
            // Update session data
            $user_data['password'] = $new_password;
        } else {
            $change_password_message = "Error updating password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MONEYPLUS+</title>
    <style>
        :root {
            --bg-image: url('https://images.unsplash.com/photo-1501785888041-af3ef285b470?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
            --card-bg: rgba(255,255,255,0.25);
            --card-border: rgba(255,255,255,0.3);
            --text-primary: #ffffff;
            --text-secondary: rgba(255,255,255,0.9);
            --text-shadow: 0 1px 2px rgba(0,0,0,0.7);
            --profile-bg: rgba(255,255,255,0.3);
            --button-bg: rgba(255,255,255,0.25);
            --vip-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        body.dark-mode {
            --bg-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1501785888041-af3ef285b470?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
            --card-bg: rgba(0,0,0,0.4);
            --card-border: rgba(255,255,255,0.15);
            --text-primary: #ffffff;
            --text-secondary: rgba(255,255,255,0.85);
            --text-shadow: 0 1px 3px rgba(0,0,0,0.8);
            --profile-bg: rgba(255,255,255,0.2);
            --button-bg: rgba(255,255,255,0.15);
            --vip-gradient: linear-gradient(135deg, #8a2be2 0%, #4b0082 100%);
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background: var(--bg-image) center/cover no-repeat fixed;
            color: var(--text-primary);
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            transition: background 0.4s ease;
        }
        .container {
            width: 100%;
            max-width: 375px;
            padding: 20px;
            box-sizing: border-box;
            position: relative;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            position: relative;
        }
        /* Stylish MONEYPLUS+ header */
        .header h1 {
            font-size: 36px;
            font-weight: 900;
            margin: 0;
            background: linear-gradient(90deg, #00ffea, #00c3ff, #0072ff, #8b00ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 3px 12px rgba(0, 195, 255, 0.5);
            letter-spacing: 2px;
        }
        /* Welcome Message */
        .welcome-message {
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            margin: 8px 0 6px 0;
            color: var(--text-primary);
            text-shadow: var(--text-shadow);
            opacity: 0.95;
        }
        .welcome-message span {
            background: linear-gradient(90deg, #ff00ff, #00ffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
        }
        /* NEW: Current Date Display */
        .current-date {
            text-align: center;
            font-size: 15px;
            color: var(--text-secondary);
            margin: 0 0 20px 0;
            opacity: 0.8;
            text-shadow: var(--text-shadow);
        }
        .profile-wrapper {
            position: relative;
        }
        .profile-icon {
            width: 52px;
            height: 52px;
            background-color: var(--profile-bg);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            border: 1px solid var(--card-border);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 26px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .profile-icon:hover {
            background-color: rgba(255,255,255,0.4);
        }
        .profile-menu {
            position: absolute;
            top: 65px;
            right: 0;
            width: 240px;
            background-color: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1000;
            overflow: hidden;
        }
        .profile-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .profile-menu-item {
            padding: 14px 16px;
            color: var(--text-primary);
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .profile-menu-item:hover {
            background-color: rgba(255,255,255,0.15);
        }
        .profile-menu-item.logout {
            color: #ff3b30;
        }
        .profile-info {
            padding: 16px;
            border-bottom: 1px solid var(--card-border);
            font-size: 15px;
        }
        .profile-info div {
            margin-bottom: 8px;
        }
        .profile-info strong {
            color: var(--text-primary);
        }
        .dark-mode-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 24px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255,255,255,0.2);
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #6200ee;
        }
        input:checked + .slider:before {
            transform: translateX(24px);
        }
        .balance-card,
        .button,
        .notifications-container,
        .vip-section {
            background-color: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        .balance-card {
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 30px;
        }
        .balance-label {
            font-size: 16px;
            color: var(--text-secondary);
            text-shadow: var(--text-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .balance-amount {
            font-size: 48px;
            font-weight: 700;
            margin: 12px 0;
            color: var(--text-primary);
            text-shadow: var(--text-shadow);
        }
        /* Colourful VIP Level Bar */
        .vip-level-bar {
            margin-top: 20px;
            text-align: center;
        }
        .vip-level-text {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 10px;
            background: linear-gradient(90deg, #ff00cc, #3333ff, #00ffcc, #ffcc00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }
        .vip-progress-container {
            height: 12px;
            background-color: rgba(255,255,255,0.2);
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .vip-progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, #ff00cc, #3333ff, #00ffcc, #ffcc00);
            border-radius: 6px;
            transition: width 1s ease;
            box-shadow: 0 0 10px rgba(255,0,204,0.6);
        }
        .buttons {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
        }
        .button {
            background-color: var(--button-bg);
            border-radius: 12px;
            padding: 16px;
            font-size: 17px;
            font-weight: 600;
            color: var(--text-primary);
            text-shadow: var(--text-shadow);
            flex: 1;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .button:active {
            transform: scale(0.96);
        }
        .notifications-container {
            height: 180px;
            padding: 16px;
            margin-bottom: 30px;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
        }
        .notifications-container::before {
            content: "Recent Activity";
            position: absolute;
            top: 8px;
            left: 16px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            text-shadow: var(--text-shadow);
            z-index: 1;
        }
        .ticker-wrapper {
            height: 140px;
            overflow: hidden;
            margin-top: 30px;
        }
        .notification-ticker {
            display: flex;
            flex-direction: column;
            animation: scroll-up 35s linear infinite;
        }
        .notification-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            font-size: 16px;
            color: var(--text-primary);
            text-shadow: var(--text-shadow);
            padding: 10px 0;
            flex-shrink: 0;
            white-space: nowrap;
        }
        .phone { color: #a0eaff; }
        .action-cashout { color: #90ee90; font-weight: 600; }
        .action-add { color: #00bfff; font-weight: 600; }
        .amount { color: #ffd700; font-weight: bold; }
        .vip {
            background-color: rgba(255,215,0,0.4);
            color: #ffffff;
            padding: 4px 12px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: bold;
            border: 1px solid var(--card-border);
        }
        @keyframes scroll-up {
            0% { transform: translateY(0); }
            100% { transform: translateY(-50%); }
        }
        .vip-tabs {
            display: flex;
            margin-bottom: 20px;
            background-color: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--card-border);
        }
        .vip-tab {
            flex: 1;
            padding: 14px;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s;
        }
        .vip-tab.active {
            background: var(--vip-gradient);
            color: white;
        }
        .vip-tab:active {
            transform: scale(0.96);
        }
        .vip-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }
        .vip-section {
            background: var(--vip-gradient);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .vip-section:active {
            transform: scale(0.96);
        }
        .vip-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }
        .vip-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 8px 0;
            text-shadow: var(--text-shadow);
        }
        .vip-subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
            text-shadow: var(--text-shadow);
        }
        .help-footer {
            width: 100%;
            max-width: 375px;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            line-height: 1.6;
            color: var(--text-secondary);
            text-shadow: var(--text-shadow);
            opacity: 0.9;
            margin-top: auto;
        }
        .help-footer a {
            color: #00d4ff;
            text-decoration: none;
            font-weight: 600;
        }
        .help-footer a:hover {
            text-decoration: underline;
        }
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background-color: var(--card-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 24px;
            text-align: left;
            max-width: 320px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }
        .modal-title {
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
            color: var(--text-primary);
            text-shadow: var(--text-shadow);
        }
        .modal input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: rgba(255,255,255,0.2);
            border: 1px solid var(--card-border);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 16px;
        }
        .modal-message {
            text-align: center;
            margin: 12px 0;
            font-size: 15px;
            color: <?php echo $change_password_message && strpos($change_password_message, 'successfully') !== false ? '#90ee90' : '#ff6b6b'; ?>;
        }
        .modal-close {
            background-color: rgba(0,122,255,0.8);
            border: none;
            border-radius: 12px;
            padding: 10px 24px;
            font-size: 16px;
            cursor: pointer;
            margin: 16px auto 0;
            display: block;
            color: white;
        }
        .change-pass-btn {
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 16px;
            width: 100%;
            text-align: left;
            padding: 14px 16px;
            cursor: pointer;
        }
        .change-pass-btn:hover {
            background-color: rgba(255,255,255,0.15);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MONEYPLUS+</h1>
            <div class="profile-wrapper">
                <div class="profile-icon" id="profileBtn">👤</div>
                <div class="profile-menu" id="profileMenu">
                    <?php if ($is_logged_in): ?>
                        <div class="profile-info">
                            <div><strong>Username:</strong> <?php echo htmlspecialchars($user_data['user_name']); ?></div>
                            <div><strong>Password:</strong> <?php echo str_repeat('•', strlen($user_data['password'])); ?></div>
                        </div>
                        <div class="profile-menu-item change-pass-btn" id="changePassBtn">Change Password</div>
                    <?php else: ?>
                        <div class="profile-menu-item">Please log in</div>
                    <?php endif; ?>
                    <div class="profile-menu-item">Profile Settings</div>
                    <div class="profile-menu-item">Account</div>
                    <div class="profile-menu-item dark-mode-item">
                        Dark Mode
                        <label class="toggle-switch">
                            <input type="checkbox" id="darkModeSwitch">
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="profile-menu-item logout" id="logoutBtn">Log Out</div>
                </div>
            </div>
        </div>

        <!-- Welcome Message with Username -->
        <div class="welcome-message" id="welcomeMessage">
            Welcome, <span id="userNameDisplay">Guest</span>!
        </div>

        <!-- Current Date Display -->
        <div class="current-date">
            December 17, 2025
        </div>

        <div class="balance-card">
            <div class="balance-label">
                <span>Cash balance</span>
                <span>></span>
            </div>
            <div class="balance-amount" id="balanceAmount">$0.00</div>
            <!-- Colourful VIP Level Bar -->
            <div class="vip-level-bar">
                <div class="vip-level-text" id="vipLevelText">Basic Member</div>
                <div class="vip-progress-container">
                    <div class="vip-progress-fill" id="vipProgressFill"></div>
                </div>
            </div>
        </div>
        <div class="buttons">
            <button class="button" id="addCashBtn">Add Cash</button>
            <button class="button" id="cashOutBtn">Cash Out</button>
        </div>
        <div class="notifications-container">
            <div class="ticker-wrapper">
                <div class="notification-ticker" id="notificationTicker"></div>
            </div>
        </div>
        <div class="vip-tabs">
            <div class="vip-tab active" data-tab="upgrade">Upgrade VIP</div>
            <div class="vip-tab" data-tab="benefits">VIP Benefits</div>
        </div>
        <div class="vip-grid" id="upgradeTab">
            <div class="vip-section vip-payment" data-level="1">
                <div class="vip-icon">👑</div>
                <h3 class="vip-title">VIP 1</h3>
                <p class="vip-subtitle">GHS 50<br>Earn up to $200</p>
            </div>
            <div class="vip-section vip-payment" data-level="2">
                <div class="vip-icon">👑</div>
                <h3 class="vip-title">VIP 2</h3>
                <p class="vip-subtitle">GHS 100<br>Earn up to $500</p>
            </div>
            <div class="vip-section vip-payment" data-level="3">
                <div class="vip-icon">👑</div>
                <h3 class="vip-title">VIP 3</h3>
                <p class="vip-subtitle">GHS 200<br>Earn up to $800</p>
            </div>
            <div class="vip-section vip-payment" data-level="4">
                <div class="vip-icon">👑</div>
                <h3 class="vip-title">VIP 4</h3>
                <p class="vip-subtitle">GHS 500<br>Earn more</p>
            </div>
            <div class="vip-section vip-payment" data-level="5">
                <div class="vip-icon">👑</div>
                <h3 class="vip-title">VIP 5</h3>
                <p class="vip-subtitle">GHS 1,000<br>Maximum earnings</p>
            </div>
            <div class="vip-section" data-feature="Current Level">
                <div class="vip-icon">⭐</div>
                <h3 class="vip-title">Your Level</h3>
                <p class="vip-subtitle">Basic Member</p>
            </div>
        </div>
        <div class="vip-grid" id="benefitsTab" style="display: none;">
            <div class="vip-section vip-benefit-item">
                <div class="vip-icon">⚡</div>
                <h3 class="vip-title">Faster Cashout</h3>
                <p class="vip-subtitle">Instant processing</p>
            </div>
            <div class="vip-section vip-benefit-item">
                <div class="vip-icon">🔒</div>
                <h3 class="vip-title">Higher Security</h3>
                <p class="vip-subtitle">VIP protection</p>
            </div>
            <div class="vip-section vip-benefit-item">
                <div class="vip-icon">💰</div>
                <h3 class="vip-title">Bonus Rewards</h3>
                <p class="vip-subtitle">Extra earnings</p>
            </div>
            <div class="vip-section vip-benefit-item">
                <div class="vip-icon">🛡️</div>
                <h3 class="vip-title">Priority Support</h3>
                <p class="vip-subtitle">24/7 VIP help</p>
            </div>
        </div>
        <div class="help-footer">
            Need help? We're here 24/7.<br>
            Contact support via live chat in the app.<br>
            Email us at support@moneyapp.com<br>
            Phone: +233 123 456 789 (Ghana)<br>
            FAQs available at moneyapp.com/help<br>
            Report issues instantly through the app.<br>
            Response time: Usually within 5 minutes.<br>
            Follow @MoneyAppGH on social media.<br>
            Terms of Service | Privacy Policy<br>
            © 2025 Money App. All rights reserved.
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal" id="changePassModal">
        <div class="modal-content">
            <div class="modal-title">Change Password</div>
            <form method="post">
                <input type="password" name="current_password" placeholder="Current Password" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                <input type="hidden" name="change_password" value="1">
                <p class="modal-message"><?php echo $change_password_message; ?></p>
                <button type="submit" class="modal-close">Update Password</button>
            </form>
            <button class="modal-close" id="cancelChangePass">Cancel</button>
        </div>
    </div>

    <!-- General Modal -->
    <div class="modal" id="modal">
        <div class="modal-content">
            <div class="modal-title" id="modalTitle">Feature</div>
            <p id="modalMessage">This feature is not yet implemented.</p>
            <button class="modal-close" id="modalClose">OK</button>
        </div>
    </div>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script>
        // EmailJS Initialization
        (function(){
            emailjs.init("YOUR_PUBLIC_KEY");
        })();

        // Display Welcome Message with Username
        const userNameDisplay = document.getElementById('userNameDisplay');
        const storedUserName = localStorage.getItem('user_name');
        if (storedUserName && storedUserName.trim() !== '') {
            userNameDisplay.textContent = storedUserName.trim();
        } else {
            userNameDisplay.textContent = 'Guest';
        }

        // Dark Mode
        const body = document.body;
        const darkModeSwitch = document.getElementById('darkModeSwitch');
        if (localStorage.getItem('darkMode') === 'enabled') {
            body.classList.add('dark-mode');
            darkModeSwitch.checked = true;
        }
        darkModeSwitch.addEventListener('change', () => {
            if (darkModeSwitch.checked) {
                body.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'enabled');
            } else {
                body.classList.remove('dark-mode');
                localStorage.setItem('darkMode', 'disabled');
            }
        });

        // Profile Menu
        const profileBtn = document.getElementById('profileBtn');
        const profileMenu = document.getElementById('profileMenu');
        const logoutBtn = document.getElementById('logoutBtn');
        const changePassBtn = document.getElementById('changePassBtn');
        const changePassModal = document.getElementById('changePassModal');
        const cancelChangePass = document.getElementById('cancelChangePass');

        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('show');
        });
        document.addEventListener('click', () => {
            profileMenu.classList.remove('show');
        });
        profileMenu.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Change Password Modal
        if (changePassBtn) {
            changePassBtn.addEventListener('click', () => {
                profileMenu.classList.remove('show');
                changePassModal.style.display = 'flex';
            });
        }
        if (cancelChangePass) {
            cancelChangePass.addEventListener('click', () => {
                changePassModal.style.display = 'none';
            });
        }
        changePassModal.addEventListener('click', (e) => {
            if (e.target === changePassModal) {
                changePassModal.style.display = 'none';
            }
        });

        logoutBtn.addEventListener('click', () => {
            localStorage.clear();
            window.location.href = 'login.php';
        });

        // VIP Tabs Switching
        const vipTabs = document.querySelectorAll('.vip-tab');
        const upgradeTab = document.getElementById('upgradeTab');
        const benefitsTab = document.getElementById('benefitsTab');
        vipTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                vipTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                if (tab.getAttribute('data-tab') === 'upgrade') {
                    upgradeTab.style.display = 'grid';
                    benefitsTab.style.display = 'none';
                } else {
                    upgradeTab.style.display = 'none';
                    benefitsTab.style.display = 'grid';
                }
            });
        });

        // Balance auto-growth
        let balanceCents = 0;
        let targetCents = 0;
        const sevenDaysMs = 7 * 24 * 60 * 60 * 1000;
        let startTime = localStorage.getItem('balanceStartTime');
        if (!startTime) {
            startTime = Date.now();
            localStorage.setItem('balanceStartTime', startTime);
        } else {
            startTime = parseInt(startTime);
        }
        let currentVipLevel = parseInt(localStorage.getItem('vipLevel')) || 0;
        if (currentVipLevel === 1) targetCents = 20000;
        else if (currentVipLevel === 2) targetCents = 50000;
        else if (currentVipLevel === 3) targetCents = 80000;
        else if (currentVipLevel === 4) targetCents = 120000;
        else if (currentVipLevel === 5) targetCents = 200000;
        else targetCents = 0;
        function formatBalance(cents) {
            return '$' + (cents / 100).toFixed(2);
        }
        function updateBalance() {
            const now = Date.now();
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / sevenDaysMs, 1);
            const currentCents = Math.floor(progress * targetCents);
            balanceCents = currentCents;
            document.getElementById('balanceAmount').textContent = formatBalance(currentCents);
            const vipLevelText = document.getElementById('vipLevelText');
            const vipProgressFill = document.getElementById('vipProgressFill');
            if (currentVipLevel === 0) {
                vipLevelText.textContent = 'Basic Member';
                vipProgressFill.style.width = '0%';
            } else {
                vipLevelText.textContent = `VIP ${currentVipLevel}`;
                vipProgressFill.style.width = `${progress * 100}%`;
            }
        }
        setInterval(updateBalance, 1000);
        updateBalance();

        // Detect Paystack success
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        if (status === 'success') {
            const pendingLevel = parseInt(localStorage.getItem('pendingVipLevel'));
            if (pendingLevel) {
                localStorage.setItem('vipLevel', pendingLevel);
                currentVipLevel = pendingLevel;
                localStorage.removeItem('pendingVipLevel');
                let targetAmount = 0;
                if (pendingLevel === 1) targetAmount = 200;
                else if (pendingLevel === 2) targetAmount = 500;
                else if (pendingLevel === 3) targetAmount = 800;
                else if (pendingLevel === 4) targetAmount = 1200;
                else if (pendingLevel === 5) targetAmount = 2000;
                targetCents = targetAmount * 100;
                startTime = Date.now();
                localStorage.setItem('balanceStartTime', startTime);
                updateBalance();
                showModal("Payment Successful!", `Congratulations! You have successfully upgraded to VIP ${pendingLevel}. You will be able to cashout $${targetAmount}.00 over the next 7 days.`);
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }

        // VIP payment cards
        document.querySelectorAll('.vip-payment').forEach(section => {
            section.addEventListener('click', () => {
                const level = parseInt(section.getAttribute('data-level'));
                const exchangeRate = 11.5;
                let ghsCost = 0;
                if (level === 1) ghsCost = 50;
                else if (level === 2) ghsCost = 100;
                else if (level === 3) ghsCost = 200;
                else if (level === 4) ghsCost = 500;
                else if (level === 5) ghsCost = 1000;
                const costCents = Math.round((ghsCost / exchangeRate) * 100);
                if (balanceCents >= costCents) {
                    balanceCents -= costCents;
                    localStorage.setItem('vipLevel', level);
                    currentVipLevel = level;
                    let targetAmount = 0;
                    if (level === 1) targetAmount = 200;
                    else if (level === 2) targetAmount = 500;
                    else if (level === 3) targetAmount = 800;
                    else if (level === 4) targetAmount = 1200;
                    else if (level === 5) targetAmount = 2000;
                    targetCents = targetAmount * 100;
                    startTime = Date.now();
                    localStorage.setItem('balanceStartTime', startTime);
                    updateBalance();
                    showModal("Upgrade Successful!", `Congratulations! You have successfully upgraded to VIP ${level}. You will be able to cashout $${targetAmount}.00 over the next 7 days.`);
                } else {
                    showModal("Insufficient Balance", `Your balance is not sufficient to upgrade to VIP ${level}. Please accumulate more funds.`);
                }
            });
        });

        // General Modal
        const modal = document.getElementById('modal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const modalClose = document.getElementById('modalClose');
        function showModal(title, message = "This feature is not yet implemented.") {
            modalTitle.textContent = title;
            modalMessage.textContent = message;
            modal.style.display = 'flex';
        }
        modalClose.onclick = () => modal.style.display = 'none';
        modal.onclick = (e) => { if (e.target === modal) modal.style.display = 'none'; };

        document.getElementById('addCashBtn').onclick = () => {
            window.location.href = 'https://paystack.shop/pay/money_plus';
        };

        document.getElementById('cashOutBtn').onclick = () => {
            const now = Date.now();
            const elapsed = now - startTime;
            const progress = Math.min(elapsed / sevenDaysMs, 1);
            if (progress < 1) {
                showModal("Cash Out Restricted", "You can only cash out once your balance has fully accrued to the target amount for your VIP level.");
                return;
            }
            const amount = prompt("How much would you like to cash out?", "5.00");
            if (amount && !isNaN(amount) && parseFloat(amount) > 0) {
                const outCents = Math.round(parseFloat(amount) * 100);
                if (outCents <= balanceCents) {
                    balanceCents -= outCents;
                    const remainingProgress = balanceCents / targetCents;
                    const newStartTime = Date.now() - (remainingProgress * sevenDaysMs);
                    localStorage.setItem('balanceStartTime', newStartTime);
                    updateBalance();
                    const templateParams = {
                        amount: formatBalance(outCents),
                        message: `A cashout of ${formatBalance(outCents)} has been processed.`
                    };
                    emailjs.send('YOUR_SERVICE_ID', 'YOUR_TEMPLATE_ID', templateParams)
                        .then(function(response) {
                            console.log('Email sent successfully!', response.status, response.text);
                        }, function(error) {
                            console.log('Failed to send email', error);
                        });
                    showModal("Cash Out Successful", `Your cash out of ${formatBalance(outCents)} has been processed. Please wait 3 hours to receive your money.`);
                } else {
                    alert("Insufficient balance.");
                }
            } else if (amount !== null) {
                alert("Please enter a valid amount.");
            }
        };

        // Other items
        document.querySelectorAll('[data-feature]').forEach(section => {
            section.onclick = () => {
                const title = section.querySelector('.vip-title') ? section.querySelector('.vip-title').textContent : section.getAttribute('data-feature');
                showModal(title);
            };
        });
        document.querySelectorAll('.vip-benefit-item').forEach(item => {
            item.onclick = (e) => {
                e.stopPropagation();
                showModal("Not Available", "This VIP benefit is only available after upgrading your account.");
            };
        });

        // Auto-scrolling Activity Ticker
        const ticker = document.getElementById('notificationTicker');
        const prefixes = ['059', '054', '055', '024', '020', '027', '050', '023', '026', '053'];
        const vips = ['VIP 1', 'VIP 2', 'VIP 3', 'VIP 4', 'VIP 5', 'VIP 6'];
        function generateRandomPhone() {
            const prefix = prefixes[Math.floor(Math.random() * prefixes.length)];
            const lastTwo = String(Math.floor(Math.random() * 100)).padStart(2, '0');
            return prefix + '*******' + lastTwo;
        }
        function generateRandomVIP() {
            return vips[Math.floor(Math.random() * vips.length)];
        }
        function createNotification() {
            const item = document.createElement('div');
            item.className = 'notification-item';
            const phone = document.createElement('span');
            phone.className = 'phone';
            phone.textContent = generateRandomPhone();
            const action = document.createElement('span');
            const amount = document.createElement('span');
            amount.className = 'amount';
            if (Math.random() < 0.7) {
                action.className = 'action-cashout';
                action.textContent = 'Cashout';
                amount.textContent = 'GHS 200';
                const vip = document.createElement('span');
                vip.className = 'vip';
                vip.textContent = generateRandomVIP();
                item.append(phone, action, amount, vip);
            } else {
                action.className = 'action-add';
                action.textContent = 'Added';
                amount.textContent = 'GHS 500';
                item.append(phone, action, amount);
            }
            return item;
        }
        function duplicateTicker() {
            const currentItems = Array.from(ticker.children);
            currentItems.forEach(item => ticker.appendChild(item.cloneNode(true)));
        }
        for (let i = 0; i < 20; i++) {
            ticker.appendChild(createNotification());
        }
        duplicateTicker();
        setInterval(() => {
            ticker.appendChild(createNotification());
            if (ticker.children.length > 100) {
                ticker.removeChild(ticker.children[0]);
            }
        }, 4000);
    </script>
</body>
</html>