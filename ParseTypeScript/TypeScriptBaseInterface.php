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
class TypeScriptBaseInterface
{
    public string $name;

    /**
     * @var TypeScriptProperty[]
     */
    public array $properties = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    private function isCustomType(string $type): bool
    {
        if (str_starts_with($type, 'CUSTOM:')) {
            return true;
        }

        return false;
    }

    private function cleanCustomType(string $type): string
    {
        if (str_starts_with($type, 'CUSTOM:')) {
            return substr($type, 7);
        }

        return $type;
    }

    public function __toString()
    {
        $imports = [];
        $pieces = [];

        foreach ($this->properties as $property) {

            if (Parser::PARAM_UNKNOWN === $property->type) {
                continue;
            }

            $displayType = $this->cleanCustomType($property->type);

            if (in_array($displayType, ['number', 'string', 'boolean', 'any[]']) === false) {
                if (!$this->isCustomType($property->type)) {
                    $rel = str_replace('[]', '', $displayType);

                    if ($this->name !== $rel) {
                        $imports[] = 'import { ' . $rel . ' } from "./' . $rel . '";';
                    }
                }
            }

            $propertyForDisplay = new TypeScriptProperty($property->name, $displayType, $property->isNullable);
            $pieces[] = '  ' . (string) $propertyForDisplay  . ';';
        }

        $result = "";
        $result .= implode("\n", array_unique($imports));
        $result .= "\nexport interface {$this->name} {\n";
        $result .= implode("\n", $pieces);
        $result .= "\n}\n";

        return $result;
    }
}
