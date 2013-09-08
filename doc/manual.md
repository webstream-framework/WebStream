# WebStream
WebStreamはMVCアーキテクチャをベースとしたWebアプリケーションフレームワークです。  
さらにS(Service)層を追加した4層構造のアーキテクチャとなっています。  
***
##MVC+Sアーキテクチャ
###Controller
Contollerではクライアントからのリクエストを受け付け、必要に応じてServiceまたはModelに処理を移譲します。処理結果はViewへ渡します。原則的にControllerにビジネスロジックを記述してはなりません。

###Service
ServiceではContollerから受け取ったリクエストやデータを処理します。メインとなるビジネスロジックはServiceに記述します。データベースへの問い合わせが必要な場合はModelへ問い合わせます。また、Serviceでは開発者が個別に作成したライブラリを利用することができます。Serviceで処理するロジックがない場合などはServiceを定義する必要はありません。

###Model
ModelはControllerまたはServiceからのリクエストやデータを元にデータベースに問い合わせます。Serviceが定義されない場合はControllerから直接呼び出しが可能です。Modelには原則的にデータベースに関連するロジックやデータベース問い合わせ処理を記述します。データベースに問い合わせたり、特にロジックを記述する必要がない場合はModelの定義は必要ありません。

###View
ViewはControllerから渡された描画データをHTMLとして出力します。HTMLの描画はWebStream独自のテンプレート機能を利用します。テンプレートの描画を支援するヘルパー機能もあります。Helperクラスを定義することで、テンプレート内でロジックを使用することが容易になります。Helperクラスの定義は任意です。

***
##命名規則
###ページ名
ページ名とは、Webページごとにつける固有の名称で、アクセスするURLに対してController、Service、Model、View、Helperの各クラスのprefixに付く名前がページ名に相当します。

###クラス名
クラス名とファイル名は一致させる必要があります。例えば、SampleControllerクラスはSampleController.phpに定義されていなければなりません。これはService, Model, Helperすべて同様です。クラス名はアッパーキャメルケースで定義します。  
Viewテンプレートはviews/(ページ名)に.tmpl形式で保存します。  
ページ名が「sample」の場合、各クラスは以下のように命名します。

####コントローラクラス
`クラス名`: SampleController  
`ファイルパス`: app/controllers/SampleController.php  

####サービスクラス
`クラス名`: SampleService  
`ファイルパス`: app/services/SampleService.php  

####モデルクラス
`クラス名`: SampleModel  
`ファイルパス`: app/models/SampleModel.php  

####ヘルパークラス
`クラス名`: SampleModel  
`ファイルパス`: app/helpers/SampleHelper.php  

####ビューテンプレート
`ファイルパス`: app/views/sample.tmpl  

####メソッド名
メソッド名はローワーキャメルケースで定義します。

***
##ルーティング定義
###routes.php
ルーティング設定により、URI設計を行うことができます。ルーティングにはmod_rewiteが必要です。  
ルーティング定義は`$WEBSTREAM_HOME/config/routes.php`に記述します。  

    namespace WebStream;
    Router::setRule(
        array(
            '/login' => 'sample#login'
            '/blog/:id' => 'blog#entry'
        )
    );

ルーティングルールは配列で定義し、キーにURIパス定義、バリューにクラス、アクション定義を記述します。誤った定義が記述された場合、例外が発生します。

###URIパス定義
URIパスは`/path/to`形式で定義します。またURIには変数の設定が可能で、`:value`形式で記述します。例えば、/blog/:idと定義し、/blog/10にアクセスした場合、Controllerクラスでは以下の方法で値を取得出来ます。

    namespace WebStream;
    class BlogController extends CoreController {
        public function execute($params) {
            $id = $params['id']; // 10
        }
    }

###validates.php
バリデーション設定により、GET/POST/PUT/DELETEでのリクエストに含まれるパラメータをチェックすることができます。  
バリデーション定義は`$WEBSTREAM_HOME/config/validates.php`に記述します。  

    namespace WebStream;
    Validator::setRule(
        array(
            "sample#validateForm" => array(
                "post#name" => "required",
                "get#page"  => "number"
            )
        )
    );





###クラス、アクション定義
クラス、アクション定義は`class#method`形式で定義します。`class`はページ名と同じです。`method`は実行するクラスのメソッド名をスネークケースに変換して指定します。例えば、メソッド名が`executeIndexPage`だった場合、`execute_index_page`とします。

