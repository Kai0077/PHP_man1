<?php

require_once '../../initialise.php';

$employeeID = (int) ($_GET['id'] ?? 0);

if ($employeeID === 0) {
    header('Location: index.php');
    exit;
}

require_once ROOT_PATH . '/classes/EmployeeDatabase.php';

$employeeDB = new EmployeeDB();
$employee = $employeeDB->getByID($employeeID);

if (!$employee) {
    $errorMessage = 'There was an error retrieving employee information.';
} 

$pageTitle = 'View Employee';
include_once ROOT_PATH . '/public/header.php';

?>

    <nav>
        <ul>
            <li><a href="index.php" title="Homepage">Back</a></li>
        </ul>
    </nav>
    <main>
        <?php if (isset($errorMessage)): ?>
            <section>
                <p class="error"><?=$errorMessage ?></p>
            </section>
        <?php else: ?>
            <p><strong>First name: </strong><?=$employee->getFirstName() ?></p>
            <p><strong>Last name: </strong><?=$employee->getLastName() ?></p>
            <p><strong>Email: </strong><?=$employee->getEmail() ?></p>
            <p><strong>Birth date: </strong><?=$employee->getBirthDate()->format('Y-m-d') ?></p>
            <p><strong>Department: </strong><?=$employee->getDepartmentName() ?></p>
        <?php endif; ?>
    </main>

<?php include_once ROOT_PATH . '/public/footer.php'; ?>