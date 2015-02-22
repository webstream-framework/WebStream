<?php
namespace WebStream\Annotation;

use WebStream\Core\CoreInterface;
use WebStream\Annotation\Base\Annotation;
use WebStream\Annotation\Base\IMethod;
use WebStream\Annotation\Base\IRead;
use WebStream\Annotation\Container\AnnotationContainer;
use WebStream\Module\Logger;
use WebStream\Module\Container;
use WebStream\Module\Utility;
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
    use Utility;

    /**
     * @var AnnotationContainer アノテーションコンテナ
     */
    private $annotaion;

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
    public function onInject(AnnotationContainer $annotation)
    {
        $this->annotation = $annotation;
        Logger::debug("@Header injected.");
    }

    /**
     * {@inheritdoc}
     */
    public function onInjected()
    {
        return $this->annotation;
    }

    /**
     * {@inheritdoc}
     */
    public function onMethodInject(CoreInterface &$instance, Container $container, \ReflectionMethod $method)
    {
        $action = $this->camel2snake($container->router->action());
        $classpathWithAction = $method->class . "#" . $action;
        $allowMethods = $this->annotation->allowMethod;
        $ext = $this->annotation->contentType;

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
            if (!$this->inArray($container->request->requestMethod(), $allowMethods)) {
                $errorMsg = "Not allowed request method '" . $container->request->requestMethod() . "' in " . $classpathWithAction;
                throw new InvalidRequestException($errorMsg);
            }

            Logger::debug("Accepted request method '" . $container->request->requestMethod() . "' in " . $classpathWithAction);

            if ($ext !== null) {
                $contentType = $this->contentTypeList[$ext];
                if ($contentType === null) {
                    $errorMsg = "Invalid value '$ext' in 'contentType' attribute of @Header.";
                    throw new AnnotationException($errorMsg);
                }
                Logger::debug("Accepted contentType '$ext' in " . $classpathWithAction);
            }
        }
    }
}
