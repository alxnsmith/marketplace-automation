composer update
sail yarn install

sail artisan key:generate
sail artisan migrate:fresh --seed
sail artisan storage:link

echo "Done!"