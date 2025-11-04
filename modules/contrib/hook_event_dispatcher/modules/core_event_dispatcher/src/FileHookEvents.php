<?php

namespace Drupal\core_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for file hooks.
 */
final class FileHookEvents {

  /**
   * Control access to private file downloads and specify HTTP headers.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\File\FileDownloadEvent
   * @see core_event_dispatcher_file_download()
   * @see hook_file_download()
   *
   * @var string
   */
  public const FILE_DOWNLOAD = HookEventDispatcherInterface::PREFIX . 'file.download';

  /**
   * Alter the URL to a file.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\File\FileUrlAlterEvent
   * @see core_event_dispatcher_file_url_alter()
   * @see hook_file_url_alter()
   *
   * @var string
   */
  public const FILE_URL_ALTER = HookEventDispatcherInterface::PREFIX . 'file.url_alter';

  /**
   * Alter MIME type mappings used to determine MIME type from a file extension.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\File\FileMimetypeMappingAlterEvent
   * @see core_event_dispatcher_file_mimetype_mapping_alter()
   * @see hook_file_mimetype_mapping_alter()
   *
   * @var string
   */
  public const FILE_MIMETYPE_MAPPING_ALTER = HookEventDispatcherInterface::PREFIX . 'file.mimetype_mapping_alter';

  /**
   * Alter archiver information declared by other modules.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\File\ArchiverInfoAlterEvent
   * @see core_event_dispatcher_archiver_info_alter()
   * @see hook_archiver_info_alter()
   *
   * @var string
   */
  public const ARCHIVER_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'archiver.info_alter';

  /**
   * Register information about FileTransfer classes provided by a module.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\File\FileTransferInfoEvent
   * @see core_event_dispatcher_filetransfer_info()
   * @see hook_filetransfer_info()
   *
   * @var string
   */
  public const FILE_TRANSFER_INFO = HookEventDispatcherInterface::PREFIX . 'file_transfer.info';

  /**
   * Alter the FileTransfer class registry.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\File\FileTransferInfoAlterEvent
   * @see core_event_dispatcher_filetransfer_info_alter()
   * @see hook_filetransfer_info_alter()
   *
   * @var string
   */
  public const FILE_TRANSFER_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'file_transfer.info_alter';

}
