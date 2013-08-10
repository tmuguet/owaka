<?php

class Task_Ssh extends Minion_Task
{

    protected function _execute(array $params)
    {
        $sftp = new Net_SFTP('dev.kasagi.fr');
        $key  = new Crypt_RSA();
        $key->loadKey(file_get_contents('/Users/tmuguet/.ssh/identity'));
        if (!$sftp->login('tmuguet', $key)) {
            exit('Login Failed');
        }
        print_r($sftp->nlist()); // == $sftp->nlist('.')
        print_r($sftp->rawlist()); // == $sftp->rawlist('.')
    }
}
