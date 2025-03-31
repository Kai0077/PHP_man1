<?php

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/Project.php';
require_once ROOT_PATH . '/classes/Employee.php';
require_once ROOT_PATH . '/classes/ProjectDatabase.php';
require_once ROOT_PATH . '/classes/EmployeeDatabase.php';

$projectDB = new ProjectDatabase();
$employeeDB = new EmployeeDB();

$projectID = (int) ($_GET['id'] ?? 0);
$project = $projectDB->getById($projectID);
$allEmployees = $employeeDB->getAll();

if (!$project || !$allEmployees) {
    $errorMessage = 'Unable to load project or employee data.';
}

$formData = [
    'name' => $project ? $project->getName() : '',
    'new_employee' => '',
    'old_employee' => ''
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['name'] = trim($_POST['name'] ?? '');
    $formData['new_employee'] = $_POST['new_employee'] !== '' ? (int) $_POST['new_employee'] : null;
    $formData['old_employee'] = $_POST['old_employee'] !== '' ? (int) $_POST['old_employee'] : null;

    if ($formData['name'] === '') {
        $errors[] = 'Project name is required.';
    }

    if (empty($errors)) {
        $success = $projectDB->update(
            $projectID,
            $formData['name'],
            $formData['new_employee'],
            $formData['old_employee']
        );

        if ($success) {
            header('Location: index.php');
            exit;
        } else {
            $errorMessage = 'Failed to update project.';
        }
    }
}

$pageTitle = 'Edit Project';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<nav>
    <ul>
        <li><a href="index.php">Back to Projects</a></li>
    </ul>
</nav>

<main>
    <h1>Edit Project</h1>

    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul class="error-list">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form action="update.php?id=<?= $projectID ?>" method="POST">
        <div>
            <label for="name">Project Name</label>
            <input type="text" id="name" name="name"
                   value="<?= htmlspecialchars($formData['name']) ?>" required>
        </div>

        <div>
            <label for="old_employee">Remove Employee (optional)</label>
            <select name="old_employee" id="old_employee">
                <option value="">-- Select to remove --</option>
                <?php foreach ($project->getEmployees() as $employee): ?>
                    <option value="<?= $employee->getId() ?>">
                        <?= htmlspecialchars($employee->getFirstName() . ' ' . $employee->getLastName()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="new_employee">Add Employee (optional)</label>
            <select name="new_employee" id="new_employee">
                <option value="">-- Select to add --</option>
                <?php foreach ($allEmployees as $employee): ?>
                    <option value="<?= $employee->getId() ?>">
                        <?= htmlspecialchars($employee->getFirstName() . ' ' . $employee->getLastName()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <button type="submit">Save Changes</button>
        </div>
    </form>
</main>

<?php include_once ROOT_PATH . '/public/footer.php'; ?>