<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Executes commands remotely or locally.
 *
 * @package   Core
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Command
{

    /**
     * Indicates if commands should be remote or local
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
     * @param Model_Project &$project Project
     * @param string        $basedir  Base dir
     * 
     * @throws RuntimeException Remote and cannot read private key
     * @throws RuntimeException Remote and cannot login
     * @throws RuntimeException Remote and server key changed
     */
    public function __construct(Model_Project &$project, $basedir = DOCROOT)
    {
        if ($project->is_remote) {
            $this->isRemote = TRUE;
            if (!is_readable($project->privatekey_path)) {
                throw new RuntimeException('Could not read private key');
            }

            $this->remoteConnection = new Net_SFTP($project->host, $project->port);
            $key                    = new Crypt_RSA();
            $key->loadKey(file_get_contents($project->privatekey_path));
            if (!$this->remoteConnection->login($project->username, $key)) {
                throw new RuntimeException('Could not login to ' . $project->host);
            }
            $remotekey = trim($this->remoteConnection->getServerPublicHostKey());
            if ($remotekey != $project->public_host_key) {
                throw new RuntimeException(
                'Server public host key has changed. Expected: ' . $project->public_host_key . '; Actual: ' . $remotekey
                );
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
     * @param string $command     Command is not escaped
     * @param int    &$return_var If present, then the return status of the executed command will be written to this variable.
     * 
     * @return string Output of the command
     */
    public function execute($command, &$return_var = NULL)
    {
        if ($this->isRemote) {
            $res        = $this->remoteConnection->exec('cd ' . $this->pwd() . ' && ' . $command . '; echo +res=$?');
            $matches    = array();
            preg_match('/\+res=(-?\d+)/', $res, $matches);
            $return_var = $matches[1];
            $res        = trim(str_replace($matches[0], '', $res));
            return $res;
        } else {
            $result = array();
            exec($command, $result, $return_var);
            return implode(PHP_EOL, $result);
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
                if ($file == '.' || $file == '..') {
                    continue;
                }

                $result &= $this->_rcopy($source . '/' . $file, $dest . DIR_SEP . $file);
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

    /**
     * Makes directory
     * 
     * @param string $pathname  The directory path.
     * @param int    $mode      Mode
     * @param bool   $recursive Allows the creation of nested directories specified in the pathname.
     * 
     * @return bool TRUE on success or FALSE on failure.
     */
    public function mkdir($pathname, $mode = 0755, $recursive = false)
    {
        if ($this->isRemote) {
            return $this->remoteConnection->mkdir($pathname, $mode, $recursive);
        } else {
            return mkdir($pathname, $mode, $recursive);
        }
    }
}