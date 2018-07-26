<?php


use PHPassword\Dto\DtoInterface;
use PHPassword\Dto\DtoSerializer;
use PHPassword\UnitTest\PersonDto;
use PHPUnit\Framework\TestCase;

class DtoSerializerTest extends TestCase
{
    /**
     * @var PersonDto
     */
    private static $childDto;

    public static function setUpBeforeClass()
    {
        $childDto = new PersonDto();
        $childDto->setName('Annika');
        $childDto->setAge(23);
        $parentDto = new PersonDto();
        $parentDto->setName('Sonja');
        $parentDto->setAge(45);
        $childDto->setParent($parentDto);

        static::$childDto = $childDto;
    }

    /**
     * @throws Exception
     */
    public function testSerialize()
    {
        $serializer = new DtoSerializer();

        $serializedString = $serializer->serialize(static::$childDto);
        $this->assertJson($serializedString);

        $deserializedArray = json_decode($serializedString, true);
        $this->assertArrayHasKey(DtoSerializer::INDEX_CLASS, $deserializedArray);
        $this->assertArrayHasKey(DtoSerializer::INDEX_DATA, $deserializedArray);
    }

    /**
     * @throws \Exception
     */
    public function testUnserialize()
    {
        $serializer = new DtoSerializer();
        $serializedString = $serializer->serialize(static::$childDto);
        /* @var PersonDto $compareDto */
        $compareDto = $serializer->unserialize($serializedString);

        $this->assertInstanceOf(DtoInterface::class, $compareDto);
        $this->assertSame(static::$childDto->getName(), $compareDto->getName());
        $this->assertSame(static::$childDto->getAge(), $compareDto->getAge());
        $this->assertSame(static::$childDto->getParent()->getName(), $compareDto->getParent()->getName());
        $this->assertSame(static::$childDto->getParent()->getAge(), $compareDto->getParent()->getAge());
    }
}