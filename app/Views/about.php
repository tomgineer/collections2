<?= $this->extend('layout_main') ?>
<?= $this->section('main') ?>

<section class="max-w-4xl mx-auto p-4 my-8 lg:my-16 pb-24">
    <article class="grid lg:grid-cols-[auto_1fr] lg:gap-24">
        <div class="order-1 lg:order-2">
            <div class="prose lg:prose-lg max-w-none">

                <img class="h-12 lg:h-16 w-auto" src="<?= base_url('gfx/logo.svg?v=' . SYS_VERSION) ?>" alt="Collections Logo">

                <h1>The Story</h1>

                <p>
                    When I started my collection, as expected, I had only a few CDs, a few movies, books, and issues of Arkas. I remembered exactly what I owned, and when I wanted to buy something it was easy to avoid buying something I already had.
                </p>

                <p>
                    The first collection that began to grow, and which I have been maintaining for at least 30 years, was Arkas. The result was that I eventually ended up with quite a few duplicate issues.
                </p>

                <p>
                    The problem became bigger when I started collecting CDs, which was also a hobby that began at a very young age. At some point my collection was destroyed, and I rebuilt it, this time twice as large. It became impossible to remember what I had already bought, which resulted in me buying some duplicates.
                </p>

                <p>
                    All my notes are in Obsidian, so naturally I created four Markdown files there, one for each category (CDs, Blu-Rays, Books, Arkas). The process was extremely simple and still works to this day. I created one table per page and simply entered the discs or anything else I bought. I would not put the CDs in their place, for example, until I had recorded them. Since we are talking about plain text files, this only takes a few minutes. I also kept the format very simple, such as Artist | Title. In my life, simple things work; complex ones I stop using after a while.
                </p>

                <p>
                    However, I had one problem. I could not view my lists on my phone when I was in a store. No big deal, I thought; I would write a PHP application that reads these files and displays them on a website. Which is exactly what I did. I converted the files inside Obsidian using the Webpage HTML Export extension by Nathan George, and the PHP system simply stripped out the junk code and displayed them on a page. This is how the Project Collections was created, which I have on GitHub:
                    <a href="https://github.com/tomgineer/collections" target="_blank" rel="noopener noreferrer">https://github.com/tomgineer/collections</a>.
                    There was no database, no users, no administration, nothing. It was simple, and that is why it worked. Just a mechanism that scanned for changes in the HTML files I uploaded.
                </p>

                <p>
                    It worked well, but it did not have a database. As the size of my collection grew, searches and other things I wanted to do started to not work the way I wanted. So I decided that it was time to add a database, include my favorite framework, and turn it into a more serious project. I rewrote the whole thing in three days. That is how the Collections II Project was created:
                    <a href="https://github.com/tomgineer/collections2" target="_blank" rel="noopener noreferrer">https://github.com/tomgineer/collections2</a>,
                    which is what you are looking at now.
                </p>

                <p>
                    It now works exactly the way I want. It still scans HTML files that I export from Obsidian, but this time it separates them nicely into categories and stores them in the database.
                </p>

                <p>
                    This simple little system works perfectly. I continue to enter products in Obsidian, meaning I always have them in the classic Markdown format that I like, and then I simply export them and they appear on the World Wide Web.
                </p>

            </div>
        </div>

        <div class="order-2 lg:order-1 flex flex-col gap-4 lg:gap-8">
            <?php for ($i = 1; $i <= 11; $i++): ?>
                <img
                    class="w-full lg:w-[300px] aspect-square rounded-3xl border-2 border-base-100 shadow-2xl"
                    src="<?= base_url('gfx/webp/about_' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '.webp?v='.SYS_VERSION) ?>"
                    alt="About Picture"
                    loading="lazy">
            <?php endfor; ?>
        </div>
    </article>
</section>

<?= $this->endSection() ?>
