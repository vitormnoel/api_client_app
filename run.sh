#!/bin/bash -e
while getopts m: flag
do
    # shellcheck disable=SC2220
    case "${flag}" in
        m) mode=${OPTARG};;
    esac
done

docker compose up -d --build
docker compose run php80-service composer install

if [ ! -z $mode ]
then
    if [ $mode == "dev" ]
    then
         docker compose exec -it php80-service bash
    else
         echo "flag mode (-m) is invalid"
    fi
fi