<?php
class Employee
{
    public $name;
    public $surName; 
    public $salary;

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function setSurname($surname)
    {
        $this->surName = $surname;

        return $this;
    }

    public function setSalary($salary)
    {
        $this->salary = $salary;

        return $this;
    }

    public function __toString()
    {
        $employeeInfo = 'Name: ' . $this->name . PHP_EOL;
        $employeeInfo .= 'Surname: ' . $this->surName . PHP_EOL;
        $employeeInfo .= 'Salary: ' . $this->salary . PHP_EOL;

        return $employeeInfo;
    }
}

# Create a new instance of the Employee class:
$employee = new Employee();

# Employee Tom Smith has a salary of 100:
echo $employee->setName('Tom')
              ->setSurname('Smith')
              ->setSalary('100');