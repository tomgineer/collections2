<?= $this->extend('layout_main') ?>
<?= $this->section('main') ?>

<!-- Search -->
<section class="max-w-4xl mx-auto p-4 mt-4 lg:mt-12 relative">
    <div class="relative">
        <input
            type="text"
            placeholder="Search anything..."
            class="input input-xl w-full pr-14 focus:outline-secondary focus:border-base-content/40"
            data-js-search />
        <button
            type="button"
            aria-label="Clear search"
            class="btn btn-sm btn-primary absolute right-2 top-1/2 z-10 -translate-y-1/2 hidden"
            data-js-search-clear>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <div class="absolute bottom-0 translate-y-full left-0 z-50 hidden
        bg-base-100 p-4 rounded-lg border border-base-content/40 shadow-lg w-full" data-js-results>
    </div>
</section>

<section class="max-w-7xl mx-auto p-4 mt-1 lg:mt-8">
    <!-- Popular -->
    <ul class="flex flex-wrap gap-1 mb-6 lg:mb-12">
        <?php foreach ($mostPopular as $popular): ?>
            <li>
                <button class="btn btn-sm lg:btn-md" data-search-term="<?= esc($popular['creator']) ?>">
                    <?= esc($popular['creator']) ?>
                    <div class="badge badge-xs lg:badge-sm badge-primary"><?= esc($popular['count']) ?></div>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Media Types -->
    <ul class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4 lg:mb-24">
        <?php foreach ($mediaTypes as $media): ?>
            <?php $link_url = site_url('media/' . $media['alias']) ?>
            <li class="bg-base-200 overflow-hidden rounded-2xl shadow">
                <a class="contents" href="<?= site_url('media/' . $media['alias']) ?>">
                    <div class="group aspect-square overflow-hidden">
                        <img
                            src="<?= base_url('gfx/webp/') . $media['alias'] . '.webp' ?>"
                            alt="<?= $media['alias'] ?>"
                            loading="lazy"
                            class="w-full h-full transition-transform duration-300 ease-out group-hover:scale-110">
                    </div>
                    <h2 class="text-3xl lg:text-5xl font-extrabold tracking-tight bg-gradient-to-r from-primary via-secondary to-accent
                        bg-clip-text text-transparent py-4 px-6">
                        <?= $media['media_type'] ?>
                    </h2>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<?= $this->include('partials/front_text') ?>
<?= $this->endSection() ?>
