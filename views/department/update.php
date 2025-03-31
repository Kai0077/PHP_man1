<?php

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/Department.php';
require_once ROOT_PATH . '/classes/DepartmentDatabase.php';

$departmentDB = new DepartmentDatabase();
$departmentID = (int) ($_GET['id'] ?? 0);
$department = $departmentDB->getById($departmentID);

if (!$department) {
    $errorMessage = 'Department not found.';
}

$formData = [
    'name' => $department ? $department->getName() : ''
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['name'] = trim($_POST['name'] ?? '');

    if ($formData['name'] === '') {
        $errors[] = 'Department name is required.';
    }

    if (empty($errors)) {
        $updatedDepartment = new Department(
            id: $departmentID,
            name: $formData['name']
        );

        if ($departmentDB->update($updatedDepartment)) {
            header('Location: index.php');
            exit;
        } else {
            $errorMessage = 'Failed to update department.';
        }
    }
}

$pageTitle = 'Edit Department';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<nav>
    <ul>
        <li><a href="index.php">Back</a></li>
    </ul>
</nav>

<main>
    <h1>Edit Department</h1>

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

    <form action="update.php?id=<?= $departmentID ?>" method="POST">
        <div>
            <label for="name">Department Name</label>
            <input type="text" id="name" name="name"
                   value="<?= htmlspecialchars($formData['name']) ?>" required>
        </div>

        <div>
            <button type="submit">Save Changes</button>
        </div>
    </form>
</main>

<?php include_once ROOT_PATH . '/public/footer.php'; ?>