<?= $this->extend('layout_main') ?>
<?= $this->section('main') ?>

<section class="max-w-4xl mx-auto p-4 mt-8">
    <input type="search" placeholder="Search anything..." class="input input-xl w-full" />
</section>

<section class="max-w-7xl mx-auto p-4 mt-12">
    <ul class="grid grid-cols-4 gap-4">
        <?php foreach ($mediaTypes as $media): ?>
            <?php $link_url = site_url('media/' . $media['alias']) ?>
            <li class="group rounded-2xl overflow-hidden shadow border-2 border-base-300 hover:border-orange-500">
                <a class="contents" href="<?= site_url('media/' . $media['alias']) ?>">
                    <img
                        src="<?= base_url('gfx/webp/') . $media['alias'] . '.webp' ?>"
                        alt="<?= $media['alias'] ?>"
                        loading="lazy"
                        class="w-full h-full object-cover transition-transform duration-300 ease-out group-hover:scale-110">
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

<section class="prose prose-lg mx-auto mt-12">
    <h1>Tom's Collection</h1>
    <p>
        I love physical media because it feels real. A disc, a cartridge, a book on a shelf has weight, history, and presence in a way a download never will. Physical media does not disappear because a license expires, a server goes offline, or a platform changes its mind. <strong>It is mine, permanently</strong>. Every scratch, cover design, manual, and case tells a story about when and how it was made, and when I found it. Collecting is not just about consuming content, it is about preserving moments, formats, and the feeling of discovery that comes with holding something tangible.
    </p>
    <p>
        Owning physical media is also a smart long-term choice. It offers independence from subscriptions, algorithms, and digital decay. Formats may change, but a well-kept collection remains accessible, tradable, and often even increases in value over time. Physical media encourages intentional listening, watching, and playing instead of endless scrolling. It slows things down in a good way, turning media into an experience rather than background noise. <strong>This collection exists to celebrate ownership</strong>, craftsmanship, and the joy of building something that lasts.
    </p>
</section>


<?= $this->endSection() ?>