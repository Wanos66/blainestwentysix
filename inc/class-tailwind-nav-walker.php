<?php
/**
 * Updated code 12:33 PM 2025-11-23.
 */

class Tailwind_Nav_Walker extends Walker_Nav_Menu {

    private $current_item_is_mega = false;
    private $current_submenu_id   = '';

    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat("\t", $depth);

        // Add submenu ID only for top-level items
        $id_attr = '';
        if ($depth === 0 && $this->current_submenu_id) {
            $id_attr = ' id="' . esc_attr($this->current_submenu_id) . '" aria-label="Submenu"';
        }

        if ( 0 === $depth ) {
            if ( $this->current_item_is_mega ) {

                // DO NOT CHANGE ANY TAILWIND CLASSES BELOW — this preserves your working state
                $output .= "\n$indent<ul$id_attr class=\"sub-menu mega-menu-panel nav-mega opacity-0 invisible pointer-events-none translate-y-[6px] transition-all duration-200 ease-out group-hover:opacity-100 group-hover:visible group-hover:pointer-events-auto group-hover:translate-y-0 [.mega-open_&]:opacity-100 [.mega-open_&]:visible [.mega-open_&]:pointer-events-auto [.mega-open_&]:translate-y-0 \">\n";

            } else {
                $output .= "\n$indent<ul$id_attr class=\"sub-menu dropdown-panel\">\n";
            }

        } else {
            $output .= "\n$indent<ul class=\"sub-menu mega-sub\">\n";
        }
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {

        $indent      = str_repeat("\t", $depth);
        $classes     = empty($item->classes) ? [] : (array) $item->classes;
        $has_children = in_array('menu-item-has-children', $classes, true);

        // Mega-menu detection
        if ( 0 === $depth ) {
            $title   = trim($item->title);
            $is_mega = $has_children && ( $title === 'Electrical' || in_array('mega-menu', $classes, true) );
            $this->current_item_is_mega = $is_mega;

            if ( $is_mega ) {
                $classes[] = 'has-mega';
                $classes[] = 'group';
            }

            // Set submenu ID for ARIA linking
            if ( $has_children ) {
                $this->current_submenu_id = 'submenu-' . $item->ID;
            } else {
                $this->current_submenu_id = '';
            }
        }

        // Depth class for styling
        $classes[]   = 'menu-item-depth-' . $depth;
        $class_names = trim(implode(' ', array_filter($classes)));
        $class_attr  = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        // Add data attribute for mega-menu JS hooks
        $extra = ($depth === 0 && $this->current_item_is_mega) ? ' data-has-mega' : '';

        // <li> gets role="none"
        $output .= $indent . '<li' . $class_attr . $extra . ' role="none">';

        // Link attributes
        $atts = [];
        $atts['id']     = 'menuitem-' . $item->ID;
        $atts['href']   = ! empty( $item->url ) ? $item->url : '';
        $atts['class']  = 'block px-3 py-2';
        $atts['role']   = 'menuitem';

        // Only top-level items with children get ARIA
        if ( 0 === $depth && $has_children ) {
            $atts['aria-haspopup'] = 'true';
            $atts['aria-expanded'] = 'false';

            if ($this->current_submenu_id) {
                $atts['aria-controls'] = $this->current_submenu_id;
            }
        }

        // Assemble link attributes
        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( $value !== '' ) {
                $attributes .= ' ' . $attr . '="' . esc_attr($value) . '"';
            }
        }

        $title     = esc_html( $item->title );
        $item_html = "<a$attributes>$title</a>";

        $output .= $item_html;
    }

    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>\n";
    }
}
