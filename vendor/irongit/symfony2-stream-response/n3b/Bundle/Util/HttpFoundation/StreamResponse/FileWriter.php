<?php

namespace n3b\Bundle\Util\HttpFoundation\StreamResponse;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Description of FileWriter
 *
 * @author neb
 */
class FileWriter implements StreamWriterInterface
{
    protected $file;
    
    public function __construct($path)
    {
        $this->file = new File($path);
    }
    
    public function write($stream_writer_option=null)
    {
        $this->file->openFile()->fpassthru();
    }
    
    public function getFile()
    {
        return $this->file;
    }
}
