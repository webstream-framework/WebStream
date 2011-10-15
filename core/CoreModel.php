<?php
/**
 * CoreModelクラス
 * @author Ryuichi TANAKA.
 * @since 2011/09/11
 */
class CoreModel {
    /** appディレクトリ */
    private $app_dir;
    /** ページ名 */
    private $page_name;
    
    /** DBインスタンスを格納するメンバ変数 */
    protected $db;
    
    /**
     * Modelクラス全体の初期化
     */
    final public function __construct() {
        $this->db = Database::manager();
    }
}
