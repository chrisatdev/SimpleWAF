
<?php
/**
 * Simple Web Application Firewall (WAF) - Version 1.0
 * 
 * Author: Christian Benitez
 * GitHub: https://github.com/chrisatdev
 * 
 * Description:
 * This PHP-based WAF provides basic protection for web applications by blocking 
 * requests based on geolocation, common attack patterns (XSS, SQL injection), 
 * and rate-limiting. It is designed to be easy to integrate and customize for 
 * different security requirements.
 * 
 * Features:
 * - Blocks access based on country (using GeoLite2 database)
 * - Protects against common web attacks (XSS, SQL injection, script attacks)
 * - Implements rate limiting to prevent abuse of the application
 * - Logs blocked requests for monitoring and auditing purposes
 * 
 * Version: 1.0
 * License: MIT
 * 
 * Usage:
 * Include this WAF at the start of your PHP script to protect your application.
 * Example: 
 *     require_once 'SimpleWAF.php';
 *     $waf = new SimpleWAF();
 *     $waf->protect();
 * 
 * Note:
 * - Ensure you have the GeoLite2 database from MaxMind installed for geolocation.
 * - Customize the blocked countries, rate limits, and attack patterns as needed.
 * 
 */

require_once 'vendor/autoload.php'; 

use GeoIp2\Database\Reader;

class SimpleWAF {
  private $reader;
  private $blockedCountries = ['RU', 'SA', 'IR', 'CN', 'IN']; // Blocked by country code
  private $whitelistedIps = ['127.0.0.1']; // Add IPs

  public function __construct() {
    $this->reader = new Reader(__DIR__ . DIRECTORY_SEPARATOR . 'db/GeoLite2-Country.mmdb');
  }

  // List of dangerous patterns
  private $patterns = [
    '/(<|>)/i',                                          // XSS Prevention
    '/(union|select|insert|update|delete|drop|alter)/i', // SQL Injection Prevention
    '/(script|onerror|onload|alert)/i',                  // Script attacks on forms
  ];

  // Checking a string against patterns
  public function sanitize($input) {
    foreach ($this->patterns as $pattern) {
      if (preg_match($pattern, $input)) {
        $this->logBlockedRequest($_SERVER['REMOTE_ADDR'], 'Malicious Input Detected');
        return $this->blockRequest("Entry detected as malicious.");
        // die("Entry detected as malicious.");
      }
    }
    return $input;
  }

  public function getCountryCode($ip) {
    try {
      $record = $this->reader->country($ip);
      return $record->country->isoCode; 
    } catch (Exception $e) {
      return null;  // If the IP cannot be geolocated, returns null
    }
  }

  public function blockByCountry($ip) {
    /**
     * Uncomment the code if you need to whitelist it 
     */
    // if (in_array($ip, $this->whitelistedIps)) {
    //   return;  // If the IP is whitelisted, do nothing
    // }
    $this->rateLimit($ip);
    $countryCode = $this->getCountryCode($ip);
    if (in_array($countryCode, $this->blockedCountries)) {
      $this->logBlockedRequest($ip, "Blocked Country [{$countryCode}]");
      return $this->blockRequest("Access blocked. Your country is restricted.");
      // die("Access blocked. Your country is restricted.");
    }
  }

  private function rateLimit($ip) {
    $limit = 100; // Maximum number of requests allowed in a time period
    $timeFrame = 3600; // In seconds (1 hour)
    $currentTime = time();
    $file = __DIR__ . "/logs/rate_limit_$ip.txt";

    $fp = fopen($file, 'c+');
    if (flock($fp, LOCK_EX)) { // Exclusive lock
      if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if ($data['count'] >= $limit && ($currentTime - $data['time']) < $timeFrame) {
            fclose($fp);
            return $this->blockRequest("Too many requests. Try again later.");
        } elseif (($currentTime - $data['time']) > $timeFrame) {
            $data = ['count' => 1, 'time' => $currentTime];
        } else {
            $data['count']++;
        }
      } else {
        $data = ['count' => 1, 'time' => $currentTime];
      }

      ftruncate($fp, 0); // Clear file before writing
      fwrite($fp, json_encode($data));
      fflush($fp);
      flock($fp, LOCK_UN); // Release the lock
    }

    fclose($fp);
  }
  
  private function blockRequest($message = "Access denied.") {
    http_response_code(403);
    echo "<h1>403 Forbidden</h1>";
    echo "<p>$message</p>";
    exit;
  }

  public function protect() {
    // Blocking using IP
    $userIp = $_SERVER['REMOTE_ADDR'];
    $this->blockByCountry($userIp);

    // Protecting GET requests
    foreach ($_GET as $key => $value) {
      $_GET[$key] = $this->sanitize($value);
    }

    // Protecting POST requests
    foreach ($_POST as $key => $value) {
      $_POST[$key] = $this->sanitize($value);
    }

    // Protecting COOKIES
    foreach ($_COOKIE as $key => $value) {
      $_COOKIE[$key] = $this->sanitize($value);
    }

    // Protecting REQUEST
    foreach ($_REQUEST as $key => $value) {
      $_REQUEST[$key] = $this->sanitize($value);
    }
  }

  private function logBlockedRequest($ip, $reason) {
    $logMessage = date('Y-m-d H:i:s') . " - IP: $ip - Reason: $reason" . PHP_EOL;
    file_put_contents(__DIR__ . '/logs/waf_log.txt', $logMessage, FILE_APPEND);
  }
}

