<?php $theme = getTheme(); ?>

<div class="grid-item grid-item--12">
    <?php

        the_posts_pagination(array(
            'mid_size'  => 5,
            'prev_text' => $theme->__('vorherige Seite'),
            'next_text' => $theme->__('nächste Seite'),
            'screen_reader_text' => $theme->__('Weitere Beträge')
        ));

    ?>
</div>
<div class="grid-item grid-item--12">
    <?php

        the_posts_pagination(array(
            'mid_size'  => 5,
            'prev_text' => $theme->__('vorherige Seite'),
            'next_text' => $theme->__('nächste Seite'),
            'screen_reader_text' => $theme->__('Weitere Beträge')
        ));

    ?>
</div>