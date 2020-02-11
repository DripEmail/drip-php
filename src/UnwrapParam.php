<?php


namespace Drip;
use Drip\Exception\InvalidArgumentException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

trait UnwrapParam
{
    /**
     * @param array|mixed $param
     * @param $paramName
     * @return array
     * @throws InvalidArgumentException
     */
    protected static function unwrapParam($param, $paramName)
    {
        try {
            if (is_array($param)) {
                v::key($paramName, v::notEmpty())->assert($param);
                $unwrappedParam = $param[$paramName];
                unset($param[$paramName]); // clear it from the params
            } else {
                $unwrappedParam = $param;
                $param = [];
            }
            return [$unwrappedParam, $param];
        } catch (ValidationException $e) {
            throw new InvalidArgumentException($e->getFullMessage());
        }
    }
}