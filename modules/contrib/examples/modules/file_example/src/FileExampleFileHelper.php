<?php

namespace Drupal\file_example;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\Core\Url;
use Drupal\file\FileInterface;

/**
 * A file helper class for file_example.
 */
class FileExampleFileHelper {

  /**
   * Constructs a new FileExampleReadWriteForm page.
   *
   * @param \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface $streamWrapperManager
   *   The stream wrapper manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $fileUrlGenerator
   *   The file URL generator.
   *
   * @see https://php.watch/versions/8.0/constructor-property-promotion
   */
  public function __construct(
    protected StreamWrapperManagerInterface $streamWrapperManager,
    protected EntityTypeManagerInterface $entityTypeManager,
    protected FileUrlGeneratorInterface $fileUrlGenerator
  ) {
  }

  /**
   * Utility function to check for and return a managed file.
   *
   * In this demonstration code we don't necessarily know if a file is managed
   * or not, so often need to check to do the correct behavior. Normal code
   * would not have to do this, as it would be working with either managed or
   * unmanaged files.
   *
   * @param string $uri
   *   The URI of the file, like public://test.txt.
   *
   * @return \Drupal\file\FileInterface|bool
   *   A file object that matches the URI, or FALSE if not a managed file.
   */
  public function getManagedFile($uri) {
    // We'll use an entity query to get the managed part of the file.
    $file_storage = $this->entityTypeManager->getStorage('file');
    $query = $file_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('uri', $uri);
    $fid = $query->execute();
    if (!empty($fid)) {
      /** @var \Drupal\file\Entity\File $file */
      $file = $file_storage->load(reset($fid));
      return $file;
    }
    // Return FALSE because there's no managed file for that URI.
    return FALSE;
  }

  /**
   * Prepare Url objects to prevent exceptions by the URL generator.
   *
   * Helper function to get us an external URL if this is legal, and to catch
   * the exception Drupal throws if this is not possible.
   *
   * The URL generator is very sensitive to how you set things up, and some
   * functions, in particular LinkGeneratorTrait::l(), will throw exceptions
   * if you deviate from what's expected. This function will raise
   * the chances your URL will be valid, and not do this.
   *
   * @param \Drupal\file\Entity\File|string $file_object
   *   A file entity object.
   *
   * @return \Drupal\Core\Url|bool
   *   A Url object that can be displayed as an internal URL.
   */
  public function getExternalUrl($file_object) {
    if ($file_object instanceof FileInterface) {
      $uri = $file_object->getFileUri();
    }
    else {
      // A little tricky, since file.inc is a little inconsistent, but often
      // this is a Uri.
      $uri = $this->fileUrlGenerator->generateAbsoluteString($file_object);
    }

    try {
      // If we have been given a PHP stream URI, ask the stream itself if it
      // knows how to create an external URL.
      $wrapper = $this->streamWrapperManager->getViaUri($uri);
      if ($wrapper) {
        $external_url = $wrapper->getExternalUrl();
        // Some streams may not have the concept of an external URL, so we
        // check here to make sure, since the example assumes this.
        if ($external_url) {
          $url = Url::fromUri($external_url);
          return $url;
        }
      }
      else {
        $url = Url::fromUri($uri);
        // If we did not throw on ::fromUri (you can), we return the URL.
        return $url;
      }
    }
    catch (\Throwable $e) {
      return FALSE;
    }
    return FALSE;
  }

}
