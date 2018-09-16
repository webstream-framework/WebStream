<?php
namespace WebStream\Annotation\Attributes;

use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IAnnotatable;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Base\IRead;
use WebStream\Container\Container;
use WebStream\Exception\Extend\AnnotationException;
use WebStream\Exception\Extend\InvalidRequestException;

/**
 * Header
 * @author Ryuichi TANAKA.
 * @since 2013/10/20
 * @version 0.4
 *
 * @Annotation
 * @Target("METHOD")
 */
class Header extends Annotation implements IMethod, IRead
{
    /**
     * @var array<string> 注入アノテーション情報
     */
    private $injectAnnotation;

    /**
     * @var array<string> 読み込みアノテーション情報
     */
    private $readAnnotation;

    /**
     * @var array<string, string> mimeタイプリスト
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
    public function onInject(array $injectAnnotation)
    {
        $defaultInjectAnnotation = ['allowMethod' => null, 'contentType' => null];
        $this->injectAnnotation = array_merge($defaultInjectAnnotation, $injectAnnotation);
        $this->readAnnotation = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAnnotationInfo(): array
    {
        return $this->readAnnotation;
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(IAnnotatable $instance, \ReflectionMethod $method, Container $container)
    {
        $allowMethods = $this->injectAnnotation['allowMethod'];
        $logger = $container->logger;

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
            if (!array_key_exists($container->requestMethod, array_flip($allowMethods))) {
                $errorMsg = "Not allowed request method '" . $container->requestMethod;
                throw new InvalidRequestException($errorMsg);
            }

            $logger->debug("Accepted method '" . implode(',', $allowMethods) . "'");
        }

        $ext = $this->injectAnnotation['contentType'] ?: 'html';

        if (!is_string($ext)) {
            $errorMsg = "contentType' attribute of @Header must be string.";
            throw new AnnotationException($errorMsg);
        }

        $contentType = null;
        if (array_key_exists($ext, $this->contentTypeList)) {
            $contentType = $this->contentTypeList[$ext];
        }
        if ($contentType === null) {
            $errorMsg = "Invalid value '$ext' in 'contentType' attribute of @Header.";
            throw new AnnotationException($errorMsg);
        }

        $this->readAnnotation['contentType'] = $ext;
        $logger->debug("Accepted contentType '$ext'");
    }
}
