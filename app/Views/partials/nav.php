<?php $cur_url = current_url() ?>

<nav class="navbar bg-base-100 border-b border-base-content/10 fixed top-0 z-50">
    <div class="flex-none lg:hidden">
        <label for="site-nav-drawer" aria-label="open sidebar" class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </label>
    </div>

    <div class="flex-1 flex items-center">
        <a href="<?= base_url() ?>" class="btn btn-ghost">
            <img class="h-6 w-auto" src="<?= base_url('gfx/logo.svg') ?>" alt="Collections Logo">
        </a>

        <ul class="hidden lg:flex lg:gap-1">
            <?php foreach ($mediaTypes as $media): ?>
                <?php $link_url = site_url('media/' . $media['alias']) ?>
                <li>
                    <a class="btn hover:btn-primary <?=($cur_url===$link_url?'btn-secondary':'btn-ghost')?>" href="<?=$link_url?>">
                        <?= esc($media['media_type']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="flex-none dropdown dropdown-end">
        <label tabindex="0" class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z" />
            </svg>
        </label>

        <ul tabindex="0"
            class="dropdown-content menu mt-2 p-2 w-40
                bg-base-300/60
                backdrop-blur-lg
                border border-base-content/10
                ring-1 ring-white/5
                shadow-2xl
                rounded-xl">
            <li>
                <a href="<?=site_url('about')?>">About</a>
            </li>
        </ul>
    </div>
</nav>

