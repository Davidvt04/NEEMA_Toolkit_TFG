<?php

declare(strict_types=1);

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__, 4) . '/');
}

if (!defined('WPINC')) {
    define('WPINC', 'wp-includes');
}

if (!defined('OBJECT')) {
    define('OBJECT', 'OBJECT');
}

if (!defined('MINUTE_IN_SECONDS')) {
    define('MINUTE_IN_SECONDS', 60);
}

if (!function_exists('add_action')) {
    function add_action() {}
}

if (!function_exists('add_filter')) {
    function add_filter() {}
}

if (!function_exists('apply_filters')) {
    function apply_filters($tag, $value) {
        return $value;
    }
}

if (!function_exists('do_action')) {
    function do_action() {}
}

if (!function_exists('remove_action')) {
    function remove_action($hook_name, $callback, $priority = 10) {
        return true;
    }
}

if (!function_exists('remove_filter')) {
    function remove_filter($hook_name, $callback, $priority = 10) {
        return true;
    }
}

if (!function_exists('get_template_directory')) {
    function get_template_directory() {
        return dirname(__DIR__);
    }
}

if (!function_exists('get_template_directory_uri')) {
    function get_template_directory_uri() {
        return 'http://example.test/wp-content/themes/neema-theme';
    }
}

if (!function_exists('home_url')) {
    function home_url($path = '') {
        return 'http://example.test' . $path;
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($text) {
        return trim((string) $text);
    }
}

if (!function_exists('sanitize_textarea_field')) {
    function sanitize_textarea_field($text) {
        return trim((string) $text);
    }
}

if (!function_exists('is_singular')) {
    function is_singular($post_type = '') {
        return $GLOBALS['__test_is_singular'] ?? false;
    }
}

if (!function_exists('is_user_logged_in')) {
    function is_user_logged_in() {
        return $GLOBALS['__test_is_user_logged_in'] ?? false;
    }
}

if (!function_exists('is_admin')) {
    function is_admin() {
        return $GLOBALS['__test_is_admin'] ?? false;
    }
}

if (!function_exists('wp_get_current_user')) {
    function wp_get_current_user() {
        return $GLOBALS['__test_current_user'] ?? (object) ['ID' => 0, 'roles' => []];
    }
}

if (!function_exists('get_transient')) {
    function get_transient($key) {
        $values = $GLOBALS['__test_transients'] ?? [];
        return $values[$key] ?? false;
    }
}

if (!function_exists('set_transient')) {
    function set_transient($key, $value, $expiration = 0) {
        $GLOBALS['__test_transients'][$key] = $value;
        $GLOBALS['__test_set_transient_calls'][] = [$key, $value, $expiration];
        return true;
    }
}

if (!function_exists('delete_transient')) {
    function delete_transient($key) {
        unset($GLOBALS['__test_transients'][$key]);
        $GLOBALS['__test_delete_transient_calls'][] = $key;
        return true;
    }
}

if (!class_exists('WP_Query')) {
    class WP_Query {
        public $posts = [];
        private $current_index = 0;

        public function __construct($args = null) {
            if (isset($GLOBALS['__test_wp_query_callback']) && is_callable($GLOBALS['__test_wp_query_callback'])) {
                $this->posts = call_user_func($GLOBALS['__test_wp_query_callback'], $args);
                return;
            }

            $posts = $GLOBALS['__test_wp_query_posts'] ?? [];

            if (is_array($args) && !empty($args['post__not_in'])) {
                $excluded = array_map('intval', (array) $args['post__not_in']);
                $posts = array_values(array_filter($posts, static function ($post) use ($excluded) {
                    return !in_array((int) $post->ID, $excluded, true);
                }));
            }

            if (is_array($args) && isset($args['posts_per_page']) && (int) $args['posts_per_page'] > -1) {
                $posts = array_slice($posts, 0, (int) $args['posts_per_page']);
            }

            $this->posts = $posts;
        }

        public function have_posts() {
            return $this->current_index < count($this->posts);
        }

        public function the_post() {
            if (!$this->have_posts()) {
                return null;
            }

            $post = $this->posts[$this->current_index];
            $this->current_index++;
            $GLOBALS['__test_current_post'] = $post;
            $GLOBALS['__test_current_post_id'] = is_object($post) && isset($post->ID) ? $post->ID : null;

            return $post;
        }
    }
}

if (!function_exists('pll_current_language')) {
    function pll_current_language() {
        return $GLOBALS['__test_pll_current_language'] ?? 'es';
    }
}

if (!function_exists('pll_translate_string')) {
    function pll_translate_string($string, $lang) {
        $translations = $GLOBALS['__test_pll_translate_string'] ?? [];
        return $translations[$lang][$string] ?? $string;
    }
}

if (!function_exists('get_user_meta')) {
    function get_user_meta($user_id, $meta_key, $single = false) {
        $values = $GLOBALS['__test_user_meta'] ?? [];
        return $values[$user_id][$meta_key] ?? [];
    }
}

if (!function_exists('update_user_meta')) {
    function update_user_meta($user_id, $meta_key, $value) {
        $GLOBALS['__test_update_user_meta_calls'][] = [$user_id, $meta_key, $value];
        $GLOBALS['__test_user_meta'][$user_id][$meta_key] = $value;
        return true;
    }
}

if (!function_exists('get_post_meta')) {
    function get_post_meta($post_id, $meta_key, $single = false) {
        $values = $GLOBALS['__test_post_meta'] ?? [];
        if (is_object($post_id) && isset($post_id->ID)) {
            $post_id = $post_id->ID;
        }

        if (!isset($values[$post_id]) || !array_key_exists($meta_key, $values[$post_id])) {
            return [];
        }

        $value = $values[$post_id][$meta_key];

        if ($single) {
            return $value;
        }

        return is_array($value) ? $value : [$value];
    }
}

if (!function_exists('pll_get_term')) {
    function pll_get_term($term_id, $lang) {
        $values = $GLOBALS['__test_pll_get_term'] ?? [];
        return $values[$lang][$term_id] ?? $term_id;
    }
}

if (!function_exists('pll_get_post')) {
    function pll_get_post($post_id, $lang = '') {
        $values = $GLOBALS['__test_pll_get_post'] ?? [];
        return $values[$lang][$post_id] ?? $post_id;
    }
}

if (!function_exists('get_current_user_id')) {
    function get_current_user_id() {
        return $GLOBALS['__test_current_user_id'] ?? 0;
    }
}

if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action = -1) {
        return $GLOBALS['__test_nonce_verification_result'] ?? true;
    }
}

