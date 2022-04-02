<?php
declare(strict_types=1);

namespace Rinsvent\DTO2Data;

use ReflectionProperty;
use Rinsvent\AttributeExtractor\MethodExtractor;
use Rinsvent\AttributeExtractor\PropertyExtractor;
use Rinsvent\DTO2Data\Attribute\Schema;
use Rinsvent\Transformer\Transformer;
use Rinsvent\Transformer\Transformer\Meta;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class Dto2DataConverter
{
    private PropertyAccessorInterface $propertyAccessor;
    private Transformer $transformer;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->enableMagicMethods()
            ->getPropertyAccessor();
        $this->transformer = new Transformer();
    }

    public function convert($data, string $schemaClass): array
    {
        $schema = new $schemaClass();
        if (!$schema instanceof Schema) {
            throw new \InvalidArgumentException(
                'Schema should be instance of Rinsvent\DTO2Data\Attribute\Schema'
            );
        }
        return $this->convertByMap($data, $schema->getMap(), $schema->getTags($data));
    }

    private function convertByMap($data, array $map, array $tags): array
    {
        $result = [];
        if (is_iterable($data)) {
            foreach ($data as $item) {
                $result[] = $this->processItem($item, $map, $tags);
            }
        } else {
            $result = $this->processItem($data, $map, $tags);
        }
        return $result;
    }

    private function processItem($data, array $map, array $tags): array
    {
        $result = [];
        foreach ($map as $key => $item) {
            try {
                switch (true) {
                    // key -> propertyPath (===).
                    case is_int($key) && is_string($item):
                        $result[$item] = $this->grabValue($data, $item);
                        $result[$item] = $this->transform($data, $item, $result[$item], $tags);
                        break;
                    // key -> propertyPath (!==)
                    case is_string($key) && is_string($item):
                        $result[$key] = $this->grabValue($data, $item);
                        $result[$key] = $this->transform($data, $item, $result[$key], $tags);
                        break;
                    // key -> recursive data processing
                    case is_string($key) && is_array($item):
                        $result[$key] = $this->convertByMap($this->grabValue($data, $key), $item, $tags);
                        break;
                    // key -> virtual field
                    case is_string($key) && is_callable($item):
                        $result[$key] = call_user_func_array($item, [$data]);
                        break;
                    // key -> data processing with transformer
                    case $item instanceof Meta:
                    case (new $item) instanceof Meta:
                        $meta = is_string($item) ? new $item : $item;
                        $result[$key] = $this->transformer->transform($data, $meta);
                        break;
                    // key -> recursive data processing with other schema
                    case $item instanceof Schema:
                    case (new $item) instanceof Schema:
                        $schemaClass = is_object($item) ? $item::class : $item;
                        $result[$key] = $this->convert($data[$key] ?? null, $schemaClass);
                        break;
                    default:
                        $result[$key] = null;
                }
            } catch (\Throwable) {
                $result[$key] = null;
            }
        }
        return $result;
    }

    private function transform(object|array|null $data, string $path, mixed $value, array $tags): mixed
    {
        $metas = $this->grabTransformMetas($data, $path);
        foreach ($metas as $meta) {
            $value = $this->transformer->transform($value, $meta, $tags);
        }
        return $value;
    }

    private function grabValue(object|array|null $value, string $path): mixed
    {
        if (null === $value) {
            return null;
        }
        $path = is_array($value) && 0 === mb_substr_count($path, '.') &&
        false === mb_strpos($path, '[')
            ? "[{$path}]"
            : $path;
        return $this->propertyAccessor->getValue($value, $path);
    }

    /**
     * @return Meta[]
     */
    private function grabTransformMetas(mixed $data, string $propertyPath): array
    {
        $result = [];
        $propertyName = $propertyPath;
        $propertyPathParts = explode('.', $propertyPath);
        if (count($propertyPathParts) > 1) {
            $propertyName = array_shift($propertyPathParts);
            $pathToObject = implode('.', $propertyPathParts);
            $object = $this->grabValue($data, $pathToObject);
            if (!is_object($object)) {
                return [];
            }
        }

        if (!is_object($data)) {
            return [];
        }

        if (property_exists($data, $propertyName)) {
            $reflectionProperty = new ReflectionProperty($data, $propertyName);
            $methodExtractor = new PropertyExtractor($reflectionProperty->class, $propertyName);
            while ($meta = $methodExtractor->fetch(Meta::class)) {
                $result[] = $meta;
            }
            if ($result) {
                return $result;
            }
        }

        foreach (['get', 'has', 'is'] as $prefix) {
            $methodName = $prefix . ucfirst($propertyName);
            if (method_exists($data, $methodName)) {
                $reflectionMethod = new \ReflectionMethod($data, $methodName);
                $methodExtractor = new MethodExtractor($reflectionMethod->class, $methodName);
                while ($meta = $methodExtractor->fetch(Meta::class)) {
                    $result[] = $meta;
                }
            }
            if ($result) {
                return $result;
            }
        }

        return [];
    }
}
