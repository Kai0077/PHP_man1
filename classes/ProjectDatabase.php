<?php

require_once 'Database.php';
require_once 'Logger.php';
require_once 'Project.php';
require_once 'Employee.php';

Class ProjectDatabase extends Database
{
    /**
     * retrieves all projects from the database
     * @return <arrary> An associative array with projects information or false if error accours
     */
    public function getAll(): array|false
    {
        $sql = <<<SQL
            SELECT nProjectID, cName
            FROM project
            ORDER BY cName;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $projects = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $projects[] = new Project(id: $row['nProjectID'], name:$row['cName']);
            }
            return $projects; 
        }
        catch(PDOException $e)
        {
            $this->pdo->rollBack();
            Logger::logText('Error getting projects, rolling back', $e);
            return false;
        }
    }

    public function getById(int $projectID): Project|false
    {
        $sql = <<<SQL
                SELECT
                p.nProjectID,
                p.cName,
                e.nEmployeeID,
                e.cFirstName,
                e.cLastName,
                e.cEmail,
                e.dBirth
                FROM project p
                LEFT JOIN emp_proy ep ON p.nProjectID = ep.nProjectID
                LEFT JOIN employee e ON ep.nEmployeeID = e.nEmployeeID
                WHERE p.nProjectID = :projectID
                ORDER BY e.cLastName, e.cFirstName;
            SQL;

        try {
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':projectID', $projectID);
        $stmt->execute();

        $employees = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $projectId = $row['nProjectID'];
            $projectName = $row['cName'];

            if ($row['nEmployeeID'] !== null) {
            $birthDate = DateTime::createFromFormat('Y-m-d', $row['dBirth']);
            $employees[] = new Employee(
                id: $row['nEmployeeID'],
                firstName: $row['cFirstName'],
                lastName: $row['cLastName'],
                email: $row['cEmail'],
                birthDate: $birthDate,
                projectId: $projectId,
                projectName: $projectName
            );
            }
        }

        if ($projectName === null) {
            return false;
        }

        $project = new Project(id: $projectId, name: $projectName);
        $project->setEmployees($employees);

        return $project;
        } catch (PDOException $e) {
        Logger::logText('Error getting project details: ', $e);
        return false;
        }
    }

    /**
     * Update an existing project
     * @param Project $project
     * @return bool
     */
    public function update(int $projectID, ?string $name = null, ?int $newEmployee = null, ?int $oldEmployee = null): bool
    {
        try {
        $this->pdo->beginTransaction();
        $hasUpdates = false;

        if ($name !== null) {
            $sql = <<<SQL
                    UPDATE project 
                    SET cName = :name 
                    WHERE nProjectID = :projectID
                SQL;

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':projectID', $projectID);
            $stmt->execute();
            $hasUpdates = true;
        }

        if ($oldEmployee !== null) {
            $sql = <<<SQL
                    DELETE FROM emp_proy 
                    WHERE nProjectID = :projectID 
                    AND nEmployeeID = :oldEmployee
                SQL;

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':projectID', $projectID);
            $stmt->bindValue(':oldEmployee', $oldEmployee);
            $stmt->execute();
            $hasUpdates = true;
        }

        if ($newEmployee !== null) {
            $sql = <<<SQL
                    INSERT INTO emp_proy (nProjectID, nEmployeeID) 
                    VALUES (:projectID, :newEmployee)
                SQL;

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':projectID', $projectID);
            $stmt->bindValue(':newEmployee', $newEmployee);
            $stmt->execute();
            $hasUpdates = true;
        }

        if ($hasUpdates) {
            $this->pdo->commit();
        } else {
            $this->pdo->rollBack();
        }

        return true;
        } catch (PDOException $e) {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
        Logger::logText('Error updating project: ', $e);
        return false;
        }
    }

    /**
     * Delete a project by ID
     * @param int $projectID
     * @return bool
     */
    public function delete(int $projectID): bool
    {
        $sql = <<<SQL
            DELETE p, ep FROM project p
            LEFT JOIN emp_proy ep ON p.nProjectID = ep.nProjectID
            WHERE p.nProjectID = :projectID
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':projectID', $projectID, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            Logger::logText('Error deleting project, rolling back', $e);
            return false;
        }
    }

    /**
     * Search for projects by name
     * @param string $searchText
     * @return array|false An array of Project objects or false on error
     */
    public function search(string $searchText): array|false
    {
        $sql = <<<SQL
            SELECT nProjectID, cName
            FROM project
            WHERE cName LIKE :search
            ORDER BY cName;
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':search', "%$searchText%");
            $stmt->execute();

            $projects = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $projects[] = new Project(id: $row['nProjectID'], name: $row['cName']);
            }

            return $projects;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            Logger::logText('Error searching projects, rolling back', $e);
            return false;
        }
    }

    /**
     * Insert a new project
     * @param Project $project
     * @return bool True if insertion was successful, false otherwise
     */
    public function insert(Project $project): bool
    {
        $sql = <<<SQL
            INSERT INTO project (cName)
            VALUES (:name);
        SQL;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':name', $project->getName());
            $stmt->execute();

            return $stmt->rowCount() === 1;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            Logger::logText('Error inserting project, rolling back', $e);
            return false;
        }
    }

    






}