if (!function_exists('wp_nonce_field')) {
    function wp_nonce_field($action = -1, $name = '_wpnonce', $referer = true, $echo = true) {
        $output = '<input type="hidden" name="' . htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8') . '" value="nonce" />';
        if ($echo) {
            echo $output;
        }
        return $output;
    }
}

if (!function_exists('wp_insert_post')) {
    function wp_insert_post($data, $wp_error = false, $fire_after_hooks = true) {
        $GLOBALS['__test_insert_post_calls'][] = [$data, $wp_error, $fire_after_hooks];
        return $GLOBALS['__test_insert_post_return'] ?? 123;
    }
}

if (!function_exists('wp_update_post')) {
    function wp_update_post($data, $wp_error = false, $fire_after_hooks = true) {
        $GLOBALS['__test_update_post_calls'][] = [$data, $wp_error, $fire_after_hooks];
        return $GLOBALS['__test_update_post_return'] ?? true;
    }
}

if (!function_exists('wp_delete_post')) {
    function wp_delete_post($post_id, $force_delete = false) {
        $GLOBALS['__test_delete_post_calls'][] = [$post_id, $force_delete];
        return true;
    }
}

if (!function_exists('wp_mail')) {
    function wp_mail($to, $subject, $message, $headers = array(), $attachments = array()) {
        $GLOBALS['__test_wp_mail_calls'][] = [$to, $subject, $message, $headers, $attachments];
        return true;
    }
}

if (!function_exists('get_userdata')) {
    function get_userdata($user_id) {
        return $GLOBALS['__test_user_data'][$user_id] ?? null;
    }
}

if (!function_exists('get_permalink')) {
    function get_permalink($post_id = 0) {
        return 'http://example.test/?p=' . $post_id;
    }
}

