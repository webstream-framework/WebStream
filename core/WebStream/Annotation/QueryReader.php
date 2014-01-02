<?php
namespace WebStream\Annotation;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use WebStream\Module\Logger;
use WebStream\Exception\ResourceNotFoundException;

/**
 * QueryReader
 * @author Ryuichi TANAKA.
 * @since 2013/12/28
 * @version 0.4
 */
class QueryReader extends AnnotationReader
{
    /** namespace */
    private $id;

    /** query */
    private $query;

    /**
     * Override
     */
    public function readAnnotation($refClass, $method, $container)
    {
        $reader = new DoctrineAnnotationReader();
        $refMethod = $refClass->getMethod($method);
        $namespace = $refClass->getNamespaceName();

        $methodAnnotation = $reader->getMethodAnnotation($refMethod, "\WebStream\Annotation\Query");
        $filepath = STREAM_ROOT . "/" . STREAM_APP_ROOT . "/" . $methodAnnotation->getValue();
        Logger::debug("Query xml file: " . $filepath);

        if (file_exists($filepath)) {
            $xml = simplexml_load_file($filepath);
            $elems = $xml->xpath("//mapper[@namespace='$namespace']/*[@id='$this->id']");
            $this->query = trim($elems[0]);
        } else {
            throw new ResourceNotFoundException("Query file is not found: " . $filepath);
        }
    }

    /**
     * SQLの属性IDを設定する
     * @param string 属性ID
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * SQLを返却する
     * @return string SQL
     */
    public function getQuery()
    {
        return $this->query;
    }
}
