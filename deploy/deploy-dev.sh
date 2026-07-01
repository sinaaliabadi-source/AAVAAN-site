#!/bin/bash
# اسکریپت deploy برای محیط Development
# روش اجرا روی سرور: bash deploy/deploy-dev.sh

set -e

DEPLOY_PATH="/var/www/aavaan-dev"
BACKUP_PATH="/var/www/aavaan-backups/dev_$(date +%Y%m%d_%H%M%S)"
PHP="php8.2"
ARTISAN="$PHP $DEPLOY_PATH/artisan"

echo "🚀 شروع deploy به محیط Development..."

# پشتیبان‌گیری از .env قبل از هر چیز
echo "📦 پشتیبان‌گیری..."
mkdir -p $BACKUP_PATH
cp $DEPLOY_PATH/.env $BACKUP_PATH/.env.bak 2>/dev/null || true

# اجرای maintenance mode
echo "🔧 فعال‌سازی حالت تعمیر..."
$ARTISAN down --retry=30

# نصب وابستگی‌های PHP (شامل dev packages برای محیط توسعه)
echo "📚 نصب Composer packages..."
cd $DEPLOY_PATH
composer install --optimize-autoloader --no-interaction

# نصب و build frontend
echo "🎨 Build frontend..."
npm ci
npm run build

# migration پایگاه داده (بدون --force برای امنیت بیشتر در محیط توسعه)
echo "🗄️ اجرای migrations..."
$ARTISAN migrate

# پاک‌سازی cache
echo "🧹 پاک‌سازی cache..."
$ARTISAN config:clear
$ARTISAN route:clear
$ARTISAN view:clear

# در محیط development کش پیکربندی/روت/ویو ساخته نمی‌شود تا تغییرات بلافاصله اعمال شوند

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

echo "🎉 Deploy به محیط Development با موفقیت انجام شد!"
