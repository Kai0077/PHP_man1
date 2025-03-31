<?php

require_once 'Database.php';
require_once 'Logger.php';
require_once 'Department.php';
require_once 'Employee.php';

class DepartmentDatabase extends Database
{
    /**
     * Retrieves all departments
     * @return array|false Array of Department objects, or false on error
     */
    public function getAll(): array|false
    {
        $sql = <<<SQL
            SELECT nDepartmentID, cName
            FROM department
            ORDER BY cName;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $departments = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $departments[] = new Department(id: $row['nDepartmentID'], name: $row['cName']);
            }

            return $departments;
        } catch (PDOException $e) {
            Logger::logText('Error getting all departments: ', $e);
            return false;
        }
    }

    /**
     * Get one department by ID (with its employees)
     * @param int $departmentID
     * @return Department|false
     */
    public function getByID(int $departmentID): Department|false
    {
        $sql = <<<SQL
            SELECT 
                d.nDepartmentID,
                d.cName,
                e.nEmployeeID,
                e.cFirstName,
                e.cLastName,
                e.cEmail,
                e.dBirth
            FROM department d
            LEFT JOIN employee e ON d.nDepartmentID = e.nDepartmentID
            WHERE d.nDepartmentID = :departmentID
            ORDER BY e.cLastName, e.cFirstName;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':departmentID', $departmentID);
            $stmt->execute();

            $employees = [];
            $departmentName = null;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $departmentName = $row['cName'];
                if ($row['nEmployeeID'] !== null) {
                    $birthDate = DateTime::createFromFormat('Y-m-d', $row['dBirth']);
                    $employees[] = new Employee(
                        id: $row['nEmployeeID'],
                        firstName: $row['cFirstName'],
                        lastName: $row['cLastName'],
                        email: $row['cEmail'],
                        birthDate: $birthDate,
                        departmentId: $departmentID,
                        departmentName: $departmentName
                    );
                }
            }

            if ($departmentName === null) {
                return false;
            }

            $department = new Department(id: $departmentID, name: $departmentName);
            $department->setEmployees($employees);

            return $department;
        } catch (PDOException $e) {
            Logger::logText("Error retrieving department $departmentID: ", $e);
            return false;
        }
    }

    /**
     * Search departments by name
     */
    public function search(string $searchText): array|false
    {
        $sql = <<<SQL
            SELECT nDepartmentID, cName
            FROM department
            WHERE cName LIKE :search
            ORDER BY cName;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':search', "%$searchText%");
            $stmt->execute();

            $departments = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $departments[] = new Department(id: $row['nDepartmentID'], name: $row['cName']);
            }

            return $departments;
        } catch (PDOException $e) {
            Logger::logText('Error searching departments: ', $e);
            return false;
        }
    }

    /**
     * Insert new department
     */
    public function insert(Department $department): bool
    {
        $sql = <<<SQL
            INSERT INTO department (cName)
            VALUES (:name);
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $department->getName());
            $stmt->execute();

            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error inserting department: ', $e);
            return false;
        }
    }

    /**
     * Update department
     */
    public function update(Department $department): bool
    {
        $sql = <<<SQL
            UPDATE department
            SET cName = :name
            WHERE nDepartmentID = :id;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $department->getName());
            $stmt->bindValue(':id', $department->getId(), PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText('Error updating department: ', $e);
            return false;
        }
    }

    /**
     * Delete department
     */
    public function delete(int $departmentID): bool
    {
        $sql = <<<SQL
            DELETE FROM department
            WHERE nDepartmentID = :id
            AND (SELECT COUNT(*) FROM employee WHERE nDepartmentID = :id) = 0;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $departmentID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            Logger::logText("Error deleting department ID $departmentID: ", $e);
            return false;
        }
    }
}