<?php
class Eventos_Post_Type {
    public function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_taxonomy'));
    }

    public function register_post_type() {
        $labels = array(
            'name' => __('Eventos', 'eventos-manager'),
            'singular_name' => __('Evento', 'eventos-manager'),
            'menu_name' => __('Eventos', 'eventos-manager'),
            'add_new' => __('Adicionar Novo', 'eventos-manager'),
            'add_new_item' => __('Adicionar Novo Evento', 'eventos-manager'),
            'edit_item' => __('Editar Evento', 'eventos-manager'),
            'new_item' => __('Novo Evento', 'eventos-manager'),
            'view_item' => __('Ver Evento', 'eventos-manager'),
            'search_items' => __('Buscar Eventos', 'eventos-manager'),
            'not_found' => __('Nenhum evento encontrado', 'eventos-manager'),
            'not_found_in_trash' => __('Nenhum evento encontrado na lixeira', 'eventos-manager')
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-calendar-alt',
            'supports' => array('title', 'editor', 'thumbnail'),
            'rewrite' => array('slug' => 'eventos'),
            'show_in_rest' => true
        );

        register_post_type('evento', $args);
    }

    public function register_taxonomy() {
        $labels = array(
            'name' => __('Tipos de Evento', 'eventos-manager'),
            'singular_name' => __('Tipo de Evento', 'eventos-manager'),
            'search_items' => __('Buscar Tipos', 'eventos-manager'),
            'all_items' => __('Todos os Tipos', 'eventos-manager'),
            'edit_item' => __('Editar Tipo', 'eventos-manager'),
            'update_item' => __('Atualizar Tipo', 'eventos-manager'),
            'add_new_item' => __('Adicionar Novo Tipo', 'eventos-manager'),
            'new_item_name' => __('Novo Nome de Tipo', 'eventos-manager'),
            'menu_name' => __('Tipos de Evento', 'eventos-manager')
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'tipo-evento'),
            'show_in_rest' => true
        );

        register_taxonomy('tipo_evento', 'evento', $args);
    }
}