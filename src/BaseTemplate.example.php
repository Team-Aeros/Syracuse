<?php

class Template_{data:tpl_name} {

    private $_params;

    public function __construct(array $params = []) {
        $this->_params = $params;
    }

    public function getUpdatedTime() : string {
        return {data:tpl_last_updated};
    }
}