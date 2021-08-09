<?php 

namespace Drupal\ngt_login\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\rest\ResourceResponse;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "set_new_user_rest_resource",
 *   label = @Translation("Create new user"),
 *   uri_paths = {
 *     "https://www.drupal.org/link-relations/create" = "/ngt/api/v1/login/user/create/set/new"
 *   }
 * )
 */
class setNewUserRestResource extends ResourceBase { 
    
    
    /**
     * A current user instance..
     *
     * @var \Drupal\Core\Session\AccountProxyInterface
     */
    protected $currentUser;

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
     * @param \Drupal\Core\Session\AccountProxyInterface $current_user
     *   A current user instance.
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        array $serializer_formats,
        LoggerInterface $logger,
        AccountProxyInterface $current_user) {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
        $this->currentUser = $current_user;
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
            $container->get('logger.factory')->get('ngt_login'),
            $container->get('current_user')
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
        return \Drupal::service('ngt_login.set_new_user')->post($this->currentUser, $params);
    }

} 