<?php $cur_url = current_url() ?>

<div class="drawer-side z-10 pt-16">
    <label for="site-nav-drawer" aria-label="close sidebar" class="drawer-overlay"></label>

    <ul class="menu bg-base-200 min-h-full w-74 p-4 max-w-full">
        <?php foreach ($mediaTypes as $media): ?>
            <?php $link_url = site_url('media/' . $media['alias']) ?>
            <li>
                <a class="btn justify-start <?=($cur_url === $link_url ? 'btn-secondary' : 'btn-ghost')?>" href="<?=$link_url?>">
                    <?= esc($media['media_type']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
