#!/bin/bash
set -e
if [ "$(id -u)" -ne "1000" ]; then
	echo "your user id is not 1000, shared folder won't have a good uid"
	exit 1
fi
docker -h &> /dev/null || (echo "You need docker installed" && exit 1)
docker pull mojo4242/jirafeau-dev:apache2-php7
name=jirafeau-dev-$(date +%Y%m%d%H%M%S)
docker run --name $name -d -p 8000:80 -v $(pwd):/var/www/html mojo4242/jirafeau-dev:apache2-php7 /usr/sbin/apache2ctl -D FOREGROUND
echo "You can now open http://127.0.0.1:8000/"
echo "Press enter to destroy instance..."
read
docker stop -t 0 $name
docker rm $name
echo "Instance destroyed"
