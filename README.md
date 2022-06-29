# sunnyside

## Installation

Before you start, you must have the latest version of the following software installed globally :

- apache or nginx
- mariadb
- php
- git
- composer
- node
- npm
- yarn
- gulp-cli

NOTE: The `public` folder must be set as your document root.

NOTE: If you're running this project with `nginx`, use this config [https://github.com/nystudio107/nginx-craft]

- Create a SQL database named `sunnyside` using the `utf8_unicode_ci` collation ✅
- Import the SQL dump in the /docs/database directory ✅
- Run this command `composer install --no-scripts && npm install && ./craft setup && npm run generate && npm run serve` ✅
- Make sure your `.env` is correctly configured ✅
- Also make sure to replace your .git/ repo in this project if you have 'cloned' the base. (craft3-base) and delete this line 🗑

## Configure Server sync assets & database (server to local)

- Replace every paths and username/password in the `.env.sh.example` and create a .env.sh fire from it ✅
- Run this command `./byhoffman-setup` ✅
- Make sure /scripts/.env.sh configuration is the same as `.env.sh` ✅
- Run this command `./scripts/pull_db.sh && ./scripts/pull_assets.sh` ✅

## Configure PWA

- Replace every instance of `REPLACE_ME_*` in the `package.json` file and delete this line 🗑
- Replace every instance of `REPLACE_ME_*` in the `public/webappmanifest.json` file and delete this line 🗑

## Configure Auto-Deploy

- Replace every instance of `REPLACE_ME_*` in the `bitbucket-pipelines.yml` file and delete this line 🗑
- Replace every instance of `REPLACE_ME_*` in the `deploy.sh` file and delete this line 🗑

## Local development with Laravel Valet

- Access your local website with `sunnyside.test`
- You need to have this Laravel Valet driver installed [https://bitbucket.org/hoffmanagency/craft-valet] if you use
Laravel Valet version 1.x.x.
- If you don't use `valet park`, create a link using `valet link sunnyside` to access your site with `sunnyside.test`.
- Only run `npm run build` when you want to generate Critical CSS for production. Otherwise, gulp build is reserved for Production only.

## Updating this project

####  Sync local dev database and assets from the live production/staging server
- If you are supporting a production/staging website and want to sync the assets from the Production Version :
- Configure your `.env.sh` file
- Run `./pull.sh`

#### Craft core updates

Only update Craft CMS in your **local development environment** using the latest production database dump and commit
all changes to files in the git repository. Never execute a Craft update directly on the production. Same goes for plugins.

## Craft control panel

- Local: `/craftcms`
- Staging: `/craftcms`
- Production: `/craftcms`

**Authentication**

- Username: Administrator
- Password: admin123

## Gulp Tasks

#### Serve
- `npm run serve` Build and watch for files changes, uses LiveReload.

#### See more commands/concepts in the `docs/` folder.

