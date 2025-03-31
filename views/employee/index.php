<?php

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/EmployeeDatabase.php';

$searchText = trim($_GET['search'] ?? '');

$employeeDB = new EmployeeDB();
if ($searchText === '') {
    $employees = $employeeDB->getAll();
} else {
    $employees = $employeeDB->search($searchText);
}
if (!$employees) {
    $errorMessage = 'There was an error while retrieving the list of employees.';
}

$pageTitle = 'Employees';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';
?>

    <main>
    <section class="search-bar">
        <form action="index.php" method="get">
            <label for="txtSearch">Search:</label>
            <input type="search" id="txtSearch" name="search" placeholder="Enter name or keyword">
            <button type="submit">Search</button>
        </form>
    </section>

    <?php if (empty($employees)): ?>
        <section class="no-results">
            <p class="error">No employees found.</p>
        </section>
    <?php endif; ?>

    <section class="action-bar" style="margin-top: 1em;">
        <form action="new.php" method="get">
            <button type="submit">Add New Employee</button>
        </form>
    </section>

    <?php if (!empty($employees)): ?>
        <section class="employee-table" style="margin-top: 1em;">
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Birth Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td><?= htmlspecialchars($employee->getFirstName()) ?></td>
                            <td><?= htmlspecialchars($employee->getLastName()) ?></td>
                            <td><?= htmlspecialchars($employee->getBirthDate()->format('Y-m-d')) ?></td>
                            <td>
                                <a href="view.php?id=<?= htmlspecialchars($employee->getId()) ?>">View</a>
                                |
                                <a href="update.php?id=<?= htmlspecialchars($employee->getId()) ?>">Edit</a>
                                |
                                <a href="delete.php?id=<?= htmlspecialchars($employee->getId()) ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php endif; ?>
</main>
<?php include_once ROOT_PATH . '/public/footer.php'; ?>