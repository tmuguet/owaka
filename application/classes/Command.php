<?php

/**
 * Executes commands remotely or locally.
 *
 * @package Core
 */
class Command
{

    /**
     * Indicates if commands should be remote or locals
     * @var bool
     */
    protected $isRemote = FALSE;

    /**
     * Remote connection
     * @var Net_SFTP 
     */
    protected $remoteConnection = NULL;

    /**
     * Base dir for executing commands
     * @var string
     */
    protected $baseDir = NULL;

    /**
     * Constructor
     * 
     * @param Model_Project $project Project
     * @param string        $basedir Base dir
     * 
     * @throws Exception Remote and impossible to login
     */
    public function __construct(Model_Project &$project, $basedir = DOCROOT)
    {
        if ($project->is_remote) {
            $this->isRemote = TRUE;

            $this->remoteConnection = new Net_SFTP($project->host, $project->port);
            $key                    = new Crypt_RSA();
            $key->loadKey(file_get_contents($project->privatekey_path));
            if (!$this->remoteConnection->login($project->username, $key)) {
                throw new Exception('Could not login to ' . $project->host);
            }
        } else {
            $this->baseDir = $basedir;
        }
    }

    /**
     * Change directory
     * 
     * @param string $directory The new current directory
     * 
     * @return bool TRUE on success or FALSE on failure.
     */
    public function chdir($directory)
    {
        if ($this->isRemote) {
            return $this->remoteConnection->chdir($directory);
        } else {
            return chdir($directory);
        }
    }

    /**
     * Change directory to base dir
     * 
     * @return bool TRUE on success or FALSE on failure.
     */
    public function chtobasedir()
    {
        if ($this->isRemote) {
            return true;
        } else {
            return chdir($this->baseDir);
        }
    }

    /**
     * Gets the current working directory
     * 
     * @return string the current working directory on success, or FALSE on failure.
     */
    public function pwd()
    {
        if ($this->isRemote) {
            return $this->remoteConnection->pwd();
        } else {
            return getcwd();
        }
    }

    /**
     * Executes a command.
     * 
     * @param string $command Command is not escaped
     * 
     * @return string Output of the command
     */
    public function execute($command)
    {
        if ($this->isRemote) {
            return $this->remoteConnection->exec($command);
        } else {
            $result = array();
            exec($command, $result);
            return implode("\n", $result);
        }
    }

    /**
     * Recursively copy a file/directory
     * 
     * @param string $source Source
     * @param string $dest   Destination
     * 
     * @return boolean True if copy succeeds
     */
    public function rcopy($source, $dest)
    {
        if ($this->isRemote) {
            return $this->_rcopy($source, $dest);
        } else {
            return File::rcopy($source, $dest);
        }
    }

    /**
     * Recursively copy a remote file/directory
     * 
     * @param string $source Source
     * @param string $dest   Destination
     * 
     * @return boolean True if copy succeeds
     */
    private function _rcopy($source, $dest)
    {
        if ($this->is_dir($source)) {
            if (!file_exists($dest)) {
                mkdir($dest, 0700);
            }
            $objects = $this->remoteConnection->nlist($source);
            $result  = true;
            foreach ($objects as $file) {
                if ($file == "." || $file == "..") {
                    continue;
                }

                $result &= $this->_rcopy($source . '/' . $file, $dest . DIRECTORY_SEPARATOR . $file);
            }
            return $result;
        } elseif ($this->is_file($source)) {
            return $this->remoteConnection->get($source, $dest);
        } else {
            return false;
        }
    }

    /**
     * Tells whether the filename is a directory
     * 
     * @param string $filename Path to the file.
     * 
     * @return bool TRUE if the filename exists and is a directory, FALSE otherwise.
     */
    public function is_dir($filename)
    {
        if ($this->isRemote) {
            $stat = $this->remoteConnection->lstat($filename);
            return ($stat !== FALSE && $stat['type'] == NET_SFTP_TYPE_DIRECTORY);
        } else {
            return is_dir($filename);
        }
    }

    /**
     * Tells whether the filename is a regular file
     * 
     * @param string $filename Path to the file
     *
     * @return bool TRUE if the filename exists and is a regular file, FALSE otherwise.
     */
    public function is_file($filename)
    {
        if ($this->isRemote) {
            $stat = $this->remoteConnection->lstat($filename);
            return ($stat !== FALSE && $stat['type'] == NET_SFTP_TYPE_REGULAR);
        } else {
            return is_file($filename);
        }
    }
}