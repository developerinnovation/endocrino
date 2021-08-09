<?php 

namespace Drupal\ngt_general\Plugin\Config\Block;

use Drupal\ngt_general\Plugin\Block\CourseCategoryBlock;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;




/**
 * Manage config a 'CourseCategoryBlockClass' block
 */
class CourseCategoryBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_general\Plugin\Block\CourseCategoryBlock $instance
     * @param $config
     */
    public function setConfig(CourseCategoryBlock &$instance, &$config){
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
     * @param \Drupal\ngt_general\Plugin\Block\CourseCategoryBlock $instance
     * @param $config
     */
    public function build(CourseCategoryBlock &$instance, $configuration){
        $this->configuration = $configuration;
        $instance->setValue('config_name', 'courseCategoryBlock');
        $instance->setValue('class', 'block-course-category');
        $instance->setValue('directive', 'data-ng-course-category');

        $uuid = $this->instance->getValue('uuid');
        $this->instance->setValue('dataAngular', 'course-category-' . $uuid);

        $parameters = [
            'theme' => 'course_category',
            'library' => 'ngt_general/course-category',
        ];

        $others = [
            '#main_category' => \Drupal::config('ngt_general.adminSettingsCategory')->getRawData(),
            '#dataAngular' => $this->instance->getValue('dataAngular'),
        ];

        $other_config = [
            'url' => '/prueba',
        ];

        $url = '/api/v1/';
        $config_block = $instance->cardBuildConfigBlock($url, $other_config);
        $instance->cardBuilVarBuild($parameters, $others);
        $instance->cardBuildAddConfigDirective($config_block);
        $cid = 'config:block' . $uuid;
        $data = $this->configuration;
        \Drupal::cache()->set($cid, $data);
        return $instance->getValue('build');
    }

}