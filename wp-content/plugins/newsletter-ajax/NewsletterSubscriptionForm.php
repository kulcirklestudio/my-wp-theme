<?php
/**
 * Plugin Name: Newsletter Subscription Form
 * Description: Custom AJAX newsletter subscription with duplicate email prevention
 * Version: 1.1
 * Author: Kuldeep
 */

if (!defined('ABSPATH')) {
    exit;
}

// Create table on activation
function newsletter_create_db_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'newsletter_subscribers';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        subscribed_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY email_unique (email)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'newsletter_create_db_table');

// Enqueue scripts
function newsletter_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script(
        'newsletter-ajax',
        plugin_dir_url(__FILE__) . 'newsletter-ajax.js',
        array('jquery'),
        '1.1',
        true
    );
    wp_localize_script('newsletter-ajax', 'newsletter_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('newsletter-subscribe-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'newsletter_enqueue_scripts');

// Shortcode: [newsletter_form]
function newsletter_subscription_form_shortcode() {
    ob_start();
    ?>
    <div class="newsletter-wrapper">
        <form id="newsletter-subscription-form" class="newsletter-form">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required placeholder="Your name">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="your@email.com">
            </div>

            <button type="submit" class="submit-btn">Subscribe</button>
        </form>

        <div id="newsletter-response" class="response-message"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('newsletter_form', 'newsletter_subscription_form_shortcode');

// AJAX Handler
function newsletter_subscribe_ajax_handler() {
    check_ajax_referer('newsletter-subscribe-nonce', 'nonce');

    global $wpdb;
    $table_name = $wpdb->prefix . 'newsletter_subscribers';

    $name  = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');

    if (empty($name) || empty($email) || !is_email($email)) {
        wp_send_json_error('Please enter a valid name and email.');
        wp_die();
    }

    // Check for existing email
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE email = %s",
        $email
    ));

    if ($exists > 0) {
        wp_send_json_error('This email is already subscribed.');
        wp_die();
    }

    // Insert
    $result = $wpdb->insert(
        $table_name,
        [
            'name'  => $name,
            'email' => $email
        ],
        ['%s', '%s']
    );

    if ($result !== false) {
        wp_send_json_success('Thank you! You have been successfully subscribed.');
    } else {
        wp_send_json_error('Failed to subscribe. Please try again.');
    }

    wp_die();
}
add_action('wp_ajax_newsletter_subscribe', 'newsletter_subscribe_ajax_handler');
add_action('wp_ajax_nopriv_newsletter_subscribe', 'newsletter_subscribe_ajax_handler');