<?php

namespace PHPassword\Dto;

trait SimpleDtoImplementation
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
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