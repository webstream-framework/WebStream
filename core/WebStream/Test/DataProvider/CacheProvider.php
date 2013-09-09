<?php
namespace WebStream\Test\DataProvider;

/**
 * CacheProvider
 * @author Ryuichi TANAKA.
 * @since 2013/09/08
 * @version 0.4
 */
trait CacheProvider {
    public function saveProvider()
    {
        return [
            ["cache_test_save_string", "abcde"],
            ["cache_test_save_integer", 12345],
            ["cache_test_save_array", ["abcde"]],
            ["cache_test_save_object", new \stdClass()]
        ];
    }

    public function metaDataProvider()
    {
        return [
            ["cache_test_metadata"],
            ["cache_test_metadata_ttl", 10]
        ];
    }

    public function deleteCacheProvider()
    {
        return [
            ["cache_test_delete"]
        ];
    }

    public function overwriteSaveProvider()
    {
        return [
            ["cache_test_overwrite_save", "abcde", "fghij"]
        ];
    }

    public function writeArrayDataProvider()
    {
        return [
            ["cache_test_write_array", ["array"]]
        ];
    }

    public function invalidSaveDirProvider()
    {
        return [
            ["/dummy"]
        ];
    }

    public function invalidDeletePathProvider()
    {
        return [
            ["dummy"]
        ];
    }

    public function timeOverCacheProvider()
    {
        return [
            ["cache_test_time_over", 1]
        ];
    }
}
