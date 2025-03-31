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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        if ($departmentDB->delete($departmentID)) {
            header('Location: index.php');
            exit;
        } else {
            $errorMessage = 'Failed to delete department.';
        }
    } else {
        header('Location: index.php');
        exit;
    }
}

$pageTitle = 'Delete Department';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<main>
    <h1>Delete Department</h1>

    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php else: ?>
        <p>Are you sure you want to delete the department <strong><?= htmlspecialchars($department->getName()) ?></strong>?</p>

        <?php $employees = $department->getEmployees(); ?>

        <?php if (!empty($employees)): ?>
            <p>This department has the following employees:</p>
            <ul>
                <?php foreach ($employees as $employee): ?>
                    <li>
                        <?= htmlspecialchars($employee->getFirstName() . ' ' . $employee->getLastName()) ?> —
                        <?= htmlspecialchars($employee->getEmail()) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Note:</strong> You cannot delete this department becouse there still are employees under it</p>
        <?php else: ?>
            <p>This department has no employees.</p>
        <?php endif; ?>

        <form action="delete.php?id=<?= $departmentID ?>" method="POST">
            <input type="hidden" name="confirm" value="yes">
            <button type="submit">Yes, delete</button>
            <a href="index.php"><button type="button">Cancel</button></a>
        </form>
    <?php endif; ?>
</main>

<?php include_once ROOT_PATH . '/public/footer.php'; ?>