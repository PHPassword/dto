<?php

namespace PHPassword\UnitTest;


use PHPassword\Dto\DtoInterface;
use PHPassword\Dto\SimpleDtoImplementation;

class PersonDto implements DtoInterface
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var int
     */
    private $age = 30;

    /**
     * @var PersonDto
     */
    private $parent;

    use SimpleDtoImplementation;

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param int $age
     */
    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    /**
     * @param PersonDto $parent
     */
    public function setParent(PersonDto $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @return PersonDto
     */
    public function getParent(): PersonDto
    {
        return $this->parent;
    }
}