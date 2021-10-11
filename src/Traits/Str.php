<?php
// +----------------------------------------------------------------------
// | 注释
// +----------------------------------------------------------------------
// | Copyright (c) 义幻科技 http://www.mobimedical.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: Michael23
// +----------------------------------------------------------------------
// | date: 2021-10-11
// +----------------------------------------------------------------------
namespace MicroTool\HyperfRpcClient\Traits;

trait Str
{
    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    protected static $snakeCache = [];

    /**
     * Convert a string to snake case.
     */
    public function snake($value, $delimiter = '_')
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = $this->lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param string $value
     * @return string
     */
    public function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }


    /**
     * Replace a given value in the string sequentially with an array.
     */
    public function replaceArray($search, array $replace, $subject)
    {
        foreach ($replace as $value) {
            $subject = $this->replaceFirst($search, (string)$value, $subject);
        }

        return $subject;
    }

    /**
     * Replace the first occurrence of a given value in the string.
     */
    public function replaceFirst($search, $replace, $subject)
    {
        if ($search == '') {
            return $subject;
        }

        $position = strpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Replace the last occurrence of a given value in the string.
     */
    public function replaceLast($search, $replace, $subject)
    {
        $position = strrpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }
}
