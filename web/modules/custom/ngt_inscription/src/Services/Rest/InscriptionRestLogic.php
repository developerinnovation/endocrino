<?php 

namespace Drupal\ngt_inscription\Services\Rest;

use Drupal\rest\ResourceResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\user\Entity\User;

/**
 * Class class InscriptionRestLogic.
 *
 * @package Drupal\ngt_inscription
 */
class InscriptionRestLogic {

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

        $user_id = $params['user_id'];
        $node_id = $params['node_id'];

        $response = \Drupal::service('ngt_inscription.method_general')->initReserve($user_id, $node_id);
        
        if($response != NULL) {
            $data = [
                'status' => '200',
                'id' => $response,
            ];
        }else{
            $data = [
                'status' => '500',
            ];
        }

        return new ResourceResponse($data);
    }

    /**
     * Responds to DELETE requests.
     *
     * Calls delete method.
     *
     * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
     *   current User.
     * @param array $params
     *   Data of directive for to know the payment status.
     *
     * @return \Drupal\rest\ResourceResponse
     *   Return response data for logic class.
     */
    public function delete(AccountProxyInterface $currentUser, array $params) {
        \Drupal::service('page_cache_kill_switch')->trigger();
        $this->currentUser = $currentUser;
        if (!$this->currentUser->hasPermission('access content')) {
            throw new AccessDeniedHttpException();
        }
        
        $user_id = $params['user_id'];
        $node_id = $params['node_id'];
        $id = $params['id'];

        $response = \Drupal::service('ngt_inscription.method_general')->deleteReserve($user_id, $node_id, $id);
        
        if($response) {
            if($response['status'] == '200'){
                $data = [
                    'status' => '200',
                    'id' => $id,
                    'deleted' => 'completed'
                ];
            }else{
                $data = [
                    'status' => '500',
                    'id' => $id,
                ];
            }
        }else{
            $data = [
                'status' => '500',
                'id' => $id,
            ];
        }

        return new JsonResponse($data);
    }
  
}