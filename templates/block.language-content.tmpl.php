<?php 
/**
 * template file for rendering reviews-slider block on frontend dynamically
 */
$lang = $attributes['lang'] ?? 'el';
$current_lang = \pll_current_language('slug');

if( !function_exists('pll_current_language') || $lang === $current_lang ) :

    echo $content;

endif;
?>
