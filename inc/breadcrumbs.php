<?php

/*
 * Breadcrumbs
 * based on http://snipplr.com/view/57988/ and https://gist.github.com/TCotton/4723438
 */

function get_term_parents($id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = array()) {
    $chain = '';
    $parent = &get_term($id, $taxonomy);
    try {
        if (is_wp_error($parent)) {
            throw new Exception('is_wp_error($parent) has throw error ' . $parent->get_error_message());
        }
    } catch (exception $e) {
        echo __('Caught exception: ', 'gcat'), $e->getMessage(), "\n";
// use something less drastic than die() in production code
//die();
    }
    if ($nicename) {
        $name = $parent->slug;
    } else {
        $name = htmlspecialchars($parent->name, ENT_QUOTES, 'UTF-8');
    }
    if ($parent->parent && ($parent->parent != $parent->term_id) && !in_array($parent->parent, $visited)) {
        $visited[] = $parent->parent;
        $chain .= get_term_parents($parent->parent, $taxonomy, $link, $separator, $nicename, $visited);
    }
    if ($link) {
        $chain .= '<a href="' . get_term_link($parent->slug, $taxonomy) . '">' . $name . '</a>' . $separator;
    } else {
        $chain .= $parent->name . $separator;
    }
    return $chain;
}

function get_tag_id($tag) {
    global $wpdb;
    $link_id = $wpdb->get_var($wpdb->prepare("SELECT term_id FROM $wpdb->terms WHERE name =  %s", $tag));
    return $link_id;
}

function my_breadcrumb($id = null) {
    echo '<a href=" ' . home_url() . ' ">' . __('Home') . '</a> &gt; ';
    echo '<a href="';
//echo '/catalog';
    echo get_post_type_archive_link('goods');
    echo '">';
    echo __('Catalog', 'gcat');
    echo "</a> &gt; ";
    if (is_category() || is_single() || is_tax()) {
        if (is_single()) {
            $cat = get_the_term_list(isset($post->ID), 'goods_category', '', ', ', '');
// make sure uncategorised is not used
            if (!stristr($cat, 'Uncategorized')) {
                echo $cat;
                echo '  &gt; ';
            }
            echo '<a href="' . get_permalink(get_the_ID()) . '">';
            the_title();
            echo '</a>';
        }
        if (is_category()) {
            $cat = get_category_parents(get_query_var('cat'), true, ' &gt; ');
// remove last &gt;
            echo preg_replace('/&gt;\s$|&gt;$/', '', $cat);
        }
        if (is_tax()) {
            $tag = single_tag_title('', false);
            $tag = get_tag_id($tag);
            $term = get_term_parents($tag, get_query_var('taxonomy'), true, ' &gt; ');
// remove last &gt;
            echo preg_replace('/&gt;\s$|&gt;$/', '', $term);
        }
    } elseif (is_page()) {
        if ($id != null) {
            $an = get_post_ancestors($id);
            if (isset($an['0'])) {
                $parent = '<a href="' . get_permalink($an['0']) . '">' . ucwords(get_the_title($an['0'])) . '</a>';
                echo!is_null($parent) ? $parent . " &gt; " : null;
            }
            $parent = get_the_title($id);
            $parent = '<a href="' . get_permalink($id) . '">' . ucwords($parent) . '</a>';
            echo!is_null($parent) ? $parent . " &gt; " : null;
        }
        echo '<a href="' . get_permalink(get_the_ID()) . '">';
        ucwords(the_title());
        echo '</a>';
    }
}

