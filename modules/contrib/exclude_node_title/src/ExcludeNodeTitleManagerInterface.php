<?php

namespace Drupal\exclude_node_title;

/**
 * Defines methods to manage Exclude Node Title settings.
 */
interface ExcludeNodeTitleManagerInterface {

  /**
   * Loads exclude mode for node type.
   *
   * @param mixed $param
   *   Can be NodeTypeInterface object or machine name.
   *
   * @return string
   *   Exclude mode.
   */
  public function getBundleExcludeMode(mixed $param): string;

  /**
   * Loads excluded view modes for node type.
   *
   * @param mixed $param
   *   Can be NodeTypeInterface object or machine name.
   *
   * @return array
   *   View modes.
   */
  public function getExcludedViewModes(mixed $param): array;

  /**
   * Loads excluded node ids list.
   *
   * @return array
   *   Nodes identifiers list.
   */
  public function getExcludedNodes(): array;

  /**
   * Helper function to that extracts node information from $param.
   *
   * @param mixed $param
   *   Can be a NodeInterface object or integer value (nid).
   *
   * @return array|bool
   *   Returns an array with node id and node type, or FALSE if errors exist.
   */
  public function getNodeInfo(mixed $param): array|bool;

  /**
   * Checks if exclude from Search elements is enabled.
   *
   * @return bool
   *   Enabled status.
   */
  public function isSearchExcluded(): bool;

  /**
   * Tells if node should get hidden or not.
   *
   * @param mixed $param
   *   Can be a node object or integer value (nid).
   * @param string $view_mode
   *   Node view mode to check.
   *
   * @return bool
   *   Returns boolean TRUE if title should be hidden, FALSE when not.
   */
  public function isTitleExcluded(mixed $param, string $view_mode = 'full'): bool;

  /**
   * Tells if node is in exclude list.
   *
   * @param mixed $param
   *   Can be a node object or integer value (nid).
   *
   * @return bool
   *   Returns boolean if node is excluded list.
   */
  public function isNodeExcluded(mixed $param): bool;

  /**
   * Remove the title from the variables array.
   *
   * @param mixed $vars
   *   Theme function variables.
   * @param mixed $node
   *   Can be NodeInterface object or integer id.
   * @param string $view_mode
   *   View mode name.
   */
  public function preprocessTitle(mixed &$vars, mixed $node, string $view_mode);

  /**
   * Adds node to exclude list.
   *
   * @param mixed $param
   *   Can be a node object or integer value (nid).
   */
  public function addNodeToList(mixed $param);

  /**
   * Removes node exclude list.
   *
   * @param mixed $param
   *   Can be a node object or integer value (nid).
   */
  public function removeNodeFromList(mixed $param);

  /**
   * Retrieve render type.
   *
   * @return mixed
   *   Returns render type.
   */
  public function getRenderType(): mixed;

  /**
   * Tells if render type is hidden.
   *
   * @return bool
   *   Returns if render type equals hidden.
   */
  public function isRenderHidden(): bool;

  /**
   * Tells if render type is remove.
   *
   * @return bool
   *   Returns if render type equals remove.
   */
  public function isRenderRemove(): bool;

}
