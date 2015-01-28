<?php
namespace WebStream\Annotation\Reader;

use WebStream\Module\Logger;
use WebStream\Module\Utility;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Exception\Extend\AnnotationException;
use WebStream\Exception\Extend\InvalidRequestException;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * HeaderReader
 * @author Ryuichi TANAKA.
 * @since 2013/10/20
 * @version 0.4
 */
class HeaderReader extends AbstractAnnotationReader implements AnnotationReadInterface
{
    use Utility;

    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotation;

    /**
     * @var array<string> mimeタイプリスト
     */
    private $contentTypeList = [
        'txt'   => 'text/plain',
        'jpeg'  => 'image/jpeg',
        'jpg'   => 'image/jpeg',
        'gif'   => 'image/gif',
        'png'   => 'image/png',
        'tiff'  => 'image/tiff',
        'tif'   => 'image/tiff',
        'bmp'   => 'image/bmp',
        'ico'   => 'image/x-icon',
        'svg'   => 'image/svg+xml',
        'xml'   => 'application/xml',
        'xsl'   => 'application/xml',
        'rss'   => 'application/rss+xml',
        'rdf'   => 'application/rdf+xml',
        'atom'  => 'application/atom+xml',
        'zip'   => 'application/zip',
        'html'  => 'text/html',
        'htm'   => 'text/html',
        'css'   => 'text/css',
        'csv'   => 'text/csv',
        'js'    => 'text/javascript',
        'jsonp' => 'text/javascript',
        'json'  => 'application/json',
        'pdf'   => 'application/pdf',
        'file'  => 'application/octet-stream'
    ];

    /**
     * {@inheritdoc}
     */
    public function onRead()
    {
        $this->annotation = $this->reader->getAnnotation("WebStream\Annotation\Header");
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $annotationContainer = new AnnotationContainer();

        if ($this->annotation === null) {
            return $annotationContainer;
        }

        try {
            $refClass = $this->reader->getReflectionClass();
            $container = $this->reader->getContainer();
            $action = $this->camel2snake($container->action);

            while ($refClass !== false) {
                $classpathWithAction = $refClass->getName() . "#" . $action;
                if (array_key_exists($classpathWithAction, $this->annotation)) {
                    // 複数指定されても先頭のみ有効
                    $annotation = array_shift($this->annotation[$classpathWithAction]);
                    $annotationContainer = $annotation;
                    $allowMethods = $annotation->allowMethod;

                    // 指定無しの場合はチェックしない(すべてのメソッドを許可する)
                    if ($allowMethods !== null) {
                        if (!is_array($allowMethods)) {
                            $allowMethods = [$allowMethods];
                        }

                        for ($i = 0; $i < count($allowMethods); $i++) {
                            if (!preg_match("/^(?:(?:P(?:OS|U)|GE)T|(?:p(?:os|u)|ge)t|DELETE|delete)$/", $allowMethods[$i])) {
                                $errorMsg = "Invalid value '" . $allowMethods[$i] . "' in 'allowMethod' attribute of @Header.";
                                throw new AnnotationException($errorMsg);
                            }
                            $allowMethods[$i] = strtoupper($allowMethods[$i]);
                        }

                        // 複数指定した場合、一つでも許可されていればOK
                        if (!in_array($container->request->requestMethod(), $allowMethods)) {
                            $errorMsg = "Not allowed request method '" . $container->request->requestMethod() . "' in " . $container->request->getPathInfo();
                            throw new InvalidRequestException($errorMsg);
                        }
                    }

                    Logger::debug("Accepted request method '" . $container->request->requestMethod() . "' in " . $classpathWithAction);

                    $ext = $annotation->contentType;
                    if ($ext !== null) {
                        $contentType = $this->contentTypeList[$ext];
                        if ($contentType === null) {
                            $errorMsg = "Invalid value '$ext' in 'contentType' attribute of @Header.";
                            throw new AnnotationException($errorMsg);
                        }
                        Logger::debug("Accepted contentType '$ext' in " . $classpathWithAction);
                    }

                    // 読み込みできた時点で終了
                    break;
                }

                $refClass = $refClass->getParentClass();
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e);
        }

        return $annotationContainer;
    }
}
