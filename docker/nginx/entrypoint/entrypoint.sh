#!/bin/bash
echo "Starting entrypoint for ${ORG_ENV}...";

cd /var/www/html

phpenmod intl
phpenmod gd
phpenmod mbstring
phpenmod zip

MAX_CHILDREN=$MAX_CHILDREN
sed -i "s/%%MAX_CHILDREN%%/$MAX_CHILDREN/" /etc/php/8.1/fpm/pool.d/www.conf

service nginx start
service php8.1-fpm start

ADMIN_PASSWORD=$ADMIN_PASSWORD
ADMIN_USERNAME=$ADMIN_USERNAME
ORG_ENV=$ORG_ENV
APP_NAME=$APP_NAME
MYSQL_DATABASE=$MYSQL_DATABASE
MYSQL_HOST=$MYSQL_HOST
MYSQL_UNIX_SOCKET=$MYSQL_UNIX_SOCKET
MYSQL_USER=$MYSQL_USER
MYSQL_PASSWORD=$MYSQL_PASSWORD
HASHED_PASSWORD=`php /entrypoint/bcrypt_password_hash.php`

cp /var/www/html/.env.dist /var/www/html/.env

sed -i "s,%%ADMIN_PASSWORD%%,$HASHED_PASSWORD," /var/www/html/.env
sed -i "s/%%ADMIN_USERNAME%%/${ADMIN_USERNAME}/" /var/www/html/.env
sed -i "s/%%APP_ENV%%/${ORG_ENV}/" /var/www/html/.env
sed -i "s/%%APP_NAME%%/${APP_NAME}/" /var/www/html/.env
sed -i "s/%%MYSQL_HOST%%/${MYSQL_HOST}/" /var/www/html/.env
sed -i "s/%%MYSQL_USER%%/${MYSQL_USER}/" /var/www/html/.env
sed -i "s/%%MYSQL_PASSWORD%%/${MYSQL_PASSWORD}/" /var/www/html/.env
sed -i "s/%%MYSQL_DATABASE%%/${MYSQL_DATABASE}/" /var/www/html/.env
sed -i "s,%%MYSQL_UNIX_SOCKET%%,${MYSQL_UNIX_SOCKET}," /var/www/html/.env

mkdir -p /var/www/html/var
chown -R www-data:www-data /var/www/html

if [ "${ORG_ENV}" = "dev" ]
then
  sed -i "s/APP_ENV=prod/APP_ENV=${ORG_ENV}/" /var/www/html/.env
  /entrypoint/waitforit.sh $MYSQL_HOST:3306 -t 100
fi

echo "Running migrations"
bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

GREEN='\033[0;32m'
NC='\033[0m' # No Color
printf "${GREEN}Setup completed!${NC}"

tail -f /var/www/html/var/log/${ORG_ENV}.log
