<?php

namespace Rinsvent\DTO2Data;

use ReflectionProperty;
use Rinsvent\AttributeExtractor\ClassExtractor;
use Rinsvent\AttributeExtractor\MethodExtractor;
use Rinsvent\AttributeExtractor\PropertyExtractor;
use Rinsvent\DTO2Data\Attribute\DataPath;
use Rinsvent\DTO2Data\Attribute\HandleTags;
use Rinsvent\DTO2Data\Attribute\Schema;
use Rinsvent\DTO2Data\DTO\Map;
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
        return $this->convertByMap($object, $schema->map, $tags);
    }

    public function convertByMap(object $object, array $map, array $tags = []): array
    {
        $data = [];

        $reflectionObject = new \ReflectionObject($object);
        foreach ($map as $propertyKey => $property) {
            if (is_array($property)) {
                $propertyName = $propertyKey;
            } else {
                $propertyName = $property;
            }

            $property = new ReflectionProperty($object, $propertyName);
            $value = $this->getValue($object, $property);
            $childMap = $property;
            if ($childMap && is_array($childMap) && !is_scalar($value)) {
                if (is_array($value)) {
                    $tmpValue = [];
                    foreach ($value as $objectDataItem) {
                        $tmpValue[] = $this->convertByMap($objectDataItem, $childMap, $tags);
                    }
                    $value = $tmpValue;
                } else {
                    $value = $this->convertByMap($value, $childMap, $tags);
                }
            }
            $this->processTransformers($property, $value, $tags);
            $dataPath = $this->grabDataPath($property, $tags);
            $dataPath = $dataPath ?? $propertyName;
            $data[$dataPath] = $value;
        }

//        foreach ($map->methods as $methodName => $childMap) {
//            $method = new \ReflectionMethod($object, $methodName);
//            $value = $this->getMethodValue($object, $method);
//            if ($childMap) {
//                $value = $this->convertByMap($value, $childMap, $tags);
//            }
//            $this->processMethodTransformers($method, $value, $tags);
//            $dataPath = $this->grabMethodDataPath($method, $tags);
//            $dataPath = $dataPath ?? $propertyName;
//            $data[$dataPath] = $value;
//        }

        $this->processClassTransformers($reflectionObject, $data, $tags);

        return $data;
    }

    public function convertArrayByMap(array $data, array $map, array $tags = []): array
    {

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
                $reflectionMethod = new \ReflectionMethod($object, $tagsMeta->method);
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

    protected function processMethodTransformers(\ReflectionMethod $method, &$data, array $tags): void
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

    private function getMethodValue(object $object, \ReflectionMethod $method)
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

    private function grabMethodDataPath(\ReflectionMethod $method, array $tags): ?string
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
