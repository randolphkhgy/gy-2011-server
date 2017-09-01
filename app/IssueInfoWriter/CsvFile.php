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
            $this->writeToFile(implode(',', array_values($number)) . PHP_EOL);
        }

        return $this;
    }
}
