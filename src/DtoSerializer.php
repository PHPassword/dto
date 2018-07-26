<?php

namespace PHPassword\Dto;


class DtoSerializer
{
    const INDEX_CLASS = '___class___';

    const INDEX_DATA = '___data___';

    /**
     * @param DtoInterface $dto
     * @return string
     */
    public function serialize(DtoInterface $dto) : string
    {
        return json_encode($this->normalize($dto));
    }

    private function normalize(DtoInterface $dto) : array
    {
        $data = $dto->toArray();
        foreach($data as &$value){
            if($value instanceof DtoInterface){
                $value = $this->normalize($value);
            }
        }

        return [
            self::INDEX_CLASS => get_class($dto),
            self::INDEX_DATA => $data
        ];
    }

    /**
     * @param string $json
     * @return DtoInterface
     * @throws DtoException
     */
    public function unserialize(string $json) : DtoInterface
    {
        if(($dtoArray = json_decode($json, true)) === false
            || !isset($dtoArray[self::INDEX_CLASS])
            || !isset($dtoArray[self::INDEX_DATA])){
            throw new DtoException('Could not unserialize JSON to DTO. Invalid JSON.');
        }

        return $this->toObject($dtoArray);
    }

    /**
     * @param array $dtoArray
     * @return DtoInterface
     */
    private function toObject(array $dtoArray) : DtoInterface
    {
        $dtoClass = $dtoArray[self::INDEX_CLASS];
        $dtoData = $dtoArray[self::INDEX_DATA];

        /* @var DtoInterface $dto */
        $dto = new $dtoClass;
        foreach($dtoData as &$value){
            if(is_array($value)
                && isset($value[self::INDEX_CLASS])
                && isset($value[self::INDEX_DATA])){
                $value = $this->toObject($value);
            }
        }

        $dto->fromArray($dtoData);

        return $dto;
    }
}