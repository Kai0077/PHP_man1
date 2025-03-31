<?php

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/Project.php';
require_once ROOT_PATH . '/classes/ProjectDatabase.php';

$projectDB = new ProjectDatabase();
$projectID = (int) ($_GET['id'] ?? 0);
$project = $projectDB->getById($projectID);

if (!$project) {
    $errorMessage = 'Project not found.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['confirm'] === 'yes') {
        if ($projectDB->delete($projectID)) {
            header('Location: index.php');
            exit;
        } else {
            $errorMessage = 'Failed to delete project.';
        }
    } else {
        header('Location: index.php');
        exit;
    }
}

$pageTitle = 'Delete Project';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<nav>
    <ul>
        <li><a href="index.php">Back to Projects</a></li>
    </ul>
</nav>

<main>
    <h1>Delete Project</h1>

    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php else: ?>
        <p>Are you sure you want to delete the project <strong><?= htmlspecialchars($project->getName()) ?></strong>?</p>

        <?php $employees = $project->getEmployees(); ?>

        <?php if (!empty($employees)): ?>
            <p>This project has the following assigned employees:</p>
            <ul>
                <?php foreach ($employees as $employee): ?>
                    <li><?= htmlspecialchars($employee->getFirstName() . ' ' . $employee->getLastName()) ?></li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Note:</strong> This action will also remove all employee assignments.</p>
        <?php else: ?>
            <p>No employees are currently assigned to this project.</p>
        <?php endif; ?>

        <form action="delete.php?id=<?= $projectID ?>" method="POST">
            <input type="hidden" name="confirm" value="yes">
            <button type="submit">Yes, delete</button>
            <a href="index.php"><button type="button">Cancel</button></a>
        </form>
    <?php endif; ?>
</main>

<?php include_once ROOT_PATH . '/public/footer.php'; ?>