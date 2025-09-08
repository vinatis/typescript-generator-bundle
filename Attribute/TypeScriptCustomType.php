<?php


namespace Vinatis\TypeScriptGeneratorBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class TypeScriptCustomType
{
    public function __construct(
        public readonly string $type
    ) {
    }
}
