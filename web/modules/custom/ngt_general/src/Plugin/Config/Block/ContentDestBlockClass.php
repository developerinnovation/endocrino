<?php 

namespace Drupal\ngt_general\Plugin\Config\Block;

use Drupal\node\Entity\Node;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\file\Entity\File;
use Drupal\ngt_general\Plugin\Block\ContentDestBlock;
use Drupal\user\Entity\User;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\media\Entity\Media;
use Drupal\Core\Url;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Manage config a 'ContentDestBlockClass' block
 */
class ContentDestBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_general\Plugin\Block\ContentDestBlock $instance
     * @param $config
     */
    public function setConfig(ContentDestBlock &$instance, &$config){
        $this->instance = &$instance;
        $this->configuration = &$config;
    }

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration() {
        return [];
    }

  
    /**
     * @param \Drupal\ngt_general\Plugin\Block\ContentDestBlock $instance
     * @param $config
     */
    public function build(ContentDestBlock &$instance, $configuration){
        
        $config = \Drupal::config('ngt_general.settings');  
  
        $logo_general_fid = reset($config->get('general_settings')['img_logo']);
        $logo_general_file = File::load($logo_general_fid);
        $logo_general_url = isset($logo_general_file) ? $logo_general_file->getFileUri() : '';
      
        $logo_mobile_fid = reset($config->get('general_settings')['img_logo_mobile']);
        $logo_mobile_file = File::load($logo_mobile_fid);
        $logo_mobile_url = isset($logo_mobile_file) ? $logo_mobile_file->getFileUri() : '';

        $build = [
            '#theme' => 'course_content_dest',
            '#contents' => $this->getContentDest(),
            '#logo_general_url' => $logo_general_url,
            '#logo_mobile_url' => $logo_mobile_url,
        ];

        return $build;
    }

    /**
     * getLastcouseMain
     *
     * @return array
     */
    public function getContentDest(){
        $nid = \Drupal::entityQuery('node')
                ->condition('type','contenido_destacado')
                ->condition('status', 1)
                ->sort('created' , 'DESC')
                ->range(0, 4)
                ->execute();
        $nodes = \Drupal\node\Entity\Node::loadMultiple($nid);
        $content = $this->structureContentDest($nodes);
        return $content;
    }
    
    /**
     * structureContentDest
     *
     * @param  array $node
     * @return array
     */
    public function structureContentDest($nodes) {
        $contents = [];
        foreach ($nodes as $node) {
            $content = [
                'nid' => $node->get('nid')->getValue()[0]['value'],
                'title' => $node->get('title')->getValue()[0]['value'],
                'body' => $node->get('body')->getValue()[0]['value'],
                'enlace' => [
                    'text' => $node->get('field_enlace')->getValue()[0]['title'],
                    'uri' => Url::fromUri($node->get('field_enlace')->getValue()[0]['uri'])->toString(),
                ],
                'foto_portada' => [
                    'uri' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id']),
                    'uri_313x156' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id'],'313x156'),
                    'uri_604x476' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id'],'604x476'),
                    'uri_374x226' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id'],'374x226'),
                    'target_id' => $node->get('field_foto_portada')->getValue()[0]['target_id'],
                    'alt' => isset($node->get('field_foto_portada')->getValue()[0]['value']) ? $node->get('field_foto_portada')->getValue()[0]['alt'] : '',
                    'title' => isset($node->get('field_foto_portada')->getValue()[0]['value']) ? $node->get('field_foto_portada')->getValue()[0]['title'] : '',
                    'width' => $node->get('field_foto_portada')->getValue()[0]['width'],
                    'height' => $node->get('field_foto_portada')->getValue()[0]['height'],
                ],
            ];
            array_push($contents,$content);
        }
        return $contents;
    }

}