***
##Controllerクラス
###定義方法
Controllerクラスは以下のように定義します。

    // $WEBSTREAM_HOME/app/controllers/SampleController.php
    namespace WebStream;
    class SampleController extends CoreController {
        // アクションメソッド
        public function execute() {}
    }
    
名前空間に「WebStream」を指定します。定義したControllerクラスは必ず`CoreController`クラスを継承します。

###クラス内で使用可能なメンバ変数
メンバ変数名|利用可能メソッド名|説明
---------|---------------|---
$request|get($key)    |GETパラメータを取得
 |post($key)   |POSTパラメータを取得
 |put($key)    |PUTパラメータを取得
 |delete($key) |DELETEパラメータを取得
$session|restart($expire, $path, $domain)|セッションを再開始する

###メソッドに指定可能なアノテーション(Controller)
Controllerクラスのメソッドに指定可能なアノテーションは以下のとおりです。

アノテーションマーク|説明|サンプル
---------|---|---
@Inject|アノテーションを有効にする(必須)|@Inject
@Request|リクエストメソッドを制御する|@Request("GET")<br>@Request("POST","PUT")
@Response|レスポンスのステータスコードを指定|@Request("200")
@Filter|前処理、後処理フィルタをかける|@Filter("before")<br>@Filter("after")
@Security|セキュリティ制御をかける|@Security("CSRF")
@Cache|レスポンスキャッシュを有効にする(秒)|@Cache("10")
@Format|出力するファイル形式を設定する|@Format("xml")<br>@Format("json")
@Callback|JSONPコールバックを設定|@Format("jsonp")<br>@Callback"callback")
@BasicAuth|基本認証を有効にする|@BasicAuth("config/basic_auth.ini")
@Render|テンプレートを描画する|@Render("sample.tmpl")<br>@Render("sample.tmpl","sample")
@Layout|共通テンプレートを描画する|@Layout("base.tmpl")
@Error|エラーハンドリングする|@Error<br>@Error("SessionTimeout")

###リクエストパラメータ取得
アクションメソッドで引数を指定することでリクエストパラメータを取得できます。リクエストパラメータは連想配列として`$params`で取得できます。GET/POST/PUT/DELETEメソッドのパラメータは`$this->request`で取得できます。

    namespace WebStream;
    class SampleController extends CoreController {
        // アクションメソッド
        public function execute($params) {
            $get = $this->request->get("hoge");
            $post = $this->request->post("huga");
        }
    }

###フィルタ
Controllerクラスに`before`メソッド、`after`メソッドを定義すると、Controllerクラスのアクションメソッドの実行前、実行後にそれぞれ任意の処理が実行可能です。

    namespace WebStream;
    class SampleController extends CoreController {
        /**
         * @Inject
         * @Filter("before")
         */
        public function before() {
            // SampleController#execute実行前に実行
        }
        
        /**
         * @Inject
         * @Filter("after")
         */
        public function after() {
            // SampleController#execute実行後に実行
        }
        
        public funciton execute() {}
    }

###エラーのカスタマイズ
アクションメソッドを呼び出したときに何らかのエラーが起きた場合、通常はデフォルトのエラー画面が表示されて異常終了しますが、`@Error`アノテーションを使うとエラーハンドリングが可能です。ハンドリング後、独自のエラーページに遷移させるなどすることができます。エラーハンドリング可能な定義の一覧は以下の通りです。

アノテーション定義|説明
---------------|---
@Error("CSRF")|CSRFエラーを捕捉
@Error("SessionTimeout")|セッションタイムアウトを捕捉
@Error("Validate")|バリデーションエラーを捕捉
@Error("MethodNotAllowed")|許可されないHTTPメソッドアクセスを捕捉
@Error("ForbiddenAccess")|アクセス禁止エラーを捕捉
@Error("ResourceNotFound")|存在しないリソースへのアクセスエラーを捕捉
@Error|全てのエラーを捕捉

エラーハンドリングするメソッド名は任意です。エラーハンドリングメソッドでは引数を取ることが出来ます。第一引数は例外のエラーオブジェクトです。第二引数は`@Error("Validate)`でのみ使用可能で、バリデーションエラーオブジェクトが取得できます。なお、引数の指定は省略可能です。

    namespace WebStream;
    class SampleController extends CoreController {
        // エラーが発生した場合、呼ばれない
        public funciton execute($params) {}
        
        // こちらに遷移する
        /**
         * @Inject
         * @Error("Validate")
         */
        public function validateError($e, $info) {
            // $e: エラーオブジェクト
            // $info: バリデーションエラーパラメータ
        }

        /**
         * @Inject
         * @Error("SessionTimeout")
         */
        public function sessionTimeoutError($e) {
            // $e: エラーオブジェクト
        }
    }

