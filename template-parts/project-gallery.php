<?php
/**
 * Project Gallery – main image + thumbnails
 *
 * ACF repeater: project_gallery
 *   - sub field: image (Image, return = Array)
 */

$images        = get_field('project_gallery');

if ($images && is_array($images) && count($images)) :

    // Use first image as initial main image
    $first = $images[0]['image'];
    $first_url = $first['url'] ?? '';
    $first_alt = $first['alt'] ?? '';
    ?>
    <div class="space-y-4" data-project-gallery>
        <!-- MAIN IMAGE -->
        <div class="project-main-wrapper relative rounded-xs overflow-hidden shadow-md opacity-100"
                data-project-main-wrapper>
        <img src="<?php echo esc_url($first_url); ?>" alt="<?php echo esc_attr($first_alt); ?>" class="w-full h-auto object-cover block" data-project-main-image/>
        </div>

        <!-- THUMBNAILS -->
        <div class="flex gap-3 overflow-x-auto pb-1">
            <?php foreach ($images as $index => $row) :
                $img = $row['image'];
                $url = $img['url'] ?? '';
                $alt = $img['alt'] ?? '';
                $is_active = (0 === $index);
                ?>
                <button type="button" class="relative flex-shrink-0 w-24 h-24 rounded-xs overflow-hidden border transition
    <?php echo $is_active ? 'border-transparent opacity-100' : 'border-gray-300 opacity-70'; ?>
    hover:opacity-100 focus:outline-none" data-project-thumb data-full="<?php echo esc_url($url); ?>" data-alt="<?php echo esc_attr($alt); ?>">

                    <img src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($alt); ?>" class="w-full h-full object-cover"/>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
<?php
endif;
