There are several scripts included in craft-scripts, each of which perform different functions. They all use a shared .env.sh to function. This .env.sh should be created on each environment where you wish to run the craft-scripts, and it should be excluded from your git repo via .gitignore.

- NOTE: For all the commands, you must 'CD' into the scripts folder `cd scripts`

# Set permissions
The set_perms.sh script sets the Craft CMS install file permissions in a strict manner, to assist in hardening Craft CMS installs.
- Set permissions using `./set_perms.sh`

# Clear Caches
The clear_caches.sh script clears the Craft CMS caches by removing all of the craft/storage/runtime/ cache dirs, as well as emptying the craft_templatecaches db table. It can also clear Redis db caches if LOCAL_REDIS_DB_ID is set, and it can clear FastCGI Cache if LOCAL_FASTCGI_CACHE_DIR is set.
- Clear caches using `./clear_caches.sh`

# Sync Files

##  Sync local dev assets from the live pro­duc­tion serv­er
The pull_db.sh script pulls down a database dump from a remote server, and then dumps it into your local database. It backs up your local database before doing the dump.
- Import assets using `./pull_assets.sh`

## Sync local dev data­base from the live pro­duc­tion serv­er
The pull_assets.sh script pulls down an arbitrary number of asset directories from a remote server, since we keep client-uploadable assets out of the git repo. The directories it will pull down are specified in LOCAL_ASSETS_DIRS. It will also pull down the Craft userphotos and rebrand directories from craft/storage by default. The directories it will pull down are specified in LOCAL_CRAFT_FILE_DIRS
- Import Database using `./pull_db.sh`

# Pull Backups
The pull_backups.sh script pulls down the backups created by craft-scripts from a remote server, and synced into the LOCAL_BACKUPS_PATH. For database backups, a sub-directory REMOTE_DB_NAME/db inside the REMOTE_BACKUPS_PATH directory is used for the database backups. For asset backups, a sub-directory REMOTE_DB_NAME/assets inside the REMOTE_BACKUPS_PATH directory is used for the asset backups.
- Pull backups using `./pull_backups.sh`

# Sync Backups to S3
The sync_backups_to_s3.sh script syncs the backups from LOCAL_BACKUPS_PATH to the Amazon S3 bucket specified in REMOTE_S3_BUCKET. If you have defined a optional subfolder, it will contain the backups to the path defined in REMOTE_S3_PATH. This script assumes that you have already installed awscli and have configured it with your credentials.
- Sync Backup to S3 using `.sync_backups_to_s3.sh`

#Backup Database
The backup_db.sh script backs up the local database into a timestamped, gzip compressed archive into the directory set via LOCAL_BACKUPS_PATH. It will also automatically rotate out (delete) any backups that are older than GLOBAL_DB_BACKUPS_MAX_AGE old.The database backups exclude temporary/cache tables, and are stored in the sub-directory LOCAL_DB_NAME/db, inside of LOCAL_BACKUPS_PATH.
- Backup Databse using `./backup_db.sh`

#Backup Assets
The backup_assets.sh script backs up an arbitrary number of asset directories to the directory specified in LOCAL_BACKUPS_PATH. The directories it backs are up specified in LOCAL_ASSETS_DIRS, just as they were for the pull_assets.sh script. It will also back up the Craft userphotos and rebrand directories from craft/storage by default. The directories it will backup are specified in LOCAL_CRAFT_FILE_DIRS
- Backup Assets using `./backup_assets.sh`

# Restore Databse
The restore_db.sh restores the local database to the database dumb passed in via command line argument. It backs up your local database before doing the restore.You can pass in either a path to a .sql file or .gz file to restore_db.sh, and it will do the right thing based on the file type.
- Restore using `./restore_db.sh`

# Restore Assets
The restore_assets.sh restores the assets from the backup that has been created with backup_assets.sh.
- Restore using `./restore_assets.sh`