if (!function_exists('get_role')) {
    function get_role($role_name) {
        if (!isset($GLOBALS['__test_roles'][$role_name])) {
            $GLOBALS['__test_roles'][$role_name] = new class {
                public array $caps = [];

                public function add_cap($capability) {
                    $this->caps[] = $capability;
                }
            };
        }

        return $GLOBALS['__test_roles'][$role_name];
    }
}

if (!function_exists('has_post_thumbnail')) {
    function has_post_thumbnail($post_id = null) {
        return $GLOBALS['__test_has_post_thumbnail'][$post_id] ?? true;
    }
}

if (!function_exists('get_post_status')) {
    function get_post_status($post_id = null) {
        return $GLOBALS['__test_post_status'][$post_id] ?? 'draft';
    }
}

if (!function_exists('wp_redirect')) {
    function wp_redirect($location, $status = 302) {
        $GLOBALS['__test_redirect_calls'][] = [$location, $status];
        return true;
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) {
        return false;
    }
}

if (!function_exists('get_post_thumbnail_id')) {
    function get_post_thumbnail_id($post_id) {
        return $GLOBALS['__test_thumbnail_ids'][$post_id] ?? 0;
    }
}

if (!function_exists('set_post_thumbnail')) {
    function set_post_thumbnail($post_id, $thumbnail_id) {
        $GLOBALS['__test_set_thumbnail_calls'][] = [$post_id, $thumbnail_id];
        return true;
    }
}

if (!function_exists('delete_post_thumbnail')) {
    function delete_post_thumbnail($post_id) {
        $GLOBALS['__test_delete_thumbnail_calls'][] = $post_id;
        return true;
    }
}

if (!function_exists('get_object_taxonomies')) {
    function get_object_taxonomies($object_type, $output = 'names') {
        return $GLOBALS['__test_object_taxonomies'][$object_type] ?? [];
    }
}

if (!function_exists('wp_get_object_terms')) {
    function wp_get_object_terms($object_id, $taxonomy, $args = array()) {
        return $GLOBALS['__test_object_terms'][$object_id][$taxonomy] ?? [];
    }
}

if (!function_exists('wp_set_object_terms')) {
    function wp_set_object_terms($object_id, $terms, $taxonomy, $append = false) {
        $GLOBALS['__test_set_object_terms_calls'][] = [$object_id, $terms, $taxonomy, $append];
        return true;
    }
}

if (!function_exists('delete_post_meta')) {
    function delete_post_meta($post_id, $meta_key, $meta_value = '') {
        $GLOBALS['__test_delete_post_meta_calls'][] = [$post_id, $meta_key, $meta_value];
        return true;
    }
}

if (!function_exists('update_post_meta')) {
    function update_post_meta($post_id, $meta_key, $meta_value) {
        $GLOBALS['__test_update_post_meta_calls'][] = [$post_id, $meta_key, $meta_value];
        $GLOBALS['__test_post_meta'][$post_id][$meta_key] = $meta_value;
        return true;
    }
}

if (!function_exists('add_post_meta')) {
    function add_post_meta($post_id, $meta_key, $meta_value, $unique = false) {
        $GLOBALS['__test_add_post_meta_calls'][] = [$post_id, $meta_key, $meta_value, $unique];
        return true;
    }
}

if (!function_exists('maybe_unserialize')) {
    function maybe_unserialize($data) {
        return $data;
    }
}

if (!function_exists('wp_trim_words')) {
    function wp_trim_words($text, $num_words = 55, $more = null) {
        return $text;
    }
}

if (!function_exists('current_time')) {
    function current_time($type, $gmt = 0) {
        return time();
    }
}

if (!function_exists('date_i18n')) {
    function date_i18n($format, $timestamp = false, $gmt = false) {
        return date($format, $timestamp ?: time());
    }
}

if (!function_exists('wp_send_json_error')) {
    function wp_send_json_error($data = null) {
        $GLOBALS['__test_wp_send_json_error'][] = $data;
        throw new RuntimeException('wp_send_json_error called');
    }
}

if (!function_exists('wp_send_json_success')) {
    function wp_send_json_success($data = null) {
        $GLOBALS['__test_wp_send_json_success'][] = $data;
        throw new RuntimeException('wp_send_json_success called');
    }
}

if (!function_exists('update_meta_cache')) {
    function update_meta_cache($meta_type, $object_ids) {
        $GLOBALS['__test_update_meta_cache_calls'][] = [$meta_type, $object_ids];
        return true;
    }
}

