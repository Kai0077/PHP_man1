<?php

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/Department.php';
require_once ROOT_PATH . '/classes/Employee.php';
require_once ROOT_PATH . '/classes/DepartmentDatabase.php';

$departmentDB = new DepartmentDatabase();
$departmentID = (int) ($_GET['id'] ?? 0);
$department = $departmentDB->getById($departmentID);

if (!$department) {
    $errorMessage = 'Department not found.';
}

$pageTitle = 'View Department';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<nav>
    <ul>
        <li><a href="index.php">Back to Departments</a></li>
        <li><a href="update.php?id=<?= $departmentID ?>">Edit</a></li>
        <li><a href="delete.php?id=<?= $departmentID ?>" onclick="return confirm('Are you sure you want to delete this department?');">Delete</a></li>
    </ul>
</nav>

<main>
    <h1>Department Details</h1>

    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php else: ?>
        <section>
            <p><strong>Name:</strong> <?= htmlspecialchars($department->getName()) ?></p>
        </section>

        <section style="margin-top: 2em;">
            <h2>Employees in this Department</h2>

            <?php $employees = $department->getEmployees(); ?>

            <?php if (empty($employees)): ?>
                <p>No employees in this department.</p>
            <?php else: ?>
                <table border="1" cellpadding="8" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Birth Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?= htmlspecialchars($employee->getFirstName() . ' ' . $employee->getLastName()) ?></td>
                                <td><?= htmlspecialchars($employee->getEmail()) ?></td>
                                <td><?= htmlspecialchars($employee->getBirthDate()->format('Y-m-d')) ?></td>
                                <td>
                                    <a href="../employees/view.php?id=<?= $employee->getId() ?>">View</a> |
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<?php include_once ROOT_PATH . '/public/footer.php'; ?>