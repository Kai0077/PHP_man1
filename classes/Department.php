<?php

Class Department
{
  private int $id;
  private string $name;
  private array $employees = [];

  public function __construct(int $id, string $name)
  {
    $this->id = $id;
    $this->name = $name;
  }

  public function setId(int $id): void
  {
    $this->id = $id;
  }

  public function setName(string $name): void
  {
    $this->name = $name;
  }

  public function setEmployees(array $employees): void
  {
    $this->employees = $employees;
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getEmployees(): array
  {
    return $this->employees;
  }
}