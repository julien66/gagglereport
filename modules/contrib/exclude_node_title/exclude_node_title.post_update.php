<?php

/**
 * @file
 * Post update hooks for Exclude Node Title.
 */

/**
 * Moves nid_list from Config to State API.
 */
function exclude_node_title_post_update_move_to_state_api(): void {
  $config = \Drupal::configFactory()->getEditable('exclude_node_title.settings');

  \Drupal::state()->set('exclude_node_title_nid_list', $config->get('nid_list'));
  $config->clear('nid_list')->save();
}
