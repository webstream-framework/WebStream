<?php
namespace WebStream\Template;

use WebStream\Module\Utility;
use WebStream\Module\Container;
use WebStream\Exception\Extend\ResourceNotFoundException;

/**
 * Twig
 * @author Ryuichi Tanaka
 * @since 2015/03/18
 * @version 0.4.0
 */
class Twig implements ITemplateEngine
{
    use Utility;

    /**
     * @var Container 依存コンテナ
     */
    private $container;

    /**
     * @var Twig_Loader_Filesystem Twigローダ
     */
    private $loader;

    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        $this->loader = new \Twig_Loader_Filesystem();
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $params)
    {
        $dirname = $this->camel2snake($this->container->router->routingParams()['controller']);
        $filepath = STREAM_APP_ROOT . "/app/views/" . $dirname . "/" . $this->container->filename;
        $realpath = realpath($filepath);

        $filename = null;
        $dirpath = null;

        if ($realpath !== false) {
            $filename = basename($realpath);
            $dirpath = dirname($realpath);
        }

        $filepath = STREAM_APP_ROOT . "/app/views/" . STREAM_VIEW_SHARED . "/" . $this->container->filename;
        $realpath = realpath($filepath);

        $sharedFilename = null;
        $sharedDirpath = null;

        if ($realpath !== false) {
            $sharedFilename = basename($realpath);
            $sharedDirpath = dirname($realpath);
        }

        $filename = $filename ?: $sharedFilename;

        if ($filename === null) {
            throw new ResourceNotFoundException("Invalid template file: " . safetyOut($this->container->filename));
        }

        if ($dirpath !== null) {
            $this->loader->addPath($dirpath);
        }
        if ($sharedDirpath !== null) {
            $this->loader->addPath($sharedDirpath);
        }

        $escaper = new \Twig_Extension_Escaper(true);
        $twig = new \Twig_Environment($this->loader, [
            'cache' => STREAM_APP_ROOT . "/app/views/" . STREAM_VIEW_CACHE,
            'auto_reload' => true,
            'debug' => $this->container->debug
        ]);
        if ($this->container->debug) {
            $twig->addExtension(new \Twig_Extension_Debug());
        }

        echo $twig->loadTemplate($filename)->render([
            "model" => $params["model"],
            "helper" => $params["helper"]
        ]);
    }
}
