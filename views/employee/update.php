<?php

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/Employee.php';
require_once ROOT_PATH . '/classes/EmployeeDatabase.php';
require_once ROOT_PATH . '/classes/DepartmentDatabase.php';

$employeeDB = new EmployeeDB();
$departmentDB = new DepartmentDatabase();
$departments = $departmentDB->getAll();

$employeeID = (int) ($_GET['id'] ?? 0);
$employee = $employeeDB->getById($employeeID);

$departmentDB = new DepartmentDatabase();
$departments = $departmentDB->getAll();

if (!$employee || !$departments) {
    $errorMessage = 'Unable to load employee or department data.';
}

$formData = [
    'first_name' => $employee->getFirstName(),
    'last_name' => $employee->getLastName(),
    'email' => $employee->getEmail(),
    'birth_date' => $employee->getBirthDate()->format('Y-m-d'),
    'department_id' => $employee->getDepartmentId()
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'birth_date' => $_POST['birth_date'] ?? '',
        'department_id' => (int) ($_POST['department_id'] ?? 0),
    ];

    $errors = $employeeDB->validate($formData);

    if (empty($errors)) {
        if ($employeeDB->update($employeeID, $formData)) {
            header('Location: index.php');
            exit;
        } else {
            $errorMessage = 'Unable to update employee.';
        }
    }
}

$pageTitle = 'Edit Employee';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<nav>
    <ul>
        <li><a href="index.php">Back</a></li>
    </ul>
</nav>

<main>
    <h1>Edit Employee</h1>

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

    <form action="update.php?id=<?= $employeeID ?>" method="POST">
        <div>
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name"
                   value="<?= htmlspecialchars($formData['first_name']) ?>" required>
        </div>

        <div>
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name"
                   value="<?= htmlspecialchars($formData['last_name']) ?>" required>
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($formData['email']) ?>" required>
        </div>

        <div>
            <label for="birth_date">Birth Date</label>
            <input type="date" id="birth_date" name="birth_date"
                   value="<?= htmlspecialchars($formData['birth_date']) ?>" required>
        </div>

        <div>
            <label for="department">Department</label>
            <select name="department_id" id="department" required>
                <option value="">Select a department</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?= $dept->getId() ?>"
                        <?= $formData['department_id'] === $dept->getId() ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dept->getName()) ?>
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