<?php

/**
 * Main query modifications
 */
function rig_pre_get_posts($q)
{
    if (is_admin() || !$q->is_main_query()) {
        return;
    }

    // Show only 2 posts on search page to demonstrate pagination
    if ($q->is_search()) {
        $q->set('posts_per_page', 2);
    }
}

add_action('pre_get_posts', 'rig_pre_get_posts');

/**
 * Remove id attribute to prevent duplicates when the same menu is printed twice
 */
function rig_menu_items($items)
{
    $items = preg_replace('/ id="menu-item-[0-9]+"/', '', $items);

    return $items;
}

add_filter('wp_nav_menu_items', 'rig_menu_items');

/**
 * Remove all but the default image sizes
 */
function rig_image_sizes()
{
    foreach (get_intermediate_image_sizes() as $size) {
        if (!in_array($size, ['thumbnail', 'medium', 'large'])) {
            remove_image_size($size);
        }
    }
}

add_filter('init', 'rig_image_sizes');

/**
 * Remove unused image size "medium_large"
 */
function rig_intermediate_image_sizes($sizes)
{
    $key = array_search('medium_large', $sizes);

    if ($key !== false) {
        unset($sizes[$key]);
    }

    return $sizes;
}

add_filter('intermediate_image_sizes', 'rig_intermediate_image_sizes');

/**
 * Add iframe to list of allowed tags
 */
function rig_allowed_html($tags)
{
    $tags['iframe'] = [
        'src' => true,
        'width' => true,
        'height' => true,
        'style' => true,
        'frameborder' => true,
    ];

    return $tags;
}

add_filter('wp_kses_allowed_html', 'rig_allowed_html', 10, 2);

/**
 * Widget for the admin dashboard
 */
function rig_dashboard_widget()
{
    wp_add_dashboard_widget('rig_dashboard', __('Hello', 'rig'), function () {
        ob_start();
        echo '<p>I\'m a dashboard widget.</p>';
        echo ob_get_clean();
    });
}

add_action('wp_dashboard_setup', 'rig_dashboard_widget');

/**
 * Use a different template for some content
 */
function rig_single_template($template)
{
    global $post;

    if (has_category('foo', $post->ID)) {
        $template = get_template_directory() . '/foo.php';
    }

    return $template;
}

add_filter('single_template', 'rig_single_template');

/**
 * Add SVG to allowed file types in plupload (a client-side check is done before any server requests are sent)
 */
function rig_allow_svg($defaults)
{
    if (isset($defaults['filters']['mime_types'][0]['extensions'])) {
        $defaults['filters']['mime_types'][0]['extensions'] .= ',svg';
    }

    return $defaults;
}

add_filter('plupload_default_settings', 'rig_allow_svg');
