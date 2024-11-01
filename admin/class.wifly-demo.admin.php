<?php

class Wifly_Demo_Feedback_Admin
{
    public static function AdminInit()
    {
        add_action('admin_menu', 'Wifly_Demo_Feedback_Admin::admin_menu');
        add_action( 'wp_loaded', 'Wifly_Demo_Feedback_Admin::register_endpoints' );
        add_action('admin_post_get_dump', 'Wifly_Demo_Feedback_Admin::get_dump');
        add_action('admin_post_add_feedback', 'Wifly_Demo_Feedback_Admin::add_feedback');
        add_action('admin_post_add_category', 'Wifly_Demo_Feedback_Admin::add_category');
        add_action('admin_post_edit_category', 'Wifly_Demo_Feedback_Admin::edit_category');
        add_action('admin_post_delete_category', 'Wifly_Demo_Feedback_Admin::delete_category');
    }

    public static function register_endpoints()
    {
        add_rewrite_endpoint( '/add_feedback', EP_ROOT );
        add_action( 'template_redirect', 'Wifly_Demo_Feedback_Admin::public_requests' );
    }

    public static function admin_menu()
    {
        add_menu_page('Feedback page', 'Feedback page', 'manage_options', 'feedback_page', 'Wifly_Demo_Feedback_Admin::render_page', 'dashicons-format-quote');
        add_submenu_page('feedback_page', 'Settings', 'Settings', 'manage_options', 'settings_page', 'Wifly_Demo_Feedback_Admin::render_settings_page');
        add_submenu_page('feedback_page', 'How does it work?', 'How does it work?', 'manage_options', 'faq', 'Wifly_Demo_Feedback_Admin::render_faq_page');
    }

    public static function render_page()
    {
        require_once WIFLY_DEMO_FEEDBACK_PLUGIN_DIR . 'templates/admin-page.php';
    }

    public static function render_settings_page()
    {
        require_once WIFLY_DEMO_FEEDBACK_PLUGIN_DIR . 'templates/settings-page.php';
    }

    public static function render_faq_page()
    {
        require_once WIFLY_DEMO_FEEDBACK_PLUGIN_DIR . 'templates/faq-page.php';
    }

    public static function get_feedback(){
        require_once WIFLY_DEMO_FEEDBACK_PLUGIN_DIR.'providers/class.feedback.provider.php';
    }

    public static function add_category(){
        if (!isset($_POST['wifly_feedback_category_nonce']) || !wp_verify_nonce($_POST['wifly_feedback_category_nonce'], 'add_category')) {
            wp_die("Nonce error");
        }
        $title = sanitize_title($_POST['title']);

        require_once WIFLY_DEMO_FEEDBACK_PLUGIN_DIR.'providers/class.feedback.provider.php';
        WiflyDemoFeedbackProvider::addCategory($title);
        wp_safe_redirect( $_POST['_wp_http_referer'] );
    }

    public static function edit_category(){
        if (!isset($_POST['wifly_feedback_category_nonce']) || !wp_verify_nonce($_POST['wifly_feedback_category_nonce'], 'edit_category')) {
            wp_die("Nonce error");
        }

        $data = [];
        $data += ['title' => sanitize_title($_POST['title'])];
        $data += ['id' => sanitize_text_field($_POST['id'])];

        require_once WIFLY_DEMO_FEEDBACK_PLUGIN_DIR.'providers/class.feedback.provider.php';
        WiflyDemoFeedbackProvider::editCategory($data);
        wp_safe_redirect( $_POST['_wp_http_referer'] );
    }

    public static function delete_category(){
        if (!isset($_POST['wifly_feedback_category_nonce']) || !wp_verify_nonce($_POST['wifly_feedback_category_nonce'], 'delete_category')) {
            wp_die("Nonce error");
        }

        $id = sanitize_text_field($_POST['id']);

        require_once WIFLY_DEMO_FEEDBACK_PLUGIN_DIR.'providers/class.feedback.provider.php';
        WiflyDemoFeedbackProvider::deleteCategory($id);
        wp_safe_redirect( $_POST['_wp_http_referer'] );
    }

    public static function public_requests()
    {
        global $wp_query;
        $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        if ( $path == '/add_feedback') {
            self::add_feedback();
            exit;
        }
    }

    public static function add_feedback(){
        if (!isset($_POST['wifly_feedback_nonce']) || !wp_verify_nonce($_POST['wifly_feedback_nonce'], 'add_feedback')) {
            wp_die("Nonce error");
        }
        require_once WIFLY_DEMO_FEEDBACK_PLUGIN_DIR.'providers/class.feedback.provider.php';
        WiflyDemoFeedbackProvider::addFeedback($_POST['data']);
        wp_safe_redirect( $_POST['_wp_http_referer'] );
    }

    public static function get_dump(){
        if (!isset($_POST['wifly_feedback_nonce']) || !wp_verify_nonce($_POST['wifly_feedback_nonce'], 'get_dump')) {
            wp_die("Nonce error");
        }
        require_once WIFLY_DEMO_FEEDBACK_PLUGIN_DIR.'providers/class.feedback.provider.php';
        WiflyDemoFeedbackProvider::CSVDump();
    }

}