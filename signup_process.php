<?php 

if (isset($_POST['g-recaptcha-response'])) {
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $secret_key = 'YOUR_SECRET_KEY_HERE';
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secret_key,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result);

    if (!$response->success) {
        // CAPTCHA failed - handle error (e.g., show message)
        die('reCAPTCHA verification failed. Please try again.');
    }
} else {
    die('Please complete the reCAPTCHA.');
}

// Continue with signup if CAPTCHA passes...

?>