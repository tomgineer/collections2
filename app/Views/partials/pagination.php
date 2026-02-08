<?php if (($totalPages ?? 1) > 1): ?>
    <nav class="max-w-xl mx-auto flex justify-center">
        <div class="join">
            <?php if (($page ?? 1) > 1): ?>
                <a class="join-item btn btn-lg btn-square hover:btn-secondary" href="<?= site_url('media/' . $alias) . '?page=' . ((int) $page - 1) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
            <?php endif; ?>

            <button class="join-item btn btn-lg font-heading text-base-content/70 text-base" aria-current="page">
                Page <?= (int) ($page ?? 1) ?> / <?= (int) ($totalPages ?? 1) ?>
            </button>

            <?php if (($page ?? 1) < ($totalPages ?? 1)): ?>
                <a class="join-item btn btn-lg btn-square hover:btn-secondary" href="<?= site_url('media/' . $alias) . '?page=' . ((int) $page + 1) ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </nav>
<?php endif; ?>