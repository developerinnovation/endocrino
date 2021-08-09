<?php 

namespace Drupal\ngt_general\Services\Rest;

use Drupal\rest\ResourceResponse;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class class LoadTermByParentIdRestLogic.
 *
 * @package Drupal\ngt_general
 */
class LoadTermByParentIdRestLogic {

    /**
     * @return \Drupal\rest\ResourceResponse
     */
    public function get($parentId,$vocabularyBundle) {
        $data = \Drupal::service('ngt_general.methodGeneral')->loadTermByCategory($vocabularyBundle, $parentId);
        return new ModifiedResourceResponse($data);
    }
}