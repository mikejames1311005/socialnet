# SocialNet

This is my SocialNet web application project for CS4451.
 ### Name: Tran Thanh Dat
 ### TROY ID: 1695358

I built it with PHP, MySQL, Nginx, and Linux as required. The app allows an admin to create users, users to sign in, view their home page, edit their profile description, view profile pages, and sign out. I tested the project on my Ubuntu VM using Nginx, MySQL, and php8.4-fpm

## What Works

- Admin can create new users at `/admin/newuser.php`
- Users can sign in at `/socialnet/signin.php`
- After signing in, users are redirected to `/socialnet/index.php`
- Home page shows the logged-in user's username and full name
- Home page also shows other users in the system
- The other users list links to `/socialnet/profile.php?owner=username`
- Setting page updates the logged-in user's profile description
- Profile page shows the selected user's profile content
- About page shows my student name and student number
- SignOut resets the session and redirects back to Sign In

## Project Files

- `db.sql` creates the `socialnet` database and the `account` table.
- `config.php` stores the database connection settings.
- `socialnet/common.php` contains the database connection, session helper, login check, and output escaping helper.
- `socialnet/menubar.php` contains the shared menu for the main pages.
- `admin/newuser.php` is the admin page for creating users.
- `socialnet/signin.php`, `index.php`, `setting.php`, `profile.php`, `about.php`, and `signout.php` are the required SocialNet pages.

## Database

The database name is `socialnet`.

The only table is `account`.

The table columns are:

- `id`
- `username`
- `fullname`
- `password`
- `description`

Passwords are saved with `password_hash()` when users are created, and checked with `password_verify()` when users sign in.

## Small Setup Notes From My Testing

While setting this up on my VM, I ran into a few common environment issues:

- SSH did not work at first until `openssh-server` was installed and running.
- Nginx had a duplicate `default_server` configuration because an old default config was still enabled.
- The browser could not access the site until I allowed port 80 through UFW.
- The PHP-FPM socket had to match my PHP version. In my VM, it was `php8.4-fpm.sock`.

I included the setup steps below so the project can be tested again in a new Ubuntu environment.

## Linux Setup With Nginx, MySQL, and PHP-FPM

These commands are for Ubuntu or a similar Linux VM.

1. Install the required packages:

```bash
sudo apt update
sudo apt install nginx mysql-server php-fpm php-mysql git curl
```

If SSH access is needed and it is not running yet:

```bash
sudo apt install openssh-server
sudo systemctl enable --now ssh
```

2. Put the project in `/var/www/socialnet`:

```bash
cd /var/www
if [ -d socialnet ]; then
    sudo mv socialnet "socialnet_backup_$(date +%Y%m%d_%H%M%S)"
fi
sudo git clone https://github.com/mikejames1311005/socialnet.git socialnet
sudo chown -R www-data:www-data /var/www/socialnet
```

The backup command is only needed if an old copy of the project already exists.

3. Create the database and table:

```bash
cd /var/www/socialnet
sudo mysql < db.sql
```

4. Create a MySQL user for the application:

```bash
sudo mysql
```

Then run these SQL commands inside the MySQL prompt. Replace `your_mysql_password` with the password you want to use:

```sql
CREATE USER 'socialnet_user'@'localhost' IDENTIFIED BY 'your_mysql_password';
GRANT ALL PRIVILEGES ON socialnet.* TO 'socialnet_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

If the user already exists, use this instead:

```sql
ALTER USER 'socialnet_user'@'localhost' IDENTIFIED BY 'your_mysql_password';
GRANT ALL PRIVILEGES ON socialnet.* TO 'socialnet_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

5. Edit `/var/www/socialnet/config.php` and set the same password:

```php
'db_user' => 'socialnet_user',
'db_pass' => 'your_mysql_password',
```

6. Check the PHP-FPM socket name:

```bash
ls /run/php/
```

For example, my VM used:

```text
php8.4-fpm.sock
```

7. Create the Nginx site:

```bash
sudo nano /etc/nginx/sites-available/socialnet
```

Example configuration. Change `php8.4-fpm.sock` if your VM has a different PHP version:

```nginx
server {
    listen 80 default_server;
    server_name _;
    root /var/www/socialnet;
    index index.php index.html;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
    }

    location ~ /(config.php|db.sql|README.md)$ {
        deny all;
    }
}
```

8. Enable the site and restart Nginx:

```bash
sudo rm -f /etc/nginx/sites-enabled/default
sudo ln -sf /etc/nginx/sites-available/socialnet /etc/nginx/sites-enabled/socialnet
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl restart php8.4-fpm
```

Use your real PHP-FPM service name if it is not `php8.4-fpm`.

If UFW is enabled, allow the browser to reach Nginx:

```bash
sudo ufw allow 80/tcp
```

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
- `/socialnet/about.php` shows the student name and student number.
- `/socialnet/signout.php` signs out and redirects to sign in.

5. Confirm protected pages redirect to sign in after signing out:

```text
http://your-vm-ip/socialnet/index.php
http://your-vm-ip/socialnet/setting.php
http://your-vm-ip/socialnet/profile.php
```

## Notes

This project does not use Laravel, Composer, Docker, or any PHP framework. I kept it as simple PHP files so the code is easier to explain. The `db.sql` file only creates the database and table. It does not insert test users, so you can create fresh users from `/admin/newuser.php`.
