<?php

require_once '../../initialise.php';

require_once ROOT_PATH . '/classes/Project.php';
require_once ROOT_PATH . '/classes/ProjectDatabase.php';

$projectDB = new ProjectDatabase();

$formData = [
    'name' => ''
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['name'] = trim($_POST['name'] ?? '');

    if ($formData['name'] === '') {
        $errors[] = 'Project name is required.';
    }

    if (empty($errors)) {
        $newProject = new Project(id: 0, name: $formData['name']);

        if ($projectDB->insert($newProject)) {
            header('Location: index.php');
            exit;
        } else {
            $errorMessage = 'Failed to create project.';
        }
    }
}

$pageTitle = 'New Project';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<nav>
    <ul>
        <li><a href="index.php">Back to Projects</a></li>
    </ul>
</nav>

<main>
    <h1>Create New Project</h1>

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
            <label for="name">Project Name</label>
            <input type="text" id="name" name="name"
                   value="<?= htmlspecialchars($formData['name']) ?>" required>
        </div>

        <div>
            <button type="submit">Create Project</button>
        </div>
    </form>
</main>

<?php include_once ROOT_PATH . '/public/footer.php'; ?>