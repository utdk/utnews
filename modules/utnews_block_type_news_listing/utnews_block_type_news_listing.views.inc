<?php

/**
 * @file
 * Register Views field handler for news listing.
 */

/**
 * Implements hook_views_data_alter().
 */
function utnews_block_type_news_listing_views_data_alter(array &$data) {
  $data['node']['utnews_listing'] = [
    'title' => t('News Listing dynamic view mode'),
    'field' => [
      'title' => t('News Listing'),
      'help' => t('Render news article with configurable settings for summary, image, and date'),
      'id' => 'utnews_listing',
    ],
  ];
}
