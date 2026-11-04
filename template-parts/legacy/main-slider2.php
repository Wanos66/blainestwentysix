<?php
$rows = get_field('home_slider');
if ($rows) { ?>

    <div class="slider">
        <!-- fade css -->

        <?php foreach ($rows as $row) {
            $image = $row['desktop_image']; ?>
            <div class="myslide fade">
                <img class="slider__image" src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" style="width: 100%; height: auto;">
            </div>
        <?php } ?>

        <a class="prev">&#10094;</a> <a class="next">&#10095;</a>

        <div class="dotsbox" style="text-align:center">
            <?php for ($i = 0; $i < count($rows); $i++) { ?>
                <span class="dot"></span>
            <?php } ?>
        </div>

    </div>

<?php } ?>
