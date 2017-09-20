<?php

namespace App\IssueInfoWriter;

class CsvFile extends TmpFile
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
            $this->writeToFile(implode(',', array_map([$this, 'formatValue'], array_values($number))) . PHP_EOL);
        }

        return $this;
    }

    /**
     * @param  mixed  $value
     * @return string
     */
    protected function formatValue($value)
    {
        if (is_null($value)) {
            return '';
        } else {
            $string = (string) $value;
            return ($this->quotesNeeded($string)) ? $this->escapeString($string) : $string;
        }
    }

    /**
     * @param  string  $string
     * @return string
     */
    protected function escapeString($string)
    {
        return '"' . str_replace('"', '""', $string) . '"';
    }

    /**
     * @param  string  $string
     * @return bool
     */
    protected function quotesNeeded($string)
    {
        $strlen = strlen($string);
        if ($strlen) {
            $quoteStarted  = ($string[0] == '"');
            $quoteEnded    = (substr($string, -1) == '"');
            $commaIncluded = (strpos($string, ',') !== false);

            return ($quoteStarted || $quoteEnded || $commaIncluded);
        }
        return false;
    }
}
