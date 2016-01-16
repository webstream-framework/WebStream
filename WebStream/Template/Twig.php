<?php
namespace WebStream\Template;

use WebStream\Module\Utility\CommonUtils;
use WebStream\Module\Container;
use WebStream\Exception\Extend\ResourceNotFoundException;

/**
 * Twig
 * @author Ryuichi Tanaka
 * @since 2015/03/18
 * @version 0.7
 */
class Twig implements ITemplateEngine
{
    use CommonUtils;

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
        $applicationInfo = $this->container->applicationInfo;
        $dirname = $this->camel2snake($this->container->router->pageName);
        $templateDir = $applicationInfo->applicationRoot . "/app/views/" . $dirname;
        $sharedDir = $applicationInfo->applicationRoot . "/app/views/" . $applicationInfo->sharedDir;

        if (is_dir($templateDir)) {
            $this->loader->addPath($templateDir);
        }
        if (is_dir($sharedDir)) {
            $this->loader->addPath($sharedDir);
        }

        $escaper = new \Twig_Extension_Escaper(true);
        $twig = new \Twig_Environment($this->loader, [
            'cache' => $applicationInfo->applicationRoot . "/app/views/" . $applicationInfo->cacheDir,
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
