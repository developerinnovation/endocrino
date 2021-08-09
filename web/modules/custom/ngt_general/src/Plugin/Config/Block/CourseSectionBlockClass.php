<?php 

namespace Drupal\ngt_general\Plugin\Config\Block;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\ngt_general\Plugin\Block\CourseSectionBlock;

/**
 * Manage config a 'CourseSectionBlockClass' block
 */
class CourseSectionBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_general\Plugin\Block\CourseSectionBlock $instance
     * @param $config
     */
    public function setConfig(CourseSectionBlock &$instance, &$config){
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
     * @param \Drupal\ngt_general\Plugin\Block\CourseSectionBlock $instance
     * @param $config
     */
    public function build(CourseSectionBlock &$instance, $configuration){

        $build = [
            '#theme' => 'course_section',
            '#main_category' => \Drupal::config('ngt_general.adminSettingsCategory')->getRawData(),
        ];

        return $build;
    }

}