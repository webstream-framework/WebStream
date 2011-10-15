<?php
/**
 * 各種Exceptionクラス
 * @author Ryuichi Tanaka
 * @since 2011/09/29
 */
/** CSRFエラー例外 */
class CsrfException extends Exception {}
/** ルーティング失敗例外 */
class RouterException extends Exception {}
/** リソース取得失敗例外 */
class ResoureceNotFoundException extends Exception {}
