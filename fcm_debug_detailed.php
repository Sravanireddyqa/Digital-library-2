<?php
/**
 * FCM Debug with Detailed Error Output
 * Run: http://localhost/digitallibrary_API/fcm_debug_detailed.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

echo "<h2>🔔 FCM Detailed Debug</h2>";
echo "<pre>";

// 1. Check service account
$serviceAccountFile = __DIR__ . '/firebase-service-account.json';
echo "1. Service Account File: ";
if (file_exists($serviceAccountFile)) {
    $sa = json_decode(file_get_contents($serviceAccountFile), true);
    echo "✅ Found\n";
    echo "   Project ID: " . $sa['project_id'] . "\n";
    echo "   Client Email: " . $sa['client_email'] . "\n";
} else {
    echo "❌ NOT FOUND at: $serviceAccountFile\n";
    die("Cannot proceed without service account file!");
}

// 2. Check cURL
echo "\n2. cURL Extension: ";
if (function_exists('curl_version')) {
    $curlInfo = curl_version();
    echo "✅ Enabled (v" . $curlInfo['version'] . ")\n";
    echo "   SSL: " . $curlInfo['ssl_version'] . "\n";
} else {
    echo "❌ NOT ENABLED - Enable in php.ini!\n";
    die("cURL is required!");
}

// 3. Check OpenSSL
echo "\n3. OpenSSL Extension: ";
if (function_exists('openssl_sign')) {
    echo "✅ Enabled\n";
} else {
    echo "❌ NOT ENABLED - Enable extension=openssl in php.ini!\n";
    die("OpenSSL is required!");
}

// 4. Try to get access token
echo "\n4. Getting Firebase Access Token...\n";

try {
    // Create JWT
    $now = time();
    $expiry = $now + 3600;

    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $payload = [
        'iss' => $sa['client_email'],
        'sub' => $sa['client_email'],
        'aud' => 'https://oauth2.googleapis.com/token',
        'iat' => $now,
        'exp' => $expiry,
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging'
    ];

    $headerEncoded = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
    $payloadEncoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
    $signatureInput = $headerEncoded . '.' . $payloadEncoded;

    // Sign with private key
    $privateKey = openssl_pkey_get_private($sa['private_key']);
    if (!$privateKey) {
        echo "   ❌ Failed to load private key!\n";
        die();
    }
    echo "   ✅ Private key loaded\n";

    $signature = '';
    if (!openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
        echo "   ❌ Failed to sign JWT!\n";
        die();
    }
    echo "   ✅ JWT signed\n";

    $jwt = $signatureInput . '.' . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
    echo "   JWT: " . substr($jwt, 0, 50) . "...\n";

    // Exchange JWT for access token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    echo "\n   Token Request Response (HTTP $httpCode):\n";
    if ($curlError) {
        echo "   ❌ cURL Error: $curlError\n";
    } else {
        $tokenData = json_decode($response, true);
        if (isset($tokenData['access_token'])) {
            echo "   ✅ Access Token Received!\n";
            echo "   Token: " . substr($tokenData['access_token'], 0, 30) . "...\n";
            echo "   Expires in: " . $tokenData['expires_in'] . " seconds\n";

            // 5. Try to send test notification
            echo "\n5. Sending Test FCM Message...\n";

            // Get first FCM token
            $conn = getConnection();
            $result = $conn->query("SELECT token FROM fcm_tokens LIMIT 1");
            if ($result->num_rows > 0) {
                $fcmToken = $result->fetch_assoc()['token'];
                echo "   FCM Token: " . substr($fcmToken, 0, 30) . "...\n";

                $url = 'https://fcm.googleapis.com/v1/projects/' . $sa['project_id'] . '/messages:send';
                echo "   URL: $url\n";

                $message = [
                    'message' => [
                        'token' => $fcmToken,
                        'notification' => [
                            'title' => 'Test Notification',
                            'body' => 'This is a debug test!'
                        ],
                        'data' => [
                            'type' => 'test',
                            'timestamp' => date('Y-m-d H:i:s')
                        ]
                    ]
                ];

                $ch2 = curl_init();
                curl_setopt($ch2, CURLOPT_URL, $url);
                curl_setopt($ch2, CURLOPT_POST, true);
                curl_setopt($ch2, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $tokenData['access_token'],
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode($message));

                $fcmResponse = curl_exec($ch2);
                $fcmHttpCode = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
                $fcmCurlError = curl_error($ch2);
                curl_close($ch2);

                echo "\n   FCM Response (HTTP $fcmHttpCode):\n";
                if ($fcmCurlError) {
                    echo "   ❌ cURL Error: $fcmCurlError\n";
                } else {
                    echo "   Response: $fcmResponse\n";
                    if ($fcmHttpCode == 200) {
                        echo "\n   ✅✅✅ NOTIFICATION SENT SUCCESSFULLY! ✅✅✅\n";
                    } else {
                        echo "\n   ❌ FCM Error - Check response above for details\n";
                    }
                }
            } else {
                echo "   ❌ No FCM tokens in database!\n";
            }
            $conn->close();

        } else {
            echo "   ❌ Failed to get access token!\n";
            echo "   Response: $response\n";
        }
    }

} catch (Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>