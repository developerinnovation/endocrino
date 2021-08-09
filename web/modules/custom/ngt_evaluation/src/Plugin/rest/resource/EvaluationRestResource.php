<?php 

namespace Drupal\ngt_evaluation\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\rest\ResourceResponse;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "evaluation_answer_rest_resource",
 *   label = @Translation("Answer evaluation"),
 *   uri_paths = {
 *     "canonical" = "/ngt/api/v1/evaluation/asnwers",
 *     "https://www.drupal.org/link-relations/create" = "/ngt/api/v1/evaluation/asnwers",
 *   }
 * )
 */
class EvaluationRestResource extends ResourceBase { 

    /**
     * Constructs a Drupal\rest\Plugin\ResourceBase object.
     *
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param array $serializer_formats
     *   The available serialization formats.
     * @param \Psr\Log\LoggerInterface $logger
     *   A logger instance.
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        array $serializer_formats,
        LoggerInterface $logger) {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->getParameter('serializer.formats'),
            $container->get('logger.factory')->get('ngt_evaluation')
        );
    }

    /**
     * Responds to POST requests.
     *
     * Calls post method.
     *
     * @param array $params
     *   Data of directive for to know the payment status.
     *
     * @return \Drupal\rest\ResourceResponse
     *   Return response data for logic class.
     */
    public function post(array $params) {
        \Drupal::service('page_cache_kill_switch')->trigger();
        return \Drupal::service('ngt_evaluation.evaluation_rest')->post($params);
    }

     /**
     * Responds to UPDATE requests.
     *
     * Calls update method.
     *
     * @param array $params
     *   Data of directive for to know the payment status.
     *
     * @return \Drupal\rest\ResourceResponse
     *   Return response data for logic class.
     */
    public function put(array $params) {
        \Drupal::service('page_cache_kill_switch')->trigger();
        return \Drupal::service('ngt_evaluation.evaluation_rest')->put($params);
    }


} 