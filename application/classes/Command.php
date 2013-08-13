<?php

class Command
{

    protected $isRemote         = FALSE;
    protected $remoteConnection = NULL;
    protected $baseDir          = NULL;

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

    public function chdir($dir)
    {
        if ($this->isRemote) {
            $this->remoteConnection->chdir($dir);
        } else {
            chdir($dir);
        }
    }
    
    public function pwd() {
        if ($this->isRemote) {
            return $this->remoteConnection->pwd();
        } else {
            return getcwd();
        }
    }

    public function chtobasedir()
    {
        if (!$this->isRemote) {
            chdir($this->baseDir);
        }
    }

    public function execute($command)
    {
        if ($this->isRemote) {
            return $this->remoteConnection->exec($command);
        } else {
            $result = array();
            exec($command, $result);
            return $result;
        }
    }

    public function rcopy($source, $dest)
    {
        if ($this->isRemote) {
            $this->_rcopy($source, $dest);
        } else {
            File::rcopy($source, $dest);
        }
    }

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

    public function is_dir($dir)
    {
        if ($this->isRemote) {
            $stat = $this->remoteConnection->lstat($dir);
            return ($stat !== FALSE && $stat['type'] == NET_SFTP_TYPE_DIRECTORY);
        } else {
            return is_dir($dir);
        }
    }

    public function is_file($file)
    {
        if ($this->isRemote) {
            $stat = $this->remoteConnection->lstat($file);
            return ($stat !== FALSE && $stat['type'] == NET_SFTP_TYPE_REGULAR);
        } else {
            return is_file($file);
        }
    }
}