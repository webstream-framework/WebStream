<?php


class Sample extends CoreModel {
    
    // CoreModelによって共通のメソッドを実装
    /**
     * 方針
     * @Injectでインジェクトポイントを指定
     * これを使ってSQLを流しこむ
     * 
     *  $sample = new Sample();
     *  $sample->bind(array("name", $name))
     * 
     */
     
    /**
     * @Inject
     * @Database("diarysys")
     * @Table("diary5")
     * ここでテーブルをひもづける
     * テーブルの各カラムのメソッドを自動的に全て実装する
     * アノテーションで指定したDB,Tableにアクセス出来ない場合は例外。
     * メソッド生成ルール：
     * ・カラム名の先頭に「get」または「set」を付ける
     * ・カラムがスネークケースの場合、キャメルケースに置換する、カラム名の先頭を大文字にする
     * 　blog_title -> getBlogTitle, setBlogTitle
     * ・アンダースコアがない場合そのまま
     * 　title -> getTitle, setTitle
     * ・型チェックを入れる
     *  char/varchar, Integer, String/Text/Blob, Date　などここはもっと調査する
     * 　→めんどうならここはPDO/DBにまかせるのもあり
     * ・auto_incrementの付いたカラムはgettter/setterを作らない
     * ・notnull/uniqueチェックはしない(PDO/DBの例外に移譲)
     * ・外部キーはチェックしない
     * 
     * initializeメソッドはCoreModel#initializeをオーバーライド。
     */
    public function initialize() {
        // ここに初期処理を書いてもいい。
        // SQLもここに書くのがベストかも。
        $this->sql("select * from dual");
        // superするのがメイン。
        // CoreModelではこのクラスを特定して、iniaizlizeメソッドのアノテーションを読み取り
        // メソッドを自動定義する。
        // 動的にメソッドを定義するのは難しいっぽいので、__call経由でDBアクセスする。
        // こうすれば外部からはメソッドアクセスでDBの内容をとっているように見える。
        parent::initialize();
    }
    
    /**
     * 普通に外から
     * これはCoreModelで実装したほうがいいかも。
     */
    public function sql($sql) {
        $this->sql = $sql;
    }
    
    /**
     * これは外から通常通り渡すしかない…。
     * これはCoreModelで実装したほうがいいかも。
     */
    public function bind($bind = array()) {
        $this->bind = $bind
    }
    
    
}
