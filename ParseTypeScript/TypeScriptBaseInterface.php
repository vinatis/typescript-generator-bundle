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

    /**
     * Nettoie le type en enlevant les marqueurs custom
     */
    private function cleanCustomType(string $type): string
    {
        if (str_starts_with($type, 'CUSTOM:')) {
            return substr($type, 7);
        }

        if (str_starts_with($type, 'CUSTOM_WITH_IMPORT:')) {
            return substr($type, 19);
        }

        return $type;
    }

    public function __toString()
    {
        $imports = [];
        $pieces = [];

        foreach ($this->properties as $property) {

            // Vérifier d'abord PARAM_UNKNOWN avant de nettoyer
            if (Parser::PARAM_UNKNOWN === $property->type) {
                continue;
            }

            // Nettoyer le type pour l'affichage
            $displayType = $this->cleanCustomType($property->type);

            if (in_array($displayType, ['number', 'string', 'boolean', 'any[]']) === false) {

                // Vérifier si c'est un type custom (pas d'import nécessaire)
                if (!str_starts_with($property->type, 'CUSTOM:')) {
                    $rel = str_replace('[]', '', $displayType);

                    if ($this->name !== $rel) {

                        if (str_starts_with($property->type, 'CUSTOM_WITH_IMPORT:')) {
                            $matches = [];
                            preg_match('/\b[A-Z][a-zA-Z]*\b/', $rel, $matches);
                            $rel = $matches[0];
                        }

                        $imports[] = "import type { " . $rel . " } from './" . $rel . "';";
                    }
                }
            }

            // Utiliser le type nettoyé pour l'affichage
            $propertyForDisplay = new TypeScriptProperty($property->name, $displayType, $property->isNullable, $property->isOptional);
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
