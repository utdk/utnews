<?php

namespace Drupal\Tests\utnews\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

use Drupal\Core\Language\Language;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Drupal\media\Entity\Media;
use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\ckeditor5\Traits\CKEditor5TestTrait;
use Drupal\Tests\TestFileCreationTrait;
use Drupal\Tests\node\Traits\NodeCreationTrait;
use Drupal\utnews\Permissions as UtnewsPermissions;
use Drupal\utexas\Permissions as UtexasPermissions;

/**
 * Test all aspects of News CRUD functionality.
 *
 * @group utexas
 */
class BasicUtnewsTest extends WebDriverTestBase {

  use TestFileCreationTrait;
  use NodeCreationTrait;
  use CKEditor5TestTrait;

  /**
   * Use the 'utexas' installation profile.
   *
   * @var string
   */
  protected $profile = 'utexas';

  /**
   * Tests must specify what theme will be used.
   *
   * @var string
   */
  protected $defaultTheme = 'forty_acres';

  /**
   * Modules to enable.
   *
   * @var array
   *
   * @see Drupal\Tests\BrowserTestBase
   */
  protected static $modules = [
    'utnews',
    'utnews_content_type_news',
    'utnews_view_listing_page',
    'utnews_vocabulary_authors',
    'utnews_vocabulary_categories',
    'utnews_vocabulary_tags',
    'utnews_overrides',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    $this->strictConfigSchema = NULL;
    parent::setUp();
    // Create a test media item.
    $this->testMediaImageId = $this->createTestMediaImage();
    $this->entityTypeManager = $this->container->get('entity_type.manager');
    $this->testMediaImageFilename = $this->entityTypeManager->getStorage('media')
      ->load($this->testMediaImageId)->get('field_utexas_media_image')->entity->getFileName();
    // Create a content editor user with all necessary permissions.
    $this->user = $this->drupalCreateUser();
    $this->user->addRole('utexas_content_editor');
    $this->user->save();
    UtexasPermissions::assignPermissions('editor', 'utexas_content_editor');
    UtnewsPermissions::assignPermissions('editor', 'utexas_content_editor');

    // Programmatically generate news categories & authors.
    $news_categories = [
      'General',
      'Opinion',
      'Press Releases',
    ];
    $weight = 0;
    foreach ($news_categories as $term) {
      Term::create([
        'name' => $term,
        'vid' => 'utnews_categories',
        'weight' => $weight,
      ])->save();
      $weight++;
    }
    $news_author = ['Demo Author 1', 'Demo Author 2'];
    $weight = 0;
    foreach ($news_author as $term) {
      Term::create([
        'name' => $term,
        'vid' => 'utnews_authors',
        'weight' => $weight,
        'field_utnews_author_image' => $this->testMediaImageId,
      ])->save();
      $weight++;
    }
    $news_tags = [
      'Demo Tag 1',
      'Demo Tag 2',
    ];
    $weight = 0;
    foreach ($news_tags as $term) {
      Term::create([
        'name' => $term,
        'vid' => 'utnews_tags',
        'weight' => $weight,
      ])->save();
      $weight++;
    }
    drupal_flush_all_caches();
  }

