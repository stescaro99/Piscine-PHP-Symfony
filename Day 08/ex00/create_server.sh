#!/usr/bin/env bash

sudo apt-get update
sudo apt-get install -y curl
sudo apt-get install -y php5
sudo apt-get install -y apache2
sudo apt-get install -y mysql-server
sudo apt-get install -y git
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
