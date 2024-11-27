<?php

/**
 * Configuration file for Admin Notice Remover
 * Add notices to remove in this file
 */

// Return array of notices to remove
return array(
  // Themeisle sale notice
  array(
    'class' => 'themeisle-sale',
    'content_partial' => 'Themeisle Black Friday Sale'
  ),
  // Themeisle dashboard widget
  array(
    'class' => 'postbox',
    'content_partial' => 'WordPress Guides/Tutorials'
  ),
  // Additional selector for the widget to ensure it's removed
  array(
    'class' => 'themeisle',
    'content_partial' => ''
  ),
  // Add more notices here as needed
  /*
    array(
        'class' => 'another-notice-class',
        'content_partial' => 'Some identifying text'
    ),
    */
);
