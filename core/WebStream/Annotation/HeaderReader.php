<?php
namespace WebStream\Annotation;

use WebStream\Module\Utility;
use WebStream\Module\Logger;
use WebStream\Exception\Extend\AnnotationException;
use WebStream\Exception\Extend\InvalidRequestException;
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
    public function readAnnotation($refClass, $method, $container)
    {
        $reader = new DoctrineAnnotationReader();

        try {
            $refMethod = $refClass->getMethod($method);
            if ($reader->getMethodAnnotation($refMethod, "\WebStream\Annotation\Inject")) {
                $annotation = $reader->getMethodAnnotation($refMethod, "\WebStream\Annotation\Header");
                if ($annotation !== null) {
                    $allowMethods = $annotation->getAllowMethod();
                    if ($allowMethods !== null) {
                        if (!is_array($allowMethods)) {
                            $allowMethods = [$allowMethods];
                        }

                        // 指定したリクエストメソッドのチェック
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

                        Logger::debug("Accepted request method '" . $container->request->requestMethod() . "'in " . $refMethod->class . "#" . $method);
                    }

                    $ext = $annotation->getContentType();
                    if ($ext !== null) {
                        $contentType = $this->contentTypeList[$ext];
                        if ($contentType === null) {
                            $errorMsg = "Invalid value '$ext' in 'contentType' attribute of @Header.";
                            throw new AnnotationException($errorMsg);
                        }
                        $this->mime = $ext;
                        Logger::debug("Accepted contentType '$ext' in " . $refMethod->class . "#" . $method);
                    }
                }
            }
        } catch (DoctrineAnnotationException $e) {
            throw new AnnotationException($e->getMessage());
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
