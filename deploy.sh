#!/bin/bash
#
#	Modify this file to deploy your project on production server
#
exit
rsync -n -varlogpt --progress --exclude="include/conf.php" --exclude="tmp" \
--exclude=".htpassw" --exclude=".htaccess" --exclude="htdocs/admin" \
--exclude="htdocs/upload" \
/home/develop/skeleton/* root@uk:/home/skeleton/

#ssh uk "/etc/init.d/php5-fpm reload; /etc/init.d/apache2 reload;"
