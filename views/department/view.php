<?php

// Load setup file and required classes
require_once '../../initialise.php';
require_once ROOT_PATH . '/classes/Department.php';
require_once ROOT_PATH . '/classes/Employee.php';
require_once ROOT_PATH . '/classes/DepartmentDatabase.php';

// Create a department database object
$departmentDB = new DepartmentDatabase();

// Get the department ID from the URL
$departmentID = (int) ($_GET['id'] ?? 0);

// Try to get the department and its employees
$department = $departmentDB->getById($departmentID);

// If department not found, show an error message later
if (!$department) {
    $errorMessage = 'Department not found.';
}

// Set the page title and include the header and navigation
$pageTitle = 'View Department';
include_once ROOT_PATH . '/public/header.php';
include_once ROOT_PATH . '/public/nav.php';

?>

<!-- Navigation buttons -->
<nav>
    <ul>
        <li><a href="index.php">Back to Departments</a></li>
        <li><a href="update.php?id=<?= $departmentID ?>">Edit</a></li>
        <li>
            <a href="delete.php?id=<?= $departmentID ?>" 
               onclick="return confirm('Are you sure you want to delete this department?');">Delete</a>
        </li>
    </ul>
</nav>

<main>
    <h1>Department Details</h1>

    <!-- Show error if department wasn't found -->
    <?php if (!empty($errorMessage)): ?>
        <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php else: ?>

        <!-- Show the department name -->
        <section>
            <p><strong>Name:</strong> <?= htmlspecialchars($department->getName()) ?></p>
        </section>

        <!-- Show employees that belong to this department -->
        <section style="margin-top: 2em;">
            <h2>Employees in this Department</h2>

            <?php $employees = $department->getEmployees(); ?>

            <!-- If no employees, show message -->
            <?php if (empty($employees)): ?>
                <p>No employees in this department.</p>
            <?php else: ?>
                <!-- Display employees in a table -->
                <table border="1" cellpadding="8" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Birth Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through each employee and show their info -->
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?= htmlspecialchars($employee->getFirstName() . ' ' . $employee->getLastName()) ?></td>
                                <td><?= htmlspecialchars($employee->getEmail()) ?></td>
                                <td><?= htmlspecialchars($employee->getBirthDate()->format('Y-m-d')) ?></td>
                                <td>
                                    <a href="../employees/view.php?id=<?= $employee->getId() ?>">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<!-- Load the page footer -->
<?php include_once ROOT_PATH . '/public/footer.php'; ?>