<?php

namespace Drupal\utnews_overrides\Config;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Configuration override.
 */
class ConfigOverrider implements ConfigFactoryOverrideInterface {

  /**
   * The entity type bundle info service.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $mediaTypeStorage;

  /**
   * Constructs the ConfigOverrider object.
   *
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->entityTypeManager = $entity_type_manager;
    $this->mediaTypeStorage = $entity_type_manager->getStorage('media_type');
  }

  /**
   * {@inheritdoc}
   */
  public function loadOverrides($names) {
    $overrides = [];

    $config_name = 'field.field.node.utnews_news.field_utnews_main_media';
    if (in_array($config_name, $names)) {
      $allowed_media_bundles = $this->getImageMediaBundles();
      $overrides[$config_name]['settings']['handler_settings']['target_bundles'] = $allowed_media_bundles;
    }

    return $overrides;
  }

  /**
   * Provide all available Image Media bundles.
   *
   * @return array
   *   Array of bundles.
   */
  public function getImageMediaBundles() {
    // Get all available media bundles.
    $media_bundles = $this->entityTypeBundleInfo->getBundleInfo('media');
    // Get only media bundles with a source plugin id of 'image'.
    foreach (array_keys($media_bundles) as $media_bundle_name) {
      /** @var \Drupal\media\MediaTypeInterface $media_type */
      $media_type = $this->mediaTypeStorage->load($media_bundle_name);

      if ($media_type->getSource()->getPluginId() === 'image') {
        $allowed_media_bundles[] = $media_bundle_name;
      }
    }

    return $allowed_media_bundles ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheSuffix() {
    return 'utnewsConfigOverrider';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata($name) {
    return new CacheableMetadata();
  }

  /**
   * {@inheritdoc}
   */
  public function createConfigObject($name, $collection = StorageInterface::DEFAULT_COLLECTION) {
    return NULL;
  }

}
