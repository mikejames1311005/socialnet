# SocialNet

SocialNet is a simple university PHP web application using PHP, MySQL, Nginx, and Linux.

## Project Files

- `/admin/newuser.php` creates new users.
- `/socialnet/signin.php` signs users in.
- `/socialnet/index.php` is the protected home page.
- `/socialnet/setting.php` edits the logged-in user's profile description.
- `/socialnet/profile.php` shows the logged-in user's profile, or another user with `?owner=username`.
- `/socialnet/about.php` shows static student information.
- `/socialnet/signout.php` signs the user out.
- `/socialnet/menubar.php` is the common menu.
- `/socialnet/common.php` contains the shared database connection, session helper, login check, and escaping helper.
- `/db.sql` creates the `socialnet` database and `account` table.
- `/config.php` stores database connection settings.

## Linux Setup With Nginx, MySQL, and PHP-FPM

These commands are for Ubuntu or a similar Linux VM.

1. Install the required packages:

```bash
sudo apt update
sudo apt install nginx mysql-server php-fpm php-mysql git
```

2. Put the project in `/var/www/socialnet`:

```bash
cd /var/www
sudo git clone https://github.com/mikejames1311005/socialnet.git socialnet
sudo chown -R www-data:www-data /var/www/socialnet
```

3. Create the database and table:

```bash
cd /var/www/socialnet
sudo mysql < db.sql
```

4. Create a MySQL user for the application:

```bash
sudo mysql
```

Then run these SQL commands inside the MySQL prompt:

```sql
CREATE USER 'socialnet_user'@'localhost' IDENTIFIED BY 'your_mysql_password';
GRANT ALL PRIVILEGES ON socialnet.* TO 'socialnet_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

5. Edit `/var/www/socialnet/config.php` and set the same password:

```php
'db_user' => 'socialnet_user',
'db_pass' => 'your_mysql_password',
```

6. Create an Nginx site:

```bash
sudo nano /etc/nginx/sites-available/socialnet
```

Example configuration:

```nginx
server {
    listen 80;
    server_name _;
    root /var/www/socialnet;
    index index.php index.html;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /(config.php|db.sql|README.md)$ {
        deny all;
    }
}
```

If your VM uses a different PHP version, check the socket name with:

```bash
ls /run/php/
```

Then replace `php8.3-fpm.sock` with the socket that exists on your VM.

7. Enable the site and restart Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/socialnet /etc/nginx/sites-enabled/socialnet
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
```

Use your real PHP-FPM service name if it is not `php8.3-fpm`.

## Testing

1. Check PHP syntax:

```bash
cd /var/www/socialnet
find . -name "*.php" -print -exec php -l {} \;
```

2. Open the admin page and create at least two users:

```text
http://your-vm-ip/admin/newuser.php
```

3. Sign in with one of the users:

```text
http://your-vm-ip/socialnet/signin.php
```

4. Test the required pages:

- `/socialnet/index.php` shows the logged-in user and links to other users.
- `/socialnet/profile.php` shows your own profile.
- `/socialnet/profile.php?owner=another_username` shows another user's profile.
- `/socialnet/setting.php` saves profile description changes.
- `/socialnet/about.php` shows the static student information.
- `/socialnet/signout.php` signs out and redirects to sign in.

5. Confirm protected pages redirect to sign in after signing out:

```text
http://your-vm-ip/socialnet/index.php
http://your-vm-ip/socialnet/setting.php
http://your-vm-ip/socialnet/profile.php
```
