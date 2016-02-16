#!/bin/bash
exit
rsync -n -varlogpt --progress --exclude="include/conf.php" --exclude="tmp" \
--exclude=".htpassw" --exclude=".htaccess" --exclude="htdocs/admin" \
--exclude="htdocs/upload" \
/home/develop/eprimar/* root@uk:/home/eprimar/

#ssh uk "/etc/init.d/php5-fpm reload; /etc/init.d/apache2 reload;"
