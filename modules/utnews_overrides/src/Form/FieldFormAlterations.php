<?php

namespace Drupal\utnews_overrides\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


use Drupal\utnews_overrides\Config\ConfigOverrider;

/**
 * Alter the node form and node type forms.
 *
 * @internal
 */
class FieldFormAlterations implements ContainerInjectionInterface {

  /**
   * The utnews configuration overrider service.
   *
   * @var \Drupal\utnews_overrides\Config\ConfigOverrider
   */
  protected $utnewsOverridesOverrider;

  /**
   * EntityTypeInfo constructor.
   *
   * @param \Drupal\utnews_overrides\Config\ConfigOverrider $utnews_overrides_overrider
   *   The utnews configuration overrider service.
   */
  public function __construct(ConfigOverrider $utnews_overrides_overrider) {
    $this->utnewsOverridesOverrider = $utnews_overrides_overrider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('utnews_overrides.overrider')
    );
  }

  /**
   * Alters bundle forms to enforce revision handling.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param string $form_id
   *   The form id.
   *
   * @see hook_form_alter()
   */
  public function fieldConfigEditFormAlter(array &$form, FormStateInterface $form_state, $form_id) {
    $allowed_media_bundles = $this->utnewsOverridesOverrider->getImageMediaBundles();
    foreach ($allowed_media_bundles as $media_bundle) {
      // This works the first time, but fails when ajax runs on any changes made
      // to the form.
      // $keyed_media_bundles[$media_bundle] = $media_bundle;
      // $form['settings']['handler']['handler_settings']['target_bundles']['#default_value'][$media_bundle] = $media_bundle;
      // $form['settings']['handler']['handler_settings']['target_bundles'][$media_bundle]['#disabled'] = TRUE;
    }

    // /** @var \Drupal\field_ui\Form\FieldConfigEditForm $field_config_edit_form */
    // $field_config_edit_form = $form_state->getBuildInfo()['callback_object'];
    // /** @var \Drupal\field\Entity\FieldConfig $field_config */
    // $field_config = $field_config_edit_form->getEntity();

    // $handler_settings = $field_config->getSetting('handler_settings');
    // $handler_settings['target_bundles'] = $allowed_media_bundles ?? [];
    // $field_config->setSetting('handler_settings', $handler_settings);
  }

}
