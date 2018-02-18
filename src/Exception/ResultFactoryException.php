<?php

namespace RunetId\Client\Exception;

final class ResultFactoryException extends \InvalidArgumentException
{
    /**
     * @param string[] $expectedTypes
     * @param mixed    $value
     *
     * @return string
     */
    public static function typesMessage(array $expectedTypes, $value)
    {
        return sprintf(
            'Expected "%s", "%s" given.',
            implode('" or "', $expectedTypes),
            is_object($value) ? get_class($value) : gettype($value)
        );
    }
}