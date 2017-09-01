<?php

namespace App\IssueInfoWriter;

abstract class TmpFile
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
     *
     * @throws \Exception
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
     * @param  array  $array
     * @return $this
     */
    abstract public function write(array $array = []);

    /**
     * @param  array  $columns
     * @return $this
     */
    protected function setColumns(array $columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @param  string  $data
     * @return $this
     */
    protected function writeToFile($data)
    {
        fwrite($this->fileHandler, $data);
        return $this;
    }

    /**
     * @param  bool  $flag
     * @return $this
     */
    protected function setWrittenFlag($flag)
    {
        $this->written = $flag;
        return $this;
    }

    /**
     * @return $this
     */
    public function prepare()
    {
        $this->closeFile();
        chmod($this->file, 0644);
        return $this;
    }

    /**
     * @return array|null
     *
     * @throws \Exception
     */
    protected function createNewFile()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'issue_');

        if ($this->file) {
            $this->fileHandler = fopen($this->file, 'w');
            $this->isOpened    = true;
            return compact('file', 'fileHandler');
        } else {
            throw new \Exception('Failed to create the csv file for writing issues to database.');
        }
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
