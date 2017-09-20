<?php

namespace App\IssueInfoWriter;

class PgDataFile extends TmpFile
{
    /**
     * @param  array  $array
     * @return $this
     */
    public function write(array $array = [])
    {
        $columns = array_keys(head($array));
        $this->setColumns($columns)->setWrittenFlag(true);

        foreach ($array as $number) {
            $this->writeToFile(implode("\t", array_map([$this, 'value'], $number)) . PHP_EOL);
        }

        return $this;
    }

    /**
     * @param  mixed  $value
     * @return mixed
     */
    protected function value($value)
    {
        switch (gettype($value)) {
            case 'NULL':
                return '\N';
            case 'integer':
                return $value;
            case 'double':
                return $value;
            default:
                return $this->escape(strval($value));
        }
    }

    /**
     * @param  string  $string
     * @return string
     */
    protected function escape($string)
    {
        $replace = [
            "\r" => '\r',
            "\n" => '\n',
            "\t" => '\t',
            '\\' => '\\\\',
        ];

        return str_replace(array_keys($replace), array_values($replace), $string);
    }
}
