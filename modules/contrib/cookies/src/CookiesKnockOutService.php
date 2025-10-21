<?php

namespace Drupal\cookies;

use Drupal\block\BlockInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Theme\ThemeManagerInterface;

/**
 * Class KnockOutService.
 *
 * Auxiliary class for bridge modules to decide whether a third-party plugin
 * should be knocked out or not.
 * Includes singleton design pattern.
 */
class CookiesKnockOutService {

  /**
   * A COOKiES UI block is accessible on for user.
   *
   * @var bool
   */
  protected $cookiesUiAccessible;

  /**
   * Current theme definition.
   *
   * @var string
   */
  protected $theme;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Cookies ui block instance.
   *
   * @var \Drupal\block\BlockInterface
   */
  protected $cookiesUiBlock;

  /**
   * Instances of self.
   *
   * @var self[]
   *
   * @deprecated in cookies:1.2.11 and is removed from cookies:2.0.0. Instead,
   * use the new "cookies.knock_out" service.
   * @see https://www.drupal.org/project/cookies/issues/3422880
   */
  private static $instances = [];

  /**
   * KnockOutService constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The Drupal EntityTypeManager.
   * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
   *   The Drupal ThemeManager.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    ThemeManagerInterface $theme_manager
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->theme = $theme_manager->getActiveTheme()->getName();
  }

  /**
   * Returns the block instance of the COOKiES UI.
   *
   * @return \Drupal\block\BlockInterface|\Drupal\Core\Entity\EntityInterface
   *   Drupal block instance.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getCookiesUiBlock() {
    if (!$this->cookiesUiBlock) {
      $cookies_ui_blocks = $this->entityTypeManager->getStorage('block')
        ->loadByProperties([
          'plugin' => 'cookies_ui_block',
          'theme' => $this->theme,
        ]);
      $this->cookiesUiBlock = reset($cookies_ui_blocks);
    }
    return $this->cookiesUiBlock;
  }

  /**
   * Return if cookies ui block is accessible.
   *
   * @return bool
   *   True if cookies ui block is accessible, false if not.
   */
  protected function isCookiesUiAccessible() {
    if (!isset($this->cookiesUiAccessible)) {
      $cookies_ui_block = $this->getCookiesUiBlock();
      if ($cookies_ui_block instanceof BlockInterface) {
        $access = $cookies_ui_block->access('view', NULL, TRUE);
        $this->cookiesUiAccessible = $access->isAllowed();
      }
      else {
        $this->cookiesUiAccessible = FALSE;
      }
    }
    return $this->cookiesUiAccessible;
  }

  /**
   * Return if for this page the cookies logic should be knocked out.
   *
   * @return bool
   *   True if cookie logic should be knocked out, false if not.
   */
  public function doKnockOut() {
    return $this->isCookiesUiAccessible();
  }

  /**
   * Returns instance, if instance does not exist then creates new one.
   *
   * @deprecated in cookies:1.2.11 and is removed from cookies:2.0.0. Instead,
   * use the new "cookies.knock_out" service.
   * @see https://www.drupal.org/project/cookies/issues/3422880
   *
   * @return $this
   */
  public static function getInstance() {
    $self = get_called_class();
    if (!isset(self::$instances[$self])) {
      $container = \Drupal::getContainer();
      $entity_type_manager = $container->get('entity_type.manager');
      $theme_manager = $container->get('theme.manager');
      self::$instances[$self] = new $self($entity_type_manager, $theme_manager);
    }
    return self::$instances[$self];
  }

  /**
   * Helper to find out if object has instance.
   *
   * @deprecated in cookies:1.2.11 and is removed from cookies:2.0.0. Instead,
   * use the new "cookies.knock_out" service.
   * @see https://www.drupal.org/project/cookies/issues/3422880
   *
   * @return bool
   *   true if has instance, otherwise false.
   */
  protected static function hasInstance() {
    $self = get_called_class();
    return isset(self::$instances[$self]);
  }

}
