<?php

// Load setup and necessary classes for departments
require_once '../../initialise.php';
require_once ROOT_PATH . '/classes/Department.php';
require_once ROOT_PATH . '/classes/DepartmentDatabase.php';

// Create the department database object
$departmentDB = new DepartmentDatabase();

// Get the department ID from the URL (or default to 0)
$departmentID = (int) ($_GET['id'] ?? 0);

// Get the department from the database using the ID
$department = $departmentDB->getById($departmentID);

// If the department was not found, show an error
if (!$department) {
    $errorMessage = 'Department not found.';
}

// Pre-fill the form with the current department name
$formData = [
    'name' => $department ? $department->getName() : ''
];

// Array to hold any form validation errors
$errors = [];

// If the form is submitted (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated department name from the form
    $formData['name'] = trim($_POST['name'] ?? '');

    // Check if the name is empty
    if ($formData['name'] === '') {
        $errors[] = 'Department name is required.';
    }

    // If there are no errors, try to update the department
    if (empty($errors)) {
        // Create a Department object with the updated name
        $updatedDepartment = new Department(
            id: $departmentID,
            name: $formData['name']
        );

        // Attempt to update the department in the database
        if ($departmentDB->update($updatedDepartment)) {
            // If successful, go back to the department list
            header('Location: index.php');
            exit;
        } else {
            // If update fails, show error
            $errorMessage = 'Failed to update department.';
        }
    }
}

// Set page title and include the header and navigation
$pageTitle = 'Edit Department';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<!-- Navigation link to go back to the department list -->
<nav>
    <ul>
        <li><a href="index.php">Back</a></li>
    </ul>
</nav>

<main>
    <h1>Edit Department</h1>

    <!-- Show error message if something went wrong -->
    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <!-- Show validation errors (if any) -->
    <?php if (!empty($errors)): ?>
        <ul class="error-list">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Form to update department name -->
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

<!-- Include the footer -->
<?php include_once ROOT_PATH . '/public/footer.php'; ?>