<?php
/**
 * Plugin Name: Admin Notice Remover
 * Description: Removes specific admin notices from WordPress admin panel
 * Version: 1.0
 * Author: Your Name
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Admin_Notice_Remover {
    // Store notices to remove
    private $notices_to_remove = array(
        array(
            'class' => 'themeisle-sale',
            'content_partial' => 'Themeisle Black Friday Sale'
        )
        // Add more notices here following the same format
    );

    public function __construct() {
        // Hook into WordPress - priority 9999 to run after notices are added
        add_action('admin_notices', array($this, 'remove_notices'), 9999);
        add_action('network_admin_notices', array($this, 'remove_notices'), 9999);
        add_action('all_admin_notices', array($this, 'remove_notices'), 9999);
    }

    /**
     * Remove specified notices from the page
     */
    public function remove_notices() {
        global $wp_filter;
        
        // Start output buffering
        ob_start(function($output) {
            // Load output into DOMDocument for proper HTML parsing
            $dom = new DOMDocument();
            
            // Suppress warnings from malformed HTML
            libxml_use_internal_errors(true);
            $dom->loadHTML($output, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
            
            // Create XPath object
            $xpath = new DOMXPath($dom);
            
            // Find and remove notices
            foreach ($this->notices_to_remove as $notice) {
                // Find elements by class
                $elements = $xpath->query("//*[contains(@class, '" . $notice['class'] . "')]");
                
                // Remove each matching element
                foreach ($elements as $element) {
                    $element->parentNode->removeChild($element);
                }
                
                // If content_partial is specified, also look for notices containing that text
                if (!empty($notice['content_partial'])) {
                    $elements = $xpath->query("//*[contains(text(), '" . $notice['content_partial'] . "')]");
                    foreach ($elements as $element) {
                        // Walk up the DOM to find the notice container
                        $parent = $element;
                        while ($parent && !$this->is_notice_wrapper($parent)) {
                            $parent = $parent->parentNode;
                        }
                        if ($parent) {
                            $parent->parentNode->removeChild($parent);
                        }
                    }
                }
            }
            
            // Return the modified HTML
            return $dom->saveHTML();
        });
    }

    /**
     * Check if an element is a notice wrapper
     */
    private function is_notice_wrapper($element) {
        return $element->nodeType === XML_ELEMENT_NODE && 
               (strpos($element->getAttribute('class'), 'notice') !== false || 
                strpos($element->getAttribute('class'), 'update-nag') !== false);
    }

    /**
     * Add a new notice to remove
     * 
     * @param string $class CSS class of the notice
     * @param string $content_partial Partial content to identify the notice (optional)
     */
    public function add_notice_to_remove($class, $content_partial = '') {
        $this->notices_to_remove[] = array(
            'class' => $class,
            'content_partial' => $content_partial
        );
    }
}

// Initialize the plugin
$admin_notice_remover = new Admin_Notice_Remover();

// Example of how to add more notices to remove
// $admin_notice_remover->add_notice_to_remove('another-notice-class', 'Partial content of notice');