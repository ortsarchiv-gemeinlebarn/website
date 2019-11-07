<?php $theme = getTheme(); ?>

<a href="<?php the_permalink(); ?>" class="grid-item grid-item--24 post-list-item">
    <h2 class="post-list-item__headline"><?php $theme->e(get_the_title()); ?></h2>
    <div class="post-list-item__meta">
        <span class="post-list-item__type"><?php echo $theme->getPostType(get_post_type()); ?></span>
        <span class="post-list-item__divider">&bull;</span>
        <span class="post-list-item__date"><?php $theme->e(get_the_date('d. F Y')); ?></span>
        <span class="post-list-item__divider">&bull;</span>
        <span class="post-list-item__author"><?php $theme->e(get_the_author()); ?></span>
    </div>
</a>