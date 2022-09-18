echo " > composer update"
composer update
echo " > sail up -d"
sail up -d
echo " > sail yarn install"
sail yarn install --force

echo " > sail artisan key:generate"
sail artisan key:generate
echo " > sail artisan migrate:fresh --seed"
sail artisan migrate:fresh --seed
echo " > sail artisan storage:link"
sail artisan storage:link

echo " > sail stop"
sail stop

echo "Done!"