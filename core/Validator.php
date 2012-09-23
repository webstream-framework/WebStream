<?php
namespace WebStream;
/**
 * Validatorクラス
 * @author Ryuichi TANAKA.
 * @since 2012/09/13
 */
class Validator {
    /** バリデーションルール */
    private static $rules;
    /** エラー内容 */
    private $error;
    
    /**
     * コンストラクタ
     */
    public function __construct() {
        $this->validateRules();
    }
    
    /**
     * バリデーションルールを設定する
     * @param Hash バリデーションルール定義
     */
    public static function setRule($rules) {
        self::$rules = $rules;
    }
    
    /**
     * バリデーションエラー情報を返却する
     * @return Hash エラー情報
     */
    public function getError() {
        return $this->error;
    }
    
    /**
     * バリデーションエラー情報をセットする
     * @param String バリデーションルール
     * @param String リクエストキー
     * @param String リクエストパラメータ値
     */
    private function setError($rule, $name, $value) {
        $this->error = array(
            "rule" => $rule,
            "name" => $name,
            "value" => $value
        );
    }
    
    /**
     * 入力値が妥当かどうか検証する
     * @param String 'Controller#Action'形式の文字列
     * @param Hash リクエストパラメータ
     * @param String リクエストメソッド
     */
    public function validateParameter($ca, $params, $method) {
        if (!array_key_exists($ca, self::$rules)) {
            return false;
        }
        $rules = self::$rules[$ca];
        Logger::info("Request method: ${method}");
        Logger::info("Request parameter validation is execute: ${ca}");
        // リクエストに対するバリデーションルールを展開
        foreach ($rules as $key => $value) {
            if (preg_match('/^((?:p(?:os|u)|ge)t|delete)#(.*)/', $key, $matches)) {
                if ($matches[1] === $method) {
                    $key = $matches[2];
                }
                else {
                    continue;
                }
            }
            else {
                continue;
            }
            // 複数あるバリデーションルールを一つずつ検証
            $ruleList = preg_split('/\|/', $value);
            foreach ($ruleList as $rule) {
                // required
                if ($this->ruleRequired($rule)) {
                    $this->checkRequired($key, $params);
                }
                // min_length
                if ($this->ruleMinLength($rule)) {
                    $this->checkMinLength($rule, $key, $params);
                }
                // max_length
                if ($this->ruleMaxLength($rule)) {
                    $this->checkMaxLength($rule, $key, $params);
                }
                // min
                if ($this->ruleMin($rule)) {
                    $this->checkMin($rule, $key, $params);
                }
                // max
                if ($this->ruleMax($rule)) {
                    $this->checkMax($rule, $key, $params);
                }
                // equal
                if ($this->ruleEqual($rule)) {
                    $this->checkEqual($rule, $key, $params);
                }
                // length
                if ($this->ruleLength($rule)) {
                    $this->checkLength($rule, $key, $params);
                }
                // range
                if ($this->ruleRange($rule)) {
                    $this->checkRange($rule, $key, $params);
                }
                // number
                if ($this->ruleNumber($rule)) {
                    $this->checkNumber($rule, $key, $params);
                }
                // regexp
                if ($this->ruleRegexp($rule)) {
                    $this->checkRegexp($rule, $key, $params);
                }
            }
        }
    }
    
    /**
     * バリデーションルールを検証する
     * ルールの整合性まではチェックしない
     */
    private function validateRules() {
        foreach (self::$rules as $ca => $rules) {
            // CA部分('controller#action')が間違っている(存在しない)場合はエラーとする
            $ca = preg_split('/#/', $ca);
            // インポートが失敗した場合、または、クラスの呼び出しに失敗した場合エラー
            $controller = Utility::snake2ucamel($ca[0]) . "Controller";
            // Controllerクラスが存在しなければ例外
            if (!import(STREAM_APP_DIR . "/controllers/" . $controller) || !class_exists(STREAM_CLASSPATH . $controller)) {
                $errorMsg = "Validation rule error. Controller name is invalid: $ca[0]";
                throw new ClassNotFoundException($errorMsg);
            }
            // Actionメソッドが存在しなければ例外
            $class = new \ReflectionClass(STREAM_CLASSPATH . $controller);
            $action = Utility::snake2lcamel($ca[1]);
            if (!$class->hasMethod($action)) {
                $errorMsg = "Validation rule error. Action name is invalid: $ca[1]";
                throw new MethodNotFoundException($errorMsg);
            }
            foreach ($rules as $key => $rule) {
                // パラメータキーのprefix(xxx#)が妥当かどうかチェック
                if (!preg_match('/^(?:(?:p(?:os|u)|ge)t|delete)#/', $key)) {
                    throw new ValidatorException("Request method is invalid: ${key}");
                }
                // バリデーションルールを検証する
                // 区切り文字「|」で分割しそれぞれ検証する
                $params = preg_split('/\|/', $rule);
                foreach ($params as $param) {
                    if ($this->ruleRequired($param)) break;
                    if ($this->ruleMinLength($param)) break;
                    if ($this->ruleMaxLength($param)) break;
                    if ($this->ruleMin($param)) break;
                    if ($this->ruleMax($param)) break;
                    if ($this->ruleEqual($param)) break;
                    if ($this->ruleLength($param)) break;
                    if ($this->ruleRange($param)) break;
                    if ($this->ruleNumber($param)) break;
                    if ($this->ruleRegexp($param)) break;
                    throw new ValidatorException("Invalid validation rule: ${param}");
                }
            }
        }
    }
    
