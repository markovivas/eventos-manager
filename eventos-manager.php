<?php
/**
 * Plugin Name: Gerenciador de Eventos
 * Description: Plugin para cadastro e exibição de eventos com calendário.  
 * Shortcodes disponíveis: [mostra-calendario], [mostra-prox-eventos limit="5" tipo=""], [eventos-completo], [mostra-calendario-widget]
 * Version: 1.0
 * Author: Marco Antonio Vivas
 * Text Domain: gerenciador-eventos
 */


if (!defined('ABSPATH')) {
    exit; // Sai se acessado diretamente
}

define('EVENTOS_MANAGER_VERSION', '1.1.0');
define('EVENTOS_MANAGER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EVENTOS_MANAGER_PLUGIN_URL', plugin_dir_url(__FILE__));

// Carrega as classes do plugin
require_once EVENTOS_MANAGER_PLUGIN_DIR . 'includes/class-eventos-post-type.php';
require_once EVENTOS_MANAGER_PLUGIN_DIR . 'includes/class-eventos-shortcodes.php';
require_once EVENTOS_MANAGER_PLUGIN_DIR . 'includes/class-eventos-admin.php';

class Eventos_Manager {
    public function __construct() {
        new Eventos_Post_Type();
        new Eventos_Shortcodes();
        if (is_admin()) {
            new Eventos_Admin();
        }
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_get_eventos_calendario', array($this, 'get_eventos_calendario'));
        add_action('wp_ajax_nopriv_get_eventos_calendario', array($this, 'get_eventos_calendario'));
        add_filter('single_template', array($this, 'load_single_template'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'eventos-manager-css',
            EVENTOS_MANAGER_PLUGIN_URL . 'assets/css/eventos-manager.css',
            array(),
            EVENTOS_MANAGER_VERSION
        );

        wp_enqueue_script('jquery');

        wp_enqueue_script(
            'eventos-manager-js',
            EVENTOS_MANAGER_PLUGIN_URL . 'assets/js/eventos-manager.js',
            array('jquery'),
            EVENTOS_MANAGER_VERSION,
            true
        );

        wp_localize_script(
            'eventos-manager-js',
            'eventosManager',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('eventos_manager_nonce')
            )
        );
    }

    public function get_eventos_calendario() {
        check_ajax_referer('eventos_manager_nonce', 'security');

        $start_date = isset($_POST['start']) ? sanitize_text_field($_POST['start']) : date('Y-m-d', strtotime('-1 month'));
        $end_date = isset($_POST['end']) ? sanitize_text_field($_POST['end']) : date('Y-m-d', strtotime('+1 month'));

        $args = array(
            'post_type' => 'evento',
            'posts_per_page' => -1,
            'meta_key' => 'data_evento',
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'data_evento',
                    'value' => array($start_date, $end_date),
                    'compare' => 'BETWEEN',
                    'type' => 'DATE'
                )
            )
        );

        $eventos = new WP_Query($args);
        $calendar_events = array();

        if ($eventos->have_posts()) {
            while ($eventos->have_posts()) {
                $eventos->the_post();
                $data_evento = get_post_meta(get_the_ID(), 'data_evento', true);
                $hora_evento = get_post_meta(get_the_ID(), 'hora_evento', true);
                $tipos = get_the_terms(get_the_ID(), 'tipo_evento');
                $tipo = !empty($tipos) && !is_wp_error($tipos) ? $tipos[0]->name : '';

                $calendar_events[] = array(
                    'title' => get_the_title(),
                    'start' => $data_evento . ($hora_evento ? 'T' . $hora_evento : ''),
                    'tipo' => $tipo,
                    'url' => get_permalink()
                );
            }
            wp_reset_postdata();
        }

        wp_send_json_success($calendar_events);
    }

    public function load_single_template($template) {
        global $post;
        if ($post->post_type === 'evento') {
            $plugin_template = EVENTOS_MANAGER_PLUGIN_DIR . 'single-evento.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $template;
    }
}

new Eventos_Manager();

// Register widget
class Eventos_Upcoming_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'eventos_upcoming_widget',
            __('Próximos Eventos', 'eventos-manager'),
            array('description' => __('Mostra os próximos eventos', 'eventos-manager'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        echo do_shortcode('[mostra-prox-eventos limit="' . esc_attr($instance['limit']) . '"]');
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $limit = !empty($instance['limit']) ? $instance['limit'] : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Número de eventos:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo esc_attr($limit); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['limit'] = (!empty($new_instance['limit'])) ? intval($new_instance['limit']) : 5;
        return $instance;
    }
}

add_action('widgets_init', function() {
    register_widget('Eventos_Upcoming_Widget');
});