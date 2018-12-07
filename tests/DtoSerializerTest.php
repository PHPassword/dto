<?php


use PHPassword\Dto\DtoException;
use PHPassword\Dto\DtoInterface;
use PHPassword\Dto\DtoSerializer;
use PHPassword\UnitTest\AnnotatedPersonDto;
use PHPassword\UnitTest\PersonDto;
use PHPUnit\Framework\TestCase;

class DtoSerializerTest extends TestCase
{
    /**
     * @var PersonDto
     */
    private static $childDto;

    /**
     * @var AnnotatedPersonDto
     */
    private static $annotatedChildDto;

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

        $annotatedChild = new AnnotatedPersonDto();
        $annotatedChild->setName('Phil');
        $annotatedChild->setAge(29);
        $annotatedChild->setParent($parentDto);
        static::$annotatedChildDto = $annotatedChild;
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

    /**
     * @throws DtoException
     */
    public function testUnserializeFail()
    {
        $serializer = new DtoSerializer();
        $this->expectException(DtoException::class);

        $serializer->unserialize('{uezdehuadws/#+');
    }

    /**
     * @throws Exception
     */
    public function testAnnotatedSerialize()
    {
        $serializer = new DtoSerializer();

        $serializedString = $serializer->serialize(static::$annotatedChildDto);
        $this->assertJson($serializedString);

        $deserializedArray = json_decode($serializedString, true);
        $this->assertArrayHasKey('name', $deserializedArray[DtoSerializer::INDEX_DATA]);
        $this->assertSame('Phil', $deserializedArray[DtoSerializer::INDEX_DATA]['name']);
        $this->assertArrayHasKey('age', $deserializedArray[DtoSerializer::INDEX_DATA]);
        $this->assertSame(29, $deserializedArray[DtoSerializer::INDEX_DATA]['age']);
        $this->assertArrayNotHasKey('partent', $deserializedArray[DtoSerializer::INDEX_DATA]);
    }

    /**
     * @throws \Exception
     */
    public function testAnnotatedUnserialize()
    {
        $serializer = new DtoSerializer();
        $serializedString = $serializer->serialize(static::$annotatedChildDto);
        /* @var AnnotatedPersonDto $compareDto */
        $compareDto = $serializer->unserialize($serializedString);

        $this->assertInstanceOf(DtoInterface::class, $compareDto);
        $this->assertSame(static::$annotatedChildDto->getName(), $compareDto->getName());
        $this->assertSame(static::$annotatedChildDto->getAge(), $compareDto->getAge());

        $this->expectException(\TypeError::class);
        $compareDto->getParent();
    }
}