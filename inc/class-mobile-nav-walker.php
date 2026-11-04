<?php
/**
 * MOBILE NAV WALKER
 * Clean expandable menu for mobile only
 */
class Mobile_Nav_Walker extends Walker_Nav_Menu {

    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat("\t", $depth);

        $output .= "\n$indent<ul class=\"sub-menu ml-4 border-l border-gray-300 pl-4 space-y-2\">\n";
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {

        $indent = ( $depth ) ? str_repeat("\t", $depth) : '';
        $classes = empty( $item->classes ) ? [] : (array) $item->classes;

        $has_children = in_array( 'menu-item-has-children', $classes );

        $output .= $indent . '<li class="' . implode(' ', $classes) . '">';

        $atts = [];
        $atts['href'] = ! empty( $item->url ) ? $item->url : '';
        $atts['class'] = 'block py-2';

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $attributes .= " $attr=\"" . esc_attr( $value ) . "\"";
            }
        }

        $title = apply_filters( 'the_title', $item->title, $item->ID );

        $output .= "<a$attributes>$title</a>";
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}



