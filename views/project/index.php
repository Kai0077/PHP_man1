<?php

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/Project.php';
require_once ROOT_PATH . '/classes/ProjectDatabase.php';

$projectDB = new ProjectDatabase();

$searchText = trim($_GET['search'] ?? '');

if ($searchText === '') {
    $projects = $projectDB->getAll();
} else {
    $projects = $projectDB->search($searchText);
}

if ($projects === false) {
    $errorMessage = 'There was an error retrieving the list of projects.';
}

$pageTitle = 'Projects';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<main>
    <section class="search-bar">
        <form action="index.php" method="get">
            <label for="search">Search:</label>
            <input type="search" id="search" name="search" value="<?= htmlspecialchars($searchText) ?>" placeholder="Enter project name or keyword">
            <button type="submit">Search</button>
        </form>
    </section>

    <section class="action-bar" style="margin-top: 1em;">
        <form action="new.php" method="get">
            <button type="submit">Add New Project</button>
        </form>
    </section>

    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <?php if (empty($projects)): ?>
        <p>No projects found.</p>
    <?php else: ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <tr>
                        <td><?= htmlspecialchars($project->getName()) ?></td>
                        <td>
                            <a href="view.php?id=<?= $project->getId() ?>">View</a> |
                            <a href="update.php?id=<?= $project->getId() ?>">Edit</a> |
                            <a href="delete.php?id=<?= $project->getId() ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<?php include_once ROOT_PATH . '/public/footer.php'; ?>