###Serviceの呼び出し
ContollerクラスからServiceクラスを呼び出せます。Serviceクラスは以下のように呼び出します。  
Contollerクラス内でページ名のメンバ変数(ページ名がsampleなら`$this->Sample`)が利用可能で、Serviceクラスのインスタンスが格納されています。

    namespace WebStream;
    class SampleController extends CoreController {
        public funciton execute() {
            // $this->(ページ名のアッパーキャメルケース)->Serviceクラスのメソッド
            $this->Sample->execute(); // SampleService#executeの実行
        }
    }


###Modelの呼び出し
ContollerクラスからModelクラスを呼び出せます。ControllerクラスからModelクラスを呼び出すときは、Serviceクラスが定義されていない場合に限ります。  
Contollerクラス内でページ名のメンバ変数(ページ名がsampleなら`$this->Sample`)が利用可能で、Modelクラスのインスタンスが格納されています。

    namespace WebStream;
    class SampleController extends CoreController {
        public funciton execute() {
            // $this->(ページ名のアッパーキャメルケース)->Modelクラスのメソッド
            $this->Sample->execute(); // SampleModel#executeの実行
        }
    }


##Serviceクラス
###定義方法
Serviceクラスは以下のように定義します。

    // $WEBSTREAM_HOME/app/services/SampleServicer.php
    namespace WebStream;
    class SampleService extends CoreService {
        public function execute() {}
    }
    
名前空間に「WebStream」を指定します。定義したServiceクラスは必ずCoreServiceクラスを継承します。

###Libraryの呼び出し
Serviceクラスからは開発者が自由に定義したLibraryクラスを呼び出すことができます。Libraryクラスには制約はありません。Libraryクラスは以下の場所に配置します。

    $WEBSTREAM_HOME/app/libraries/

クラスを配置すると自動的にパスが通りますので、明示的なrequireやincludeは不要です。例えば、Weatherクラスを定義したファイルを配置した場合、以下のように呼び出せます。

    namespace WebStream;
    class SampleService extends CoreService {
        public function execute() {
            $weather = new Weather();
        }
    }

###Modelの呼び出し
ServiceクラスからModelクラスを呼び出すには以下のように記述します。

    namespace WebStream;
    class SampleService extends CoreService {
        public function execute() {
            // $this->(ページ名のアッパーキャメルケース)->Modelクラスのメソッド
            $this->Sample->getData(); // SampleModel#getDataの実行
        }
    }
    

##Modelクラス
###定義方法
Modelクラスは以下のように定義します。

    // $WEBSTREAM_HOME/app/models/SampleModel.php
    namespace WebStream;
    class SampleModel extends CoreModel {
        public function getData() {}
    }

名前空間に「WebStream」を指定します。定義したModelクラスは必ずCoreModelクラスを継承します。

####データベースアクセス
データベースへのアクセス設定(データベース名、ユーザID等)は以下のファイルに記述します。

    ; $WEBSTREAM_HOME/config/database.ini
    host = localhost
    user = mysql
    password = mysql
    dbname = test
    dbms = mysql
    

Modelクラス内では$this->dbオブジェクトを経由してデータベースにアクセスします。$this->dbオブジェクトでは主に以下の4つのメソッドが利用可能です。

    select, insert, update, delete

いずれも第一引数にSQL文(文字列)、第二引数にプリペアードステートメントでバインドする変数(連想配列)を指定することができます。
Modelのデータベースアクセス方法は3つあります。

###SQLを直接指定
Modelクラスのメソッド内またはServiceからSQLを渡すことで実行します。

    namespace WebStream;
    class SampleModel extends CoreModel {
        public function getData() {
            $sql = "select name from users where id = :id";
            $bind = array("id" => "10000001");
            return $this->db->select($sql, $bind);
        }
    }
    
###アノテーションによるSQL指定
SQLをメソッドに直接書かず、外部ファイルから読み出します。外部ファイルは以下の場所に配置します。

    // ファイル名に制約はないが拡張子は.propertiesとする
    $WEBSTREAM_HOME/sql/users.properties
    
