<?php get_header(); ?>
<div class="container">
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <h1 class="entry-title"><?php the_title(); ?></h1>
    <div class="entry-content"><?php the_content(); ?></div>
  </article>
<?php endwhile; else: ?>
  <p><?php esc_html_e('No content found. Assign a static front page or add posts.', 'jeanneau-lite-theme'); ?></p>
<?php endif; ?>
</div>
<?php get_footer(); ?>