    /**
     * 'required'に対する入力値チェック
     * @param String リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkRequired($key, $params)  {
        if (!array_key_exists($key, $params) || (array_key_exists($key, $params) && empty($params[$key]))) {
            $this->setError("required", $key, null);
            $errorMsg = "Validation rule error. '${key}' is required.";
            throw new ValidatorException($errorMsg);
        }
    }
    
    /**
     * 'min_length[n]'に対する入力値チェック
     * @param String バリデーションルール
     * @param String リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkMinLength($rule, $key, $params) {
        if (preg_match('/^(min_length)\[(0|[1-9]\d*)\]$/', $rule, $matches)) {
            $length = intval($matches[2]);
            if (array_key_exists($key, $params) && mb_strlen($params[$key], "UTF-8") < $length) {
                $rule = $matches[1] . "[" . $matches[2] . "]";
                $this->setError($rule, $key, $params[$key]);
                $errorMsg = "Validation rule error. '${key}' must be more than ${length}.";
                throw new ValidatorException($errorMsg);
            }
        }
    }

    /**
     * 'max_length[n]'に対する入力値チェック
     * @param String バリデーションルール
     * @param String リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkMaxLength($rule, $key, $params) {
        if (preg_match('/^(max_length)\[(0|[1-9]\d*)\]$/', $rule, $matches)) {
            $length = intval($matches[2]);
            if (array_key_exists($key, $params) && mb_strlen($params[$key], "UTF-8") > $length) {
                $rule = $matches[1] . "[" . $matches[2] . "]";
                $this->setError($rule, $key, $params[$key]);
                $errorMsg = "Validation rule error. '${key}' must be less than ${length}.";
                throw new ValidatorException($errorMsg);
            }
        }
    }

    /**
     * 'min[n]'に対する入力値チェック
     * @param String バリデーションルール
     * @param String リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkMin($rule, $key, $params) {
        if (preg_match('/^(min)\[([-]?\d+\.?\d+?)\]$/', $rule, $matches)) {
            $num = intval($matches[2]);
            if (array_key_exists($key, $params) && intval($params[$key]) < $num) {
                $rule = $matches[1] . "[" . $matches[2] . "]";
                $this->setError($rule, $key, $params[$key]);
                $errorMsg = "Validation rule error. '${key}' must be more than ${num}.";
                throw new ValidatorException($errorMsg);
            }
        }
    }

    /**
     * 'max[n]'に対する入力値チェック
     * @param String バリデーションルール
     * @param String リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkMax($rule, $key, $params) {
        if (preg_match('/^(max)\[([-]?\d+\.?\d+?)\]$/', $rule, $matches)) {
            $num = intval($matches[2]);
            if (array_key_exists($key, $params) && intval($params[$key]) > $num) {
                $rule = $matches[1] . "[" . $matches[2] . "]";
                $this->setError($rule, $key, $params[$key]);
                $errorMsg = "Validation rule error. '${key}' must be less than ${num}.";
                throw new ValidatorException($errorMsg);
            }
        }
    }
    
    /**
     * 'equal[s]'に対する入力値チェック
     * @param String バリデーションルール
     * @param String リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkEqual($rule, $key, $params) {
        if (preg_match('/^(equal)\[(.*)\]$/', $rule, $matches)) {
            $val = $matches[2];
            if (array_key_exists($key, $params) && $params[$key] !== $val) {
                $rule = $matches[1] . "[" . $matches[2] . "]";
                $this->setError($rule, $key, $params[$key]);
                $errorMsg = "Validation rule error. '${key}' must be equals ${val}.";
                throw new ValidatorException($errorMsg);
            }
        }
    }

    /**
     * 'length[n]'に対する入力値チェック
     * @param String バリデーションルール
     * @param String リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkLength($rule, $key, $params) {
        if (preg_match('/^(length)\[(0|[1-9]\d*)\]$/', $rule, $matches)) {
            $num = intval($matches[2]);
            if (array_key_exists($key, $params) && mb_strlen($params[$key], "UTF-8") !== $num) {
                $rule = $matches[1] . "[" . $matches[2] . "]";
                $this->setError($rule, $key, $params[$key]);
                $errorMsg = "Validation rule error. '${key}' must be equals ${num}.";
                throw new ValidatorException($errorMsg);
            }
        }
    }

    /**
     * 'ramge[n..m]'に対する入力値チェック
     * @param String バリデーションルール
     * @param String リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkRange($rule, $key, $params) {
        if (preg_match('/^(range)\[([-]?\d+\.?\d+?)\.\.([-]?\d+\.?\d+?)\]$/', $rule, $matches)) {
            $low = $matches[2];
            $high = $matches[3];
            if (array_key_exists($key, $params) && (intval($params[$key]) < $low || intval($params[$key]) > $high)) {
                $rule = $matches[1] . "[" . $low . ".." . $high . "]";
                $this->setError($rule, $key, $params[$key]);
                $errorMsg = "Validation rule error. '${key}' must be between ${low} and ${high}.";
                throw new ValidatorException($errorMsg);
            }
        }
    }
    
    /**
     * 'number'に対する入力値チェック
     * @param String バリデーションルール
     * @param String リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkNumber($rule, $key, $params) {
        if (preg_match('/^(number)$/', $rule) && array_key_exists($key, $params) && !preg_match('/^\d$/', $params[$key])) {
            $this->setError($rule, $key, $params[$key]);
            $errorMsg = "Validation rule error. '$params[$key]' is not number.";
            throw new ValidatorException($errorMsg);
        }
    }
    
    /**
     * 'regexp[s]'に対する入力値チェック
     * @param String バリデーションルール
     * @param String リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkRegexp($rule, $key, $params) {
        if (preg_match('/^(regexp)\[(\/.*?\/[a-z]*)\]$/', $rule, $matches)) {
            $regexp = $matches[2];
            if (array_key_exists($key, $params) && !preg_match($regexp, $params[$key])) {
                $rule = $matches[1] . "[" . $matches[2] . "]";
                $this->setError($rule, $key, $params[$key]);
                $errorMsg = "Validation rule error. '${key}' is unmatche regular expression '${regexp}'.";
                throw new ValidatorException($errorMsg);
            }
        }
    }

    /**
     * 'required'の構文を検証
     * @param String バリデーションルール
     * @return Boolean 検証結果
     */
    private function ruleRequired($rule) {
        return preg_match('/^(required)$/', $rule);
    }
    
