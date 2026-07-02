#!/bin/bash
# اسکریپت deploy برای محیط production
# روش اجرا روی سرور: bash deploy/deploy-prod.sh

set -e

DEPLOY_PATH="/var/www/aavaan"
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
