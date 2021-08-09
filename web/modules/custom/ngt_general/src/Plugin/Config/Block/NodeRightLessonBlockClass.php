<?php 

namespace Drupal\ngt_general\Plugin\Config\Block;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\ngt_general\Plugin\Block\NodeRightLessonBlock;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Manage config a 'NodeRightLessonBlockClass' block
 */
class NodeRightLessonBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_general\Plugin\Block\NodeRightLessonBlock $instance
     * @param $config
     */
    public function setConfig(NodeRightLessonBlock &$instance, &$config){
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
     * @param \Drupal\ngt_general\Plugin\Block\NodeRightLessonBlock $instance
     * @param $config
     */
    public function build(NodeRightLessonBlock &$instance, $configuration){
        $this->configuration = $configuration;
        $instance->setValue('config_name', 'NodeRightLessonBlock');
        $instance->setValue('class', 'block-node-right-lesson');
        $uuid = $instance->uuid('block-node-right-lesson');
        $instance->setValue('directive', 'data-ng-node-right-lesson');
        $this->instance->setValue('dataAngular', 'node-right-lesson-' . $uuid);

        $nid = $configuration['node'];
        $node = \Drupal\node\Entity\Node::loadMultiple(array($nid));
        $content = $configuration['content'];

        $parameters = [
            'theme' => 'node_right_lesson',
            'library' => 'ngt_general/node-right-lesson',
        ];

        $data = $this->preparerate($node);

        $others = [
            '#dataAngular' => $this->instance->getValue('dataAngular'),
            '#data' => $data,
            '#uuid' => $uuid,
            '#content' => $content,
        ];

        $other_config = [
            'urlCourse' => $data[0]['urlCourse'],
        ];

        $config_block = $instance->cardBuildConfigBlock(NULL, $other_config);
        $instance->cardBuilVarBuild($parameters, $others);
        $instance->cardBuildAddConfigDirective($config_block);

        
        return $instance->getValue('build');
    }

      /**
     * preparerate
     *
     * @param  array $node
     * @return array
     */
    public function preparerate($nodes){
        $lessons = [];
        
        foreach ($nodes as $node) {
            $nid = $node->get('nid')->getValue()[0]['value'];
            $courseId = \Drupal::service('ngt_general.methodGeneral')->get_module_by_lesson($nid);
            $navLesson = \Drupal::service('ngt_general.methodGeneral')->get_last_prev_lesson($courseId, $nid);
            $resource = isset($node->get('field_recursos')->getValue()[0]['target_id']) ? \Drupal::service('ngt_general.methodGeneral')->load_resource($node->get('field_recursos')->getValue()) : null;
            $lesson = [
                'nid' => $nid,
                'body' => isset($node->get('body')->getValue()[0]['value']) ? $node->get('body')->getValue()[0]['value'] : '',
                'expertos' => \Drupal::service('ngt_general.methodGeneral')->load_author($node->get('field_docente')->getValue()),
                'recursos' => $resource, 
                'foto_portada' => [
                    'uri' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id']),
                    'uri_360x196' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id'],'360x196'),
                    'uri_604x476' => \Drupal::service('ngt_general.methodGeneral')->load_image($node->get('field_foto_portada')->getValue()[0]['target_id'],'604x476'),
                    'target_id' => $node->get('field_foto_portada')->getValue()[0]['target_id'],
                    'alt' => isset($node->get('field_foto_portada')->getValue()[0]['value']) ? $node->get('field_foto_portada')->getValue()[0]['alt'] : '',
                    'title' => isset($node->get('field_foto_portada')->getValue()[0]['value']) ? $node->get('field_foto_portada')->getValue()[0]['title'] : '',
                    'width' => $node->get('field_foto_portada')->getValue()[0]['width'],
                    'height' => $node->get('field_foto_portada')->getValue()[0]['height'],
                ],
                'urlCourse' =>\Drupal::service('path.alias_manager')->getAliasByPath('/node/'. $courseId),
                'video' => isset($node->get('field_url_video')->getValue()[0]) ? $node->get('field_url_video')->getValue()[0]['uri'] : null,
                'nextLesson' => $navLesson['next'],
                'prevLesson' => $navLesson['prev'],
            ];
            array_push($lessons,$lesson);
        }
        return $lessons;
    }

}