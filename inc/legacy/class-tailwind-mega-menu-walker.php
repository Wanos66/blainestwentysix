<?php
class Tailwind_Mega_Walker extends Walker_Nav_Menu {

    // OPEN SUBMENU LEVEL
    public function start_lvl( &$output, $depth = 0, $args = null ) {

        // MEGA MENU (only depth=0 for Electrical)
        if ( $depth === 0 && $args->menu->slug === 'electrical' ) {

            $output .= '
            <div class="absolute left-0 top-full w-screen bg-white shadow-lg border-t z-50 py-10 mega-wrap">
                <div class="container-wide mx-auto grid grid-cols-6 gap-10 px-8">
            ';

        }
        // SIMPLE DROPDOWN (Showrooms)
        elseif ( $depth === 0 ) {

            $output .= '
            <ul class="absolute left-0 top-full bg-white shadow-lg border z-50 min-w-[200px] py-3 space-y-1 hidden group-hover:block">
            ';

        }
        // SUB-SUBMENU
        else {
            $output .= '<ul class="ml-4 space-y-2">';
        }
    }

    // CLOSE SUBMENU
    public function end_lvl( &$output, $depth = 0, $args = null ) {

        if ( $depth === 0 && $args->menu->slug === 'electrical' ) {
            $output .= '</div></div>';
        }
        else {
            $output .= '</ul>';
        }
    }

    // OPEN ITEM
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {

        $title = esc_html( $item->title );
        $url   = esc_url( $item->url );
        $classes = $item->classes;

        $is_heading = in_array( 'menu-item-has-children', $classes );

        // ELECTRICAL HEADINGS (Laundry, Refrigeration…)
        if ( $depth === 0 && $args->menu->slug === 'electrical' ) {

            $output .= '
                <div class="space-y-3">
                    <a class="font-semibold text-gray-900 block hover:text-blue-600" href="' . $url . '">' . $title . '</a>
                ';

        }
        // ELECTRICAL SUBLINKS
        elseif ( $depth === 1 && $args->menu->slug === 'electrical' ) {

            $output .= '<a class="block text-sm text-gray-600 hover:text-blue-600" href="' . $url . '">' . $title . '</a>';

        }
        // SHOWROOMS SIMPLE DROPDOWN
        elseif ( $depth === 1 && $args->menu->slug === 'showrooms' ) {

            $output .= '<li><a class="block px-4 py-2 hover:bg-gray-100" href="' . $url . '">' . $title . '</a></li>';

        }
    }

    // CLOSE ITEM
    public function end_el( &$output, $item, $depth = 0, $args = null ) {

        if ( $depth === 0 && $args->menu->slug === 'electrical' ) {
            $output .= '</div>'; // closing column
        }
    }
}

