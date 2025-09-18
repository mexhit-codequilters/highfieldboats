<?php
/**
 * Template for the site front page using provided index.html snapshot.
 */
get_header();
jeanneau_lite_render_static('front-page');
get_footer();
$home_url = home_url('/');

// Resolve common pages safely by slug, with a sensible fallback if the page doesn't exist yet.
function cq_link_by_slug($slug, $fallback_path = null) {
    $p = get_page_by_path($slug, OBJECT, 'page');
    if ($p instanceof WP_Post) {
        return get_permalink($p->ID);
    }
    // Fallback to a path if provided (e.g., '/boats/sailboat'), else assume '/{slug}'
    $fallback_path = $fallback_path ?: '/' . ltrim($slug, '/');
    return home_url($fallback_path);
}

// Edit the slugs below to match your actual page slugs in WP
$about_url      = cq_link_by_slug('about-us');                 // fallback: /about-us
$powerboats_url = cq_link_by_slug('powerboats');               // fallback: /powerboats
$sailboats_url  = cq_link_by_slug('sailboats', '/boats/sailboat'); // fallback provided to match your structure

// Optional: sub-ranges (set these slugs to your actual pages or taxonomy archives)
$cap_camarat_url       = cq_link_by_slug('cap-camarat');
$db_yachts_url         = cq_link_by_slug('db-yachts');
$merry_fisher_url      = cq_link_by_slug('merry-fisher');
$merry_fisher_sport_url= cq_link_by_slug('merry-fisher-sport');

$sun_odyssey_url       = cq_link_by_slug('sun-odyssey');
$jeanneau_yachts_url   = cq_link_by_slug('jeanneau-yachts');
$sun_fast_url          = cq_link_by_slug('sun-fast');
?>