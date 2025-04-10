<?php

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/Employee.php';
require_once ROOT_PATH . '/classes/EmployeeDatabase.php';
require_once ROOT_PATH . '/classes/DepartmentDatabase.php';

$employeeDB = new EmployeeDB();
$departmentDB = new DepartmentDatabase();
$departments = $departmentDB->getAll();

$formData = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'birth_date' => '',
    'department_id' => 0
];

$errors = [];

if (!$departments) {
    $errorMessage = 'There was an error retrieving the department list.';
}

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
        if ($employeeDB->insert($formData)) {
            header('Location: index.php');
            exit;
        } else {
            $errorMessage = 'Unable to add new employee.';
        }
    }
}

$pageTitle = 'Add Employee';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<nav>
    <ul>
        <li><a href="index.php">Back</a></li>
    </ul>
</nav>

<main>
    <h1>Add Employee</h1>

    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error-list">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="new.php" method="POST">
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
            <label for="department_id">Department</label>
            <select name="department_id" id="department_id" required>
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
            <button type="submit">Submit</button>
        </div>
    </form>
</main>

<?php include_once ROOT_PATH . '/public/footer.php'; ?>