if (!function_exists('get_posts')) {
    function get_posts($args = array()) {
        if (isset($GLOBALS['__test_get_posts_callback']) && is_callable($GLOBALS['__test_get_posts_callback'])) {
            return call_user_func($GLOBALS['__test_get_posts_callback'], $args);
        }

        return $GLOBALS['__test_get_posts_return'] ?? array();
    }
}

if (!function_exists('get_users')) {
    function get_users($args = array()) {
        return $GLOBALS['__test_get_users_return'] ?? array();
    }
}

if (!function_exists('get_page_by_path')) {
    function get_page_by_path($page_path, $output = OBJECT, $post_type = 'page') {
        $values = $GLOBALS['__test_pages_by_path'] ?? [];
        return $values[$page_path] ?? null;
    }
}

if (!function_exists('get_the_title')) {
    function get_the_title($post_id = 0) {
        if ($post_id === 0 && isset($GLOBALS['__test_current_post']) && is_object($GLOBALS['__test_current_post']) && isset($GLOBALS['__test_current_post']->post_title)) {
            return $GLOBALS['__test_current_post']->post_title;
        }

        $values = $GLOBALS['__test_the_title'] ?? [];
        return $values[$post_id] ?? '';
    }
}

if (!function_exists('get_the_ID')) {
    function get_the_ID() {
        return $GLOBALS['__test_current_post_id'] ?? 0;
    }
}

if (!function_exists('get_the_post_thumbnail')) {
    function get_the_post_thumbnail($post_id, $size = 'post-thumbnail', $attr = array()) {
        return '<img src="http://example.test/thumb-' . htmlspecialchars((string) $post_id, ENT_QUOTES, 'UTF-8') . '.jpg" alt="" />';
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return $url;
    }
}

if (!function_exists('get_post_type')) {
    function get_post_type($post = null) {
        if (is_object($post) && isset($post->post_type)) {
            return $post->post_type;
        }

        return $GLOBALS['__test_post_types'][$post] ?? null;
    }
}

if (!function_exists('get_edit_post_link')) {
    function get_edit_post_link($post_id = 0) {
        return 'http://example.test/edit.php?post=' . $post_id;
    }
}

if (!function_exists('number_format_i18n')) {
    function number_format_i18n($number) {
        return number_format((float) $number);
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('wp_add_dashboard_widget')) {
    function wp_add_dashboard_widget(...$args) {
        $GLOBALS['__test_dashboard_widget_calls'][] = $args;
    }
}

if (!function_exists('add_meta_box')) {
    function add_meta_box(...$args) {
        $GLOBALS['__test_add_meta_box_calls'][] = $args;
    }
}

if (!function_exists('check_ajax_referer')) {
    function check_ajax_referer($action = -1, $query_arg = false, $die = true) {
        return true;
    }
}

if (!function_exists('set_query_var')) {
    function set_query_var($key, $value) {
        $GLOBALS['__test_query_vars'][$key] = $value;
    }
}

if (!function_exists('get_query_var')) {
    function get_query_var($key, $default = '') {
        return $GLOBALS['__test_query_vars'][$key] ?? $default;
    }
}

if (!function_exists('get_field')) {
    function get_field($field_name, $post_id = null) {
        $values = $GLOBALS['__test_acf_fields'] ?? [];
        return $values[$post_id][$field_name] ?? null;
    }
}

if (!function_exists('get_template_part')) {
    function get_template_part($slug, $name = null, $args = array()) {
        $GLOBALS['__test_template_part_calls'][] = [$slug, $name, $args];
    }
}

if (!function_exists('add_theme_support')) {
    function add_theme_support($feature, ...$args) {
        $GLOBALS['__test_add_theme_support_calls'][] = [$feature, $args];
    }
}

if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action = -1) {
        return 'nonce';
    }
}

if (!class_exists('WP_STATISTICS\\DB')) {
    eval('namespace WP_STATISTICS; class DB {}');
}

if (!function_exists('wp_list_pluck')) {
    function wp_list_pluck($list, $field) {
        $output = array();
        foreach ((array) $list as $item) {
            if (is_array($item) && array_key_exists($field, $item)) {
                $output[] = $item[$field];
                continue;
            }
            if (is_object($item) && isset($item->{$field})) {
                $output[] = $item->{$field};
            }
        }
        return $output;
    }
}

