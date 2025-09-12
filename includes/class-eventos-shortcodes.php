<?php
class Eventos_Shortcodes {
    public function __construct() {
        add_shortcode('mostra-calendario', array($this, 'render_calendario'));
        add_shortcode('mostra-prox-eventos', array($this, 'render_proximos_eventos'));
        add_shortcode('eventos-completo', array($this, 'render_eventos_completo'));
        add_shortcode('mostra-calendario-widget', array($this, 'render_calendario_widget')); // NOVO SHORTCODE
    }

    public function render_calendario($atts) {
        ob_start();
        ?>
        <div id="eventos-calendario" class="em-calendario-wrapper" data-view="full">
            <div class="em-calendario-header em-toolbar">
                <div class="em-toolbar-section">
                    <button class="em-nav-btn" data-nav="prev">&lt;</button>
                    <button class="em-nav-btn" data-nav="next">&gt;</button>
                    <button class="em-nav-btn em-today-btn" data-nav="today">Hoje</button>
                </div>
                <div class="em-toolbar-section em-toolbar-center"><h3 class="em-mes-ano"></h3></div>
                <div class="em-toolbar-section em-toolbar-right">
                    <button class="em-view-btn" data-view="fullscreen" title="Tela Cheia">⛶</button>
                </div>
            </div>
            <div class="em-dias-semana"></div>
            <div class="em-dias-grid"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_proximos_eventos($atts) {
        $atts = shortcode_atts(array(
            'limit' => 5,
            'tipo' => ''
        ), $atts, 'mostra-prox-eventos');

        $args = array(
            'post_type' => 'evento',
            'posts_per_page' => intval($atts['limit']),
            'meta_key' => 'data_evento',
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'data_evento',
                    'value' => date('Y-m-d'),
                    'compare' => '>=',
                    'type' => 'DATE'
                )
            )
        );

        if (!empty($atts['tipo'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'tipo_evento',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($atts['tipo'])
                )
            );
        }

        $eventos = new WP_Query($args);

        ob_start();
        ?>
        <div class="proximos-eventos-box">
            <h4>Próximos Eventos</h4>
            <ul class="proximos-eventos-lista">
                <?php if ($eventos->have_posts()) : ?>
                    <?php while ($eventos->have_posts()) : $eventos->the_post(); ?>
                        <?php
                        $data_evento = get_post_meta(get_the_ID(), 'data_evento', true);
                        $hora_evento = get_post_meta(get_the_ID(), 'hora_evento', true);
                        $tipos = get_the_terms(get_the_ID(), 'tipo_evento');
                        ?>
                        <li>
                            <div class="evento-data">
                                <?php echo date_i18n('d M', strtotime($data_evento)); ?>
                            </div>
                            <div>
                                <div class="evento-titulo"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
                                <?php if ($hora_evento) : ?>
                                    <div class="evento-hora"><?php echo $hora_evento; ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if ($tipos && !is_wp_error($tipos)) : ?>
                                <span class="evento-tipo"><?php echo $tipos[0]->name; ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; wp_reset_postdata(); ?>
                <?php else : ?>
                    <li>Nenhum evento agendado</li>
                <?php endif; ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_eventos_completo($atts) {
        ob_start();
        ?>
        <div class="noticias-eventos-wrapper">
            <div class="noticias-col">
                <?php echo $this->render_calendario($atts); ?>
            </div>
            <div class="eventos-col">
                <?php echo $this->render_proximos_eventos($atts); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_calendario_widget($atts) {
        ob_start();
        ?>
        <div id="eventos-calendario-widget" class="em-calendario-wrapper" data-view="widget">
            <div class="em-calendario-header em-toolbar">
                <div class="em-toolbar-section">
                    <button class="em-nav-btn" data-nav="prev">&lt;</button>
                    <button class="em-nav-btn" data-nav="next">&gt;</button>
                </div>
                <div class="em-toolbar-section em-toolbar-center"><h3 class="em-mes-ano"></h3></div>
                <div class="em-toolbar-section"></div>
            </div>
            <div class="em-dias-semana"></div>
            <div class="em-dias-grid"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}