    /**
     * 'min_length[n]'の構文を検証
     * @param String バリデーションルール
     * @return Boolean 検証結果
     */
    private function ruleMinLength($rule) {
        return preg_match('/^(min_length)\[(0|[1-9]\d*)\]$/', $rule);
    }
    
    /**
     * 'max_length[n]'の構文を検証
     * @param String バリデーションルール
     * @return Boolean 検証結果
     */
    private function ruleMaxLength($rule) {
        return preg_match('/^(max_length)\[(0|[1-9]\d*)\]$/', $rule);
    }
    
    /**
     * 'min[n]'の構文を検証
     * @param String バリデーションルール
     * @return Boolean 検証結果
     */
    private function ruleMin($rule) {
        return preg_match('/^(min)\[([-]?\d+\.?\d+?)\]$/', $rule);
    }
    
    /**
     * 'max[n]'の構文を検証
     * @param String バリデーションルール
     * @return Boolean 検証結果
     */
    private function ruleMax($rule) {
        return preg_match('/^(max)\[([-]?\d+\.?\d+?)\]$/', $rule);
    }
    
    /**
     * 'equal[s]'の構文を検証
     * @param String バリデーションルール
     * @return Boolean 検証結果
     */
    private function ruleEqual($rule) {
        return preg_match('/^(equal)\[(.*)\]$/', $rule);
    }
    
    /**
     * 'length[n]'の構文を検証
     * @param String バリデーションルール
     * @return Boolean 検証結果
     */
    private function ruleLength($rule) {
        return preg_match('/^(length)\[(0|[1-9]\d*)\]$/', $rule);
    }
    
    /**
     * 'range[n..m]'の構文を検証
     * @param String バリデーションルール
     * @return Boolean 検証結果
     */
    private function ruleRange($rule) {
        if (preg_match('/^(range)\[([-]?\d+\.?\d+?)\.\.([-]?\d+\.?\d+?)\]$/', $rule, $matches)) {
            return intval($matches[2]) < intval($matches[3]);
        }
        return false;
    }
    
    /**
     * 'number'の構文を検証
     * @param String バリデーションルール
     * @return Boolean 検証結果
     */
    private function ruleNumber($rule) {
        return preg_match('/^(number)$/', $rule);
    }

    /**
     * 'regexp[/s/]'の構文を検証
     * @param String バリデーションルール
     * @return Boolean 検証結果
     */
    private function ruleRegexp($rule) {
        return preg_match('/^(regexp)\[(\/.*?\/[a-z]*)\]$/', $rule);
    }
}
