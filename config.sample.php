<?php

return array(
    'backup_dir'    => '/data/db-backups',
    'keep_files'    => 50,
    'compression'   => '', // gzip, bzip2, etc
    'db_host'       => 'localhost',
    'db_port'       => '3306',
    'db_protocol'   => '', // tcp, socket, etc
    'db_user'       => 'root',
    'db_passwd'     => 'password',
    'extra_config'  => '', // /path/to/extra/my.cnf
    'db_names'      => array(
        'some_db1',
        'other_db2',
        'tvdb',
        'airdb' 
    )
);
