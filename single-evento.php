<?php
get_header();
if (have_posts()) : while (have_posts()) : the_post();
    $data_evento = get_post_meta(get_the_ID(), 'data_evento', true);
    $hora_evento = get_post_meta(get_the_ID(), 'hora_evento', true);
    $local_evento = get_post_meta(get_the_ID(), 'local_evento', true);
    $tipos = get_the_terms(get_the_ID(), 'tipo_evento');
?>
    </br><article class="single-evento-container">
        <header class="evento-header">
            <h1><?php the_title(); ?></h1>
        </header>
        <div class="evento-details">
            <ul class="evento-info-list">
                <li><strong>Data:</strong> <?php echo date_i18n('d/m/Y', strtotime($data_evento)); ?></li>
                <?php if ($hora_evento) : ?>
                    <li><strong>Hora:</strong> <?php echo esc_html($hora_evento); ?></li>
                <?php endif; ?>
                <?php if ($local_evento) : ?>
                    <li><strong>Local:</strong> <?php echo esc_html($local_evento); ?></li>
                <?php endif; ?>
                <?php if ($tipos && !is_wp_error($tipos)) : ?>
                    <li><strong>Tipo:</strong> <span class="evento-tipo"><?php echo esc_html($tipos[0]->name); ?></span></li>
                <?php endif; ?>
            </ul>
            <div class="evento-content"><?php the_content(); ?></div>
        </div>
    </article>
<?php
endwhile; endif;
get_footer();