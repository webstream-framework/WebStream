<?php
namespace WebStream\Delegate;

use WebStream\Http\Request;
use WebStream\Exception\Extend\ValidateException;
use WebStream\Module\Logger;

/**
 * Validator
 * @author Ryuichi TANAKA.
 * @since 2012/09/13
 * @version 0.4
 */
class Validator
{
    /** バリデーションルール */
    private static $rules;
    /** リクエスト */
    private $request;
    /** ルーティング */
    private $router;

    /**
     * コンストラクタ
     * @param object DIコンテナ
     */
    public function __construct(Request $request, Router $router)
    {
        $this->request = $request;
        $this->router = $router;
    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {
        Logger::debug("Validator is clear.");
    }

    /**
     * バリデーションルールを設定する
     * @param Hash バリデーションルール定義
     */
    public static function setRule($rules)
    {
        self::$rules = $rules;
    }

    /**
     * バリデーションチェック
     */
    public function check()
    {
        $routingParams = $this->router->routingParams();
        $controller = $routingParams["controller"];
        $action = $routingParams["action"];
        $params = [];
        $method = null;

        if ($this->request->isGet()) {
            $params = $this->request->get();
            $method = "get";
        } elseif ($this->request->isPost()) {
            $params = $this->request->post();
            $method = "post";
        } elseif ($this->request->isPut()) {
            $params = $this->request->put();
            $method = "put";
        } elseif ($this->request->isDelete()) {
            $params = $this->request->delete();
            $method = "delete";
        }

        // ルールが存在しない場合は終了
        if (!array_key_exists($controller . "#" . $action, self::$rules)) {
            Logger::debug("Validation rule is not found: " . $controller . "#" . $action);

            return;
        }
        $rules = self::$rules[$controller . "#" . $action];

        // リクエストに対するバリデーションルールを展開
        $key = null;
        foreach ($rules as $methodKey => $value) {
            if (preg_match('/^((?:p(?:os|u)|ge)t|delete)#([A-Za-z0-9_-]+)$/', $methodKey, $matches)) {
                if ($method === $matches[1]) {
                    $key = $matches[2];
                } else {
                    throw new ValidateException("Validate rule is invalid: " . $methodKey);
                }
            }

            // 複数あるバリデーションルールを一つずつ検証
            $ruleList = preg_split('/\|/', $value);
            foreach ($ruleList as $rule) {
                if ($this->ruleRequired($rule)) { // required
                    $this->checkRequired($key, $params);
                } elseif ($this->ruleMinLength($rule)) { // min_length
                    $this->checkMinLength($rule, $key, $params);
                } elseif ($this->ruleMaxLength($rule)) { // max_length
                    $this->checkMaxLength($rule, $key, $params);
                } elseif ($this->ruleMin($rule)) { // min
                    $this->checkMin($rule, $key, $params);
                } elseif ($this->ruleMax($rule)) { // max
                    $this->checkMax($rule, $key, $params);
                } elseif ($this->ruleEqual($rule)) { // equal
                    $this->checkEqual($rule, $key, $params);
                } elseif ($this->ruleLength($rule)) { // length
                    $this->checkLength($rule, $key, $params);
                } elseif ($this->ruleRange($rule)) { // range
                    $this->checkRange($rule, $key, $params);
                } elseif ($this->ruleNumber($rule)) { // number
                    $this->checkNumber($rule, $key, $params);
                } elseif ($this->ruleRegexp($rule)) { // regexp
                    $this->checkRegexp($rule, $key, $params);
                } else {
                    throw new ValidatorException("Invalid validation rule in $key.");
                }
            }
        }
    }

    /**
     * 'required'に対する入力値チェック
     * @param string リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkRequired($key, $params)
    {
        if (!array_key_exists($key, $params) || (array_key_exists($key, $params) && empty($params[$key]))) {
            $errorMsg = "Validation rule error. '${key}' is required.";
            throw new ValidateException($errorMsg);
        }
    }

    /**
     * 'min_length[n]'に対する入力値チェック
     * @param string バリデーションルール
     * @param string リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkMinLength($rule, $key, $params)
    {
        if (preg_match('/^(min_length)\[(0|[1-9]\d*)\]$/', $rule, $matches)) {
            $length = intval($matches[2]);
            if (array_key_exists($key, $params) && mb_strlen($params[$key], "UTF-8") < $length) {
                $errorMsg = "Validation rule error. '${key}' must be more than ${length}.";
                throw new ValidateException($errorMsg);
            }
        }
    }

    /**
     * 'max_length[n]'に対する入力値チェック
     * @param string バリデーションルール
     * @param string リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkMaxLength($rule, $key, $params)
    {
        if (preg_match('/^(max_length)\[(0|[1-9]\d*)\]$/', $rule, $matches)) {
            $length = intval($matches[2]);
            if (array_key_exists($key, $params) && mb_strlen($params[$key], "UTF-8") > $length) {
                $errorMsg = "Validation rule error. '${key}' must be less than ${length}.";
                throw new ValidateException($errorMsg);
            }
        }
    }

    /**
     * 'min[n]'に対する入力値チェック
     * @param string バリデーションルール
     * @param string リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkMin($rule, $key, $params)
    {
        if (preg_match('/^(min)\[([-]?\d+\.?\d+?)\]$/', $rule, $matches)) {
            $num = $matches[2];
            if (array_key_exists($key, $params) && $params[$key] < $num) {
                $errorMsg = "Validation rule error. '${key}' must be more than ${num}.";
                throw new ValidateException($errorMsg);
            }
        }
    }

    /**
     * 'max[n]'に対する入力値チェック
     * @param string バリデーションルール
     * @param string リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkMax($rule, $key, $params)
    {
        if (preg_match('/^(max)\[([-]?\d+\.?\d+?)\]$/', $rule, $matches)) {
            $num = $matches[2];
            if (array_key_exists($key, $params) && $params[$key] > $num) {
                $errorMsg = "Validation rule error. '${key}' must be less than ${num}.";
                throw new ValidateException($errorMsg);
            }
        }
    }

    /**
     * 'equal[s]'に対する入力値チェック
     * @param string バリデーションルール
     * @param string リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkEqual($rule, $key, $params)
    {
        if (preg_match('/^(equal)\[(.*)\]$/', $rule, $matches)) {
            $val = $matches[2];
            if (array_key_exists($key, $params) && $params[$key] !== $val) {
                $errorMsg = "Validation rule error. '${key}' must be equals ${val}.";
                throw new ValidateException($errorMsg);
            }
        }
    }

    /**
     * 'length[n]'に対する入力値チェック
     * @param string バリデーションルール
     * @param string リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkLength($rule, $key, $params)
    {
        if (preg_match('/^(length)\[(0|[1-9]\d*)\]$/', $rule, $matches)) {
            $num = intval($matches[2]);
            if (array_key_exists($key, $params) && mb_strlen($params[$key], "UTF-8") !== $num) {
                $errorMsg = "Validation rule error. '${key}' must be equals ${num}.";
                throw new ValidateException($errorMsg);
            }
        }
    }

    /**
     * 'ramge[n..m]'に対する入力値チェック
     * @param string バリデーションルール
     * @param string リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkRange($rule, $key, $params)
    {
        if (preg_match('/^(range)\[([-]?\d+\.?\d+?)\.\.([-]?\d+\.?\d+?)\]$/', $rule, $matches)) {
            $low = $matches[2];
            $high = $matches[3];
            if (array_key_exists($key, $params) && ($params[$key] < $low || $params[$key] > $high)) {
                $errorMsg = "Validation rule error. '${key}' must be between ${low} and ${high}.";
                throw new ValidateException($errorMsg);
            }
        }
    }

    /**
     * 'number'に対する入力値チェック
     * @param string バリデーションルール
     * @param string リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkNumber($rule, $key, $params)
    {
        if (preg_match('/^(number)$/', $rule) && array_key_exists($key, $params) && !is_numeric($params[$key])) {
            $errorMsg = "Validation rule error. '$params[$key]' is not number type.";
            throw new ValidateException($errorMsg);
        }
    }

    /**
     * 'regexp[s]'に対する入力値チェック
     * @param string バリデーションルール
     * @param string リクエストキー
     * @param Hash リクエストパラメータ
     */
    private function checkRegexp($rule, $key, $params)
    {
        if (preg_match('/^(regexp)\[(\/.*?\/[a-z]*)\]$/', $rule, $matches)) {
            $regexp = $matches[2];
            if (array_key_exists($key, $params) && !preg_match($regexp, $params[$key])) {
                $rule = $matches[1] . "[" . $matches[2] . "]";
                $errorMsg = "Validation rule error. '${key}' is unmatche regular expression '${regexp}'.";
                throw new ValidateException($errorMsg);
            }
        }
    }

    /**
     * 'required'の構文を検証
     * @param string バリデーションルール
     * @return boolean 検証結果
     */
    private function ruleRequired($rule)
    {
        return preg_match('/^(required)$/', $rule);
    }

    /**
     * 'min_length[n]'の構文を検証
     * @param string バリデーションルール
     * @return boolean 検証結果
     */
    private function ruleMinLength($rule)
    {
        return preg_match('/^(min_length)\[(0|[1-9]\d*)\]$/', $rule);
    }

    /**
     * 'max_length[n]'の構文を検証
     * @param string バリデーションルール
     * @return boolean 検証結果
     */
    private function ruleMaxLength($rule)
    {
        return preg_match('/^(max_length)\[(0|[1-9]\d*)\]$/', $rule);
    }

    /**
     * 'min[n]'の構文を検証
     * @param string バリデーションルール
     * @return boolean 検証結果
     */
    private function ruleMin($rule)
    {
        return preg_match('/^(min)\[([-]?(?:0\.\d+?|[1-9][0-9]*\.?\d+?))\]$/', $rule);
    }

    /**
     * 'max[n]'の構文を検証
     * @param string バリデーションルール
     * @return boolean 検証結果
     */
    private function ruleMax($rule)
    {
        return preg_match('/^(max)\[([-]?(?:0\.\d+?|[1-9][0-9]*\.?\d+?))\]$/', $rule);
    }

    /**
     * 'equal[s]'の構文を検証
     * @param string バリデーションルール
     * @return boolean 検証結果
     */
    private function ruleEqual($rule)
    {
        return preg_match('/^(equal)\[(.*)\]$/', $rule);
    }

    /**
     * 'length[n]'の構文を検証
     * @param string バリデーションルール
     * @return boolean 検証結果
     */
    private function ruleLength($rule)
    {
        return preg_match('/^(length)\[(0|[1-9]\d*)\]$/', $rule);
    }

    /**
     * 'range[n..m]'の構文を検証
     * @param string バリデーションルール
     * @return boolean 検証結果
     */
    private function ruleRange($rule)
    {
        if (preg_match('/^(range)\[([-]?\d+\.?\d+?)\.\.([-]?\d+\.?\d+?)\]$/', $rule, $matches)) {
            if (!preg_match('/^([-]?0\.\d+|[-]?[1-9][0-9]+\.?\d*)/', $matches[2])) {
                return false;
            }
            if (!preg_match('/^([-]?0\.\d+|[-]?[1-9][0-9]+\.?\d*)/', $matches[3])) {
                return false;
            }
            $low = preg_match('/\./', $matches[2]) ? floatval($matches[2]) : intval($matches[2]);
            $high = preg_match('/\./', $matches[3]) ? floatval($matches[3]) : intval($matches[3]);

            return $low < $high;
        }

        return false;
    }

    /**
     * 'number'の構文を検証(int, long)
     * @param string バリデーションルール
     * @return boolean 検証結果
     */
    private function ruleNumber($rule)
    {
        return preg_match('/^(number)$/', $rule);
    }

    /**
     * 'double'の構文を検証
     * @param string バリデーションルール
     * @return boolean 検証結果
     */
    private function ruleDouble($rule)
    {
        return preg_match('/^(double) $/', $rule);
    }

    /**
     * 'regexp[/s/]'の構文を検証
     * @param string バリデーションルール
     * @return boolean 検証結果
     */
    private function ruleRegexp($rule)
    {
        return preg_match('/^(regexp)\[(\/.*?\/[a-z]*)\]$/', $rule);
    }
}
