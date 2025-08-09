<?php
class Eventos_Shortcodes {
    public function __construct() {
        add_shortcode('mostra-calendario', array($this, 'render_calendario'));
        add_shortcode('mostra-prox-eventos', array($this, 'render_proximos_eventos'));
        add_shortcode('eventos-completo', array($this, 'render_eventos_completo'));
        add_shortcode('mostra-calendario-widget', array($this, 'render_calendario_widget')); // NOVO SHORTCODE
    }

    public function render_calendario($atts) {
        wp_enqueue_style('eventos-manager-css');
        wp_enqueue_script('eventos-manager-js');

        ob_start();
        ?>
        <div class="calendario-box">
            <h3>Calendário de Eventos</h3>
            <div id="eventos-calendario"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_proximos_eventos($atts) {
        $atts = shortcode_atts(array(
            'limit' => 5,
            'tipo' => ''
        ), $atts, 'mostra-prox-eventos');

        wp_enqueue_style('eventos-manager-css');

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
        wp_enqueue_style('eventos-manager-css');
        wp_enqueue_script('eventos-manager-js');

        ob_start();
        ?>
        <div class="calendario-widget-box">
            <div id="eventos-calendario-widget"></div>
        </div>
        <style>
            
            #eventos-calendario-widget {
                min-width: 280px;
                min-height: 340px;
                font-size: 15px;
            }
            #eventos-calendario-widget .fc-toolbar {
                display: flex;
                justify-content: center;
                align-items: center;
                margin-bottom: 8px;
                background: none;
                border: none;
            }
            #eventos-calendario-widget .fc-center h2 {
                font-size: 1.1em;
                font-weight: 700;
                letter-spacing: 2px;
                color: #e74c3c;
                text-transform: uppercase;
                margin: 0 0 8px 0;
            }
            #eventos-calendario-widget .fc-button {
                background: transparent;
                color: #3498db;
                border: none;
                font-size: 1.2em;
                font-weight: 700;
                box-shadow: none;
                padding: 0 8px;
                margin: 0 2px;
                border-radius: 50%;
                transition: background 0.2s;
            }
            #eventos-calendario-widget .fc-button:hover {
                background: #eaf6fb;
            }
            #eventos-calendario-widget .fc-day-header {
                color: #2c3e50;
                font-weight: 700;
                background: none;
                border: none;
                font-size: 1em;
                padding: 4px 0;
            }
            #eventos-calendario-widget .fc-day {
                border: none;
                background: none;
                font-size: 1em;
                padding: 0;
                text-align: center;
                vertical-align: middle;
                cursor: pointer;
                position: relative;
            }
            #eventos-calendario-widget .fc-sat, 
            #eventos-calendario-widget .fc-sun {
                color: #bdc3c7;
            }
            #eventos-calendario-widget .fc-today {
                background: none !important;
            }
            #eventos-calendario-widget .fc-day-number {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                line-height: 32px;
                border-radius: 50%;
                transition: background 0.2s, color 0.2s, box-shadow 0.2s;
                font-weight: 500;
                margin: 0 auto;
                position: relative;
                z-index: 1;
            }
            #eventos-calendario-widget .fc-today .fc-day-number {
                background: #e74c3c;
                color: #fff;
                box-shadow: 0 2px 8px rgba(231,76,60,0.15);
            }
            #eventos-calendario-widget .fc-has-event .fc-day-number {
                background: #3498db;
                color: #fff;
                font-weight: 700;
                border: 2px solid #217dbb;
                box-shadow: 0 2px 8px rgba(52,152,219,0.15);
            }
            #eventos-calendario-widget .fc-event {
                display: none;
            }
            /* Tooltip */
            .fc-tooltip {
                position: absolute;
                z-index: 9999;
                background: #fff;
                color: #2c3e50;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(44,62,80,0.15);
                padding: 10px 14px;
                font-size: 0.97em;
                min-width: 180px;
                max-width: 220px;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.2s;
            }
            .fc-tooltip.active {
                opacity: 1;
            }
        </style>
        <script>
        jQuery(document).ready(function($){
            function addTooltip(dayEl, content) {
                let $tooltip = $('<div class="fc-tooltip"></div>').html(content);
                $('body').append($tooltip);
                $(dayEl)
                    .on('mouseenter', function(e) {
                        $tooltip.addClass('active');
                        $tooltip.css({
                            top: $(this).offset().top + 36,
                            left: $(this).offset().left - ($tooltip.outerWidth()/2) + 20
                        });
                    })
                    .on('mouseleave', function() {
                        $tooltip.removeClass('active');
                    });
            }

            if ($('#eventos-calendario-widget').length && typeof $.fn.fullCalendar !== 'undefined') {
                $('#eventos-calendario-widget').fullCalendar({
                    header: {
                        left: 'prev,next',
                        center: 'title',
                        right: ''
                    },
                    defaultView: 'month',
                    editable: false,
                    height: 340,
                    contentHeight: 340,
                    aspectRatio: 0.8,
                    events: function(start, end, timezone, callback) {
                        $.ajax({
                            url: eventosManager.ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'get_eventos_calendario',
                                security: eventosManager.nonce,
                                start: start.format('YYYY-MM-DD'),
                                end: end.format('YYYY-MM-DD')
                            },
                            success: function(response) {
                                if (response.success) {
                                    callback(response.data);
                                }
                            }
                        });
                    },
                    eventAfterAllRender: function(view) {
                        // Limpa tooltips antigos
                        $('.fc-tooltip').remove();
                        // Marca dias com eventos
                        var events = $('#eventos-calendario-widget').fullCalendar('clientEvents');
                        $('.fc-day').removeClass('fc-has-event');
                        events.forEach(function(ev){
                            var dateStr = moment(ev.start).format('YYYY-MM-DD');
                            var $day = $('.fc-day[data-date="'+dateStr+'"]');
                            $day.addClass('fc-has-event');
                            // Adiciona tooltip resumido
                            addTooltip($day, '<strong>'+ev.title+'</strong><br>'+(ev.description ? ev.description : ''));
                        });
                    },
                    eventColor: '#3498db',
                    eventTextColor: '#fff',
                    timeFormat: 'H:mm',
                    locale: 'pt-br',
                    eventRender: function(event, element) {
                        // Não exibe eventos na célula
                        return false;
                    }
                });
            }
        });
        </script>
        <?php
        return ob_get_clean();
    }
}