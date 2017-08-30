<?php

namespace App\IssueInfoWriter;

class CsvFile
{
    /**
     * @var bool|string
     */
    protected $file;

    /**
     * @var bool|resource
     */
    protected $fileHandler = false;

    /**
     * @var bool
     */
    protected $written = false;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var bool
     */
    protected $isOpened = false;

    /**
     * CsvFile constructor.
     */
    public function __construct()
    {
        $this->createNewFile();
    }

    /**
     * @return bool|string
     */
    public function file()
    {
        return $this->file;
    }

    /**
     * @return array
     */
    public function columns()
    {
        return $this->columns;
    }

    /**
     * @return bool
     */
    public function isWritten()
    {
        return $this->written;
    }

    /**
     * @param  int    $lotteryId
     * @param  array  $array
     * @return $this
     */
    public function write($lotteryId, array $array = [])
    {
        $this->columns = array_merge(['lotteryid'], array_keys(head($array)));

        array_walk($array, function ($number) use ($lotteryId) {
            fwrite($this->fileHandler, implode(',', array_merge([$lotteryId], array_values($number))) . PHP_EOL);
        });

        $this->written = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function prepare()
    {
        $this->closeFile();
        return $this;
    }

    /**
     * @return array|null
     */
    protected function createNewFile()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'issue_');

        if ($this->file) {
            $this->fileHandler = fopen($this->file, 'w');
            $this->isOpened    = true;
            return compact('file', 'fileHandler');
        }
        return null;
    }

    /**
     * @return bool
     */
    protected function isFileClosed()
    {
        return (! $this->isOpened);
    }

    /**
     * @return $this
     */
    protected function closeFile()
    {
        if (! $this->isFileClosed()) {
            fclose($this->fileHandler);
            $this->isOpened = false;
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function deleteFile()
    {
        $this->closeFile();
        unlink($this->file);
        return $this;
    }

    function __destruct()
    {
        $this->deleteFile();
    }
}