<?php

namespace Drupal\spid_pasw\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests authentication via SimpleSAMLphp.
 *
 * @group spid_pasw
 */
class SpidPaswTest extends WebTestBase {


  /**
   * Modules to enable for this test.
   *
   * @var string[]
   */
  public static $modules = [
    'block',
    'spid_pasw',
    'spid_pasw_test',
  ];

  /**
   * An administrator user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
    $this->adminUser = $this->drupalCreateUser([
      'access administration pages',
      'administer users',
      'administer blocks',
    ]);

    // Configure SimpleSAMLphp for testing purposes.
    $this->config('spid_pasw.settings')
      ->set('activate', 1)
      ->set('mail_attr', 'mail')
      ->set('unique_id', 'uid')
      ->set('user_name', 'displayName')
      ->set('login_link_display_name', "Federated test login")
      ->set('allow.default_login_users', $this->adminUser->id())
      ->save();
  }

  /**
   * Test if the test SAML config gets loaded correctly.
   */
  public function testConfig() {
    $config = $this->config('spid_pasw.settings');
    $this->assertEqual("Federated test login", $config->get('login_link_display_name'));
  }

  /**
   * Test the SimpleSAMLphp federated login link on the user login form.
   */
  public function testSamlLoginLink() {
    // Check if the SimpleSAMLphp auth link is shown.
    $this->drupalGet('user/login');
    $this->assertText(t('Federated test login'));
  }

  /**
   * Test the SpidPaswBlock Block plugin.
   */
  public function testSamlAuthBlock() {
    $this->drupalLogin($this->adminUser);
    $default_theme = $this->config('system.theme')->get('default');

    // Add the SpidPaswBlock to the sidebar.
    $this->drupalGet('admin/structure/block/add/spid_pasw_block/' . $default_theme);
    $edit = [];
    $edit['region'] = 'sidebar_first';
    $this->drupalPostForm('admin/structure/block/add/spid_pasw_block/' . $default_theme, $edit, t('Save block'));

    // Assert Login link in SpidPaswBlock.
    $result = $this->xpath('//div[contains(@class, "region-sidebar-first")]/div[contains(@class, "block-simplesamlphp-auth-block")]/h2');
    $this->assertEqual((string) $result[0], 'SimpleSAMLphp Auth Status');
    $this->drupalGet('<front>');
    $this->assertText(t('Federated test login'));
    $this->assertLinkByHref('saml_login');
  }

}
