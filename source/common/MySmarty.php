<?php

require_once(__DIR__ . "/../Smarty/libs/Smarty.class.php");

class MySmarty extends Smarty {

    public function __construct() {
        parent::__construct();
        $this->template_dir = __DIR__ . "/../templates";
        $this->compile_dir  = __DIR__ . "/../templates_c";

        // ここに書くべきではないだろうが・・・
        // 表示側では php の警告を一切表示しない
//        error_reporting(0);
    }
}