if (!function_exists('admin_url')) {
    function admin_url($path = '') {
        return 'http://example.test/wp-admin/' . ltrim($path, '/');
    }
}



if (!function_exists('is_singular')) {
    function is_singular($post_type = '') {
        return false;
    }
}

if (!function_exists('is_post_type_archive')) {
    function is_post_type_archive($post_type = '') {
        return $GLOBALS['__test_is_post_type_archive'] ?? false;
    }
}

if (!function_exists('is_page_template')) {
    function is_page_template($template = '') {
        $templates = $GLOBALS['__test_page_templates'] ?? [];
        return in_array($template, (array) $templates, true);
    }
}

if (!function_exists('get_stylesheet_directory_uri')) {
    function get_stylesheet_directory_uri() {
        return 'http://example.test/wp-content/themes/neema-theme';
    }
}

if (!function_exists('get_stylesheet_directory')) {
    function get_stylesheet_directory() {
        return dirname(__DIR__);
    }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style(...$args) {
        $GLOBALS['__test_wp_enqueue_style_calls'][] = $args;
    }
}

if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script(...$args) {
        $GLOBALS['__test_wp_enqueue_script_calls'][] = $args;
    }
}

if (!function_exists('wp_localize_script')) {
    function wp_localize_script(...$args) {
        $GLOBALS['__test_wp_localize_script_calls'][] = $args;
    }
}

if (!function_exists('wp_add_inline_script')) {
    function wp_add_inline_script(...$args) {
        $GLOBALS['__test_wp_add_inline_script_calls'][] = $args;
    }
}

if (!function_exists('selected')) {
    function selected($selected, $current, $echo = true) {
        $value = ((string) $selected === (string) $current) ? ' selected="selected"' : '';
        if ($echo) {
            echo $value;
        }
        return $value;
    }
}

if (!function_exists('checked')) {
    function checked($checked, $current = true, $echo = true) {
        $value = ((string) $checked === (string) $current) ? ' checked="checked"' : '';
        if ($echo) {
            echo $value;
        }
        return $value;
    }
}

if (!function_exists('pll__')) {
    function pll__($string, $context = '') {
        return $string;
    }
}

if (!function_exists('get_user_locale')) {
    function get_user_locale($user_id = 0) {
        return $GLOBALS['__test_user_locale'] ?? 'es_ES';
    }
}

if (!function_exists('wp_editor')) {
    function wp_editor($content, $editor_id, $settings = array()) {
        $GLOBALS['__test_wp_editor_calls'][] = [$content, $editor_id, $settings];
        echo '<textarea id="' . htmlspecialchars((string) $editor_id, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars((string) $content, ENT_QUOTES, 'UTF-8') . '</textarea>';
    }
}

if (!function_exists('wp_kses_post')) {
    function wp_kses_post($data) {
        return $data;
    }
}

if (!function_exists('current_user_can')) {
    function current_user_can($capability, ...$args) {
        return $GLOBALS['__test_current_user_can'][$capability] ?? true;
    }
}

if (!function_exists('register_post_type')) {
    function register_post_type($post_type, $args = array()) {
        $GLOBALS['__test_register_post_type_calls'][] = [$post_type, $args];
    }
}

if (!function_exists('wp_reset_postdata')) {
    function wp_reset_postdata() {
        $GLOBALS['__test_wp_reset_postdata_calls'][] = true;
    }
}

if (!function_exists('get_post')) {
    function get_post($post_id = null) {
        $values = $GLOBALS['__test_get_post_return'] ?? [];
        if (is_object($values) || is_array($values) && array_keys($values) === range(0, count($values) - 1) && count($values) === 1) {
            return $values;
        }

        if (is_array($values)) {
            return $values[$post_id] ?? null;
        }

        return $values;
    }
}

if (!function_exists('wp_send_json_error')) {
    function wp_send_json_error($data = null) {
        throw new RuntimeException('wp_send_json_error called');
    }
}

if (!function_exists('wp_send_json_success')) {
    function wp_send_json_success($data = null) {
        throw new RuntimeException('wp_send_json_success called');
    }
}

