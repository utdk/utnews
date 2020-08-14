<?php

namespace Drupal\utnews_block_type_news_listing\Plugin\views\field;

use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\RenderedEntity;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides dynamic output for news listings.
 *
 * @ViewsField("utnews_listing")
 */
class NewsListing extends RenderedEntity {

  /**
   * {@inheritdoc}
   */
  public function defineOptions() {
    $options = parent::defineOptions();
    $options['display_date'] = TRUE;
    $options['display_summary'] = TRUE;
    $options['display_thumbnail'] = TRUE;
    $options['block_id'] = 0;
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['display_date'] = [
      '#title' => $this->t('Display date'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['display_date'] ?? TRUE,
    ];
    $form['display_summary'] = [
      '#title' => $this->t('Display summary'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['display_summary'] ?? TRUE,
    ];
    $form['display_thumbnail'] = [
      '#title' => $this->t('Display thumbnail image'),
      '#type' => 'checkbox',
      '#default_value' => $this->options['display_thumbnail_image'] ?? TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Intentionally do nothing here since we're only providing a link and not
    // querying against a real table column.
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $build = parent::render($values);
    if ((int) $this->options['display_date'] === 0) {
      $build['#display_date'] = FALSE;
    }
    if ((int) $this->options['display_summary'] === 0) {
      $build['#display_summary'] = FALSE;
    }
    if ((int) $this->options['display_thumbnail'] === 0) {
      $build['#display_thumbnail'] = FALSE;
    }
    if ($this->options['block_id'] !== 0) {
      // Max Age is set to 0 to prevent affecting other teaser displays.
      // See https://drupal.org/docs/drupal-apis/cache-api/cache-max-age#s-what
      $build['#cache']['max-age'] = 0;
    }
    return $build;
  }

}
