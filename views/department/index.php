<?php

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/Department.php';
require_once ROOT_PATH . '/classes/DepartmentDatabase.php';

$departmentDB = new DepartmentDatabase();

$searchText = trim($_GET['search'] ?? '');

if ($searchText === '') {
    $departments = $departmentDB->getAll();
} else {
    $departments = $departmentDB->search($searchText);
}

if ($departments === false) {
    $errorMessage = 'There was an error retrieving the list of departments.';
}

$pageTitle = 'Departments';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<main>

    <section class="search-bar">
        <form action="index.php" method="get">
            <label for="search">Search:</label>
            <input type="search" id="search" name="search" value="<?= htmlspecialchars($searchText) ?>" placeholder="Enter department name">
            <button type="submit">Search</button>
        </form>
    </section>

    <section class="action-bar" style="margin-top: 1em;">
        <form action="new.php" method="get">
            <button type="submit"> Add New Department</button>
        </form>
    </section>

    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <?php if (empty($departments)): ?>
        <p>No departments found.</p>
    <?php else: ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departments as $department): ?>
                    <tr>
                        <td><?= htmlspecialchars($department->getName()) ?></td>
                        <td>
                            <a href="view.php?id=<?= $department->getId() ?>">View</a> |
                            <a href="update.php?id=<?= $department->getId() ?>">Edit</a> |
                            <a href="delete.php?id=<?= $department->getId() ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<?php include_once ROOT_PATH . '/public/footer.php'; ?>