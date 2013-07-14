<?php
// Sanity check, install should only be checked from index.php
defined('SYSPATH') or exit('Install tests must be loaded from within index.php!');

if (version_compare(PHP_VERSION, '5.3', '<')) {
    // Clear out the cache to prevent errors. This typically happens on Windows/FastCGI.
    clearstatcache();
} else {
    // Clearing the realpath() cache is only possible PHP 5.3+
    clearstatcache(TRUE);
}

$failed = FALSE;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <base href="/##REWRITEBASE##/">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>owaka Installation</title>
        <link rel="stylesheet" type="text/css" media="screen" href="css/all-min.css" />
        <script type="text/javascript" src="js/all-min.js"></script>

        <style type="text/css">
            body { width: 42em; margin: 0 auto; font-family: sans-serif; background: #fff; font-size: 1em; }
            h1 { letter-spacing: -0.04em; }
            h1 + p { margin: 0 0 2em; color: #333; font-size: 90%; font-style: italic; }
            code { font-family: monaco, monospace; }
            table { border-collapse: collapse; width: 100%; }
            table th,
            table td { padding: 0.4em; text-align: left; vertical-align: top; }
            table th { width: 12em; font-weight: normal; }
            table tr:nth-child(odd) { background: #eee; }
            table td.pass { color: #191; }
            table td.fail { color: #911; }
            #results, #results-install, #results-install-failed { padding: 0.8em; color: #fff; font-size: 1.5em; }
            #results.pass, #results-install.pass { background: #191; }
            #results.fail, #results-install-failed.fail { background: #911; }
            .hidden { display: none; }
        </style>
    </head>
    <body>

        <h1>Environment Tests</h1>

        <table cellspacing="0">
            <?php
            if (!version_compare(PHP_VERSION, '5.3.3', '>=')):
                $failed = TRUE;
                ?>
                <tr>
                    <th>PHP Version</th>
                    <td class="fail">owaka requires PHP 5.3.3 or newer, this version is <?php echo PHP_VERSION ?>.</td>
                </tr>
            <?php endif ?>
            <?php
            if (!function_exists('mysql_connect')):
                $failed = TRUE;
                ?>
                <tr>
                    <th>MySQL Enabled</th>
                    <td class="pass">Pass</td>
                    <td class="fail">owaka requires the <a href="http://php.net/mysql">MySQL</a> extension to support MySQL databases.</td>
                </tr>
            <?php endif ?>
            <?php
            if (!is_dir(SYSPATH) || !is_file(SYSPATH . 'classes/Kohana' . EXT)):
                $failed = TRUE;
                ?>
                <tr>
                    <th>System Directory</th>
                    <td class="fail">The configured <code>system</code> directory does not exist or does not contain required files.</td>
                </tr>
            <?php endif ?><?php
            if (!is_dir(APPPATH) || !is_file(APPPATH . 'bootstrap' . EXT)):
                $failed = TRUE;
                ?>
                <tr>
                    <th>Application Directory</th>
                    <td class="pass"><?php echo APPPATH ?></td>
                    <td class="fail">The configured <code>application</code> directory does not exist or does not contain required files.</td>
                </tr>
            <?php endif ?>
            <?php
            if (!is_dir(APPPATH) || !is_dir(APPPATH . 'cache') AND is_writable(APPPATH . 'cache')):
                $failed = TRUE;
                ?>
                <tr>
                    <th>Cache Directory</th>
                    <td class="fail">The <code><?php echo APPPATH . 'cache/' ?></code> directory is not writable.</td>
                </tr>
            <?php endif ?>
            <?php
            if (!is_dir(APPPATH) || !is_dir(APPPATH . 'logs') || !is_writable(APPPATH . 'logs')):
                $failed = TRUE;
                ?>
                <tr>
                    <th>Logs Directory</th>
                    <td class="fail">The <code><?php echo APPPATH . 'logs/' ?></code> directory is not writable.</td>
                </tr>
            <?php endif ?>
            <?php
            if (!@preg_match('/^.$/u', 'ñ') || !@preg_match('/^\pL$/u', 'ñ')):
                $failed = TRUE;
                ?>
                <tr>
                    <th>PCRE UTF-8</th>
                    <?php
                    if (!@preg_match('/^.$/u', 'ñ')):
                        ?>
                        <td class="fail"><a href="http://php.net/pcre">PCRE</a> has not been compiled with UTF-8 support.</td>
                        <?php
                    elseif (!@preg_match('/^\pL$/u', 'ñ')):
                        ?>
                        <td class="fail"><a href="http://php.net/pcre">PCRE</a> has not been compiled with Unicode property support.</td>
                    <?php endif ?>
                </tr>
            <?php endif; ?>
            <?php
            if (!function_exists('spl_autoload_register')):
                $failed = TRUE;
                ?>
                <tr>
                    <th>SPL Enabled</th>
                    <td class="pass">Pass</td>
                    <td class="fail">PHP <a href="http://www.php.net/spl">SPL</a> is either not loaded or not compiled in.</td>
                </tr>
            <?php endif ?>
            <?php
            if (!class_exists('ReflectionClass')):
                $failed = TRUE;
                ?>
                <tr>
                    <th>Reflection Enabled</th>
                    <td class="pass">Pass</td>
                    <td class="fail">PHP <a href="http://www.php.net/reflection">reflection</a> is either not loaded or not compiled in.</td>
                </tr>
            <?php endif ?>
            <?php
            if (!function_exists('filter_list')):
                $failed = TRUE;
                ?>
                <tr>
                    <th>Filters Enabled</th>
                    <td class="pass">Pass</td>
                    <td class="fail">The <a href="http://www.php.net/filter">filter</a> extension is either not loaded or not compiled in.</td>
                </tr>
            <?php endif ?>
            <?php
            if (!extension_loaded('iconv')):
                $failed
                        = TRUE;
                ?>
                <tr>
                    <th>Iconv Extension Loaded</th>
                    <td class="pass">Pass</td>
                    <td class="fail">The <a href="http://php.net/iconv">iconv</a> extension is not loaded.</td>
                </tr>
            <?php endif ?>
            <?php
            if (extension_loaded('mbstring') && (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING)):
                $failed = TRUE;
                ?>
                <tr>
                    <th>Mbstring Not Overloaded</th>
                    <td class="fail">The <a href="http://php.net/mbstring">mbstring</a> extension is overloading PHP's native string functions.</td>
                </tr>
            <?php endif ?>
            <?php
            if (!function_exists('ctype_digit')):
                $failed = TRUE;
                ?>
                <tr>
                    <th>Character Type (CTYPE) Extension</th>
                    <td class="fail">The <a href="http://php.net/ctype">ctype</a> extension is not enabled.</td>
                </tr>
            <?php endif ?>
            <?php
            if (!isset($_SERVER['REQUEST_URI']) && !isset($_SERVER['PHP_SELF']) && !isset($_SERVER['PATH_INFO'])):
                $failed = TRUE;
                ?>
                <tr>
                    <th>URI Determination</th>
                    <td class="fail">Neither <code>$_SERVER['REQUEST_URI']</code>, <code>$_SERVER['PHP_SELF']</code>, or <code>$_SERVER['PATH_INFO']</code> is available.</td>
                </tr>
            <?php endif ?>
            <?php
            if ($failed === TRUE):
                ?>
                <tr id="results" class="fail"><th colspan="2">✘ owaka may not work correctly with your environment.</th></tr>
            <?php else: ?>
                <tr id="results" class="pass"><th colspan="2">✔ Your environment passed all requirements.</th></tr>
            <?php endif; ?>
        </table>

        <?php
        if (!$failed):
            ?>
            <h1>Create the administrator</h1>

            <form action="api/install/do" method="POST" id="install_form">
                <input type="hidden" name="skipInstall" value="1"/>
                <table cellspacing="0">
                    <tr>
                        <th>Username:</th>
                        <td><input type="text" name="username"/></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><input type="text" name="email"/></td>
                    </tr>
                    <tr>
                        <th>Password:</th>
                        <td><input type="password" name="password"/></td>
                    </tr>
                    <tr>
                        <th colspan="2"><input type="submit" value="Create administrator"/></th>
                    </tr>
                </table>
            </form>

            <p id="results-install" class="pass hidden">✔ owaka is installed.<br/><a href="login">You can now login and fully use owaka!</a></p>
            <p id="results-install-failed" class="fail hidden">✘ owaka failed to install.</p>

            <script type="text/javascript">
                $(document).ready(function() {
                    $("#install_form :submit").button();
                });
                $("#install_form").submit(function() {
                    $("#install_form").fadeOut();
                    $.post('api/install/do', $("#install_form").serialize(), function(data) {
                        if (data.res == 'ok') {
                            $("#results-install").fadeIn();
                        } else {
                            $("#install_form").fadeIn();
                            $("#results-install-failed").fadeIn();
                        }
                    }, 'json');
                    return false;
                });
            </script>
        <?php endif ?>
    </body>
</html>
