<?php
namespace WebStream\Annotation;

use WebStream\Module\Logger;
use WebStream\Exception\Extend\DatabaseException;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

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

    /** XML */
    private $xml;

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->xml = null;
        Logger::debug("Query xml object is clear.");
    }

    /**
     * Override
     */
    public function readAnnotation($refClass, $method, $container)
    {
        $reader = new DoctrineAnnotationReader();
        try {
            $refMethod = $refClass->getMethod($method);
            if (!$reader->getMethodAnnotation($refMethod, "\WebStream\Annotation\Inject")) {
                return;
            }

            $namespace = $refClass->getNamespaceName();

            if ($this->xml !== null) {
                Logger::debug("Query xml file read from cache.");
                $elems = $this->xml->xpath("//mapper[@namespace='$namespace']/*[@id='$this->id']");
                $this->query = trim($elems[0]);
            } else {
                $methodAnnotation = $reader->getMethodAnnotation($refMethod, "\WebStream\Annotation\Query");
                $filepath = STREAM_APP_ROOT . "/" . $methodAnnotation->getValue();
                Logger::debug("Query xml file: " . $filepath);

                if (file_exists($filepath)) {
                    $this->xml = simplexml_load_file($filepath);
                    $elems = $this->xml->xpath("//mapper[@namespace='$namespace']/*[@id='$this->id']");
                    if (empty($elems)) {
                        throw new DatabaseException("Unmatch namespace mapper attribute in query xml and model class: " . $namespace);
                    }
                    $this->query = trim($elems[0]);
                } else {
                    throw new DatabaseException("Query file is not found: " . $filepath);
                }
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
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