ファイルには以下のような記述をします。

    users="select name from users"
    users2="select name from users where id = :id"
    
key="value"の形式で記述します。keyの名前は一意にします。これは複数の.propertiesファイルを作成しても全体で一意になるようにする必要があります。記述したSQLにはプリペアードステートメントでバインドする変数を指定できます。

次に、Modelクラスにアノテーション定義を行います。

    namespace WebStream;
    /**
     * @Inject
     * @Database("test")
     * @Table("users")
     * @Properties("sql/users.properties")
     */
    class SampleModel extends CoreModel {
         /**
          * @Inject
          * @SQL("users")
          */
         public function getData() {
             $bind = array("id" => "10000001");
             return $this->db->select($bind);
         }
    }

指定可能なアノテーションは以下のとおりです。
    
    // インジェクトポイント(アノテーションを有効にする)
    @Inject
    // クラスに定義
    @Database("データベース名")
    @Table("テーブル名1","テーブル名2",...)
    @Properties("SQLプロパティファイル1","SQLプロパティファイル1",...)
    // メソッドに定義
    @SQL("SQLプロパティファイのSQLキー")

アノテーションが適切に設定されていれば、$this->dbオブジェクトの各メソッドにSQLを指定する必要はなくなります(プリペアードステートメントのバインド変数は必要になります)。database.iniに設定したデータベース設定よりもアノテーションに設定した内容が優先されます。

###カラムマッピング
特定のカラムのデータを取得する機能です。アノテーションの指定を以下のように行います。

    namespace WebStream;
    /**
     * @Inject
     * @Database("test")
     * @Table("users")
     */
    class SampleModel extends CoreModel {}

カラムマッピング機能はメソッドの定義は不要です。指定可能なアノテーションは以下のとおりです。
    
    // インジェクトポイント(アノテーションを有効にする)
    @Inject
    // クラスに定義
    @Database("データベース名")
    @Table("テーブル名1","テーブル名2",...)
    
カラムマッピング機能を利用するにはServiceクラスから以下のように呼び出します。

    namespace WebStream;
    class SampleService extends CoreService {
        public function execute() {
        　　// usersテーブルのuser_nameカラムのデータをすべて取得
            $this->Sample->userName();
        }
    }
    
呼び出す規則は以下のとおりです。

    カラム名に"_"が含まれない場合、カラム名とメソッド名は同じ。
      例：カラム名がnameの場合、$this->Sample->name();
    カラム名に"_"が含まれる場合、_を取り除き、区切り文字を大文字(キャメルケース)にする。
      例：カラム名がuser_nameの場合、$this->Sample->userName();
     

@Tableに複数のテーブル名を指定し、かつ、別のテーブルに同じカラム名が存在する場合は、データがマージされて返却されます。

また、データの取得数を制限することができます。

    $this->Sample->name(10); // 10件取得
    $this->Sample->name(10, 20); // 先頭10件目から20件取得


##Viewテンプレート
###Controllerクラスの設定
Controlerのアクションメソッドに`@Render`および`@Layout`アノテーションを記述することでViewテンプレートを描画します。

    namespace WebStream;
    class SampleController extends CoreController {
        /**
         * @Inject
         * @Render("index.tmpl")
         */
        public function execute() {
            return array(
                'title' => "top page",
                'body'  => "Hello world."
            );
        }

        /**
         * @Inject
         * @Layout("base.tmpl")
         */
        public function execute2() {}

        /**
         * @Inject
         * @Layout("base.tmpl")
         * @Render("index.tmpl", "index")
         */
        public function execute3() {}

        /**
         * @Inject
         * @Render("index.tmpl")
         * @Render("blogparts.tmpl", "parts")
         */
        public function execute4() {}

    }
    
`@Render`の第一引数に描画するテンプレートファイル名を指定します。ファイルは$WEBSTREAM_HOME/app/views/{ページ名}/{テンプレートファイル}に格納します。`@Layout`は共通テンプレートを使用する場合に指定します。共通テンプレートファイルは$WEBSTREAM_HOME/app/views/_shared/{テンプレートファイル}に格納します。`@Render`や`@layout`は併用(入れ子構造)することもできます。その場合は`@Render`の第二引数にテンプレート名の別名を指定し、入れ子にするテンプレート内で名前を指定します。これにより、画面内の各コンテンツを部品化可能です。  
Controllerクラスのメソッドで値をハッシュ形式でreturnするとViewテンプレートに値を埋め込むことができます。

