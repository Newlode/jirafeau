#!/bin/bash
jroot=$(cd "$(dirname $0)" && pwd)
docker -h &> /dev/null || (echo "You need docker installed" && exit 1)
docker pull tutum/apache-php
docker run -v $jroot:/app  -t -i --rm -p 8080:80 tutum/apache-php
