<?php

namespace PHPassword\Dto;


interface DtoInterface
{
    /**
     * @return array
     */
    public function toArray() : array;

    /**
     * @param array $data
     */
    public function fromArray(array $data) : void;
}