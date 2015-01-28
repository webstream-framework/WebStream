<?php
namespace WebStream\Annotation;

class AnnotationInjector
{

    private $classes;

    public function __construct()
    {

    }

    public function dependencyClass($class)
    {
        $this->classes = [$class];
    }

    public function dependencyClasses(array $classes)
    {
        $this->classes = $classes;
    }

    public function inject($instance)
    {

    }

    public function execute()
    {

    }

    public function getInstance()
    {

    }

}

$injector = new AnnotationInjector($instance);
// 単独のクラスへの依存性注入
// inject/execute/getInstanceをチェーンで実行。実装がない場合はなにもしない。
$injector->injectionClass("\WebStream\Annotation\Reader\AutowiredReader");
$injector->inject($instance);
$injector->execute();
$instance = $injector->getInstance();

// 指定した順番に連続処理する
$injector->dependencyClasses([
    "\WebStream\Annotation\Reader\AutowiredReader",
    "\WebStream\Annotation\Reader\HeaderReader",
    "\WebStream\Annotation\Reader\FilterReader",
    "\WebStream\Annotation\Reader\TemplateReader",
    "\WebStream\Annotation\Reader\TemplateCacheReader",
    "\WebStream\Annotation\Reader\ExceptionHandlerReader"
]);

// 注入する
// アノテーション情報を読み出す系はなにもしない
//

// inject->execute->getInstanceを連続的に行う。
// チェーンのなかでチェーン。
$instance = $injector->inject($instance)->execute()->getInstance();
