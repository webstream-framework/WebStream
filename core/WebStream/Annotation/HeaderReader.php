<?php
namespace WebStream\Annotation;

use WebStream\Module\Utility;
use WebStream\Exception\AnnotationException;
use WebStream\Exception\InvalidRequestException;
use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;

/**
 * HeaderReader
 * @author Ryuichi TANAKA.
 * @since 2013/10/20
 * @version 0.4
 */
class HeaderReader extends AnnotationReader
{
    use Utility;

    /** mime type */
    private $mime = "html";

    /** contentType */
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
     * @Override
     */
    public function readAnnotation($refClass, $methodName, $container)
    {
        $reader = new DoctrineAnnotationReader();

        try {
            while ($refClass !== false) {
                $methods = $refClass->getMethods();
                foreach ($methods as $method) {
                    if ($refClass->getName() !== $method->class || $methodName !== $method->name) {
                        continue;
                    }
                    $annotations = $reader->getMethodAnnotations($method);

                    $isInject = false;
                    foreach ($annotations as $annotation) {
                        if ($annotation instanceof Inject) {
                            $isInject = true;
                        }
                    }

                    if ($isInject) {
                        foreach ($annotations as $annotation) {
                            if ($annotation instanceof Header) {
                                $ext = $annotation->getContentType();
                                if ($ext !== null) {
                                    $contentType = $this->contentTypeList[$ext];
                                    if ($contentType === null) {
                                        $errorMsg = "Invalid value '$ext' in 'contentType' attribute of @Header.";
                                        throw new AnnotationException($errorMsg);
                                    }
                                    $this->mime = $ext;
                                }

                                $requestMethod = $annotation->getAllowMethod();
                                if ($requestMethod !== null) {
                                    if (!preg_match("/^(?:(?:P(?:OS|U)|GE)T|(?:p(?:os|u)|ge)t|DELETE|delete)$/", $requestMethod)) {
                                        $errorMsg = "Invalid value '$requestMethod' in 'allowMethod' attribute of @Header.";
                                        throw new AnnotationException($errorMsg);
                                    }
                                    $requestMethod = strtoupper($requestMethod);
                                }

                                if ($container->request->requestMethod() !== $requestMethod) {
                                    $errorMsg = "Not allowed request method '$requestMethod' in " . $container->request->getPathInfo();
                                    throw new InvalidRequestException($errorMsg);
                                }
                            }
                        }
                    }
                }

                $refClass = $refClass->getParentClass();
            }

        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
        } catch (\ReflectionException $e) {
            throw new ClassNotFoundException("Class not found: " . $classpath);
        }
    }

    /**
     * MIMEタイプを返却する
     * @return string MIMEタイプ
     */
    public function getMimeType()
    {
        return $this->mime;
    }
}
