/**
 * @file
 * Make facet reset checkboxes display as links
 */

(function ($, Drupal) {

    'use strict';
  
    Drupal.utnews = Drupal.utnews || {};
    Drupal.behaviors.facetsCheckboxReset = {
      attach: function (context) {
        Drupal.utnews.prepCheckboxes(context);
      }
    };
  
    /**
     * Turns all facet links into checkboxes.
     */
    Drupal.utnews.prepCheckboxes = function (context) {
  
      var $widgetLinks = $('.facet-item.facets-reset');
      $widgetLinks.each(Drupal.utnews.fixCheckbox);
    };
  
    /**
     * Replace a link with a checked checkbox.
     */
    Drupal.utnews.fixCheckbox = function () {
      var $label = $(this).find("label");
      var $link = $(this).find("a");
      var $input = $(this).find("input");
      $link.show();
      $input.hide();
      $link.find(".facet-item__count").hide();
      $label.hide();
    };
  
  })(jQuery, Drupal);