###Viewテンプレートの書き方
Contollerクラスでreturnしたハッシュをテンプレートに埋め込みます。

    <!-- index.tmpl -->
    <html>
        <head>
            <title>%{$title}</title>
        </head>
        <body>
            <p>%{$body}</p>
        </body>
    </html>


使用可能構文|説明
---------|---------------
#{$hoge}|変数を評価する
%{$hoge}|変数を安全な値で評価する
!{helper_method()}|ヘルパーメソッドを呼び出す
@{template_name}|テンプレートを呼び出す
<% php_func() %>|PHP構文を評価する






===== ここまでかいた



以下の場所に配置したテンプレートファイルを描画します。

    $WEBSTREAM_HOME/app/views/(ページ名のスネークケース)/テンプレート名.tmpl
    
テンプレートファイルにControllerから変数を渡したい場合は以下のようにします。

    namespace WebStream;
    class SampleController extends CoreController {
        public function execute() {
            $this->render("index", array(
                "name" => "alice"
            ));
        }
    }

テンプレートファイルで$nameを参照することができます。  

テンプレートを各画面共通で使う共通テンプレートファイルを定義することができます。Controllerクラスには以下のように記述します。

    namespace WebStream;
    class SampleController extends CoreController {
        public function execute() {
            $this->layout("common"); // 共通テンプレート名
        }
    }

共通テンプレートファイルは以下の場所に配置します。

    $WEBSTREAM_HOME/app/views/_shared/共通テンプレート名.tmpl

render同様、layoutでも変数を渡すことが可能です。変数にテンプレートファイル名を渡せばテンプレートファイルの中でテンプレートファイルを呼び出すことも可能なので、テンプレートを細かい単位の部品に分割して管理することが容易になります。

###他の描画方法
render, layoutによる描画はHTMLの正常な画面を描画するときに使用します。それ以外の描画方法は以下のとおりになります。

    // エラー画面を描画します。描画する内容を渡します(HTML可)。
    // ステータスコード404を返却します
    $this->render_error($content);
    // RSSを描画します。
    // RSS用テンプレート、渡す値を設定します。
    $this->render_rss($template, $params);
    // ATOMを描画します。
    // ATOM用テンプレート、渡す値を設定します。
    $this->render_atom($template, $params);
    // XMLを描画します。
    // XML用テンプレート、渡す値を設定します。
    $this->render_xml($template, $params);
    // JSON、JSONPを描画します
    // 第二引数が指定された場合はJSONPの描画になります。
    $this->render_json($data, $callback);
    // リダイレクトします。
    $this->redirect($url);
    // 表示権限がない場合のエラー画面を描画します。
    // ステータスコード403を返却します。
    $this->forbidden();
    // ファイルを表示します。
    // JavaScript, CSS, 画像ファイルは画面に表示し、それ以外のファイルは
    // ダウンロードします。
    $this->render_file($filepath);

##Helperクラス
###Helperの役割
Viewには本来ロジックは記述すべきではありませんが、ロジックを書かざるをえない場合があります。Viewに直接ロジックを記述できますが、それだと可読性が下がってしまいます。HelperはViewに書くロジックを分離する役割を持ちます。

###定義方法
Helperクラスは以下のように定義します。

    namespace WebStream;
    class TestHelper {
        public function showHtml1($name) {
            return <<< HELPER
            <div class="test">$name</div>
    HELPER;
        }
    
        public function showHtml2($name) {
            return "<div class=\"test\">$name</div>";
        }
    
        public function showString($name) {
            return '<div class="test">$name</div>';
        }
    }

描画したいHTMLを返却させることでView内でHTMLとして描画することができます。HTMLとして描画するには、ヒアドキュメント形式かダブルクオーテーションで囲ったHTMLを返却します。シングルクォーテーションで囲ったHTMLは文字列として認識されるため、HTMLとしては描画されません。  

View側からHelperを呼ぶには以下のように記述します。

    !{show_html1()}
    !{show_html2()}
    !{show_string()}

!{}で囲い、Helperクラスのメソッドをスネークケースにした形式で記述します。引数を渡すことも可能です。また、以下のようにメソッド名と一致する名前(キャメルケース)でも呼ぶことは可能ですが、命名規約上スネークケースで記述することを推奨します。

    !{showHtml1()}
    !{showHtml2()}
    !{showString()}