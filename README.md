# Crazy Eights Online

Crazy Eights Online is a thrilling multiplayer card game built for the web using HTML, JavaScript, Bootstrap, and Ratchet WebSocket!

## Set up instructions

1. Create a database named `cr8_db` and import the `src/database_file.sql` into the `cr8_db` database you have created.

2. Create a `src/config.php` file and copy the code below. Paste it into the newly created `config.php` file:

```php
<?php
global $host, $user, $password, $db_name;

$host = "localhost";
$user = "root";
$password = "";
$db_name = "cr8_db";
?>
```

3. Configure a reverse proxy on your Apache or NGINX server, setting the proxy port to `8080`.

4. Run the command `php server.php`.

5. Start the HTTP server.

6. In your browser, go to `http://localhost/crazy-eights/` and login by simply clicking the login button; no login credentials are needed yet.
