<?php
// @codeCoverageIgnoreStart
if (!function_exists('hex2bin')) {

    function hex2bin($str)
    {
        $sbin = "";
        $len  = strlen($str);
        for ($i = 0; $i < $len; $i += 2) {
            $sbin .= pack("H*", substr($str, $i, 2));
        }

        return $sbin;
    }
}
// @codeCoverageIgnoreEnd

/**
 * ORM Auth driver.
 *
 * @package Core
 */
class Auth_ORM extends Kohana_Auth_ORM
{

    /**
     * Perform a hmac hash, using SHA 256.
     *
     * @param string $str string to hash
     * @param string $key key
     * 
     * @return  string
     */
    public function hashKey($str, $key)
    {
        return hash_hmac('sha256', $str, $key);
    }

    /**
     * Logs a user in.
     *
     * @param string  $user     Username
     * @param string  $response Response to challenge
     * @param boolean $remember Enable autologin
     * 
     * @return  boolean
     */
    /* protected */ function _login($user, $response, $remember)
    {
        if (!is_object($user)) {
            $username = $user;

            // Load the user
            $user = ORM::factory('User');
            $user->where($user->unique_key($username), '=', $username)->find();
        }

        if ($user->loaded()) {
            if ($user->has('roles', ORM::factory('Role', array('name' => 'login'))) && $user->checkChallenge($response)) {
                if ($remember === TRUE) {
                    // Token data
                    $data = array(
                        'user_id'    => $user->pk(),
                        'expires'    => time() + $this->_config['lifetime'],
                        'user_agent' => sha1(Request::$user_agent),
                    );

                    // Create a new autologin token
                    $token = ORM::factory('User_Token')
                            ->values($data)
                            ->create();

                    // Set the autologin cookie
                    Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
                }

                // Finish the login
                $this->complete_login($user);

                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Compare password with original (hashed). Works for current (logged in) user
     *
     * @param string $password Password
     * 
     * @return boolean
     */
    public function check_password($password)
    {
        throw new HTTP_Exception_500("Not implemented");
    }
}