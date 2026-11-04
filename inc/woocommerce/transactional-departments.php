<?php
/**
 * Electrical transactional department configuration.
 *
 * @package Blaines
 */

defined( 'ABSPATH' ) || exit;

function blaines_get_transactional_departments(): array {

    return [
        'laundry' => [
            'title'       => 'Laundry',
            'intro'       => 'Browse washing machines, tumble dryers and washer dryers available to buy online from Blaines, with local delivery or collection.',
            'image_id'    => 1472,
            'label'       => 'Washing machines · Dryers',
        ],

        'refrigeration' => [
            'title'       => 'Refrigeration',
            'intro'       => 'Browse fridges, freezers, fridge freezers, wine coolers and American style refrigeration available to buy online from Blaines, with local delivery or collection.',
            'image_id'    => 1473,
            'label'       => 'Cooling · Freezing',
        ],

        'dishwashers' => [
            'title'       => 'Dishwashers',
            'intro'       => 'Browse full size and slimline dishwashers available to buy online from Blaines, with local delivery or collection.',
            'image_id'    => 1474,
            'label'       => 'Full size · Slimline',
        ],

        'cooking' => [
            'title'       => 'Cooking',
            'intro'       => 'Browse cookers, ovens, hobs, microwaves and small cooking appliances available to buy online from Blaines, with local delivery or collection.',
            'image_id'    => 1475,
            'label'       => 'Ovens · Hobs · Cookers',
        ],

        'small-appliances' => [
            'title'       => 'Small Appliances',
            'intro'       => 'Browse kettles, toasters, food preparation and small cooking appliances available to buy online from Blaines.',
            'image_id'    => 1476,
            'label'       => 'Everyday essentials',
        ],

        'floorcare' => [
            'title'       => 'Floorcare',
            'intro'       => 'Browse vacuum cleaners, steam cleaners and carpet washers available to buy online from Blaines.',
            'image_id'    => 0, // Add your Floorcare image ID.
            'label'       => 'Vacuum cleaners · Floorcare',
        ],

        'tv-entertainment' => [
            'title'       => 'TV & Entertainment',
            'intro'       => 'Browse televisions, soundbars, home cinema, audio and TV accessories available to buy online from Blaines, with local delivery or collection.',
            'image_id'    => 1477,
            'label'       => 'TV · Audio · Cinema',
        ],
    ];
}
