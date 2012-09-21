# WebStream
WebStreamはMVCアーキテクチャをベースとしたWebアプリケーションフレームワークです。  
さらにS(Service)層を追加した4層構造のアーキテクチャとなっています。  
***
###MVC+Sアーキテクチャ
#####Controller
Contollerではクライアントからのリクエストを受け付け、必要に応じてServiceまたはModelに処理を移譲します。処理結果はViewへ渡します。原則的にControllerにビジネスロジックを記述してはなりません。

#####Service
ServiceではContollerから受け取ったリクエストやデータを処理します。メインとなるビジネスロジックはServiceに記述します。データベースへの問い合わせが必要な場合はModelへ問い合わせます。また、Serviceでは開発者が個別に作成したライブラリを利用することができます。Serviceで処理するロジックがない場合などはServiceを定義する必要はありません。

#####Model
ModelはControllerまたはServiceからのリクエストやデータを元にデータベースに問い合わせます。Serviceが定義されない場合はControllerから直接呼び出しが可能です。Modelには原則的にデータベースに関連するロジックやデータベース問い合わせ処理を記述します。データベースに問い合わせたり、特にロジックを記述する必要がない場合はModelの定義は必要ありません。

#####View
ViewはControllerから渡された描画データをHTMLとして出力します。HTMLの描画はWebStream独自のテンプレート機能を利用します。テンプレートの描画を支援するヘルパー機能もあります。Helperクラスを定義することで、テンプレート内でロジックを使用することが容易になります。Helperクラスの定義は任意です。

***
###命名規則
#####ページ名
ページ名とは、Webページごとにつける固有の名称で、アクセスするURLに対してController、Service、Model、View、Helperの各クラスのprefixに付く名前がページ名に相当します。

    // ページ名が「sample」の場合
    Controllerクラス→SampleController
    Serviceクラス→SampleService
    Modelクラス→SampleModel
    Helperクラス→SampleHelper
    Viewテンプレート→views/sampleディレクトリに格納
    

#####クラス名
クラス名とファイル名は一致させる必要があります。例えば、SampleControllerクラスはSampleController.phpに定義されていなければなりません。これはService, Model, Helperすべて同様です。クラス名はアッパーキャメルケースで定義します。

#####メソッド名
メソッド名はローワーキャメルケースで定義します。また、以下のメソッド名はオーバーライドできません。

    // Controllerクラス
    initialize, csrf, load, layout, render, render_json, render_xml, render_rss,  
    render_error, redirect, forbbiden, render_file, move, page
    // Serviceクラス
    load, page
    // Modelクラス
    (なし)
    // Helperクラス
    getHelper

***
###Controllerクラス
#####定義方法
Controllerクラスは以下のように定義します。

    // $WEBSTREAM_HOME/app/controllers/SampleController.php
    namespace WebStream;
    class SampleController extends CoreController {
        // アクションメソッド
        public function execute() {}
    }
    
名前空間に「WebStream」を指定します。定義したControllerクラスは必ずCoreControllerクラスを継承します。

#####リクエストパラメータ取得
アクションメソッドで引数を指定することでリクエストパラメータを取得できます(リクエストパラメータの設定の詳細については後述のroutes.phpを参照してください)。リクエストパラメータは連想配列として取得できます。

    namespace WebStream;
    class SampleController extends CoreController {
        // アクションメソッド
        public function execute($params) {}
    }

#####before filter, after filter
Controllerクラスにbeforeメソッド、afterメソッドを定義すると、Controllerクラスのアクションメソッドの実行前、実行後にそれぞれ任意の処理が実行可能です。

    namespace WebStream;
    class SampleController extends CoreController {
        public function before() {
            // SampleController#execute実行前に実行
        }
        
        public function after() {
            // SampleController#execute実行後に実行
        }
        
        public funciton execute() {}
    }

