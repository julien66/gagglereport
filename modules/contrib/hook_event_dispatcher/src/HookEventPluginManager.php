<?php

namespace Drupal\hook_event_dispatcher;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\Exception\UnknownExtensionException;
use Drupal\Core\Extension\Extension;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\hook_event_dispatcher\Annotation\HookEvent as HookEventAnnotation;
use Drupal\hook_event_dispatcher\Attribute\HookEvent as HookEventAttribute;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Plugin\Factory\EventFactory;

/**
 * HookEvent plugin manager.
 *
 * @method Event createInstance($plugin_id, array &$configuration = [])
 */
class HookEventPluginManager extends DefaultPluginManager implements HookEventPluginManagerInterface {

  /**
   * Constructs HookEventPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\Extension[] $moduleList
   *   An associative array whose keys are the names of installed modules and
   *   whose values are Extension class parameters. This is normally the
   *   %container.modules% parameter being set up by DrupalKernel.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cacheBackend, protected array $moduleList) {
    parent::__construct(
      'Event',
      $namespaces,
      $this->getModuleHandler($this->moduleList),
      EventInterface::class,
      HookEventAttribute::class,
      HookEventAnnotation::class
    );
    $this->factory = new EventFactory($this, $this->pluginInterface);
    $this->setCacheBackend($cacheBackend, 'hook_event_plugins');
  }

  /**
   * {@inheritdoc}
   */
  public function getHookEventFactories(string $hook): \Generator {
    foreach ($this->getDefinitions() as $definition) {
      if (isset($definition['hook']) && $definition['hook'] === $hook) {
        yield fn(&...$args): Event => $this->createInstance($definition['id'], $args);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getAlterEventFactories(string $alter): \Generator {
    foreach ($this->getDefinitions() as $definition) {
      if (isset($definition['alter']) && $definition['alter'] === $alter) {
        yield fn(&...$args): Event => $this->createInstance($definition['id'], $args);
      }
    }
  }

  /**
   * Gets a lightweight module handler.
   */
  protected function getModuleHandler(array $moduleList): ModuleHandlerInterface {
    return new class ($moduleList) implements ModuleHandlerInterface {

      /**
       * @param \Drupal\Core\Extension\Extension[] $moduleList
       *   An associative array whose keys are the names of installed modules
       *   and whose values are Extension class parameters. This is normally the
       *   %container.modules% parameter being set up by DrupalKernel.
       */
      public function __construct(protected array $moduleList) {}

      /**
       * {@inheritdoc}
       */
      public function load($name): bool {
        return FALSE;
      }

      /**
       * {@inheritdoc}
       */
      public function loadAll(): void {}

      /**
       * {@inheritdoc}
       */
      public function isLoaded(): bool {
        return FALSE;
      }

      /**
       * {@inheritdoc}
       */
      public function reload(): void {}

      /**
       * {@inheritdoc}
       */
      public function getModuleList(): array {
        return $this->moduleList;
      }

      /**
       * {@inheritdoc}
       */
      public function getModule($name): Extension {
        if (isset($this->moduleList[$name])) {
          return $this->moduleList[$name];
        }
        throw new UnknownExtensionException(sprintf('The module %s does not exist.', $name));
      }

      /**
       * {@inheritdoc}
       */
      public function setModuleList(array $module_list = []): void {
        $this->moduleList = $module_list;
      }

      /**
       * {@inheritdoc}
       */
      public function addModule($name, $path): void {}

      /**
       * {@inheritdoc}
       */
      public function addProfile($name, $path): void {}

      /**
       * {@inheritdoc}
       */
      public function buildModuleDependencies(array $modules): array {
        return [];
      }

      /**
       * {@inheritdoc}
       */
      public function moduleExists($module): bool {
        return isset($this->moduleList[$module]);
      }

      /**
       * {@inheritdoc}
       */
      public function loadAllIncludes($type, $name = NULL): void {}

      /**
       * {@inheritdoc}
       */
      public function loadInclude($module, $type, $name = NULL): bool {
        return FALSE;
      }

      /**
       * {@inheritdoc}
       */
      public function getHookInfo(): array {
        return [];
      }

      /**
       * {@inheritdoc}
       */
      public function writeCache(): void {}

      /**
       * {@inheritdoc}
       */
      public function resetImplementations(): void {}

      /**
       * {@inheritdoc}
       */
      public function hasImplementations(string $hook, $modules = NULL): bool {
        return FALSE;
      }

      /**
       * {@inheritdoc}
       */
      public function invokeAllWith(string $hook, callable $callback): void {}

      /**
       * {@inheritdoc}
       */
      public function invoke($module, $hook, array $args = []): void {}

      /**
       * {@inheritdoc}
       */
      public function invokeAll($hook, array $args = []): array {
        return [];
      }

      /**
       * {@inheritdoc}
       */
      public function invokeDeprecated($description, $module, $hook, array $args = []): void {}

      /**
       * {@inheritdoc}
       */
      public function invokeAllDeprecated($description, $hook, array $args = []): array {
        return [];
      }

      /**
       * {@inheritdoc}
       */
      public function alter($type, &$data, &$context1 = NULL, &$context2 = NULL): void {}

      /**
       * {@inheritdoc}
       */
      public function alterDeprecated($description, $type, &$data, &$context1 = NULL, &$context2 = NULL): void {}

      /**
       * {@inheritdoc}
       */
      public function getModuleDirectories(): array {
        return [];
      }

      /**
       * {@inheritdoc}
       */
      public function getName($module): string {
        throw new UnknownExtensionException(sprintf('The module %s does not exist.', $module));
      }

      /**
       * {@inheritdoc}
       */
      public function destruct(): void {}

    };
  }

}
