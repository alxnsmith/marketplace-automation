# By default - PROD mode
# If $0 is --dev - DEV mode
if [ "$0" = "--dev" ]; then
  echo "DEV mode"
  DEV_MODE=true
  cp .env.example.dev .env
else
  echo "PROD mode"
  DEV_MODE=false
  cp .env.example.prod .env
fi

echo "Install composer"
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

echo " > composer update"
composer update
