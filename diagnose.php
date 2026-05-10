<?php
/**
 * System Diagnostic Script
 * Run this at: https://yourdomain.infinityfree.net/diagnose.php
 */

?><!DOCTYPE html>
<html>
<head>
    <title>Flight Booking System - Diagnostics</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .section { border: 1px solid #ccc; padding: 15px; margin: 10px 0; }
        .pass { background: #d4edda; color: #155724; padding: 10px; margin: 5px 0; border-radius: 4px; }
        .fail { background: #f8d7da; color: #721c24; padding: 10px; margin: 5px 0; border-radius: 4px; }
        .warn { background: #fff3cd; color: #856404; padding: 10px; margin: 5px 0; border-radius: 4px; }
        h2 { color: #333; }
        code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Flight Booking System - Diagnostics</h1>
    <p>This script checks your system configuration.</p>

    <div class="section">
        <h2>1. PHP Environment</h2>
        <?php
        echo "<div class='pass'>✓ PHP Version: " . phpversion() . "</div>";
        
        if (extension_loaded('pdo')) {
            echo "<div class='pass'>✓ PDO Extension: Installed</div>";
        } else {
            echo "<div class='fail'>✗ PDO Extension: NOT installed</div>";
        }
        
        if (extension_loaded('pdo_mysql')) {
            echo "<div class='pass'>✓ PDO MySQL: Installed</div>";
        } else {
            echo "<div class='fail'>✗ PDO MySQL: NOT installed</div>";
        }
        ?>
    </div>

    <div class="section">
        <h2>2. File Configuration</h2>
        <?php
        if (file_exists(__DIR__ . '/.env')) {
            echo "<div class='pass'>✓ .env file: EXISTS</div>";
        } else {
            echo "<div class='fail'>✗ .env file: NOT FOUND</div>";
            echo "<div class='warn'>Copy .env.example to .env and fill in database credentials</div>";
        }

        if (file_exists(__DIR__ . '/config/constants.php')) {
            echo "<div class='pass'>✓ config/constants.php: EXISTS</div>";
        } else {
            echo "<div class='fail'>✗ config/constants.php: NOT FOUND</div>";
        }

        if (file_exists(__DIR__ . '/config/database.php')) {
            echo "<div class='pass'>✓ config/database.php: EXISTS</div>";
        } else {
            echo "<div class='fail'>✗ config/database.php: NOT FOUND</div>";
        }
        ?>
    </div>

    <div class="section">
        <h2>3. Environment Variables</h2>
        <?php
        // Load .env if exists
        if (file_exists(__DIR__ . '/.env')) {
            $env_lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($env_lines as $line) {
                if (strpos($line, '#') === 0) continue;
                if (strpos($line, '=') === false) continue;
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                putenv(sprintf('%s=%s', $key, $value));
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }

        $db_host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? null);
        $db_name = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? null);
        $db_user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? null);
        $db_password = getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? null);

        if ($db_host) {
            echo "<div class='pass'>✓ DB_HOST: " . htmlspecialchars($db_host) . "</div>";
        } else {
            echo "<div class='fail'>✗ DB_HOST: NOT SET</div>";
        }

        if ($db_name) {
            echo "<div class='pass'>✓ DB_NAME: " . htmlspecialchars($db_name) . "</div>";
        } else {
            echo "<div class='fail'>✗ DB_NAME: NOT SET</div>";
        }

        if ($db_user) {
            echo "<div class='pass'>✓ DB_USER: " . htmlspecialchars($db_user) . "</div>";
        } else {
            echo "<div class='fail'>✗ DB_USER: NOT SET</div>";
        }

        if ($db_password) {
            echo "<div class='pass'>✓ DB_PASSWORD: SET (hidden)</div>";
        } else {
            echo "<div class='fail'>✗ DB_PASSWORD: NOT SET</div>";
        }
        ?>
    </div>

    <div class="section">
        <h2>4. Database Connection</h2>
        <?php
        if ($db_host && $db_name && $db_user && $db_password) {
            try {
                $conn = new PDO(
                    'mysql:host=' . $db_host . ';dbname=' . $db_name . ';charset=utf8',
                    $db_user,
                    $db_password,
                    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
                );
                echo "<div class='pass'>✓ Database Connection: SUCCESS</div>";
                
                // Test tables
                $tables = ['users', 'flights', 'routes', 'bookings'];
                foreach ($tables as $table) {
                    $query = $conn->prepare("SHOW TABLES LIKE ?");
                    $query->execute([$table]);
                    if ($query->rowCount() > 0) {
                        echo "<div class='pass'>✓ Table <code>$table</code>: EXISTS</div>";
                    } else {
                        echo "<div class='fail'>✗ Table <code>$table</code>: NOT FOUND - Run schema.sql</div>";
                    }
                }
                $conn = null;
            } catch (PDOException $e) {
                echo "<div class='fail'>✗ Database Connection Failed</div>";
                echo "<div class='fail'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            echo "<div class='warn'>⚠ Cannot test - Missing configuration</div>";
        }
        ?>
    </div>

    <div class="section">
        <h2>5. API Endpoints</h2>
        <?php
        $files = ['auth.php', 'flights.php', 'bookings.php', 'admin.php', 'users.php'];
        foreach ($files as $file) {
            if (file_exists(__DIR__ . '/api/' . $file)) {
                echo "<div class='pass'>✓ api/" . htmlspecialchars($file) . ": EXISTS</div>";
            } else {
                echo "<div class='fail'>✗ api/" . htmlspecialchars($file) . ": NOT FOUND</div>";
            }
        }
        ?>
    </div>

    <div class="section">
        <h2>6. Frontend Files</h2>
        <?php
        if (file_exists(__DIR__ . '/public/index.html')) {
            echo "<div class='pass'>✓ public/index.html: EXISTS</div>";
        } else {
            echo "<div class='fail'>✗ public/index.html: NOT FOUND</div>";
        }

        if (file_exists(__DIR__ . '/public/assets/css/style.css')) {
            echo "<div class='pass'>✓ public/assets/css/style.css: EXISTS</div>";
        } else {
            echo "<div class='fail'>✗ public/assets/css/style.css: NOT FOUND</div>";
        }

        if (file_exists(__DIR__ . '/public/assets/js/app.js')) {
            echo "<div class='pass'>✓ public/assets/js/app.js: EXISTS</div>";
        } else {
            echo "<div class='fail'>✗ public/assets/js/app.js: NOT FOUND</div>";
        }
        ?>
    </div>

    <div class="section">
        <h2>7. Error Log</h2>
        <?php
        if (file_exists(__DIR__ . '/error.log')) {
            echo "<div class='pass'>✓ error.log: EXISTS</div>";
            echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 200px; overflow: auto;'>";
            $log_content = file_get_contents(__DIR__ . '/error.log');
            echo htmlspecialchars(substr($log_content, -1000)); // Last 1000 chars
            echo "</pre>";
        } else {
            echo "<div class='warn'>⚠ error.log: Not created yet (will be created on first error)</div>";
        }
        ?>
    </div>

    <div class="section" style="background: #f0f0f0;">
        <h2>✅ Next Steps</h2>
        <p><strong>If everything shows "✓ PASS":</strong></p>
        <ol>
            <li>Visit <code>https://yourdomain.infinityfree.net/public/index.html</code></li>
            <li>Try registering a new account</li>
            <li>Search for flights</li>
        </ol>
        <p><strong>If something shows "✗ FAIL":</strong></p>
        <ol>
            <li>Make sure <code>.env</code> file exists (copy from <code>.env.example</code>)</li>
            <li>Verify database credentials are correct</li>
            <li>Run <code>schema.sql</code> in PhpMyAdmin to create tables</li>
            <li>Run <code>seed.sql</code> to add sample data</li>
            <li>Check <code>error.log</code> for detailed error messages</li>
        </ol>
    </div>
</body>
</html>
