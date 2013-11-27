<?php
namespace WebStream\Annotation;

/**
 * AnnotationConstants
 * @author Ryuichi TANAKA.
 * @since 2013/10/20
 * @version 0.4.1
 */
trait AnnotationConstants {
    // @Template
    protected $TEMPLATE_VALUE_BASE = "base";
    protected $TEMPLATE_VALUE_SHARED = "shared";
    protected $TEMPLATE_VALUE_PARTS = "parts";
    protected $TEMPLATE_ATTR_VALUE = "value";
    protected $TEMPLATE_ATTR_NAME = "name";
    protected $TEMPLATE_ATTR_TYPE = "type";
    // @Filter
    protected $FILTER_VALUE_INITIALIZE = "Initialize";
    protected $FILTER_VALUE_BEFORE = "Before";
    protected $FILTER_VALUE_AFTER = "After";
    // @Type
    protected $TYPE_ATTR_VALUE = "value";
    // @Value
    protected $VALUE_ATTR_VALUE = "value";
    // @Header
    protected $HEADER_ATTR_CONTENTTYPE = "contentType";
    protected $HEADER_ATTR_ALLOWMETHOD = "allowMethod";
    // @TemplateCache
    protected $TEMPLATECACHE_ATTR_EXPIRE = "expire";
    // @ExceptionHandler
    protected $EXCEPTIONHANDLER_ATTR_VALUE = "value";
}
