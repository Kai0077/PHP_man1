<?php

require_once '../../initialise.php';
require_once ROOT_PATH . '/classes/Department.php';
require_once ROOT_PATH . '/classes/DepartmentDatabase.php';

// Create a new department database object
$departmentDB = new DepartmentDatabase();

// Get the search text from the URL (if available), or use an empty string
$searchText = trim($_GET['search'] ?? '');

// If no search text, get all departments
if ($searchText === '') {
    $departments = $departmentDB->getAll();
} else {
    // Otherwise, search for departments by name
    $departments = $departmentDB->search($searchText);
}

// If something went wrong when getting departments, set an error message
if ($departments === false) {
    $errorMessage = 'There was an error retrieving the list of departments.';
}

// Set the page title and load the page header and navigation
$pageTitle = 'Departments';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<main>

    <!-- Search form -->
    <section class="search-bar">
        <form action="index.php" method="get">
            <label for="search">Search:</label>
            <input type="search" id="search" name="search" 
                   value="<?= htmlspecialchars($searchText) ?>" 
                   placeholder="Enter department name">
            <button type="submit">Search</button>
        </form>
    </section>

    <!-- Button to go to the 'Add New Department' page -->
    <section class="action-bar" style="margin-top: 1em;">
        <form action="new.php" method="get">
            <button type="submit">Add New Department</button>
        </form>
    </section>

    <!-- Show error message if something went wrong -->
    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <!-- If there are no departments, show a message -->
    <?php if (empty($departments)): ?>
        <p>No departments found.</p>
    <?php else: ?>
        <!-- Show the departments in a table -->
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop through each department and show its name and actions -->
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

<!-- Include the footer at the bottom of the page -->
<?php include_once ROOT_PATH . '/public/footer.php'; ?>