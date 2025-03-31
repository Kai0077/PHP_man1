<?php

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/Department.php';
require_once ROOT_PATH . '/classes/DepartmentDatabase.php';

$departmentDB = new DepartmentDatabase();

$formData = [
    'name' => ''
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['name'] = trim($_POST['name'] ?? '');

    if ($formData['name'] === '') {
        $errors[] = 'Department name is required.';
    }

    if (empty($errors)) {
        $newDepartment = new Department(
            id: 0, 
            name: $formData['name']
        );

        if ($departmentDB->insert($newDepartment)) {
            header('Location: index.php');
            exit;
        } else {
            $errorMessage = 'Failed to add new department.';
        }
    }
}

$pageTitle = 'Add Department';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<nav>
    <ul>
        <li><a href="index.php">Back to Departments</a></li>
    </ul>
</nav>

<main>
    <h1>Add Department</h1>

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

    <form action="new.php" method="POST">
        <div>
            <label for="name">Department Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($formData['name']) ?>" required>
        </div>

        <div>
            <button type="submit">Create Department</button>
        </div>
    </form>
</main>

<?php include_once ROOT_PATH . '/public/footer.php'; ?>