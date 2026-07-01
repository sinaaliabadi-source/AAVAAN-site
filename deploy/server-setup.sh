#!/bin/bash
# اسکریپت نصب اولیه سرور — فقط یک بار اجرا شود
# روش اجرا: sudo bash deploy/server-setup.sh
# (این اسکریپت را از ریشه‌ی مخزن اجرا کنید تا مسیر server-config/ در دسترس باشد)

set -e

# ریشه‌ی مخزن را نسبت به محل این اسکریپت پیدا کن تا صرف‌نظر از cwd کار کند
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

echo "🖥️ نصب اولیه سرور آوان روی Ubuntu 26.04..."

# به‌روزرسانی سیستم
apt update && apt upgrade -y

# نصب پکیج‌های پایه
apt install -y curl wget git unzip software-properties-common

# نصب PHP 8.2 + extensions
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-redis \
    php8.2-gd php8.2-imagick php8.2-curl php8.2-xml php8.2-mbstring \
    php8.2-zip php8.2-bcmath php8.2-intl

# تنظیم PHP.ini برای production
sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 50M/' /etc/php/8.2/fpm/php.ini
sed -i 's/post_max_size = 8M/post_max_size = 50M/' /etc/php/8.2/fpm/php.ini
sed -i 's/memory_limit = 128M/memory_limit = 256M/' /etc/php/8.2/fpm/php.ini
sed -i 's/max_execution_time = 30/max_execution_time = 120/' /etc/php/8.2/fpm/php.ini

# نصب Nginx
apt install -y nginx

# نصب MariaDB 10.11
curl -LsSO https://downloads.mariadb.com/MariaDB/mariadb_repo_setup
bash mariadb_repo_setup --mariadb-server-version=10.11
apt install -y mariadb-server mariadb-client

# نصب Redis
apt install -y redis-server
systemctl enable redis-server
systemctl start redis-server

# نصب Node.js 22 LTS
curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
apt install -y nodejs

# نصب Composer
curl -sS https://getcomposer.org/installer | php8.2 -- --install-dir=/usr/local/bin --filename=composer

# نصب Certbot برای SSL
apt install -y certbot python3-certbot-nginx

# ساخت پوشه‌ها
mkdir -p /var/www/aavaan/public
mkdir -p /var/www/aavaan-dev/public
mkdir -p /var/www/aavaan-backups
chown -R www-data:www-data /var/www/

# کپی کانفیگ Nginx
cp "$REPO_ROOT/server-config/nginx/aavaan-prod.conf" /etc/nginx/sites-available/aavaan
cp "$REPO_ROOT/server-config/nginx/aavaan-dev.conf" /etc/nginx/sites-available/aavaan-dev
ln -sf /etc/nginx/sites-available/aavaan /etc/nginx/sites-enabled/
ln -sf /etc/nginx/sites-available/aavaan-dev /etc/nginx/sites-enabled/

nginx -t && systemctl restart nginx

echo "✅ نصب اولیه سرور کامل شد!"
echo ""
echo "مراحل بعدی:"
echo "1. mysql_secure_installation را اجرا کن"
echo "2. دیتابیس‌ها را بساز: CREATE DATABASE aavaan_prod; CREATE DATABASE aavaan_dev;"
echo "3. فایل‌های پروژه را آپلود کن"
echo "4. certbot --nginx -d aavaan.com -d dev.aavaan.com را اجرا کن"
