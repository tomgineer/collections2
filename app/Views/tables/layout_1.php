<thead>
    <tr>
        <th class="font-heading text-lg lg:text-xl text-base-content"><?= ($alias === 'cds' ? 'Artist' : 'Author') ?></th>
        <th class="font-heading text-lg lg:text-xl text-base-content">Title</th>
        <?php if ($alias === 'cds' || $alias === 'books'): ?>
            <th>&nbsp;</th>
        <?php endif; ?>
    </tr>
</thead>
<tbody>
    <?php foreach ($media as $item): ?>
        <tr>
            <td class="font-semibold">
                <?php if ($item['creator'] === '---'): ?>
                    <span class="badge badge-xs lg:badge-sm badge-dash badge-info h-auto leading-tight text-center py-1">
                        Various Artists
                    </span>
                <?php else: ?>
                    <?= esc($item['creator']) ?>
                <?php endif; ?>
            </td>
            <td class="text-base-content/80"><?= esc($item['title']) ?></td>

            <!-- Search Info -->
            <?php if ($alias === 'cds'): ?>
                <?php
                    $searchCreator = ($item['creator'] === '---') ? 'Various Artists' : $item['creator'];
                    $searchQuery = trim($searchCreator . ' ' . $item['title'] . ' CD tracklist');
                    $googleUrl = 'https://www.google.com/search?' . http_build_query(['q' => $searchQuery]);
                ?>
                <td class="text-right">
                    <a
                        class="btn btn-sm btn-circle btn-soft btn-info"
                        href="<?= esc($googleUrl, 'attr') ?>"
                        target="_blank"
                        rel="noopener noreferrer">

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-8">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 9h.01" />
                            <path d="M11 12h1v4h1" />
                        </svg>
                    </a>
                </td>
            <?php elseif ($alias === 'books'): ?>
                <?php
                    $searchCreator = ($item['creator'] === '---') ? 'Various Authors' : $item['creator'];
                    $searchQuery = trim($searchCreator . ' ' . $item['title'] . ' book');
                    $googleUrl = 'https://www.google.com/search?' . http_build_query(['q' => $searchQuery]);
                ?>
                <td class="text-right">
                    <a
                        class="btn btn-sm btn-circle btn-soft btn-info"
                        href="<?= esc($googleUrl, 'attr') ?>"
                        target="_blank"
                        rel="noopener noreferrer">

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-8">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 9h.01" />
                            <path d="M11 12h1v4h1" />
                        </svg>
                    </a>
                </td>
            <?php endif; ?>
            <!-- /Search Info -->
        </tr>
    <?php endforeach; ?>
</tbody>

