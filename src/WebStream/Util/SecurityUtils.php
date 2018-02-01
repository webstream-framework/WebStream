<?php
namespace WebStream\Util;

/**
 * SecurityUtils
 * セキュリティ系依存のUtility
 * @author Ryuichi Tanaka
 * @since 2015/12/26
 * @version 0.7
 */
trait SecurityUtils
{
    /**
     * CSRFトークンキーを返却する
     * @return string CSRFトークンキー
     */
    public function getCsrfTokenKey()
    {
        return "__CSRF_TOKEN__";
    }

    /**
     * CSRFトークンヘッダを返却する
     * @return string CSRFトークンヘッダ
     */
    public function getCsrfTokenHeader()
    {
        return "X-CSRF-Token";
    }

    /**
     * データをシリアライズ化してテキストデータにエンコードする
     * @param object 対象データ
     * @return string エンコードしたデータ
     */
    public function encode($data)
    {
        return base64_encode(serialize($data));
    }

    /**
     * データをデシリアライズ化して元のデータをデコードする
     * @param string エンコード済みデータ
     * @return object デコードしたデータ
     */
    public function decode($data)
    {
        return unserialize(base64_decode($data));
    }
}
