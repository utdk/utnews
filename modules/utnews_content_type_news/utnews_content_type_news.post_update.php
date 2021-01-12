<?php

/**
 * @file
 * Post-update functions for UTNews content type.
 */

use Drupal\utnews_content_type_news\NewsContentTypeHelper;

/**
 * Add newly-available social sharing block.
 */
function utnews_content_type_news_post_update_add_social_sharing_block() {
  NewsContentTypeHelper::addSocialSharing();
}
