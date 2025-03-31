<?php

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/Project.php';
require_once ROOT_PATH . '/classes/Employee.php';
require_once ROOT_PATH . '/classes/ProjectDatabase.php';

$projectDB = new ProjectDatabase();
$projectID = (int) ($_GET['id'] ?? 0);
$project = $projectDB->getById($projectID);

if (!$project) {
    $errorMessage = 'Project not found.';
}

$pageTitle = 'View Project';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<main>
    <h1>Project Details</h1>

    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php else: ?>
        <section>
            <p><strong>Name:</strong> <?= htmlspecialchars($project->getName()) ?></p>
        </section>

        <section style="margin-top: 2em;">
            <h2>Assigned Employees</h2>

            <?php $employees = $project->getEmployees(); ?>

            <?php if (empty($employees)): ?>
                <p>No employees assigned to this project.</p>
            <?php else: ?>
                <table border="1" cellpadding="8" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?= htmlspecialchars($employee->getFirstName() . ' ' . $employee->getLastName()) ?></td>
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