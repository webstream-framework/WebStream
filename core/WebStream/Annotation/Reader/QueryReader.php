<?php
namespace WebStream\Annotation\Reader;

use WebStream\Module\Utility;
use WebStream\Annotation\container\annotationContainer;
use WebStream\Annotation\container\annotationListContainer;
use WebStream\Exception\Extend\AnnotationException;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * QueryReader
 * @author Ryuichi TANAKA.
 * @since 2013/12/28
 * @version 0.4
 */
class QueryReader extends AbstractAnnotationReader
{
    use Utility;

    /** query container */
    private $queryContainer;

    /**
     * {@inheritdoc}
     */
    public function onRead()
    {
        $this->annotation = $this->reader->getAnnotation("WebStream\Annotation\Query");
        $this->queryContainer = new AnnotationContainer();
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->annotation === null) {
            return;
        }

        try {
            $refClass = $this->reader->getReflectionClass();
            $container = $this->reader->getContainer();
            $action = $this->camel2snake($container->router->action());

            foreach ($this->annotation as $classpath => $annotation) {
                // @Queryは複数指定を許可(複数のxmlファイル指定可)
                foreach ($annotation as $query) {
                    if ($this->queryContainer->{$classpath} === null) {
                        $this->queryContainer->{$classpath} = new AnnotationListContainer();
                    }
                    $this->queryContainer->{$classpath}->pushAsLazy(function () use ($query, $classpath) {
                        return file_exists($query->file) ? simplexml_load_file($query->file) : null;
                    });
                }
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        }
    }

    /**
     * クエリオブジェクトを返却する
     * @param mixed SQL文字列またはnull
     */
    public function getQuery($queryKey, $queryId)
    {
        $list = $this->queryContainer->get($queryKey);
        $classpath = $this->reader->getReflectionClass()->getNamespaceName();

        foreach ($list as $func) {
            $xml = $func->fetch();
            if ($xml !== null) {
                $query = $xml->xpath("//mapper[@namespace='$classpath']/*[@id='$queryId']");
                if (!empty($query)) {
                    $queryMap = ["sql" => trim($query[0]), "method" => $query[0]->getName()];

                    return $queryMap;
                }
            }
        }

        return null;
    }

}
