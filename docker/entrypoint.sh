#!/bin/sh
set -e

echo "Espereando a que la base de datos PostgreSQL ($DB_HOST:$DB_PORT) esté lista..."
php -r "
for (\$i = 0; \$i < 30; \$i++) {
    try {
        \$pdo = new PDO(
            'pgsql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '5432') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        echo \"Base de datos conectada exitosamente.\n\";
        exit(0);
    } catch (Exception \$e) {
        echo \"Esperando conexión a PostgreSQL...\n\";
        sleep(2);
    }
}
echo \"No se pudo conectar a la base de datos tras 60 segundos.\n\";
exit(1);
"

echo "Ejecutando enlace de almacenamiento (storage:link)..."
php artisan storage:link --force || true

echo "Ejecutando migraciones de la base de datos..."
php artisan migrate --force

echo "Optimizando cachés de Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Iniciando Nginx y PHP-FPM con Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
