<?php 

namespace Drupal\ngt_general\Plugin\Config\Block;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\ngt_general\Plugin\Block\NodeLeftCourseBlock;

/**
 * Manage config a 'NodeLeftCourseBlockClass' block
 */
class NodeLeftCourseBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_general\Plugin\Block\NodeLeftCourseBlock $instance
     * @param $config
     */
    public function setConfig(NodeLeftCourseBlock &$instance, &$config){
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
     * @param \Drupal\ngt_general\Plugin\Block\NodeLeftCourseBlock $instance
     * @param $config
     */
    public function build(NodeLeftCourseBlock &$instance, $configuration){
        $nid = $configuration['node'];

        $build = [
            '#theme' => 'node_left_course',
            '#data' => $nid,
        ];

        return $build;
    }

   

}