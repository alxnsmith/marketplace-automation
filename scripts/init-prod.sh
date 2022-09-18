sh scripts/init-base.sh
sh scripts/install/node.sh # Install node 18 with nvm and install yarn

echo " > yarn install"
yarn install --force

echo " > yarn build"
yarn build

echo " > php artisan key:generate"
php artisan key:generate

echo " > php artisan migrate:fresh --seed"
php artisan migrate:fresh --seed

echo " > php artisan storage:link"
php artisan storage:link

echo "Done!"
