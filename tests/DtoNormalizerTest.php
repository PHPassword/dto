<?php


use PHPassword\Dto\DtoException;
use PHPassword\Dto\DtoInterface;
use PHPassword\Dto\DtoNormalizer;
use PHPassword\UnitTest\PersonDto;
use PHPUnit\Framework\TestCase;

class DtoNormalizerTest extends TestCase
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
    public function testNormalize()
    {
        $normalizer = new DtoNormalizer();

        $normalized = $normalizer->normalize(static::$childDto);
        $this->assertArrayHasKey(DtoNormalizer::INDEX_CLASS, $normalized);
        $this->assertArrayHasKey(DtoNormalizer::INDEX_DATA, $normalized);
    }

    /**
     * @throws \Exception
     */
    public function testDenormalize()
    {
        $normalizer = new DtoNormalizer();
        $normalized = $normalizer->normalize(static::$childDto);
        /* @var PersonDto $compareDto */
        $compareDto = $normalizer->denormalize($normalized, get_class(static::$childDto));

        $this->assertInstanceOf(DtoInterface::class, $compareDto);
        $this->assertSame(static::$childDto->getName(), $compareDto->getName());
        $this->assertSame(static::$childDto->getAge(), $compareDto->getAge());
        $this->assertSame(static::$childDto->getParent()->getName(), $compareDto->getParent()->getName());
        $this->assertSame(static::$childDto->getParent()->getAge(), $compareDto->getParent()->getAge());
    }

    /**
     * @throws DtoException
     */
    public function testDenormalizeFail()
    {
        $serializer = new DtoNormalizer();
        $this->expectException(DtoException::class);

        $serializer->denormalize(
            [
                DtoNormalizer::INDEX_CLASS => \stdClass::class,
                DtoNormalizer::INDEX_DATA => []
            ],
            \stdClass::class
        );
    }
}