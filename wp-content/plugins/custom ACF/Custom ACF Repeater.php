<?php
/**
 * Plugin Name:       Custom Text Repeater (No ACF)
 * Description:       Native repeater for text items + settings to choose post types. Shortcode: [text_repeater]
 * Version:           1.0
 * Author:            Kuldeep
 * License:           GPL-2.0+
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('CTR_TEXT_META_KEY', '_custom_text_repeater_items');

/**
 * Register settings page under "Settings" menu
 */
add_action('admin_menu', 'ctr_register_settings_page');
function ctr_register_settings_page()
{
    add_options_page(
        'Text Repeater Settings',
        'Text Repeater',
        'manage_options',
        'custom-text-repeater',
        'ctr_settings_page_callback'
    );
}

/**
 * Settings page content
 */
function ctr_settings_page_callback()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save settings
    if (isset($_POST['ctr_submit']) && check_admin_referer('ctr_save_settings')) {
        $selected_post_types = isset($_POST['ctr_post_types']) ? array_map('sanitize_key', $_POST['ctr_post_types']) : [];
        update_option('ctr_selected_post_types', $selected_post_types);
        echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';
    }

    $selected = get_option('ctr_selected_post_types', ['page']); // default to pages
    $all_post_types = get_post_types(['public' => true], 'objects');
    ?>
    <div class="wrap">
        <h1>Text Repeater Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('ctr_save_settings'); ?>
            <p>Select post types where the "Custom Text Repeater" metabox should appear:</p>
            <select name="ctr_post_types[]" multiple size="10" style="width:350px;">
                <?php foreach ($all_post_types as $pt): ?>
                    <option value="<?php echo esc_attr($pt->name); ?>" <?php echo in_array($pt->name, $selected) ? 'selected' : ''; ?>>
                        <?php echo esc_html($pt->label) ?> (<?php echo esc_html($pt->name); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <p><em>Hold Ctrl/Cmd to select multiple. Choose "page", "post", your CPTs, etc.</em></p>

            <p><input type="submit" name="ctr_submit" class="button button-primary" value="Save Settings"></p>
        </form>
    </div>
    <?php
}

/**
 * Add metabox only to selected post types
 */
add_action('add_meta_boxes', 'ctr_add_repeater_metabox');
function ctr_add_repeater_metabox()
{
    $selected = get_option('ctr_selected_post_types', ['page']);

    foreach ($selected as $post_type) {
        add_meta_box(
            'custom_text_repeater_mb',
            'Custom Text Repeater',
            'ctr_metabox_content',
            $post_type,
            'normal',
            'default'
        );
    }
}

/**
 * Metabox HTML + saved values
 */
function ctr_metabox_content($post)
{
    wp_nonce_field('ctr_save_meta', 'ctr_nonce');
    ?>
    <div id="ctr-repeater-wrap">
        <!-- existing rows here if any -->
    </div>
    <button type="button" class="button" id="ctr-add-row">+ Add Text (TEST)</button>

    <script>
        jQuery(function ($) {
            $('#ctr-add-row').click(function () {
                alert('Button clicked! If you see this → JS works.');
                var html = '<div style="margin:10px 0;"><input type="text" name="ctr_texts[]" placeholder="Type here" style="width:70%"></div>';
                $('#ctr-repeater-wrap').append(html);
            });
        });
    </script>
    <?php
}

/**
 * Render one repeater row
 */
function ctr_render_row($index, $value = '')
{
    ?>
    <div class="ctr-row" style="margin-bottom:12px; padding:10px; background:#f9f9f9; border:1px solid #ddd;">
        <input type="text" name="ctr_texts[]" value="<?php echo esc_attr($value); ?>" style="width:80%;"
            placeholder="Enter text here..." />
        <button type="button" class="button ctr-remove-row" style="margin-left:10px;">Remove</button>
    </div>
    <?php
}

/**
 * Save repeater data
 */
add_action('save_post', 'ctr_save_repeater_data');
function ctr_save_repeater_data($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!isset($_POST['ctr_nonce']) || !wp_verify_nonce($_POST['ctr_nonce'], 'ctr_save_meta'))
        return;
    if (!current_user_can('edit_post', $post_id))
        return;

    // ── Debug ────────────────────────────────────────
    if (isset($_POST['ctr_texts'])) {
        error_log('ctr_texts received: ' . print_r($_POST['ctr_texts'], true));
    } else {
        error_log('No ctr_texts in POST');
    }
    // ─────────────────────────────────────────────────

    if (isset($_POST['ctr_texts'])) {
        $texts = array_filter(array_map('sanitize_text_field', (array) $_POST['ctr_texts']));
        if (!empty($texts)) {
            update_post_meta($post_id, CTR_TEXT_META_KEY, $texts);
        } else {
            delete_post_meta($post_id, CTR_TEXT_META_KEY);
        }
    }
}



/**
 * Shortcode: [text_repeater]
 */
add_shortcode('text_repeater', 'ctr_display_shortcode');
function ctr_display_shortcode($atts)
{
    $post_id = get_the_ID();
    if (!$post_id)
        return '';

    $items = get_post_meta($post_id, CTR_TEXT_META_KEY, true);
    if (!is_array($items) || empty($items)) {
        return '<p>No text items added yet.</p>';
    }

    $output = '<ul class="custom-text-repeater">';
    foreach ($items as $text) {
        $output .= '<li>' . esc_html($text) . '</li>';
    }
    $output .= '</ul>';

    return $output;
}