<?php

require_once 'Database.php';
require_once 'Logger.php';
require_once 'Employee.php';

Class EmployeeDB extends Database
{

    /**
     * It retrieves all employees from the database
     * @return An associative array with employee information,
     *         or false if there was an error
     */
    public function getAll(): array|false
    {
        $sql = <<<SQL
                SELECT nEmployeeID, cFirstName, cLastName, cEmail, dBirth, nDepartmentID
                FROM employee
                ORDER BY cFirstName, cLastName;
            SQL;

        try {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $employees = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $birthDate = DateTime::createFromFormat(format: 'Y-m-d', datetime: $row['dBirth']);
            $employees[] = new Employee(
            id: $row['nEmployeeID'],
            firstName: $row['cFirstName'],
            lastName: $row['cLastName'],
            email: $row['cEmail'],
            birthDate: $birthDate,
            departmentId: $row['nDepartmentID'],
            );
        }
        return $employees;
        } catch (PDOException $e) {
        $this->pdo->rollBack();
        Logger::logText('Error getting all employees: ', $e);
        return false;
        }
    }

    /**
     * It retrieves employees from the database based 
     * on a text search on the first and last name
     * @param $searchText The text to search in the database
     * @return An associative array with employee information,
     *         or false if there was an error
     */
    public function search(string $searchText): array|false
    {
        $sql = <<<SQL
                SELECT nEmployeeID, cFirstName, cLastName, cEmail, dBirth, nDepartmentID
                FROM employee
                WHERE cFirstName LIKE :firstName
                OR cLastName LIKE :lastName
                ORDER BY cFirstName, cLastName;
            SQL;

        try {
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':firstName', "%$searchText%");
        $stmt->bindValue(':lastName', "%$searchText%");
        $stmt->execute();

        $employees = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $birthDate = DateTime::createFromFormat(format: 'Y-m-d', datetime: $row['dBirth']);
            $employees[] = new Employee(
            id: $row['nEmployeeID'],
            firstName: $row['cFirstName'],
            lastName: $row['cLastName'],
            email: $row['cEmail'],
            birthDate: $birthDate,
            departmentId: $row['nDepartmentID'],
            );
        }
        return $employees;
        } catch (PDOException $e) {
        $this->pdo->rollBack();
        Logger::logText('Error searching for employees: ', $e);
        return false;
        }
    }

    /**
     * It retrieves information of an employee
     * @param $employeeID The ID of the employee
     * @return An associative array with employee information,
     *         or false if there was an error
     */
    function getByID(int $employeeID): Employee|false
    {
        $sql = <<<SQL
            SELECT
                e.nEmployeeID AS employee_id,
                e.cFirstName AS first_name, 
                e.cLastName AS last_name, 
                e.cEmail AS email, 
                e.dBirth AS birth_date, 
                e.nDepartmentID AS department_id, 
                d.cName AS department_name,
                p.nProjectID AS project_id,
                p.cName AS project_name
            FROM employee e
            INNER JOIN department d ON e.nDepartmentID = d.nDepartmentID
            LEFT JOIN emp_proy ep ON e.nEmployeeID = ep.nEmployeeID
            LEFT JOIN project p ON ep.nProjectID = p.nProjectID
            WHERE e.nEmployeeID = :employeeID
            ORDER BY p.cName;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':employeeID', $employeeID);
            $stmt->execute();

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $birthDate = DateTime::createFromFormat(format: 'Y-m-d', datetime: $row['birth_date']);
                $employee = new Employee(
                  id: $row['employee_id'],
                  firstName: $row['first_name'],
                  lastName: $row['last_name'],
                  email: $row['email'],
                  birthDate: $birthDate,
                  departmentId: $row['department_id'],
                  departmentName: $row['department_name'],
                );
                return $employee;
              }
              return false;
            
        } catch (PDOException $e) {
            Logger::logText('Error retrieving employee information: ', $e);
            return false;
        }
    }

    /**
     * It validates employee data before putting it into the database
     * @param $employee Employee data in an associative array
     * @return<array> An array with all validation error messages
     */
    function validate(array $employee): array
    {
        $firstName = trim($employee['first_name'] ?? '');
        $lastName = trim($employee['last_name'] ?? '');
        $email = trim($employee['email'] ?? '');
        $birthDate = trim($employee['birth_date'] ?? '');
        $departmentID = (int) ($employee['department_id'] ?? 0);
        
        $validationErrors = [];
        
        if ($firstName === '') {
            $validationErrors[] = 'First name is mandatory.';
        }
        if ($lastName === '') {
            $validationErrors[] = 'Last name is mandatory.';
        }
        if ($email === '') {
            $validationErrors[] = 'Email is mandatory.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $validationErrors[] = 'Invalid email format.';
        }
        if ($birthDate === '') {
            $validationErrors[] = 'Birth date is mandatory.';
        } elseif (!DateTime::createFromFormat('Y-m-d', $birthDate)) {
            $validationErrors[] = 'Invalid birth date format.';
        } elseif (DateTime::createFromFormat('Y-m-d', $birthDate) > new DateTime('-16 years')) {
            $validationErrors[] = 'The employee must be at least 16 years old.';
        }
        if ($departmentID === 0) {
            $validationErrors[] = 'Department is mandatory.';
        } else {
            $departmentDb = new DepartmentDatabase();
                if (!$departmentDb->getByID($departmentID)) {
                    $validationErrors[] = 'The department does not exist.';
                }
        }
        
        return $validationErrors;
    }

    /**
     * It inserts a new employee in the database
     * @param $employee An associative array with employee information
     * @return true if the insert was successful,
     *         or false if there was an error
     */
    function insert(array $employee): bool
    {
        $sql =<<<SQL
            INSERT INTO employee
                (cFirstName, cLastName, cEmail, dBirth, nDepartmentID)
            VALUES
                (:firstName, :lastName, :email, :birthDate, :departmentID);
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':firstName', $employee['first_name']);
            $stmt->bindValue(':lastName', $employee['last_name']);
            $stmt->bindValue(':email', $employee['email']);
            $stmt->bindValue(':birthDate', $employee['birth_date']);
            $stmt->bindValue(':departmentID', $employee['department_id']);
            $stmt->execute();
            
            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error inserting a new employee: ', $e);
            return false;
        }
    }

    /**
     * It updates an existing employee in the database
     * @param int $employeeID The ID of the employee to update
     * @param array $employee An associative array with updated employee information
     * @return bool True if the update was successful, false otherwise
     */
    function update(int $employeeID, array $employee): bool
    {
        $sql =<<<SQL
            UPDATE employee
            SET 
                cFirstName = :firstName,
                cLastName = :lastName,
                cEmail = :email,
                dBirth = :birthDate,
                nDepartmentID = :departmentID
            WHERE nEmployeeID = :employeeID;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':firstName', $employee['first_name']);
            $stmt->bindValue(':lastName', $employee['last_name']);
            $stmt->bindValue(':email', $employee['email']);
            $stmt->bindValue(':birthDate', $employee['birth_date']);
            $stmt->bindValue(':departmentID', $employee['department_id']);
            $stmt->bindValue(':employeeID', $employeeID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText("Error updating employee with ID $employeeID: ", $e);
            return false;
        }
    }

    /**
     * It deletes an employee from the database
     * @param int $employeeID The ID of the employee to delete
     * @return bool True if the delete was successful, false otherwise
     */
    function delete(int $employeeID): bool
    {
        $sql =<<<SQL
            DELETE FROM employee
            WHERE nEmployeeID = :employeeID;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':employeeID', $employeeID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText("Error deleting employee with ID $employeeID: ", $e);
            return false;
        }
    }


   
}