<?php
namespace WebStream\IO;

/**
 * ConsoleOutputStream
 * @author Ryuichi TANAKA.
 * @since 2016/02/27
 * @version 0.7
 */
class ConsoleOutputStream extends OutputStream
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct("");
    }

    /**
     * {@inheritdoc}
     */
    public function write($buf, int $off = null, int $len = null)
    {
        $data = null;
        if ($off === null && $len === null) {
            $data = $buf;
        } elseif ($off !== null && $len === null) {
            $data = substr($buf, $off);
        } elseif ($off === null && $len !== null) {
            $data = substr($buf, 0, $len);
        } else {
            $data = substr($buf, $off, $len);
        }

        $this->stream .= $data;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->stream = null;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        echo $this->stream;
    }
}
