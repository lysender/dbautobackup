# DB Autobackup via PHP

Automatically back up your databases via scheduled tasks or cron.

Able to limit the number of backups.

## Installation

Download the source code. Depending on your platform, you either use the batch file or the bash srcipt (*.sh). Copy the files to anywhere you like.

## Configuration

A sample config.php is given in the name of `config.sample.php`. copy it as `config.php` on the same directory. Edit `config.php` to configure the following:

* backup_dir - the location where you want to save your backup files
* keep_files - the number of backup files to keep
* db_host - the host name where the database is running
* db_user - the user who has dump access to all your listed databases on db_names
* db_passwd - the database password
* db_names - an array of database names you want to backup. Make sure your user has access to use mysqldump to these databass.

__IMPORTANT__: Make sure to create first the directories where you want to save all your backup files for each database. For example:

If your backup dir is `D:/backup` and one of your database name is `airdb`, make sure you create first `D:/backup/airdb`. This applies to all databases you want to include.

## Scheduling 

When scheduling the backup task, choose either `autobackup.sh` or `autobackup.bat` depending on your platform. For Linux, you can add the task via cron by adding this on your crontab. 

	0 0 * * *	/home/user/path/to/script/autobackup.sh >/dev/null 2>&1	

That effectively schedules backup every midnight.

Enjoy :D
