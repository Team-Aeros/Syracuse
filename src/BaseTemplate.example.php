<?php

namespace Syracuse\template;

class {data:tpl_name} {

    private $_params;

    public function __construct(array $params = []) {
        $this->_params = $params;
    }

    public function show() : void {
        {data:body};
    }

    public function getUpdatedTime() : string {
        return '{data:tpl_last_updated}';
    }
}