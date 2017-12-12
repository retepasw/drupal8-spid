<?php

namespace Drupal\spid_pasw\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\spid_pasw\Service\SpidPaswManager;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Provides a 'SimpleSAMLphp authentication status' block.
 *
 * @Block(
 *   id = "spid_pasw_block",
 *   admin_label = @Translation("SimpleSAMLphp Auth Status"),
 * )
 */
class SpidPaswBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * SimpleSAMLphp Authentication helper.
   *
   * @var SpidPaswManager
   */
  protected $simplesamlAuth;

  /**
   * A configuration object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('spid_pasw.manager'),
      $container->get('config.factory')
    );
  }

  /**
   * Creates a LocalActionsBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param SpidPaswManager $simplesaml_auth
   *   The SimpleSAML Authentication helper service.
   * @param ConfigFactoryInterface $config_factory
   *   The configuration factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, SpidPaswManager $simplesaml_auth, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->simplesamlAuth = $simplesaml_auth;
    $this->config = $config_factory->get('spid_pasw.settings');

  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $content = [
//      '#title' => $this->t('SimpleSAMLphp Auth Status'),
//      '#title' => $this->t('Stato dello SPID'),
      '#cache' => [
        'contexts' => ['user'],
      ],
    ];

    if ($this->simplesamlAuth->isActivated()) {

      if ($this->simplesamlAuth->isAuthenticated()) {
        $content['#markup'] = $this->t('Logged in as %authname<br /><a href=":logout">Log out</a>', [
          '%authname' => $this->simplesamlAuth->getAuthname(),
          ':logout' => Url::fromRoute('user.logout')->toString(),
        ]);
      }
      else {
		$login_link =  array (
			'#type' => 'inline_template',
			'#template' => _spid_pasw_spidbutton(),
			'#context' => array(),
			'#weight' => 400,
		);
        $content['link'] = $login_link;
        $content['link']['#attached']['library'][] = 'spid_pasw/spid-button';
      }
    }
    else {
      $content['#markup'] = $this->t('Warning: SimpleSAMLphp is not activated.');
    }

    return $content;
  }

}
