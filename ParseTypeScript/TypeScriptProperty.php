<?php
/**
 * This file is part of the TypeScriptGeneratorBundle.
 */

namespace Vinatis\TypeScriptGeneratorBundle\ParseTypeScript;

/**
 * @author Irontec <info@irontec.com>
 * @author ddniel16 <ddniel16>
 * @link https://github.com/irontec
 */
class TypeScriptProperty
{
    public string $name;

    public string $type;

    public bool $isNullable;

    public bool $isOptional;

    public function __construct(string $name, string $type = 'unknown', bool $isNullable = false, bool $isOptional = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->isNullable = $isNullable;
        $this->isOptional = $isOptional;
    }

    public function __toString(): string
    {
        $separator = $this->isOptional ? '?: ' : ': ';

        return $this->name . $separator . $this->type . ($this->isNullable ? ' | null' : '');
    }
}
