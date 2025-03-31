<?php

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/Employee.php';
require_once ROOT_PATH . '/classes/EmployeeDatabase.php';

$employeeDB = new EmployeeDB();

$employeeID = (int) ($_GET['id'] ?? 0);
$employee = $employeeDB->getById($employeeID);

if (!$employee) {
    $errorMessage = 'Employee not found.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        if ($employeeDB->delete($employeeID)) {
            header('Location: index.php');
            exit;
        } else {
            $errorMessage = 'Failed to delete employee.';
        }
    } else {
        header('Location: index.php');
        exit;
    }
}

$pageTitle = 'Delete Employee';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<main>
    <h1>Delete Employee</h1>

    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php else: ?>
        <p>Are you sure you want to delete <strong><?= htmlspecialchars($employee->getFirstName()) . ' ' . htmlspecialchars($employee->getLastName()) ?></strong>?</p>

        <form action="delete.php?id=<?= $employeeID ?>" method="POST">
            <input type="hidden" name="confirm" value="yes">
            <button type="submit">Yes, delete</button>
            <a href="index.php"><button type="button">Cancel</button></a>
        </form>
    <?php endif; ?>
</main>

<?php include_once ROOT_PATH . '/public/footer.php'; ?>