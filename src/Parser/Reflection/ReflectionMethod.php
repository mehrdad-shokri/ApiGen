<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use TokenReflection\IReflectionParameter;

final class ReflectionMethod extends AbstractReflectionElement implements MethodReflectionInterface
{
    public function isPrivate(): bool
    {
        return $this->reflection->isPrivate();
    }

    public function isProtected(): bool
    {
        return $this->reflection->isProtected();
    }

    public function isPublic(): bool
    {
        return $this->reflection->isPublic();
    }

    public function getDeclaringClass(): ?ClassReflectionInterface
    {
        $className = $this->reflection->getDeclaringClassName();
        return $className === '' ? null : $this->getParsedClasses()[$className];
    }

    public function getDeclaringClassName(): string
    {
        return (string) $this->reflection->getDeclaringClassName();
    }

    public function isAbstract(): bool
    {
        return $this->reflection->isAbstract();
    }

    public function isFinal(): bool
    {
        return $this->reflection->isFinal();
    }

    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }

    public function getDeclaringTrait(): ?ClassReflectionInterface
    {
        $traitName = (string) $this->reflection-> getDeclaringTraitName();
        return $traitName === '' ? null : $this->getParsedClasses()[$traitName];
    }

    public function getDeclaringTraitName(): string
    {
        return (string) $this->reflection->getDeclaringTraitName();
    }

    public function getOriginalName(): string
    {
        return (string) $this->reflection->getOriginalName();
    }

    public function getImplementedMethod(): ?MethodReflectionInterface
    {
        foreach ($this->getDeclaringClass()->getOwnInterfaces() as $interface) {
            if ($interface->hasMethod($this->getName())) {
                return $interface->getMethod($this->getName());
            }
        }

        return null;
    }

    public function getOverriddenMethod(): ?MethodReflectionInterface
    {
        $parent = $this->getDeclaringClass()->getParentClass();
        if ($parent === null) {
            return null;
        }

        foreach ($parent->getMethods() as $method) {
            if ($method->getName() === $this->getName()) {
                if (! $method->isPrivate() && ! $method->isAbstract()) {
                    return $method;
                }

                return null;
            }
        }

        return null;
    }

    /**
     * @var ParameterReflectionInterface[]
     */
    protected $parameters;

    public function getShortName(): string
    {
        return $this->reflection->getShortName();
    }

    public function returnsReference(): bool
    {
        return $this->reflection->returnsReference();
    }

    /**
     * @return ParameterReflectionInterface[]
     */
    public function getParameters(): array
    {
        if ($this->parameters === null) {
            $this->parameters = array_map(function (IReflectionParameter $parameter) {
                return $this->transformerCollector->transformReflectionToElement($parameter);
            }, $this->reflection->getParameters());
        }

        return $this->parameters;
    }
}
