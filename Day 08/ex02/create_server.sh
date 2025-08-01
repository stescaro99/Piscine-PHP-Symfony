#!/usr/bin/env bash

sudo bash -c 'cat > /etc/apache2/sites-available/production.conf <<EOF
<VirtualHost *:80>
    ServerName production
    DocumentRoot /var/www/production/web
    <Directory /var/www/production/web>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF'

sudo a2ensite production
sudo systemctl reload apache2