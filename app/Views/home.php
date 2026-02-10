<?= $this->extend('layout_main') ?>
<?= $this->section('main') ?>

<!-- Search -->
<section class="max-w-4xl mx-auto p-4 mt-4 lg:mt-12 relative">
    <input
        type="search"
        placeholder="Search anything..."
        class="input input-xl w-full focus:outline-secondary focus:border-base-content/40"
        data-js-search />
    <div class="absolute bottom-0 translate-y-full left-0 z-50 hidden
        bg-base-100 p-4 rounded-lg border border-base-content/40 shadow-lg w-full" data-js-results>
    </div>
</section>

<section class="max-w-7xl mx-auto p-4 mt-4 lg:mt-8">
    <!-- Popular -->
    <ul class="flex flex-wrap gap-1 mb-6 lg:mb-12">
        <?php foreach ($mostPopular as $popular): ?>
            <li>
                <button class="btn" data-search-term="<?= esc($popular['creator']) ?>">
                    <?= esc($popular['creator']) ?>
                    <div class="badge badge-sm badge-primary"><?= esc($popular['count']) ?></div>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>

    <!-- Media Types -->
    <ul class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-12 lg:mb-24">
        <?php foreach ($mediaTypes as $media): ?>
            <?php $link_url = site_url('media/' . $media['alias']) ?>
            <li class="bg-base-200 overflow-hidden rounded-2xl shadow">
                <a class="contents" href="<?= site_url('media/' . $media['alias']) ?>">
                    <div class="group aspect-square">
                        <img
                            src="<?= base_url('gfx/webp/') . $media['alias'] . '.webp' ?>"
                            alt="<?= $media['alias'] ?>"
                            loading="lazy"
                            class="w-full h-full object-cover transition-transform duration-300 ease-out group-hover:scale-110">
                    </div>
                    <h2 class="text-2xl lg:text-5xl font-extrabold tracking-tight bg-gradient-to-r from-primary via-secondary to-accent
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