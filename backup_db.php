<?php 

date_default_timezone_set('Asia/Manila');

/** 
 * PHP script for db auto backup
 *
 * @author Lysender <theman@lysender.com>
 */
class Db_Tool_Backup
{
    /** 
     * @var string
     */
    protected $_backup_dir;

    /** 
     * @var int
     */
    protected $_keep_files;

    /**
     * @var string
     */
    protected $_compression;

    /** 
     * @var string
     */
    protected $_db_host;

    /**
     * @var string
     */
    protected $_db_port;

    /**
     * @var string
     */
    protected $_db_protocol;

    /** 
     * @var string
     */
    protected $_db_user;

    /** 
     * @var string
     */
    protected $_db_passwd;

    /** 
     * @var array
     */
    protected $_previous_count = array();

    /** 
     * @var array
     */
    protected $_backup_files = array();

    /**
     * Database names to backup
     * Loaded via config
     *
     * @var array
     */
    protected $_db_names = array();

    /** 
     * Initialize by loading the config
     *
     */
    public function __construct()
    {
        $config = $this->_get_config();

        if (empty($config))
        {
            throw new Exception('Unable to read config');
        }

        // Load config
        foreach ($config as $key => $val)
        {
            $prop = '_'.$key;

            if (property_exists($this, $prop))
            {
                $this->{$prop} = $val;
            }
        }
    }

    /** 
     * Returns the config file data
     *
     * @return array
     */
    protected function _get_config()
    {
        $file = dirname(__FILE__).'/config.php';

        return include $file;
    }

    /**
     * Run backup
     *
     * @return boolean true | false
     */
    public function backup()
    {
        $suffix = date('Y-m-d-H-i-s');

        if ( ! empty($this->_db_names))
        {
            foreach ($this->_db_names as $name)
            {
                // Back it up
                $this->_single_backup($name, $suffix);
            }
        }
    }

    /** 
     * Performs backup for a single database
     *
     * @param string $db_name
     * @param string $suffix
     * @return boolean
     */
    protected function _single_backup($db_name, $suffix)
    {
        $filename = $this->_backup_dir.'/'.$db_name.'/'.$db_name.'_'.$suffix.'.sql';

        $params = array();
        $params[] = '-h '.$this->_db_host;
        $params[] = '--port '.$this->_db_port;
        if ($this->_db_protocol) {
            $params[] = '--protocol '.$this->_db_protocol;
        }
        $params[] = '-u '.$this->_db_user;
        $params[] = '-p'.$this->_db_passwd;

        $command = "mysqldump $db_name " . implode(' ', $params);

        if ($this->_compression) {
            $command .= " | {$this->_compression} > $filename.bz2";
        } else {
            $command = "mysqldump $db_name " . implode(' ', $params) . " > $filename";
        }

        return system($command);
    }

    /**
     * Deletes backup files that should not be kept
     * 
     * @return boolean
     */
    public function cleanup()
    {
        foreach ($this->_db_names as $name)
        {
            $this->_single_cleanup($name);
        }
    }

    /** 
     * Cleans up backup for a single db
     *
     * @param string $db_name
     * @return boolean
     */
    protected function _single_cleanup($db_name)
    {
        $this->_get_backup_files($db_name);

        $delete_files = $this->_get_files_to_delete($db_name);
        if ( ! empty($delete_files))
        {
            foreach ($delete_files as $key => $file)
            {
                $filename = $this->_backup_dir.'/'.$db_name.'/'.$file;
                $this->_delete($filename);
            }

            return TRUE;
        }

        return FALSE;
    }
    
    /**
     * Returns all files to be deleted
     * 
     * @param string $db_name
     * @return array $files | false
     */
    protected function _get_files_to_delete($db_name)
    {
        // Only return files that should not be kept
        if ( ! empty($this->_backup_files[$db_name]))
        {
            $ret = array();

            $count = count($this->_backup_files[$db_name]);

            if ($count > $this->_keep_files)
            {
                for ($c = $this->_keep_files; $c < $count; $c++)
                {
                    if (
                        isset($this->_backup_files[$db_name][$c]) && 
                        $this->_backup_files[$db_name][$c] != '.' && 
                        $this->_backup_files[$db_name][$c] != '..'
                    )
                    {
                        $ret[] = $this->_backup_files[$db_name][$c];
                    }
                }
            }

            return $ret;
        }
        return FALSE;
    }
    
    /**
     * Returns all backup files in descending order
     * 
     * @param string $db_name
     * @return array $files | boolean false
     */
    protected function _get_backup_files($db_name)
    {
        $dir = $this->_backup_dir.'/'.$db_name;

        $files = scandir($dir, 1);

        if ( ! empty($files))
        {
            $this->_backup_files[$db_name] = $files;

            return $this->_backup_files[$db_name];
        }
        return FALSe;
    }
    
    /**
     * Deletes the backup file
     * 
     * @param string $filename
     * @return boolean
     */
    protected function _delete($filename)
    {
        if (is_file($filename))
        {
            return unlink($filename);
        }

        return FALSE;
    }
    
    /**
     * Writes the counter back to the log file
     * 
     * @param string $db_name
     * @param int $count
     * @return boolean
     */
    protected function _set_count($db_name, $count)
    {
        $filename = dirname(__FILE__).'/'.$db_name.'_count';

        $file = fopen($filename, 'wb');

        if ($file)
        {
            return fwrite($file, (string) $count);
        }

        return FALSE;
    }
    
    /**
     * Retrieves the backup count from the text file
     * 
     * @param string $db_name
     * @return int
     */
    protected function _get_count($db_name)
    {
        $filename = dirname(__FILE__).'/'.$db_name.'_count';

        $file = fopen($filename, 'rb');

        if ($file)
        {
            $count = fgets($file, 4096);

            if ( ! is_numeric($count))
            {
                $count = FALSE;
            }

            fclose($file);

            $this->_previous_count[$db_name] = $count;

            return $this->_previous_count[$db_name];
        }
    }
}

// Perform backup
$backup_manager = new Db_Tool_Backup;
$backup_manager->backup();
$backup_manager->cleanup();
