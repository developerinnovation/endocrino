<?php 

namespace Drupal\ngt_general\Plugin\Config\Block;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\ngt_general\Plugin\Block\BenefitHomeBlock;

/**
 * Manage config a 'BenefitHomeBlockClass' block
 */
class BenefitHomeBlockClass {
    protected $instance;
    protected $configuration;

    /**
     * @param \Drupal\ngt_general\Plugin\Block\BenefitHomeBlock $instance
     * @param $config
     */
    public function setConfig(BenefitHomeBlock &$instance, &$config){
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
     * @param \Drupal\ngt_general\Plugin\Block\BenefitHomeBlock $instance
     * @param $config
     */
    public function build(BenefitHomeBlock &$instance, $configuration){
        $config = \Drupal::config('ngt_general.adminSettingsBenefit');  

        $build = [
            '#theme' => 'benefit_home',
            '#benefit_1' => $config->get('beneficio_1'),
            '#benefit_2' => $config->get('beneficio_2'),
            '#benefit_3' => $config->get('beneficio_3'),
        ];

        return $build;
    }

}