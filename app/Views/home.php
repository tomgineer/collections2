<?= $this->extend('layout_main') ?>
<?= $this->section('main') ?>

<section class="max-w-4xl mx-auto p-4 mt-4 lg:mt-8">
    <input type="search" placeholder="Search anything..." class="input input-xl w-full" data-js-search />
    <div data-js-results></div>
</section>

<section class="max-w-7xl mx-auto p-4 mt-4 lg:mt-8" data-section="main-selection">

    <ul class="flex flex-wrap gap-1 mb-8">
        <?php foreach ($mostPopular as $popular):?>
            <li>
                <a class="btn" href="<?=site_url('media/cds')?>">
                    <?=esc($popular['creator'])?> (<?=esc($popular['count'])?>)
                </a>
            </li>
        <?php endforeach;?>
    </ul>

    <ul class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <?php foreach ($mediaTypes as $media): ?>
            <?php $link_url = site_url('media/' . $media['alias']) ?>
            <li class="">
                <a class="contents" href="<?= site_url('media/' . $media['alias']) ?>">
                    <div class="group aspect-square overflow-hidden rounded-t-lg lg:rounded-t-2xl shadow">
                        <img
                            src="<?= base_url('gfx/webp/') . $media['alias'] . '.webp' ?>"
                            alt="<?= $media['alias'] ?>"
                            loading="lazy"
                            class="w-full h-full object-cover transition-transform duration-300 ease-out group-hover:scale-110">
                    </div>
                    <h2 class="text-xl lg:text-3xl bg-base-300 text-base-content/70 py-4 px-6 rounded-b-lg lg:rounded-b-2xl shadow">
                        <?=$media['media_type']?>
                    </h2>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<?= $this->include('partials/front_text') ?>
<?= $this->endSection() ?>
