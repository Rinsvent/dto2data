<?php

namespace Rinsvent\DTO2Data;

use ReflectionMethod;
use ReflectionProperty;
use Rinsvent\AttributeExtractor\ClassExtractor;
use Rinsvent\AttributeExtractor\MethodExtractor;
use Rinsvent\AttributeExtractor\PropertyExtractor;
use Rinsvent\DTO2Data\Attribute\DataPath;
use Rinsvent\DTO2Data\Attribute\HandleTags;
use Rinsvent\DTO2Data\Attribute\Schema;
use Rinsvent\DTO2Data\Resolver\TransformerResolverStorage;
use Rinsvent\DTO2Data\Transformer\Meta;
use Rinsvent\DTO2Data\Transformer\TransformerInterface;

class Dto2DataConverter
{
    public function getTags(object $object, array $tags = []): array
    {
        return $this->processTags($object, $tags);
    }

    public function convert(object $object, array $tags = []): array
    {
        $tags = empty($tags) ? ['default'] : $tags;
        $schema = $this->grabSchema($object, $tags);
        return $this->convertObjectByMap($object, $schema->map, $tags);
    }

    public function convertObjectByMap(object $object, array $map, array $tags = []): array
    {
        $data = [];

        $reflectionObject = new \ReflectionObject($object);
        foreach ($map as $key => $propertyInfo) {
            $sourceName = is_array($propertyInfo) ? $key : $propertyInfo;

            if (!method_exists($object, $sourceName) && !property_exists($object, $sourceName)) {
                continue;
            }

            $value = $this->grabValue($object, $sourceName);

            // Если нет карты, то не сериализуем.
            if (is_object($value) && is_array($propertyInfo)) {
                $value = $this->convertObjectByMap($value, $propertyInfo, $tags);
            } elseif (is_iterable($value)) {
                $map = is_array($propertyInfo) ? $propertyInfo : null;
                $value = $this->convertArrayByMap($value, $map, $tags);
            } elseif (!is_scalar($value) && null !== $value) {
                continue;
            }

            $this->processIterationTransformers($object, $sourceName, $value, $tags);
            $dataPath = $this->grabIterationDataPath($object, $sourceName, $tags);
            $data[$dataPath] = $value;
        }

        $this->processClassTransformers($reflectionObject, $data, $tags);

        return $data;
    }

    public function convertArrayByMap($data, ?array $map, array $tags = []): ?array
    {
        $tempValue = [];
        foreach ($data as $key => $item) {
            if (is_scalar($item)) {
                $tempValue[$key] = $item;
                continue;
            }
            if (is_iterable($item) && $map) {
                $tempValue[$key] = $this->convertArrayByMap($item, $map, $tags);
                continue;
            }
            if (is_object($item) && $map) {
                $tempValue[$key] = $this->convertObjectByMap($item, $map, $tags);
                continue;
            }
        }
        return $tempValue ?: null;
    }

    protected function grabValue(object $object, $sourceName)
    {
        if (method_exists($object, $sourceName)) {
            $reflectionSource = new ReflectionMethod($object, $sourceName);
            return $this->getMethodValue($object, $reflectionSource);
        } elseif (property_exists($object, $sourceName)) {
            $reflectionSource = new ReflectionProperty($object, $sourceName);
            return $this->getValue($object, $reflectionSource);
        }

        return null;
    }

    public function processIterationTransformers(object $object, string $sourceName, &$value, array $tags): void
    {
        if (method_exists($object, $sourceName)) {
            $reflectionSource = new ReflectionMethod($object, $sourceName);
            $this->processMethodTransformers($reflectionSource, $value, $tags);
        } elseif (property_exists($object, $sourceName)) {
            $reflectionSource = new ReflectionProperty($object, $sourceName);
            $this->processTransformers($reflectionSource, $value, $tags);
        }
    }

    public function grabIterationDataPath(object $object, string $sourceName, array $tags): string
    {
        if (method_exists($object, $sourceName)) {
            $reflectionSource = new ReflectionMethod($object, $sourceName);
            $dataPath = $this->grabMethodDataPath($reflectionSource, $tags);
        } elseif (property_exists($object, $sourceName)) {
            $reflectionSource = new ReflectionProperty($object, $sourceName);
            $dataPath = $this->grabDataPath($reflectionSource, $tags);
        }
        return $dataPath ?? $sourceName;
    }

    /**
     * Получаем теги для обработки
     */
    protected function processTags(object $object, array $tags): array
    {
        $classExtractor = new ClassExtractor($object::class);
        /** @var HandleTags $tagsMeta */
        if ($tagsMeta = $classExtractor->fetch(HandleTags::class)) {
            if (method_exists($object, $tagsMeta->method)) {
                $reflectionMethod = new ReflectionMethod($object, $tagsMeta->method);
                if (!$reflectionMethod->isPublic()) {
                    $reflectionMethod->setAccessible(true);
                }
                $methodTags = $reflectionMethod->invoke($object, ...[$tags]);
                if (!$reflectionMethod->isPublic()) {
                    $reflectionMethod->setAccessible(false);
                }
                return $methodTags;
            }
        }

        return $tags;
    }

