<?php
// Database config - change these to your actual DB credentials
$db_host = 'localhost';
$db_name = 'ecarga';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

function isPhishingURL($url) {
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return "Invalid URL format.";
    }

    $parsed_url = parse_url($url);
    $host = $parsed_url['host'] ?? '';

    // 1. Check if host is IP address
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return "Suspicious: URL contains an IP address instead of domain.";
    }

    // 2. Check for '@' symbol in URL
    if (strpos($url, '@') !== false) {
        return "Suspicious: URL contains '@' character.";
    }

    // 3. Check for multiple hyphens in domain (more than 2)
    if (substr_count($host, '-') > 2) {
        return "Suspicious: URL contains multiple hyphens.";
    }

    // 4. Check for suspicious TLDs often abused by phishers
    $suspiciousTLDs = ['xyz', 'top', 'club', 'online', 'site', 'info', 'biz', 'win', 'click', 'gq'];
    $host_parts = explode('.', $host);
    $tld = strtolower(end($host_parts));
    if (in_array($tld, $suspiciousTLDs)) {
        return "Suspicious: URL uses a suspicious TLD (.$tld).";
    }

    // 5. Check for punycode (xn-- prefix) which might indicate IDN homograph attack
    if (stripos($host, 'xn--') !== false) {
        return "Suspicious: URL contains punycode (possible homograph attack).";
    }

    // 6. Check URL length (very long URLs are suspicious)
    if (strlen($url) > 75) {
        return "Suspicious: URL is unusually long.";
    }

    // 7. Check for URL shorteners (common phishing tactic)
    $shorteners = [
        'bit.ly', 'tinyurl.com', 'goo.gl', 'ow.ly', 't.co',
        'tiny.cc', 'bit.do', 'is.gd', 'buff.ly', 'adf.ly',
        'bitly.com', 'lc.chat', 'soo.gd', 's2r.co', 'clicky.me'
    ];
    foreach ($shorteners as $short) {
        if (stripos($host, $short) !== false) {
            return "Suspicious: URL uses a known URL shortening service.";
        }
    }

    // 8. Check for excessive subdomains (e.g., >3)
    if (substr_count($host, '.') > 3) {
        return "Suspicious: URL contains multiple subdomains.";
    }

    // 9. Check for suspicious patterns in URL path
    if (isset($parsed_url['path'])) {
        // Example: phishing URLs sometimes use file extensions to imitate downloads
        $path = strtolower($parsed_url['path']);
        if (preg_match('/\.(exe|scr|zip|rar|js|php)$/', $path)) {
            return "Suspicious: URL path ends with executable or script extension.";
        }
    }

    // If none of the above flags, mark as safe
    return "URL looks safe based on heuristic analysis.";
}

// Handle form submission
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim($_POST['url'] ?? '');
    $result = isPhishingURL($url);

    // Insert into DB safely
    $stmt = $pdo->prepare("INSERT INTO url_checks (url, result) VALUES (:url, :result)");
    $stmt->execute(['url' => $url, 'result' => $result]);
}

// Fetch last 10 checks
$stmt = $pdo->query("SELECT url, result, checked_at FROM url_checks ORDER BY checked_at DESC LIMIT 10");
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="assets/phishing.css">
<title>Phishing URL Detector</title>

</head>
<body>
<div class="container">
    <h1>Phishing URL Detector</h1>
    <form method="post" novalidate>
        <input type="text" name="url" placeholder="Enter URL to check" required autocomplete="off" autofocus>
        <button type="submit">Check URL</button>
    </form>

    <?php if ($result !== null): ?>
        <h3>Result:</h3>
        <?php
            $class = 'safe';
            if (stripos($result, 'Suspicious') !== false) {
                $class = 'suspicious';
            } elseif (stripos($result, 'Invalid') !== false) {
                $class = 'invalid';
            }
        ?>
        <p class="result <?php echo $class; ?>"><?php echo htmlspecialchars($result); ?></p>
    <?php endif; ?>

    <h2>Recent URL Checks</h2>
    <table>
        <thead>
            <tr>
                <th>URL</th>
                <th>Result</th>
                <th>Checked At</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($history)): ?>
            <tr><td colspan="3" style="text-align:center;">No history found.</td></tr>
            <?php else: ?>
            <?php foreach ($history as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['url']); ?></td>
                <td><?php echo htmlspecialchars($row['result']); ?></td>
                <td><?php echo htmlspecialchars($row['checked_at']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
