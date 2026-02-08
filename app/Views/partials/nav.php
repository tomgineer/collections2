<nav class="navbar bg-base-100 sticky top-0 z-50">
    <div class="flex-none">
        <button class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </div>
    <div class="flex-1 flex items-center">
        <a href="<?= base_url() ?>" class="btn btn-ghost">
            <img class="h-6 w-auto" src="<?= base_url('gfx/logo.svg') ?>" alt="Collections Logo">
        </a>

        <ul class="flex gap-1">
            <?php foreach ($mediaTypes as $media):?>
                <li>
                    <a class="btn btn-ghost hover:btn-primary" href="<?=site_url('media/' . $media['alias'])?>"><?=esc($media['media_type'])?></a>
                </li>
            <?php endforeach;?>
        </ul>

    </div>
    <div class="flex-none">
        <button class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
        </button>
    </div>
</nav>