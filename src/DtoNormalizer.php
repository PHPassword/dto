<?php

namespace PHPassword\Dto;


use PHPassword\Serializer\NormalizerInterface;

class DtoNormalizer implements NormalizerInterface
{
    public const INDEX_CLASS = '___class___';

    public const INDEX_DATA = '___data___';

    /**
     * @param mixed $data
     * @return bool
     */
    public function supportsNormalization($data): bool
    {
        return $data instanceof DtoInterface;
    }

    /**
     * @param object $object
     * @return array
     * @throws DtoException
     */
    public function normalize($object): array
    {
        if(!$object instanceof DtoInterface){
            throw new DtoException(sprintf('Failed to normalize object of type %s', get_class($object)));
        }

        $data = $object->toArray();
        foreach($data as &$value){
            if($value instanceof DtoInterface){
                $value = $this->normalize($value);
            }
        }

        return [
            self::INDEX_CLASS => get_class($object),
            self::INDEX_DATA => $data
        ];
    }

    /**
     * @param array $data
     * @param string $class
     * @return bool
     * @throws \ReflectionException
     */
    public function supportDenormalization(array $data, string $class): bool
    {
        if(!isset($data[self::INDEX_CLASS]) || !isset($data[self::INDEX_DATA])){
            return false;
        }

        $refl = new \ReflectionClass($data[self::INDEX_CLASS]);
        return $refl->implementsInterface(DtoInterface::class);
    }

    /**
     * @param array $data
     * @param string $class
     * @return DtoInterface
     * @throws DtoException
     */
    public function denormalize(array $data, string $class)
    {
        $dtoClass = $data[self::INDEX_CLASS];
        $dtoData = $data[self::INDEX_DATA];

        /* @var DtoInterface $dto */
        $dto = new $dtoClass;
        if(!$dto instanceof DtoInterface){
            throw new DtoException(sprintf('Failed to denormalize class %s', $dtoClass));
        }

        foreach($dtoData as &$value){
            if(is_array($value)
                && isset($value[self::INDEX_CLASS])
                && isset($value[self::INDEX_DATA])){
                $value = $this->denormalize($value, $value[self::INDEX_CLASS]);
            }
        }

        $dto->fromArray($dtoData);

        return $dto;
    }
}