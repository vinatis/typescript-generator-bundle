<?php
/**
 * This file is part of the TypeScriptGeneratorBundle.
 */

namespace Vinatis\TypeScriptGeneratorBundle\ParseTypeScript;

class TypeScriptProperty
{
    public function __construct(public string $name, public string $type = 'unknown', public bool $isNullable = false, public bool $isOptional = false)
    {
    }

    public function __toString(): string
    {
        $separator = $this->isOptional ? '?: ' : ': ';

        return $this->name.$separator.$this->type.($this->isNullable ? ' | null' : '');
    }
}
