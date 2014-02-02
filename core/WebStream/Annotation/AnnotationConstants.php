<?php
namespace WebStream\Annotation;

/**
 * AnnotationConstants
 * @author Ryuichi TANAKA.
 * @since 2013/10/20
 * @version 0.4.1
 */
trait AnnotationConstants
{
    // @Template
    protected $TEMPLATE_VALUE_BASE = "base";
    protected $TEMPLATE_VALUE_SHARED = "shared";
    protected $TEMPLATE_VALUE_PARTS = "parts";
    protected $TEMPLATE_ATTR_VALUE = "value";
    protected $TEMPLATE_ATTR_NAME = "name";
    protected $TEMPLATE_ATTR_TYPE = "type";
    // @Filter
    protected $FILTER_ATTR_TYPE = "type";
    protected $FILTER_ATTR_EXCEPT = "except";
    protected $FILTER_ATTR_ONLY = "only";
    protected $FILTER_VALUE_INITIALIZE = "initialize";
    protected $FILTER_VALUE_BEFORE = "before";
    protected $FILTER_VALUE_AFTER = "after";
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
    // @Database
    protected $DATABASE_ATTR_DRIVER = "driver";
    protected $DATABASE_ATTR_CONFIG = "config";
    // @Query
    protected $QUERY_ATTR_FILE = "file";
}
