<?php
defined('SYSPATH') OR die('No direct access allowed.');

require_once MODPATH . 'phpseclib' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Crypt' . DIRECTORY_SEPARATOR . 'Random.php';

/**
 * User model
 * 
 * The challenge/response authentication algorithm is based on research from Openwall, 
 * described at http://www.openwall.info/wiki/people/solar/algorithms/challenge-response-authentication
 * 
 * @package   Model
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Model_User extends Model_Auth_User
{

    /**
     * Rules for the user model.
     *
     * @return array Rules
     */
    public function rules()
    {
        return array(
            'username' => array(
                array('not_empty'),
                array('max_length', array(':value', 32)),
                array(array($this, 'unique'), array('username', ':value')),
            ),
            'password' => array(
                array('not_empty'),
            ),
            'email'    => array(
                array('not_empty'),
                array('email'),
                array(array($this, 'unique'), array('email', ':value')),
            ),
        );
    }

    /**
     * Insert a new object to the database
     * 
     * @param Validation $validation Validation object
     * 
     * @throws Kohana_Exception
     * @return self
     */
    public function create(Validation $validation = NULL)
    {
        $challenge       = $this->_generateNewChallenge($this->password);
        $this->challenge = $challenge[0];
        $this->password  = $challenge[1];
        return parent::create($validation);
    }

    /**
     * Updates a single record or multiple records
     *
     * @param Validation $validation Validation object
     * 
     * @chainable
     * @throws Kohana_Exception
     * @return self
     */
    public function update(Validation $validation = NULL)
    {
        if ($this->changed("password") && !$this->changed("challenge")) {
            $challenge       = $this->_generateNewChallenge($this->password);
            $this->challenge = $challenge[0];
            $this->password  = $challenge[1];
        }
        return parent::update($validation);
    }

    /**
     * Disable filters
     *
     * @return array Empty filters
     */
    public function filters()
    {
        return array();
    }

    /**
     * Checks the response to a challenge
     * 
     * @param string $response Response to the challenge
     * 
     * @return boolean true if OK
     */
    public function checkChallenge($response)
    {
        $aes      = new Crypt_AES();
        $aes->setKey($response);
        $password = $aes->decrypt(hex2bin($this->password));
        $hash     = Auth::instance()->hashKey($password, $this->challenge);
        if ($hash == $response) {
            $challenge       = $this->_generateNewChallenge($password);
            $this->challenge = $challenge[0];
            $this->password  = $challenge[1];
            $this->update();
            return true;
        }
        return false;
    }

    /**
     * Generates new challenge/encrypted password and saves them.
     * 
     * If the plain password is empty, the challenge and the encrypted password are empty.
     * 
     * @param string $plainPassword Plain password
     * 
     * @return array Couple (new challenge, new encrypted password)
     */
    /* private */ function _generateNewChallenge($plainPassword)
    {
        if (empty($plainPassword)) {
            return array("", "");
        }
        $challenge        = bin2hex(crypt_random_string(32));
        $encryptedPassword = $this->_generateNewPassword($challenge, $plainPassword);
        return array($challenge, $encryptedPassword);
    }

    /**
     * Generates a new encrypted password
     * 
     * @param string $challenge     Challenge
     * @param string $plainPassword Plain password
     * 
     * @return string New encrypted password
     */
    /* private */ function _generateNewPassword($challenge, $plainPassword)
    {
        $new_hash = Auth::instance()->hashKey($plainPassword, $challenge);

        $aes               = new Crypt_AES();
        $aes->setKey($new_hash);
        $encryptedPassword = bin2hex($aes->encrypt($plainPassword));
        return $encryptedPassword;
    }
}

// @codeCoverageIgnoreStart
if (!function_exists('hex2bin')) {

    /**
     * Decodes a hexadecimally encoded binary string
     * 
     * @param string $data Hexadecimal representation of data.
     * 
     * @return string the binary representation of the given data or FALSE on failure.
     */
    function hex2bin($data)
    {
        $sbin = "";
        $len  = strlen($data);
        for ($i = 0; $i < $len; $i += 2) {
            $sbin .= pack("H*", substr($data, $i, 2));
        }

        return $sbin;
    }
}
// @codeCoverageIgnoreEnd