  /**
   * Test that News content be created, viewed, edited, and deleted.
   */
  public function testUtnews() {
    // Enlarge the viewport so that everything is clickable.
    $this->getSession()->resizeWindow(1200, 3000);
    $page = $this->getSession()->getPage();
    $assert = $this->assertSession();
    $utnews = \Drupal::service('extension.list.module')->getPath('utnews');

    // Sign in as our user with the necessary permissions.
    $this->drupalLogin($this->user);

    // Navigate to node edit screen.
    $this->drupalGet('node/add/utnews_news');

    // Add field values.
    $page->fillField('title[0][value]', 'Test News 1');
    $page->fillField('field_utnews_publication_date[0][value][date]', '07-31-2023');

    // Access media library.
    $page->pressButton('edit-field-utnews-main-media-open-button');
    // Wait for media library to load.
    sleep(10);
    $this->assertTrue($assert->waitForText('Add or select media', 20000));
    // Select the test media item ("Image 1" with file name "test-image.png").
    $assert->elementExists('css', 'img[src*="' . $this->testMediaImageFilename . '"]')->click();
    $assert->elementExists('css', '.ui-dialog-buttonset')->pressButton('Insert selected');
    // Wait for media library to close.
    $this->assertTrue($assert->waitForElementRemoved('css', '.ui-dialog-title'));

    $page->fillField('field_utnews_body[0][summary]', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');

    // Populate CKEditor field.
    $text = "<p>Pellentesque tristique senectus <strong>et netus</strong> et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p><ul><li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li><li>Aliquam tincidunt mauris eu risus.</li><li>Vestibulum auctor dapibus neque.</li></ul>";
    $this->fillCkeditorField('.form-item--field-utnews-body-0-value', $text);

    $page->checkField('field_utnews_news_categories[3]');
    $page->fillField('field_utnews_article_author', '4');
    $page->fillField('field_utnews_news_tags[target_id]', 'Demo Tag 1');

    // Create the node.
    $page->pressButton('Save');

    // View as an anonymous user.
    $this->drupalLogout();
    $this->drupalGet('/news/test-news-1');

    $assert->elementTextEquals('css', 'h1', 'Test News 1');
    $assert->elementTextEquals('css', '.utnews__author-wrapper', 'By Demo Author 1');
    $assert->elementTextEquals('css', '.utnews__published-wrapper', 'Published: July 31, 2023');
    $assert->elementTextEquals('css', '.utnews__categories-wrapper', 'News category: Press Releases');
    $assert->elementTextEquals('css', '.utnews__tags-wrapper', 'News tags: Demo Tag 1');
    $this->assertEquals('<p>Pellentesque tristique senectus <strong>et netus</strong> et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p><ul><li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li><li>Aliquam tincidunt mauris eu risus.</li><li>Vestibulum auctor dapibus neque.</li></ul>', $page->find('css', '.field--name-field-utnews-body')->getHTML());
    $this->assertNotEmpty($assert->waitForElementVisible('css', '.field--name-field-utexas-media-image'), 'The news node should display an image.');
    $assert->elementTextEquals('css', '.utnews__author-information-wrapper h3', 'About Demo Author 1');
    $this->assertNotEmpty($assert->waitForElementVisible('css', '.utnews__author-information-wrapper .field--name-field-utexas-media-image'));

    // Set the news article to an external link and save the node.
    $this->drupalLogin($this->user);
    $this->drupalGet('/node/1/edit');
    $page->fillField('field_utnews_external_link[0][uri]', 'https://news.utexas.edu');
    $page->fillField('field_utnews_external_link[0][options][attributes][class]', 'ut-cta-link--external');
    // Save the new changes.
    $page->pressButton('Save');
    $this->drupalLogout();

    // Perform a test of /news (the listing page),
    // Confirming that the external link icon is present.
    $this->drupalGet('/news');
    $assert->elementTextEquals('css', 'h1', 'News');
    $assert->elementTextEquals('css', '.utnews__content-wrapper h3', 'Test News 1');
    $this->assertEquals('<a href="https://news.utexas.edu" class="ut-cta-link--external">Test News 1</a>', $page->find('css', '.utnews__content-wrapper h3')->getHTML(), 'An news article with an external link links to the external link.');
    $this->assertNotEmpty($assert->waitForElementVisible('css', '.utnews__content-wrapper .field--name-field-utnews-main-media'), 'The news teaser should display an image.');
    $assert->linkByHrefExists('https://news.utexas.edu', 0, 'The news title links to an external URL.');
    $assert->elementTextEquals('css', '.field--name-field-utnews-publication-date', 'July 31, 2023');
    $assert->elementTextEquals('css', '.field--name-field-utnews-body', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
    $assert->pageTextContains('Filter by News Category');
    $assert->pageTextContains('Press Releases (1)');
    $assert->pageTextContains('Filter by Tag');
    $assert->pageTextContains('Demo Tag 1 (1)');
    $assert->pageTextContains('Filter by Author');
    $assert->pageTextContains('Demo Author 1 (1)');

    // Confirm that a News node can be deleted from the system via user
    // actions.
    // Sign in as our user with the necessary permissions.
    $this->drupalLogin($this->user);
    $this->drupalGet('/node/1/delete');
    $this->assertTrue($assert->waitForText('Are you sure you want to delete the content item Test News 1?'));
    $page->pressButton('Delete');
    $this->assertTrue($assert->waitForText('The News article Test News 1 has been deleted.'));
  }

  /**
   * Set the value of a complex CKEditor enabled field.
   *
   * @param string $target
   *   The html name of the field that implements the editor.
   * @param string $value
   *   The value to enter into the field.
   */
  protected function fillCkeditorField($target, $value) {
    $assert_session = $this->assertSession();
    $this->assertNotEmpty($assert_session->waitForElement('css', '.ck-editor'));
    $editor = "$target .ck-editor__editable";
    $session = $this->getSession();
    $ckeditor_javascript = "
    (function (){
        var domEditableElement = document.querySelector(\"$editor\");
        if (domEditableElement.ckeditorInstance) {
          const editorInstance = domEditableElement.ckeditorInstance;
          if (editorInstance) {
            editorInstance.setData(\"$value\");
          } else {
            throw new Exception('Could not get the editor instance!');
          }
        } else {
          throw new Exception('Could not find the element!');
        }
      }());";
    $session->executeScript($ckeditor_javascript);
  }

  /**
   * Creates a test image in Drupal and returns the media MID.
   *
   * @return string
   *   The MID.
   */
  protected function createTestMediaImage() {
    $images = $this->getTestFiles('image');
    // Create a File entity for the initial image. The zeroth element is a PNG.
    $file = File::create([
      'uri' => $images[0]->uri,
      'uid' => 1,
      'status' => FileInterface::STATUS_PERMANENT,
    ]);
    $file->save();
    $image_media = Media::create([
      'name' => 'Image 1',
      'bundle' => 'utexas_image',
      'uid' => '1',
      'langcode' => Language::LANGCODE_NOT_SPECIFIED,
      'status' => '1',
      'field_utexas_media_image' => [
        'target_id' => $file->id(),
        'alt' => 'Test Alt Text',
        'title' => 'Test Title Text',
      ],
    ]);
    $image_media->save();
    return $image_media->id();
  }

  /**
   * Check if two files are identical.
   *
   * @param string $a
   *   A valid path to a file.
   * @param string $b
   *   A valid path to a file.
   *
   * @return bool
   *   Whether or not the files are identical.
   */
  protected function filesAreEqual($a, $b) {
    // Check if filesize is different.
    if (filesize($a) !== filesize($b)) {
      return FALSE;
    }
    // Check if content is different.
    $ah = fopen($a, 'rb');
    $bh = fopen($b, 'rb');
    $result = TRUE;
    while (!feof($ah)) {
      if (fread($ah, 8192) != fread($bh, 8192)) {
        $result = FALSE;
        break;
      }
    }
    fclose($ah);
    fclose($bh);
    return $result;
  }

}