    /**
     * Трнансформируем на уровне класса
     */
    protected function processClassTransformers(\ReflectionObject $object, &$data, array $tags): void
    {
        $className = $object->getName();
        $classExtractor = new ClassExtractor($className);
        /** @var Meta $transformMeta */
        while ($transformMeta = $classExtractor->fetch(Meta::class)) {
            $transformMeta->returnType = $className;
            $filteredTags = array_diff($tags, $transformMeta->tags);
            if (count($filteredTags) === count($tags)) {
                continue;
            }

            $transformer = $this->grabTransformer($transformMeta);
            $transformer->transform($data, $transformMeta);
        }
    }

    /**
     * Трнансформируем на уровне свойст объекта
     */
    protected function processTransformers(\ReflectionProperty $property, &$data, array $tags): void
    {
        $propertyName = $property->getName();
        $propertyExtractor = new PropertyExtractor($property->class, $propertyName);
        /** @var Meta $transformMeta */
        while ($transformMeta = $propertyExtractor->fetch(Meta::class)) {
            $filteredTags = array_diff($tags, $transformMeta->tags);
            if (count($filteredTags) === count($tags)) {
                continue;
            }
            /** @var \ReflectionNamedType $reflectionPropertyType */
            $reflectionPropertyType = $property->getType();
            $propertyType = $reflectionPropertyType->getName();
            $transformMeta->returnType = $propertyType;
            $transformMeta->allowsNull = $reflectionPropertyType->allowsNull();
            $transformer = $this->grabTransformer($transformMeta);
            $transformer->transform($data, $transformMeta);
        }
    }

    protected function processMethodTransformers(ReflectionMethod $method, &$data, array $tags): void
    {
        $methodName = $method->getName();
        $methodExtractor = new MethodExtractor($method->class, $methodName);
        /** @var Meta $transformMeta */
        while ($transformMeta = $methodExtractor->fetch(Meta::class)) {
            $filteredTags = array_diff($tags, $transformMeta->tags);
            if (count($filteredTags) === count($tags)) {
                continue;
            }
            /** @var \ReflectionNamedType $reflectionMethodType */
            $reflectionMethodType = $method->getReturnType();
            $methodType = $reflectionMethodType->getName();
            $transformMeta->returnType = $methodType;
            $transformMeta->allowsNull = $reflectionMethodType->allowsNull();
            $transformer = $this->grabTransformer($transformMeta);
            $transformer->transform($data, $transformMeta);
        }
    }

    protected function grabTransformer(Meta $meta): TransformerInterface
    {
        $storage = TransformerResolverStorage::getInstance();
        $resolver = $storage->get($meta::TYPE);
        return $resolver->resolve($meta);
    }

    private function getValue(object $object, \ReflectionProperty $property)
    {
        if (!$property->isInitialized($object)) {
            return null;
        }

        if (!$property->isPublic()) {
            $property->setAccessible(true);
        }

        $value = $property->getValue($object);

        if (!$property->isPublic()) {
            $property->setAccessible(false);
        }

        return $value;
    }

    private function getMethodValue(object $object, ReflectionMethod $method)
    {
        if (!$method->isPublic()) {
            $method->setAccessible(true);
        }

        $value = $method->invoke($object);

        if (!$method->isPublic()) {
            $method->setAccessible(false);
        }

        return $value;
    }

    private function grabSchema(object $object, array $tags): ?Schema
    {
        $classExtractor = new ClassExtractor($object::class);
        /** @var Schema $schema */
        while ($schema = $classExtractor->fetch(Schema::class)) {
            $filteredTags = array_diff($tags, $schema->tags);
            if (count($filteredTags) === count($tags)) {
                continue;
            }
            return $schema;
        }

        return null;
    }

    private function grabDataPath(\ReflectionProperty $property, array $tags): ?string
    {
        $propertyName = $property->getName();
        $propertyExtractor = new PropertyExtractor($property->class, $propertyName);
        /** @var DataPath $schema */
        while ($dataPath = $propertyExtractor->fetch(DataPath::class)) {
            $filteredTags = array_diff($tags, $dataPath->tags);
            if (count($filteredTags) === count($tags)) {
                continue;
            }
            return $dataPath->path;
        }

        return null;
    }

    private function grabMethodDataPath(ReflectionMethod $method, array $tags): ?string
    {
        $methodName = $method->getName();
        $methodExtractor = new MethodExtractor($method->class, $methodName);
        /** @var DataPath $schema */
        while ($dataPath = $methodExtractor->fetch(DataPath::class)) {
            $filteredTags = array_diff($tags, $dataPath->tags);
            if (count($filteredTags) === count($tags)) {
                continue;
            }
            return $dataPath->path;
        }

        return null;
    }
}
