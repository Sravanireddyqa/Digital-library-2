<?php
/**
 * Debug FCM Push Notifications
 * Open in browser: http://localhost/digitallibrary_API/debug_push.php
 */

require_once 'db.php';

echo "<html><head><title>FCM Debug</title><style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} pre{background:#f5f5f5;padding:10px;}</style></head><body>";

echo "<h1>🔔 Push Notification Debug</h1>";

// 1. Database Connection
echo "<h2>1. Database Connection</h2>";
try {
    $conn = getConnection();
    echo "<p class='success'>✅ Database connected successfully</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Database error: " . $e->getMessage() . "</p>";
    die("</body></html>");
}

// 2. Check fcm_tokens table
echo "<h2>2. FCM Tokens in Database</h2>";
$result = $conn->query("SELECT ft.*, u.name, u.email FROM fcm_tokens ft JOIN users u ON ft.user_id = u.id ORDER BY ft.id DESC LIMIT 10");
if ($result && $result->num_rows > 0) {
    echo "<p class='success'>✅ Found " . $result->num_rows . " FCM tokens</p>";
    echo "<table border='1' cellpadding='5'><tr><th>User ID</th><th>Name</th><th>Email</th><th>Token (first 30 chars)</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . substr($row['token'], 0, 30) . "...</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>❌ No FCM tokens found! Users need to login first to register tokens.</p>";
}

// 3. Check firebase-service-account.json
echo "<h2>3. Firebase Service Account</h2>";
$saFile = __DIR__ . '/firebase-service-account.json';
if (file_exists($saFile)) {
    $sa = json_decode(file_get_contents($saFile), true);
    echo "<p class='success'>✅ firebase-service-account.json found</p>";
    echo "<p class='info'>Project ID: " . ($sa['project_id'] ?? 'NOT FOUND') . "</p>";
    echo "<p class='info'>Client Email: " . ($sa['client_email'] ?? 'NOT FOUND') . "</p>";
} else {
    echo "<p class='error'>❌ firebase-service-account.json NOT FOUND!</p>";
    echo "<p>You need to download this from Firebase Console → Project Settings → Service Accounts → Generate New Private Key</p>";
}

// 4. Test notification send (optional)
echo "<h2>4. Send Test Notification</h2>";
if (isset($_GET['test']) && isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']);
    require_once 'send_notification.php';

    echo "<p class='info'>Sending test notification to user $userId...</p>";

    $result = sendNotification(
        $userId,
        'general',
        '🎉 Test Notification',
        'This is a test push notification from Digital Library!',
        ['test' => 'true', 'timestamp' => date('Y-m-d H:i:s')]
    );

    if ($result) {
        echo "<p class='success'>✅ Notification sent! Check your device.</p>";
    } else {
        echo "<p class='error'>❌ Failed to send notification. Check error logs.</p>";
    }
} else {
    // Show list of users to send test to
    $users = $conn->query("SELECT DISTINCT ft.user_id, u.name FROM fcm_tokens ft JOIN users u ON ft.user_id = u.id");
    if ($users && $users->num_rows > 0) {
        echo "<p>Click to send test notification:</p><ul>";
        while ($u = $users->fetch_assoc()) {
            echo "<li><a href='?test=1&user_id=" . $u['user_id'] . "'>Send to " . $u['name'] . " (ID: " . $u['user_id'] . ")</a></li>";
        }
        echo "</ul>";
    }
}

// 5. Check notifications table
echo "<h2>5. Recent Notifications in Database</h2>";
$notifs = $conn->query("SELECT n.*, u.name FROM notifications n JOIN users u ON n.user_id = u.id ORDER BY n.created_at DESC LIMIT 5");
if ($notifs && $notifs->num_rows > 0) {
    echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>User</th><th>Type</th><th>Title</th><th>Created At</th><th>Read</th></tr>";
    while ($n = $notifs->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $n['id'] . "</td>";
        echo "<td>" . $n['name'] . "</td>";
        echo "<td>" . $n['type'] . "</td>";
        echo "<td>" . $n['title'] . "</td>";
        echo "<td>" . $n['created_at'] . "</td>";
        echo "<td>" . ($n['is_read'] ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='info'>No notifications found yet.</p>";
}

$conn->close();
echo "</body></html>";
?>