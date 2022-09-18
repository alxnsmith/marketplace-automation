sh scripts/init-base.sh
sh scripts/install/node.sh # Install node 18 with nvm and install yarn

echo " > yarn install"
yarn install --force

echo " > yarn build"
yarn build

echo " > php artisan key:generate"
php artisan key:generate


echo " > docker compose up -d"
docker compose up -d

echo " > docker compose exec laravel.market-automation php artisan migrate:fresh --seed"
docker compose exec laravel.market-automation php artisan migrate:fresh --seed
echo " > docker compose exec laravel.market-automation php artisan storage:link"
docker compose exec laravel.market-automation php artisan storage:link

echo " > docker compose stop"
docker compose stop

echo "Done!"
