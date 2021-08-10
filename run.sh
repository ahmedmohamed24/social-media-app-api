#!/bin/bash
docker-compose down --remove-orphans
sudo rm -rf mysql
mkdir mysql
docker-compose build && docker-compose up -d
#docker-compose exec php php /var/www/html/artisan migrate:fresh --seed
sudo chmod +755 -R src/bootstrap/cache
sudo chmod +755 -R src/storage
echo "run migration to get started"
