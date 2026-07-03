#!/bin/bash
# اسکریپت deploy برای محیط production
# روش اجرا روی سرور: bash deploy/deploy-prod.sh

set -e

DEPLOY_PATH="/var/www/aavaan/aavaan"
BACKUP_PATH="/var/www/aavaan-backups/$(date +%Y%m%d_%H%M%S)"
PHP="php8.2"
ARTISAN="$PHP $DEPLOY_PATH/artisan"

echo "🚀 شروع deploy به محیط Production..."

# پشتیبان‌گیری از .env قبل از هر چیز
echo "📦 پشتیبان‌گیری..."
mkdir -p $BACKUP_PATH
cp $DEPLOY_PATH/.env $BACKUP_PATH/.env.bak 2>/dev/null || true

# اجرای maintenance mode
echo "🔧 فعال‌سازی حالت تعمیر..."
$ARTISAN down --retry=30

# نصب وابستگی‌های PHP (بدون dev packages)
echo "📚 نصب Composer packages..."
cd $DEPLOY_PATH
composer install --no-dev --optimize-autoloader --no-interaction

# نصب و build frontend
echo "🎨 Build frontend..."
npm ci
npm run build:prod

# migration پایگاه داده
echo "🗄️ اجرای migrations..."
$ARTISAN migrate --force

# پاک‌سازی cache
echo "🧹 پاک‌سازی cache..."
$ARTISAN config:clear
$ARTISAN route:clear
$ARTISAN view:clear

# بازسازی cache بهینه
echo "⚡ بهینه‌سازی cache..."
$ARTISAN config:cache
$ARTISAN route:cache
$ARTISAN view:cache

# پاک‌سازی/تازه‌سازی OPcache
# artisan config:cache فقط فایل جدید bootstrap/cache/config.php را می‌نویسد،
# ولی اگر PHP-FPM با opcache.validate_timestamps=0 اجرا شود (رایج در production)
# پردازه‌های در حال اجرا نسخه‌ی قدیمی همان فایل را در حافظه نگه می‌دارند تا
# وقتی PHP-FPM ری‌لود شود. بدون این مرحله، مقادیر جدید config (مثل تعرفه‌های
# جدید از .env) تا قبل از ری‌لود روی سایت زنده دیده نمی‌شوند.
echo "♻️ تازه‌سازی OPcache (ری‌لود PHP-FPM)..."
if systemctl reload "${PHP}-fpm" 2>/dev/null; then
    echo "   PHP-FPM با موفقیت ری‌لود شد."
elif service "${PHP}-fpm" reload 2>/dev/null; then
    echo "   PHP-FPM با موفقیت ری‌لود شد."
else
    echo "   ⚠️ ری‌لود PHP-FPM ناموفق بود؛ لطفاً به‌صورت دستی ${PHP}-fpm را ری‌لود کنید تا config جدید اعمال شود."
fi

# storage symlink
$ARTISAN storage:link --force 2>/dev/null || true

# تنظیم دسترسی‌ها
echo "🔐 تنظیم دسترسی‌ها..."
chown -R www-data:www-data $DEPLOY_PATH/storage
chown -R www-data:www-data $DEPLOY_PATH/bootstrap/cache
chmod -R 775 $DEPLOY_PATH/storage
chmod -R 775 $DEPLOY_PATH/bootstrap/cache

# خروج از maintenance mode
echo "✅ غیرفعال‌سازی حالت تعمیر..."
$ARTISAN up

echo "🎉 Deploy به محیط Production با موفقیت انجام شد!"
