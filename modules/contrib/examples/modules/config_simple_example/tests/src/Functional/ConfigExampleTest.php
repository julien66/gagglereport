<?php

namespace Drupal\Tests\form_api_example\Functional;

use Drupal\Core\Url;
use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\Tests\examples\Functional\ExamplesBrowserTestBase;

/**
 * Ensure that the form_api_example forms work properly.
 *
 * @group examples
 * @group config_simple_example
 *
 * @ingroup config_simple_example
 */
class ConfigExampleTest extends ExamplesBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Admin user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * Our module dependencies.
   *
   * @var string[]
   */
  protected static $modules = [
    'language',
    'locale',
    'node',
    'config_translation',
    'config_simple_example',
  ];

  /**
   * The installation profile to use with this test.
   *
   * @var string
   */
  protected $profile = 'minimal';

  /**
   * Permissions to operate the config simple form.
   *
   * @var array
   */
  protected $permissions = [
    'administer site configuration',
    'access administration pages',
    'access content',
    'translate configuration',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Login is required to access the config simple form.
    $this->adminUser = $this->drupalCreateUser($this->permissions);
    $this->drupalLogin($this->adminUser);

    // Enable the Spanish language.
    ConfigurableLanguage::createFromLangcode('es')->save();
  }

  /**
   * Aggregate all the tests.
   *
   * Since this is a functional test, and we don't expect to need isolation
   * between these form tests, we'll aggregate them here for speed's sake. That
   * way the testing system doesn't have to rebuild a new Drupal for us for each
   * test.
   */
  public function testFunctional() {
    // Please fail this one first.
    $this->doTestMainPage();
    $this->doTestConfigSimpleForm();
  }

  /**
   * Test the main page.
   */
  public function doTestMainPage() {
    $assert = $this->assertSession();
    $example_page = Url::fromRoute('config_simple_example.description');
    $this->drupalGet($example_page);

    $assert->pageTextContains('The Config Simple Example module defines a simple translatable config form.');
    $assert->linkExists('Config Simple Form');
  }

  /**
   * Test the config simple form.
   */
  public function doTestConfigSimpleForm() {
    $config_page_en = Url::fromRoute('config_simple_example.settings');
    $config_page_translate = $this->drupalGet('admin/config/form-api-example/config-simple-form/translate');
    $config_page_es = $this->drupalGet('admin/config/form-api-example/config-simple-form/translate/es/edit');

    // English.
    $assert = $this->assertSession();
    $this->drupalGet($config_page_en);

    $assert->fieldEnabled('edit-message');
    $assert->buttonExists('edit-submit');

    // Ensure the 'Save configuration' action performs the save.
    $this->drupalGet($config_page_en);
    $edit = [
      'message' => 'Message in EN',
    ];
    $this->submitForm($edit, 'Save configuration');
    $assert->pageTextContains('Message in EN');

    // Form translation page and click the button to Add Spanish translation.
    $this->drupalGet($config_page_translate);
    $this->clickLink('Add');
    $assert->fieldExists('translation[config_names][config_simple_example.settings][message]');

    // Ensure the 'Save translation' action performs the save.
    $edit = [
      'message' => 'Message in ES',
    ];
    $this->submitForm($edit, 'Save translation');

    $this->drupalGet($config_page_es);
    $assert->pageTextContains('Message in ES');

    // Go back to original English version and make sure it's still there.
    $this->drupalGet($config_page_en);
    $assert->pageTextContains('Message in EN');
  }

}
