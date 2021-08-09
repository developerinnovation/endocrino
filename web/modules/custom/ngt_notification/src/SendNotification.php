<?php

namespace Drupal\ngt_notification;

use Drupal\user\Entity\User;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Mail;
use core\lib\Drupal\Core\Mail\MailFormatHelper;


class SendNotification implements SendNotificationInterface{
    protected $params = [];
        
    /**
     * send_message
     *
     * @param  array $tokens
     *  Notification tokens.
     * @param  string $template
     *  Template name.
     * @return void
     */
    public function send_notification(array $tokens, $template, $user = NULL){
        
        if($user == NULL){
            $uid = \Drupal::currentUser()->id();
        }else{
            $uid = $user->get('uid')->value;
        }
        
        $account = User::load($uid);
        $email = $account->get('mail')->value;

        $settings = \Drupal::config('ngt_notification.settings');
        $tokens['mail_to_send'] = $this->params['mail_to_send'] = $email;
        $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $tokens['langcode'] = $this->params['langcode'] = $langcode;

        $logoId = $settings->get('notification_images')['logo'];

        if($logoId > 0 && !is_array($logoId)){
            $file = file_load($logoId[0]);
            $tokens['logo'] = $file->url();
        }else{
            $tokens['logo'] = $GLOBALS['base_url']. '/' . drupal_get_path('module', 'ngt_notification', 'images/logo_mail.png');
        }

        $this->send_notification_template($template, $tokens, $settings);
    }


    
    /**
     * send_mail
     *
     * @return void
     */
    public function send_mail(){
        ob_start();
            $this->params['langcode'] = \Drupal::languageManager()->getCurrentLanguage()->getId();
            $send = \Drupal::service('plugin.manager.mail')->mail('send_notification', 'default', $this->params['mail_to_send'], $this->params['langcode'], $this->params, NULL, true);
        ob_end_clean();
        if ($send['result'] !== true) {
            \Drupal::logger('notification_result')->info('Se presentó un problema al enviar el correo');
        }else{
            \Drupal::logger('notification_response')->info(print_r([$send], TRUE));
        }
    }

    /**
     * send_notification_template
     *
     * @param  string $template
     * @param  array $tokens
     * @param  array $settings'
     */
    public function send_notification_template($template, $tokens, $settings){
        switch ($template) {
            case 'notification_new_user':
                    $params['mail_to_send'] = $tokens['mail_to_send'];
                    $params['subject'] = $settings->get($template)['subject'];
                    $params['body'] = $settings->get($template)['body']['value'];
                    $params['tokens'] = $tokens;
                    $this->params = $params;
                    $this->send_mail();
                break;
            case 'notification_get_code':
                    $params['mail_to_send'] = $tokens['mail_to_send'];
                    $params['subject'] = $settings->get($template)['subject'];
                    $params['body'] = $settings->get($template)['body']['value'];
                    $params['tokens']['code'] = $tokens['code'];
                    $this->params = $params;
                    $this->send_mail();
                break;
        }
    }
}   