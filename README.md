# shortly
A REST application to generate short url for any url and store in a SQLite Database.
- Available API: GET, POST, PUT


## Requirements

  - PHP 5.3 or higher with sqlite3

## Installation

- Create virtualhost in httpd-vhosts.conf
```bash
<VirtualHost *:80>
    DocumentRoot "PATH_TO_SRC/shortly/src"
    ServerName short.ly
        <Directory "PATH_TO_SRC/shortly/src">
           Options All
           AllowOverride All
           Require all granted
        </Directory>
    ErrorLog "/private/var/log/apache2/shortly-error_log"
    CustomLog "/private/var/log/apache2/shortly-access_log" common
</VirtualHost>
```
- Edit hosts file:
127.0.0.1 short.ly


## Features
- Submit any URL and get a standardized, shortened URL back.
- Configure a shortened URL to redirect to different targets based on the device type (mobile, tablet, desktop) of the user navigating to the shortened URL.
- Navigating to a shortened URL should redirect to the appropriate target URL.
- Can retrieve a list of all existing shortened URLs, including time since creation and target URLs (each with number of redirects).
- API requests and responses should be JSON formatted.

