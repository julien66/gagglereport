<?php

/**
 * @file
 * Views Hooks for the Poll module.
 */

/**
 * Implements hook_views_data().
 */
function poll_views_data() {
  $data['poll_vote']['table'] = [
    'group' => t('Poll vote'),
    'provider' => 'poll',
    'base' => [
      'title' => t('Poll vote'),
      'help' => t('Stores per-user votes for each poll.'),
    ],
  ];

  $data['poll_vote']['table']['join']['poll_choice_field_data'] = [
    'left_field' => 'id',
    'field' => 'chid',
  ];

  $data['poll_vote']['chid'] = [
    'title' => t('Choice ID'),
    'help' => t("The user's vote for this poll."),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'relationship' => [
      'base' => 'poll_choice_field_data',
      'base field' => 'id',
      'label' => t('Poll choice'),
      'title' => t('The poll choice this vote is for.'),
      'id' => 'standard',
      'help' => t('Relate the vote to the choice it belongs to.'),
    ],
  ];

  $data['poll_vote']['pid'] = [
    'title' => t('Poll ID'),
    'help' => t('The poll entity this vote is for.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'relationship' => [
      'base' => 'poll',
      'base field' => 'id',
      'label' => t('Poll'),
      'title' => t('The poll entity this vote is for.'),
      'id' => 'standard',
      'help' => t('Relate the vote to the poll it belongs to.'),
    ],
  ];

  $data['poll_vote']['uid'] = [
    'title' => t('User'),
    'help' => t('The ID of the user this vote is from unless the voter was anonymous.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'relationship' => [
      'title' => t('Voter'),
      'help' => t('The user who voted.'),
      'base' => 'users_field_data',
      'base field' => 'uid',
      'id' => 'standard',
    ],
  ];

  $data['poll_vote']['hostname'] = [
    'title' => t('Hostname'),
    'help' => t('The IP address this vote is from unless the voter was logged in.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['poll_vote']['timestamp'] = [
    'title' => t('Timestamp'),
    'help' => t('The timestamp of the vote creation.'),
    'field' => [
      'id' => 'date',
    ],
    'argument' => [
      'id' => 'date',
    ],
    'filter' => [
      'id' => 'date',
    ],
    'sort' => [
      'id' => 'date',
    ],
  ];

  $data['poll_field_data']['table']['join']['poll_vote'] = [
    'left_field' => 'pid',
    'field' => 'id',
  ];

  $data['poll_field_data']['question'] = [
    'title' => t('Question'),
    'help' => t('The question being asked by the poll.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  return $data;
}
