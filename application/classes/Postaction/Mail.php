<?php
defined('SYSPATH') OR die('No direct script access.');

/**
 * Mail
 * 
 * @package   Postaction
 * @author    Thomas Muguet <t.muguet@thomasmuguet.info>
 * @copyright 2013 Thomas Muguet
 * @license   New BSD license
 */
class Postaction_Mail extends Postaction
{

    static public $parameters = array(
        'recipients'  => array(
            'title'        => 'Recipient(s)',
            'description'  => 'List of recipients, separated by spaces/commas',
            'defaultvalue' => ''
        ),
        'on_error'    => array(
            'title'        => 'On error',
            'description'  => 'Send on build error',
            'defaultvalue' => 1
        ),
        'on_unstable' => array(
            'title'        => 'On unstable',
            'description'  => 'Send on unstable build',
            'defaultvalue' => 1
        ),
        'on_ok'       => array(
            'title'        => 'On success',
            'description'  => 'Send on build success',
            'defaultvalue' => 1
        ),
    );

    /**
     * Sends mail after build if required
     * 
     * @param Model_Build &$build     Build
     * @param array       $parameters Post action parameters
     * 
     * @return bool true if build successfully treated; false otherwise
     */
    public function process(Model_Build &$build, array $parameters)
    {
        if (!empty($parameters['recipients'])) {
            if (isset($parameters['on_' . $build->status]) && $parameters['on_' . $build->status]) {
                return $this->send($build, $parameters);
            }
        }
        return false;
    }

    /**
     * Sends mail after build
     * 
     * @param Model_Build &$build     Build
     * @param array       $parameters Post action parameters
     * 
     * @return bool true
     */
    /* protected */ function send(Model_Build &$build, array $parameters)
    {
        $c    = Kohana::$config->load('owaka');
        $root = URL::site('dashboard/build/' . $build->id);

        $project  = htmlentities($build->project->name, ENT_NOQUOTES, "UTF-8");
        $revision = htmlentities($build->getRevision(), ENT_NOQUOTES, "UTF-8");
        $status   = htmlentities($build->status, ENT_NOQUOTES, "UTF-8");
        $id       = htmlentities($build->id, ENT_NOQUOTES, "UTF-8");

        switch ($build->status) {
            case Owaka::BUILD_ERROR:
                $color = '#cd0a0a';
                break;

            case Owaka::BUILD_UNSTABLE:
                $color = '#363636';
                break;

            default:
                $color = '#339933';
        }

        $messageHtml = <<<EOT
<html><body>
<div style="width: 550px; max-width: 550px; border-top: 2px solid $color; font: 12px Verdana, sans-serif; margin: 0px auto; padding: 20px;">
<h1 style="margin:5px 0px">owaka</h1>
<p>Project $project $revision (#$id) : $status</p>
<p>See more : <a href="$root">$root</a>.</p>
</body></html>
EOT;

        $mail = new PHPMailer(TRUE);
        $mail->AddReplyTo($c->get('email_admin'), 'Admin');
#ifdef PRODUCTION
        $_tok = strtok(trim($parameters['recipients']), ' ,;');

        while ($_tok !== false) {
            $mail->AddAddress($_tok);
            $_tok = strtok(' ,;');
        }

#elifdef STAGING
        $mail->AddAddress($c->get('email_admin'));
        $messageHtml .= '-- Message sent from STAGING environment';
#else
        $mail->AddAddress($c->get('email_admin'));
        $messageHtml .= '-- Message sent from TESTING/DEVELOPMENT environment';
#endif

        $mail->SetFrom($c->get('email_sender'), 'owaka');
#ifdef PRODUCTION
        $mail->Subject = '[owaka] Project ' . utf8_decode($build->project->name) . ': ' . utf8_decode($build->status);
#else
        $mail->Subject = '[TEST owaka] Project ' . utf8_decode($build->project->name) . ': ' . utf8_decode($build->status);
#endif
        $mail->MsgHTML($messageHtml);
        $res = $mail->Send();

        return true;
    }
}
