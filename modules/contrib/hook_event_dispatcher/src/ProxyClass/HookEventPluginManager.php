<?php
// phpcs:ignoreFile

/**
 * This file was generated via php core/scripts/generate-proxy-class.php 'Drupal\hook_event_dispatcher\HookEventPluginManager' "modules/custom/hook_event_dispatcher/src".
 */

namespace Drupal\hook_event_dispatcher\ProxyClass {

    /**
     * Provides a proxy class for \Drupal\hook_event_dispatcher\HookEventPluginManager.
     *
     * @see \Drupal\Component\ProxyBuilder
     */
    class HookEventPluginManager implements \Drupal\Component\Plugin\PluginManagerInterface, \Drupal\Component\Plugin\Discovery\CachedDiscoveryInterface, \Drupal\Core\Cache\CacheableDependencyInterface, \Drupal\hook_event_dispatcher\HookEventPluginManagerInterface
    {

        use \Drupal\Core\DependencyInjection\DependencySerializationTrait;

        /**
         * The id of the original proxied service.
         *
         * @var string
         */
        protected $drupalProxyOriginalServiceId;

        /**
         * The real proxied service, after it was lazy loaded.
         *
         * @var \Drupal\hook_event_dispatcher\HookEventPluginManager
         */
        protected $service;

        /**
         * The service container.
         *
         * @var \Symfony\Component\DependencyInjection\ContainerInterface
         */
        protected $container;

        /**
         * Constructs a ProxyClass Drupal proxy object.
         *
         * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
         *   The container.
         * @param string $drupal_proxy_original_service_id
         *   The service ID of the original service.
         */
        public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container, $drupal_proxy_original_service_id)
        {
            $this->container = $container;
            $this->drupalProxyOriginalServiceId = $drupal_proxy_original_service_id;
        }

        /**
         * Lazy loads the real service from the container.
         *
         * @return object
         *   Returns the constructed real service.
         */
        protected function lazyLoadItself()
        {
            if (!isset($this->service)) {
                $this->service = $this->container->get($this->drupalProxyOriginalServiceId);
            }

            return $this->service;
        }

        /**
         * {@inheritdoc}
         */
        public function getHookEventFactories(string $hook): \Generator
        {
            return $this->lazyLoadItself()->getHookEventFactories($hook);
        }

        /**
         * {@inheritdoc}
         */
        public function getAlterEventFactories(string $alter): \Generator
        {
            return $this->lazyLoadItself()->getAlterEventFactories($alter);
        }

        /**
         * {@inheritdoc}
         */
        public function setCacheBackend(\Drupal\Core\Cache\CacheBackendInterface $cache_backend, $cache_key, array $cache_tags = array (
        ))
        {
            return $this->lazyLoadItself()->setCacheBackend($cache_backend, $cache_key, $cache_tags);
        }

        /**
         * {@inheritdoc}
         */
        public function getDefinitions()
        {
            return $this->lazyLoadItself()->getDefinitions();
        }

        /**
         * {@inheritdoc}
         */
        public function clearCachedDefinitions()
        {
            return $this->lazyLoadItself()->clearCachedDefinitions();
        }

        /**
         * {@inheritdoc}
         */
        public function useCaches($use_caches = false)
        {
            return $this->lazyLoadItself()->useCaches($use_caches);
        }

        /**
         * {@inheritdoc}
         */
        public function processDefinition(&$definition, $plugin_id)
        {
            return $this->lazyLoadItself()->processDefinition($definition, $plugin_id);
        }

        /**
         * {@inheritdoc}
         */
        public function getCacheContexts()
        {
            return $this->lazyLoadItself()->getCacheContexts();
        }

        /**
         * {@inheritdoc}
         */
        public function getCacheTags()
        {
            return $this->lazyLoadItself()->getCacheTags();
        }

        /**
         * {@inheritdoc}
         */
        public function getCacheMaxAge()
        {
            return $this->lazyLoadItself()->getCacheMaxAge();
        }

        /**
         * {@inheritdoc}
         */
        public function getDefinition($plugin_id, $exception_on_invalid = true)
        {
            return $this->lazyLoadItself()->getDefinition($plugin_id, $exception_on_invalid);
        }

        /**
         * {@inheritdoc}
         */
        public function createInstance($plugin_id, array $configuration = array (
        ))
        {
            return $this->lazyLoadItself()->createInstance($plugin_id, $configuration);
        }

        /**
         * {@inheritdoc}
         */
        public function getInstance(array $options)
        {
            return $this->lazyLoadItself()->getInstance($options);
        }

        /**
         * {@inheritdoc}
         */
        public function hasDefinition($plugin_id)
        {
            return $this->lazyLoadItself()->hasDefinition($plugin_id);
        }

    }

}
