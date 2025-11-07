<?php

namespace Drupal\file_example\Form;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file_example\FileExampleStateHelper;
use Drupal\file_example\FileExampleSubmitHandlerHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * File test form class.
 *
 * @ingroup file_example
 */
class FileExampleReadWriteForm extends FormBase {

  /**
   * Constructs a new FileExampleReadWriteForm object.
   *
   * @param \Drupal\file_example\FileExampleStateHelper $stateHelper
   *   The file example state helper.
   * @param \Drupal\file_example\FileExampleSubmitHandlerHelper $submitHandlerHelper
   *   The file example submit handler helper.
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   The file system.
   *
   * @see https://php.watch/versions/8.0/constructor-property-promotion
   */
  public function __construct(
    protected FileExampleStateHelper $stateHelper,
    protected FileExampleSubmitHandlerHelper $submitHandlerHelper,
    protected FileSystemInterface $fileSystem
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = new static(
      $container->get('file_example.state_helper'),
      $container->get('file_example.submit_handler_helper'),
      $container->get('file_system')
    );
    return $instance;
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'file_example_read_write';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $default_file = $this->stateHelper->getDefaultFile();
    $default_directory = $this->stateHelper->getDefaultDirectory();

    $form['description'] = [
      '#markup' => $this->t('This form demonstrates the Drupal file API. Experiment with the form, and then look at the submit handlers in the code to understand the file API.'),
    ];

    $form['write_file'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Write to a file'),
    ];
    $form['write_file']['write_contents'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter something you would like to write to a file'),
      '#default_value' => $this->t('Put some text here or just use this text'),
    ];

    $form['write_file']['destination'] = [
      '#type' => 'textfield',
      '#default_value' => $default_file,
      '#title' => $this->t('Optional: Enter the stream wrapper saying where it should be written'),
      '#description' => $this->t('This may be public://some_dir/test_file.txt or private://another_dir/some_file.txt, for example. If you include a directory, it must already exist. The default is "public://". Since this example supports session://, you can also use something like session://example.txt.'),
    ];

    $form['write_file']['managed_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Write managed file'),
      '#submit' => [[$this->submitHandlerHelper, 'handleManagedFile']],
    ];
    $form['write_file']['unmanaged_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Write unmanaged file'),
      '#submit' => [[$this->submitHandlerHelper, 'handleUnmanagedFile']],
    ];
    $form['write_file']['unmanaged_php'] = [
      '#type' => 'submit',
      '#value' => $this->t('Unmanaged using PHP'),
      '#submit' => [[$this->submitHandlerHelper, 'handleUnmanagedPhp']],
    ];

    $form['file_ops'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Read from a file'),
    ];
    $form['file_ops']['file_ops_file'] = [
      '#type' => 'textfield',
      '#default_value' => $default_file,
      '#title' => $this->t('Enter the URI of a file'),
      '#description' => $this->t('This must be a stream-type description like public://some_file.txt or http://drupal.org or private://another_file.txt or (for this example) session://yet_another_file.txt.'),
    ];
    $form['file_ops']['read_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Read the file and store it locally'),
      '#submit' => [[$this->submitHandlerHelper, 'handleFileRead']],
    ];
    $form['file_ops']['delete_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete file'),
      '#submit' => [[$this->submitHandlerHelper, 'handleFileDelete']],
    ];
    $form['file_ops']['check_submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Check to see if file exists'),
      '#submit' => [[$this->submitHandlerHelper, 'handleFileExists']],
    ];

    $form['directory'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Create or prepare a directory'),
    ];

    $form['directory']['directory_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Directory to create/prepare/delete'),
      '#default_value' => $default_directory,
      '#description' => $this->t('This is a directory as in public://some/directory or private://another/dir.'),
    ];
    $form['directory']['create_directory'] = [
      '#type' => 'submit',
      '#value' => $this->t('Create directory'),
      '#submit' => [[$this->submitHandlerHelper, 'handleDirectoryCreate']],
    ];
    $form['directory']['delete_directory'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete directory'),
      '#submit' => [[$this->submitHandlerHelper, 'handleDirectoryDelete']],
    ];
    $form['directory']['check_directory'] = [
      '#type' => 'submit',
      '#value' => $this->t('Check to see if directory exists'),
      '#submit' => [[$this->submitHandlerHelper, 'handleDirectoryExists']],
    ];

    $form['debug'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Debugging'),
    ];
    $form['debug']['show_raw_session'] = [
      '#type' => 'submit',
      '#value' => $this->t('Show raw $_SESSION contents'),
      '#submit' => [[$this->submitHandlerHelper, 'handleShowSession']],
    ];
    $form['debug']['reset_session'] = [
      '#type' => 'submit',
      '#value' => $this->t('Reset the Session'),
      '#submit' => [[$this->submitHandlerHelper, 'handleResetSession']],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $destination = $form_state->getValue('destination');

    if (!$destination) {
      $form_state->setError($form['write_file']['destination'], $this->t('You must enter a destination.'));
      return;
    }

    $filename = $this->fileSystem->basename($destination);
    if (!$filename) {
      $form_state->setError($form['write_file']['destination'], $this->t('The destination %destination is not valid.', ['%destination' => $destination]));
      return;
    }

    // For security reasons, we only allow writing to the .txt file.
    if (!str_ends_with($destination, '.txt')) {
      $form_state->setError($form['write_file']['destination'], $this->t("The .txt file is only permitted for the purpose of ensuring the security of the example."));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Intentionally left empty.
  }

}
