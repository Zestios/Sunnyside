#!/bin/sh

cd scripts || return
echo -e '\e[1m\e[34mPulling database from production server..\e[0m\n'
./pull_db.sh
echo -e '\e[1m\e[34mPulling assets from production server..\e[0m\n'
./pull_assets.sh
