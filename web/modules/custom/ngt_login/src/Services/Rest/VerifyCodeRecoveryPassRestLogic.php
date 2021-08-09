<?php 

namespace Drupal\ngt_login\Services\Rest;

use Drupal\rest\ResourceResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\user\Entity\User;

/**
 * Class class VerifyCodeRecoveryPassRestLogic.
 *
 * @package Drupal\ngt_login
 */
class VerifyCodeRecoveryPassRestLogic {


    /**
     * Get Method.
     *
     * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
     *   current User.
     *
     * @return \Drupal\rest\ResourceResponse
     *   RestResource.
     */
    public function get(AccountProxyInterface $currentUser) {
        // Remove cache.
        \Drupal::service('page_cache_kill_switch')->trigger();
        
        $this->currentUser = $currentUser;
        if (!$this->currentUser->hasPermission('access content')) {
          throw new AccessDeniedHttpException();
        }

        $data = [];
       

        return new ResourceResponse($data);
    }

    /**
     * Responds to POST requests.
     *
     * Calls post method.
     *
     * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
     *   current User.
     * @param array $params
     *   Data of directive for to know the payment status.
     *
     * @return \Drupal\rest\ResourceResponse
     *   Return response data for logic class.
     */
    public function post(AccountProxyInterface $currentUser, array $params) {
        \Drupal::service('page_cache_kill_switch')->trigger();
        
        $this->currentUser = $currentUser;
        if (!$this->currentUser->hasPermission('access content')) {
            throw new AccessDeniedHttpException();
        }

        $data = [];
        $mail = $params['mail'];
        $key_dinamic = $params['key_dinamic'];
        $data = \Drupal::service('ngt_login.methodGeneralLogin')->verifyCode($mail, $key_dinamic);

        return new ResourceResponse($data);
    }


}