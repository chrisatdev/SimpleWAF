<?php

require_once '../SimpleWAF.php'; 

// List of IPs from different countries for testing
$testIps = [
    '8.8.8.8' => 'EE.UU.',            // Google IP (should not be blocked)
    '95.163.32.35' => 'Russian',        // Russian IP example
    '2.56.89.45' => 'Saudi Arabia', // Saudi Arabia IP example
    '58.30.15.0' => 'China',          // China IP example
    '5.62.61.1' => 'Iran',            // Iran IP example
    '103.103.8.80' => 'India',        // India IP example
];

$waf = new SimpleWAF();

foreach ($testIps as $ip => $country) {
    echo "Testing with IP of $country ($ip): ";

    // Simulate blocking by country
    try {
        $waf->blockByCountry($ip);
        echo "Access allowed<br>";
    } catch (Exception $e) {
        echo "Access blocked<br>";
    }
}

