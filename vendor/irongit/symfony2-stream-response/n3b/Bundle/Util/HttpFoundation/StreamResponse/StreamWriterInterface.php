<?php

namespace n3b\Bundle\Util\HttpFoundation\StreamResponse;

/**
 *
 * @author neb
 */
interface StreamWriterInterface
{
    public function write($stream_writer_option = null);
}

