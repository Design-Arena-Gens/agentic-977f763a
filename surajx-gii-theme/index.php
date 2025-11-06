<?php
/**
 * The main template file
 *
 * @package Surajx_GII_Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<div class="container" style="padding: 3rem 0;">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="entry-content">
                    <?php the_excerpt(); ?>
                </div>
            </article>
            <?php
        endwhile;
    else :
        ?>
        <p><?php esc_html_e( 'No content found.', 'surajx-gii-theme' ); ?></p>
        <?php
    endif;
    ?>
</div>

<?php
get_footer();
