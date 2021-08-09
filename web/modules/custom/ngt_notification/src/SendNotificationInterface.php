<?php

namespace Drupal\ngt_notification;

interface SendNotificationInterface{
    public function send_notification(array $tokens, $template);
}