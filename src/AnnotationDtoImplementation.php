<?php

namespace PHPassword\Dto;

trait AnnotationDtoImplementation
{
    /**
     * @return array
     * @throws \ReflectionException
     */
    public function toArray(): array
    {
        $return = [];
        $reflection = new \ReflectionClass($this);

        foreach($reflection->getProperties() as $property){
            if($property->getDocComment()
                && preg_match('/\@dto$/m', $property->getDocComment())){
                $property->setAccessible(true);
                $return[$property->getName()] = $property->getValue($this);
            }
        }

        return $return;
    }

    /**
     * @param array $data
     */
    public function fromArray(array $data): void
    {
        foreach($data as $name => $value){
            $this->$name = $value;
        }
    }
}