#####バリデーションエラーのカスタマイズ
アクションメソッドに渡ってくるリクエストパラメータはバリデーション機能を有効にすることができます(詳細は後述)。バリデーションエラーが発生した場合、デフォルトでは例外が発生し、ステータスコード422を返却して終了しますが、Controllerクラスに@Error("Validate")アノテーションを定義することでエラーを捕捉します。

    namespace WebStream;
    class SampleController extends CoreController {
        // バリデーションエラーが発生した場合、呼ばれない
        public funciton execute($params) {}
        
        // こちらに遷移する
        /**
         * @Inject
         * @Error("Validate")
         */
        public function validate_error($errors) {
        	// $errorsに発生したエラー内容が格納される
        }
    }

これにより、補足したエラー内容を使って独自定義したViewを描画することができます。

#####Serviceの呼び出し
Serviceクラスは以下のように呼び出します。

    namespace WebStream;
    class SampleController extends CoreController {
        public funciton execute() {
            // $this->(ページ名のアッパーキャメルケース)->Serviceクラスのメソッド
            $this->Sample->execute(); // SampleService#executeの実行
        }
    }

###Serviceクラス
#####定義方法
Serviceクラスは以下のように定義します。

    // $WEBSTREAM_HOME/app/services/SampleServicer.php
    namespace WebStream;
    class SampleService extends CoreService {
        public function execute() {}
    }
    
名前空間に「WebStream」を指定します。定義したServiceクラスは必ずCoreServiceクラスを継承します。

#####Libraryの呼び出し
Serviceクラスからは開発者が自由に定義したLibraryクラスを呼び出すことができます。Libraryクラスには制約はありません。Libraryクラスは以下の場所に配置します。

    $WEBSTREAM_HOME/app/libraries/

クラスを配置すると自動的にパスが通りますので、明示的なrequireやincludeは不要です。例えば、Weatherクラスを定義したファイルを配置した場合、以下のように呼び出せます。

    namespace WebStream;
    class SampleService extends CoreService {
        public function execute() {
            $weather = new \Weather();
        }
    }

#####Modelの呼び出し
ServiceクラスからModelクラスを呼び出すには以下のように記述します。

    namespace WebStream;
    class SampleService extends CoreService {
        public function execute() {
            // $this->(ページ名のアッパーキャメルケース)->Modelクラスのメソッド
            $this->Sample->getData(); // SampleModel#getDataの実行
        }
    }
    

###Modelクラス
#####定義方法
Modelクラスは以下のように定義します。

    // $WEBSTREAM_HOME/app/models/SampleModel.php
    namespace WebStream;
    class SampleModel extends CoreModel {
        public function getData() {}
    }

名前空間に「WebStream」を指定します。定義したModelクラスは必ずCoreModelクラスを継承します。

####データベースアクセス
データベースへのアクセス設定(データベース名、ユーザID等)は以下のファイルに記述します。

    $WEBSTREAM_HOME/config/database.ini

Modelクラス内では$this->dbオブジェクトを経由してデータベースにアクセスします。$this->dbオブジェクトでは主に以下の4つのメソッドが利用可能です。

    select, insert, update, delete

いずれも第一引数にSQL文(文字列)、第二引数にプリペアードステートメントでバインドする変数(連想配列)を指定することができます。
Modelのデータベースアクセス方法は3つあります。

#####SQLを直接指定
Modelクラスのメソッド内またはServiceからSQLを渡すことで実行します。

    namespace WebStream;
    class SampleModel extends CoreModel {
        public function getData() {
            $sql = "select name from users where id = :id";
            $bind = array("id" => "10000001");
            return $this->db->select($sql, $bind);
        }
    }
    
#####アノテーションによるSQL指定
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

#####カラムマッピング
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


###Viewテンプレート
#####定義方法
Viewテンプレートの描画は以下のように行います。

    namespace WebStream;
    class SampleController extends CoreController {
        public function execute() {
            $this->render("index"); // テンプレート名
        }
    }
    
CoreController#renderにより指定したテンプレートファイルを描画します。引数には(.tmplを除いた)テンプレート名を指定します。以下の場所に配置したテンプレートファイルを描画します。

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

#####他の描画方法
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

###Helperクラス
#####Helperの役割
Viewには本来ロジックは記述すべきではありませんが、ロジックを書かざるをえない場合があります。Viewに直接ロジックを記述できますが、それだと可読性が下がってしまいます。HelperはViewに書くロジックを分離する役割を持ちます。

#####定義方法
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