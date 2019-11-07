<?php

    global $theme;
    global $count;
    global $post;

?>

<a href="<?php the_permalink(); ?>" class="grid-item grid-item--8 kapitel-list-item" style="background-image:url(<?php echo get_the_post_thumbnail_url(); ?>);">
    <div class="kapitel-list-item__overlay">
        
        <span class="kapitel-list-item__kapitel"><?php $theme->e("$count. Kapitel"); ?></span>
        <div class="kapitel-list-item__content">
            <h2 class="kapitel-list-item__headline"><?php $theme->e(get_the_title()); ?></h2>
            <span class="kapitel-list-item__author">geschrieben von <?php $theme->e(get_the_author()); ?></span>
        </div>

    </div> 
</a>