# DB Autobackup via PHP

Automatically backs up your databases via scheduled tasks or cron.

Able to limit or rotate backups so that older backups are deleted.

## Installation

Download the source code. Depending on your platform, you either use the batch file `autobackup.bat`
or the bash script `autobackup.sh`. Copy the files to anywhere you like.

## Configuration

A sample config.php is given in the name of `config.sample.php`. copy it as `config.php`
on the same directory. Edit `config.php` to configure the following:

* `backup_dir` - the location where you want to save your backup files.
* `keep_files` - the number of backup files to keep.
* `compression` - (Linux only) - allows backup compression by piping contents to compression tools like `gzip`, `bzip2`, etc.
* `extra_config` - optional, allows extra configuration for the `mysqldump` command, especially for storing credentials.
* `db_host` - the host name where the database is running.
* `protocol` - defaults to empty string, options include `tcp`, `socket`, etc.
* `db_user` - the user who has dump access to all your listed databases on db_names.
* `db_passwd` - the database password. 
* `db_names` - an array of database names you want to backup. Make sure your user has access to use mysqldump to these databases.

__IMPORTANT__: Make sure to create first the directories where you want to save all your backup files for each database. For example:

If your backup dir is `/data/db-backups` and one of your database name is `market`, make sure you create first
`/data/db-backups/market`. This applies to all databases you want to include.

## Fixing insecure command line warning

```text
Warning: Using a password on the command line interface can be insecure.
```

If you are getting the warning above, this is due to the fact that password
is passed through the command line and is considered an insecure practice.
To avoid the warning, we can use the `extra_config` config parameter.

The `extra_config` is actually used as the `--defaults-extra-file` parameter for
`mysqldump` where we can pass extra parameters to the command. However, for the context
of our backup script, we will use this parameter to securely pass the username
and password, therefore, avoid the insecure command warning.

```text
'extra_config' => '/path/to/extra/my.cnf',
```

Example content for `/path/to/extra/my.cnf`:

```text
[client]
user = root
password = password
```

If you use the `extra_config` parameter, the `db_user` and `db_passwd` parameters
are ignored and can be left blank.

## Scheduling

When scheduling the backup task, choose either `autobackup.sh` or `autobackup.bat`
depending on your platform. For Linux, you can add the task via cron by adding
this on your crontab.

```bash
0 0 * * *   /usr/bin/php /home/user/path/to/script/backup_db.php  >> /home/user/dbautobackup.log 2>&1
```

That effectively schedules backup every midnight.

Enjoy :D
