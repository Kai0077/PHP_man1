<?php 

Class Employee 
{
    private int $id;
    
    private string $firstName;

    private string $lastName;

    private string $email;

    private DateTime $birthDate;

    private ?int $departmentId;

    private ?string $departmentName;

    private ?int $projectId;

    private ?string $projectName;

    public function __construct(
        int $id,
        string $firstName,
        string $lastName,
        string $email,
        DateTime $birthDate,
        ?int $departmentId = null,
        ?string $departmentName = null,
        ?int $projectId = null,
        ?string $projectName = null
    )
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->birthDate = $birthDate;
        $this->departmentId = $departmentId;
        $this->departmentName = $departmentName;
        $this->projectId = $projectId;
        $this->projectName = $projectName;
    }

    public function setId(int $id):void
    {
        $this->id = $id;
    } 

    public function setFirstName(string $fistName):void
    {
        $this->firstName = $fistName;
    } 

    public function setLastName(string $lastName):void
    {
        $this->lastName = $lastName;
    } 

    public function setEmail(string $email):void
    {
        $this->email = $email;
    } 

    public function setBirthDate(DateTime $birthDate):void
    {
        $this->birthDate = $birthDate;
    } 

    public function setDepartmantId(?int $departmentId):void
    {
        $this->departmentId = $departmentId;
    } 

    public function setDepartmentName(?string $departmentName):void
    {
        $this->departmentName = $departmentName;
    } 

    public function setProjectId(?int $projectId):void
    {
        $this->projectId = $projectId;
    } 

    public function setProjectName(?string $projectName):void
    {
        $this->projectName = $projectName;
    } 
    

    public function getId():int
    {
        return $this->id;
    } 

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getBirthDate(): DateTime
    {
        return $this->birthDate;
    }

    public function getDepartmentId(): ?int
    {
        return $this->departmentId;
    }

    public function getDepartmentName(): ?string
    {
        return $this->departmentName;
    }

    public function getProjectId(): ?int
    {
        return $this->projectId;
    }

    public function getProjectName(): ?string
    {
        return $this->projectName;
    }




}