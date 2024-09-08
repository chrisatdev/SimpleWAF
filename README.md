# Simple WAF - Version 1.0

This is a simple Web Application Firewall (WAF) written in PHP, designed to block malicious requests based on geolocation, common attack patterns such as XSS and SQL injection, and rate limit requests per IP.

## Requirements

- PHP 7.4 or higher
- Composer (to install the dependencies)
- GeoLite2 database for geolocation (available at: https://dev.maxmind.com/geoip/geolite2-free-geolocation-data)

## Installation

1. **Clone the repository or download the files** to your web server.

2. **Install the dependencies** using Composer. Make sure Composer is installed and run the following command in the project root folder:
```bash
composer install
```

3. **Download the GeoLite2 database**:
- Create a `db/` folder in the project root.
- Download the `GeoLite2-Country.mmdb` file from the MaxMind website and place it in the `db/` folder.

4. **Make sure that the `waf_log.txt` file has write permissions** so that the system can log blocked requests:
```bash
chmod 666 waf_log.txt
```

## Features

1. **Geolocation protection:**
The WAF blocks access from specific countries. Currently, the following countries are blocked:
- Russia (RU)
- Saudi Arabia (SA)
- Iran (IR)
- China (CN)
- India (IN)

Countries can be added or removed by modifying the `blockedCountries` list in the code.

2. **Dangerous patterns:**
The WAF inspects requests (GET, POST, COOKIES, REQUEST) for common attack patterns, such as:
- XSS (Cross-site scripting)
- SQL injections
- Script injections

3. **Rate limiting:**
The number of requests allowed per IP is limited to 100 per hour. If this limit is exceeded, the IP will be temporarily blocked.

4. **Logging of blocked requests:**
Each time a request is blocked, it is logged in the `waf_log.txt` file with the IP and the reason for the block.

## Usage

To protect your application with the WAF, simply include the following code at the beginning of your main PHP files:

```php
require_once 'SimpleWAF.php';
$waf = new SimpleWAF();
$waf->protect();
```

This code will check requests and apply security measures according to the rules set.

## Customization

1. **Add IPs to the whitelist:**
If you want certain IPs to be exempt from blocking, you can add them to the `whitelistedIps` list in the `SimpleWAF.php` file.

2. **Modify geolocation rules:**
If you want to change the blocked countries, simply edit the `blockedCountries` list.

3. **Modify rate limit rules:**
You can change the request limit and timeout by modifying the `$limit` and `$timeFrame` variables in the `rateLimit` method.

## Contact

If you have questions or comments about this project, feel free to contact me.

This file provides clear instructions on installing, configuring, and customizing your WAF, making it more accessible to future users or contributors.
