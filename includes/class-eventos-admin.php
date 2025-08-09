<?php
class Eventos_Admin {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_evento_meta'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_menu', array($this, 'add_help_page'));
    }

    public function add_meta_boxes() {
        add_meta_box(
            'evento_metabox',
            __('Detalhes do Evento', 'eventos-manager'),
            array($this, 'render_evento_metabox'),
            'evento',
            'normal',
            'high'
        );
    }

    public function render_evento_metabox($post) {
        wp_nonce_field('evento_meta_nonce', 'evento_meta_nonce');

        $data_evento = get_post_meta($post->ID, 'data_evento', true);
        $hora_evento = get_post_meta($post->ID, 'hora_evento', true);
        $local_evento = get_post_meta($post->ID, 'local_evento', true);
        ?>
        <div class="evento-metabox">
            <div class="meta-field">
                <label for="data_evento"><?php _e('Data do Evento', 'eventos-manager'); ?></label>
                <input type="date" id="data_evento" name="data_evento" value="<?php echo esc_attr($data_evento); ?>" required>
            </div>
            <div class="meta-field">
                <label for="hora_evento"><?php _e('Hora do Evento', 'eventos-manager'); ?></label>
                <input type="time" id="hora_evento" name="hora_evento" value="<?php echo esc_attr($hora_evento); ?>">
            </div>
            <div class="meta-field">
                <label for="local_evento"><?php _e('Local do Evento', 'eventos-manager'); ?></label>
                <input type="text" id="local_evento" name="local_evento" value="<?php echo esc_attr($local_evento); ?>">
            </div>
        </div>
        <?php
    }

    public function save_evento_meta($post_id) {
        if (!isset($_POST['evento_meta_nonce']) || !wp_verify_nonce($_POST['evento_meta_nonce'], 'evento_meta_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['data_evento']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['data_evento'])) {
            update_post_meta($post_id, 'data_evento', sanitize_text_field($_POST['data_evento']));
        }

        if (isset($_POST['hora_evento']) && preg_match('/^\d{2}:\d{2}$/', $_POST['hora_evento'])) {
            update_post_meta($post_id, 'hora_evento', sanitize_text_field($_POST['hora_evento']));
        }

        if (isset($_POST['local_evento'])) {
            update_post_meta($post_id, 'local_evento', sanitize_text_field($_POST['local_evento']));
        }
    }

    public function enqueue_admin_scripts() {
        global $post_type;

        if ('evento' == $post_type) {
            wp_enqueue_style(
                'eventos-manager-admin-css',
                EVENTOS_MANAGER_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                EVENTOS_MANAGER_VERSION
            );
            wp_enqueue_script(
                'eventos-manager-admin-js',
                EVENTOS_MANAGER_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery'),
                EVENTOS_MANAGER_VERSION,
                true
            );
        }
    }

    public function add_help_page() {
        add_submenu_page(
            'edit.php?post_type=evento',
            __('Ajuda - Gerenciador de Eventos', 'eventos-manager'),
            __('Ajuda', 'eventos-manager'),
            'manage_options',
            'eventos-manager-help',
            array($this, 'render_help_page')
        );
    }

    public function render_help_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Ajuda - Gerenciador de Eventos', 'eventos-manager'); ?></h1>
            <p><?php _e('Este plugin permite gerenciar eventos com um calendário e shortcodes.', 'eventos-manager'); ?></p>
            <h2><?php _e('Shortcodes Disponíveis', 'eventos-manager'); ?></h2>
            <ul>
                <li><code>[mostra-calendario]</code>: <?php _e('Exibe o calendário de eventos.', 'eventos-manager'); ?></li>
                <li><code>[mostra-prox-eventos limit="5" tipo=""]</code>: <?php _e('Exibe uma lista de próximos eventos. Atributos: limit (número de eventos), tipo (slug do tipo de evento).', 'eventos-manager'); ?></li>
                <li><code>[eventos-completo]</code>: <?php _e('Exibe o calendário e a lista de próximos eventos juntos.', 'eventos-manager'); ?></li>
            </ul>
        </div>
        <?php
    }
}