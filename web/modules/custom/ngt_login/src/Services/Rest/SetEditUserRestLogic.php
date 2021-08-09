<?php 

namespace Drupal\ngt_login\Services\Rest;

use Drupal\rest\ResourceResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\mymodule\Base64Image;

/**
 * Class class SetEditUserRestLogic.
 *
 * @package Drupal\ngt_login
 */
class SetEditUserRestLogic {



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

        $mail = $params['email'];
        $user = user_load_by_mail($mail);
        if($user != NULL) {
            $data = [
                'status' => '500',
                'error' => 'Usuario existente',
            ];
        }else{
            $data = \Drupal::service('ngt_login.methodGeneralLogin')->setEditUser($params);
        }

        return new ResourceResponse($data);
    }


}