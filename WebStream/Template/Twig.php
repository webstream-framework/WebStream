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
        $templateDir = STREAM_APP_ROOT . "/app/views/" . $dirname;
        $sharedDir = STREAM_APP_ROOT . "/app/views/" . STREAM_VIEW_SHARED;

        if (is_dir($templateDir)) {
            $this->loader->addPath($templateDir);
        }
        if (is_dir($sharedDir)) {
            $this->loader->addPath($sharedDir);
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

        try {
            echo $twig->loadTemplate($this->container->filename)->render([
                "model" => $params["model"],
                "helper" => $params["helper"]
            ]);
        } catch (\Twig_Error_Loader $e) {
            throw new ResourceNotFoundException($e);
        }
    }
}
