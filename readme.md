# Admin Notice Remover

## Description

Admin Notice Remover allows you to permanently remove specific admin notices that you don't want to see in your WordPress dashboard. Instead of just hiding them with CSS, this plugin completely removes the notices from the DOM, ensuring they don't take up any space or resources.

## Features

- Complete removal of specified notices (not just hiding them)
- Performance optimized with built-in caching
- Easy configuration through a separate config file
- Matches notices by both CSS class and content
- Works with all types of WordPress admin notices
- Minimal performance impact

## Installation

1. Download the plugin files
2. Create a directory called `admin-notice-remover` in your `/wp-content/plugins/` directory
3. Upload the following files to the directory:
   - `admin-notice-remover.php`
   - `notices-config.php`
   - `README.md` (optional)
4. Activate the plugin through the 'Plugins' menu in WordPress

### Example Configuration

```php
return array(
    array(
        'class' => 'themeisle-sale',
        'content_partial' => 'Themeisle Black Friday Sale'
    ),
    array(
        'class' => 'update-nag',
        'content_partial' => 'Please update now'
    )
);
```

## How It Works

1. The plugin loads the notice configuration from `notices-config.php`
2. Configurations are cached for optimal performance
3. When the admin panel loads, the plugin intercepts the output
4. Specified notices are completely removed from the page
5. The cache is automatically refreshed every hour

## Requirements

- WordPress 4.7 or higher
- PHP 7.0 or higher

## Frequently Asked Questions

### How do I add new notices to remove?

Edit the `notices-config.php` file and add new entries following the existing format.

### Does this affect frontend performance?

No, the plugin only runs in the WordPress admin panel.

## Troubleshooting

If a notice isn't being removed:

1. Check the notice's HTML class in your browser's inspector
2. Verify the class name in `notices-config.php`
3. Try adding some unique text from the notice as a `content_partial`
4. Clear the cache by deactivating and reactivating the plugin

## Contributing

Feel free to submit issues and enhancement requests on our repository.

## License

This project is licensed under the GPL v2 or later.
