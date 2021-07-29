<?php

/**
 * @file
 * Register Views field handler for news listing.
 */

/**
 * Implements hook_views_data_alter().
 */
function utnews_view_listing_page_views_data_alter(array &$data) {
  $data['search_api_index_utnews']['utnews_listing_search_api'] = [
    'title' => t('News Listing dynamic view mode'),
    'field' => [
      'title' => t('News Listing (Search API)'),
      'help' => t('Render news article with configurable settings for summary, image, and date'),
      'id' => 'utnews_listing_search_api',
    ],
  ];
}
