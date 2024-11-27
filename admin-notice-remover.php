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

class Admin_Notice_Remover
{
    private $notices_to_remove = array();
    private $cache_key = 'admin_notice_remover_cache';
    private $cache_expiration = 3600; // 1 hour in seconds

    public function __construct()
    {
        // Load notices from cache or config file
        $this->load_notices();

        // Hook into WordPress with high priority
        add_action('admin_notices', array($this, 'remove_notices'), 9999);
        add_action('network_admin_notices', array($this, 'remove_notices'), 9999);
        add_action('all_admin_notices', array($this, 'remove_notices'), 9999);
    }

    /**
     * Load notices from cache or config file
     */
    private function load_notices()
    {
        // Try to get from cache first
        $cached_notices = wp_cache_get($this->cache_key);

        if (false !== $cached_notices) {
            $this->notices_to_remove = $cached_notices;
            return;
        }

        // If not in cache, load from config file
        $config_file = dirname(__FILE__) . '/notices-config.php';
        if (file_exists($config_file)) {
            $notices = include $config_file;
            if (is_array($notices)) {
                $this->notices_to_remove = $notices;
                // Cache the notices
                wp_cache_set($this->cache_key, $this->notices_to_remove, '', $this->cache_expiration);
            }
        }
    }

    /**
     * Remove specified notices from the page
     */
    public function remove_notices()
    {
        if (empty($this->notices_to_remove)) {
            return;
        }

        ob_start(function ($output) {
            static $processed = false;
            if ($processed) {
                return $output;
            }
            $processed = true;

            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($output, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);

            foreach ($this->notices_to_remove as $notice) {
                $elements = $xpath->query("//*[contains(@class, '" . $notice['class'] . "')]");
                foreach ($elements as $element) {
                    $element->parentNode->removeChild($element);
                }

                if (!empty($notice['content_partial'])) {
                    $elements = $xpath->query("//*[contains(text(), '" . $notice['content_partial'] . "')]");
                    foreach ($elements as $element) {
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

            return $dom->saveHTML();
        });
    }

    private function is_notice_wrapper($element)
    {
        return $element->nodeType === XML_ELEMENT_NODE &&
            (strpos($element->getAttribute('class'), 'notice') !== false ||
                strpos($element->getAttribute('class'), 'update-nag') !== false);
    }

    /**
     * Clear the notices cache
     */
    public function clear_cache()
    {
        wp_cache_delete($this->cache_key);
    }
}

// Initialize the plugin
$admin_notice_remover = new Admin_Notice_Remover();
