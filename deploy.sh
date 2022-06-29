#!/bin/bash
root="/srv/users/REPLACE_ME/apps/REPLACE_ME"
cd $root || return
echo -e '\e[1m\e[34mPulling code from remote..\e[0m\n'
git pull origin main
echo -e '\e[1m\e[34m\nInstalling required node packages..\e[0m\n'
npm install
echo -e '\e[1m\e[34m\nGenerating assets..\e[0m\n'
npm run build
echo -e '\e[1m\e[34m\nInstalling required packages..\e[0m\n'
composer install --no-interaction
echo -e '\e[1m\e[34m\nIncrementing code version..\e[0m\n'
npm run code-version
echo -e '\e[1m\e[34m\nAPI deployed\e[0m\n'
