<?php
namespace WebStream\Template;

use WebStream\Util\CommonUtils;
use WebStream\Container\Container;
use WebStream\Exception\Extend\ResourceNotFoundException;
use WebStream\Exception\Extend\InvalidArgumentException;

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

        $escaper = new \Twig_Extension_Escaper("html");
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

            // 内部エラーが起きている場合は例外を出力
            if (error_get_last() !== null) {
                $message = error_get_last()["message"];
                error_clear_last();
                throw new InvalidArgumentException($message);
            }
        } catch (\Twig_Error_Loader $e) {
            throw new ResourceNotFoundException($e);
        }
    }
}
