<?php
namespace WebStream\Test;

trait TestConstant
{
    private function getDocumentRootURL()
    {
        return "http://localhost/WebStream/core/WebStream/Test/Sample";
    }

    private function getProjectRootPath()
    {
        return "/Users/mapserver2007/Dropbox/workspace/WebStream";
    }

    private function getSampleAppPath()
    {
        return "/core/WebStream/Test/Sample";
    }

    private function getLogFilePath()
    {
        return $this->getSampleAppPath() . "/log/webstream.test.log";
    }

    private function getLogConfigPath()
    {
        return $this->getSampleAppPath() . "/config/log_config";
    }

    private function getCacheDir777()
    {
        return $this->getSampleAppPath() . "/cache777";
    }

    private function getCacheDir000()
    {
        return $this->getSampleAppPath() . "/cache000";
    }

    private function getCacheDir()
    {
        return $this->getSampleAppPath() . "/app/views/_cache";
    }

    private function getHtmlUrl()
    {
        return "http://www.yahoo.co.jp";
    }

    private function getJsonUrl()
    {
        return "http://tepco-usage-api.appspot.com/latest.json";
    }

    private function getRssUrl()
    {
        return "http://rss.dailynews.yahoo.co.jp/fc/rss.xml";
    }

    private function getBasicAuthUrl()
    {
        return "http://kensakuyoke.web.fc2.com/basic-test/test.html";
    }

    private function getNotFoundUrl()
    {
        return "http://wwww222.google.co.jp";
    }
}
