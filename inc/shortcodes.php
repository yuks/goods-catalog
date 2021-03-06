<?php

/*
 * Shortcodes to use with the catalog
 */

/*
 * Sitemap
 * Currently under development
 * Usage: [goods_sitemap]
 */

function GoodsSitemap() {
    $terms_args = array(
        'taxonomy' => 'goods_category', // get goods categories
        'orderby' => 'term_group',
        'hierarchical' => 1, // do not hide empty parent categories
    );
    $terms = get_terms('goods_category', $terms_args);
    if ($terms) :
        echo '<ul class="parents">';
        foreach ($terms as $term) :
            if ($parent = $term->parent) :
                $class = 'child';
            else :
                $class = 'parent';
            endif;

            echo '<li class="' . $class . '"><h3><a href="' . get_term_link($term) . '">' . $term->name . '</a></h3></li>';
            $parent = $term->parent;
            echo '<ul>';
            $query_args = array(
                'post_type' => 'goods',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'goods_category',
                        'field' => 'slug',
                        'terms' => $term->slug,
                        'include_children' => 0 // hide post from children categories in parent category
                    )
                )
            );
            $new = new WP_Query($query_args);
            while ($new->have_posts()) :
                $new->the_post();
                echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
            endwhile;
            echo '</ul>';
        endforeach;
        echo '</ul>';
    endif;
}

add_shortcode('goods_sitemap', 'GoodsSitemap');


/*
 * Get newest products
 * Usage: [goods_newest number=3]
 */

// output

function goods_shortcode_output() {
    $output = '';
    $output .= '<div class="grid"><div>'
            . '<div class="goods-item-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></div>'
            . '<div class="goods-item-content">'
            . '<div class="goods-item-thumb-container">';
    $output .= show_the_thumbnail();            
    $output .= show_the_product_price();
    $gc_descr = get_post_meta(get_the_ID(), 'gc_descr', true);
    if ((isset($gc_descr)) && ($gc_descr != '')) {
        $output .= '<p class="goods-descr">' . $gc_descr . '</p>';
    }
    $output .= '</div></div></div></div>';
    return $output;
}

// Add Shortcode
function GoodsNewest($atts) {

    // Attributes
    extract(shortcode_atts(
                    array(
        'number' => '3',
                    ), $atts)
    );

    // Code
    $output = '<div class="goods-insert">';
    $the_query = new WP_Query(array('post_type' => 'goods', 'posts_per_page' => $number));
    if ($the_query->have_posts()):
        while ($the_query->have_posts()):
            $the_query->the_post();
            $output .= goods_shortcode_output();
        endwhile;
    endif;
    wp_reset_query();
    return $output . '</div>';
}

add_shortcode('goods_newest', 'GoodsNewest');



/*
 * Categories Shortcode
 * Usage: [goods_categories]
 * many thanks to Alexander Chizhov & Pineapple Design Studio
 */

function GoodsCategories() {
 $terms_args = array(
  'taxonomy' => 'goods_category', // get goods categories
  'orderby' => 'term_group',
  'hierarchical' => 1, // do not hide empty parent categories
 );
 $terms = get_terms('goods_category', $terms_args);

 if ($terms) :
 echo '<ul>';
  foreach ($terms as $term) :
    echo '<li><a href="' . get_term_link($term) . '">' . $term->name . '</a></li>';
    $parent = $term->parent;
    endforeach;
 echo '</ul>';
 endif;
}
add_shortcode('goods_categories', 'GoodsCategories');

/*
 * Tags Shortcode
 * Usage: [goods_tags]
 */

function GoodsTags() {
 $terms_args = array(
  'taxonomy' => 'goods_tag', // get goods categories
  'orderby' => 'name',
  'hierarchical' => 1, // do not hide empty parent categories
 );
 $terms = get_terms('goods_tag', $terms_args);

 if ($terms) :
 echo '<ul>';
  foreach ($terms as $term) :
    echo '<li><a href="' . get_term_link($term) . '">' . $term->name . '</a></li>';
    $parent = $term->parent;
    endforeach;
 echo '</ul>';
 endif;
}
add_shortcode('goods_tags', 'GoodsTags');
