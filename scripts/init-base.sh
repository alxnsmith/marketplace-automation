echo "Install composer"
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

echo " > composer update"
composer update
