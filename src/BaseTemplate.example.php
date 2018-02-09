<?php

namespace Syracuse\template;

class {data:tpl_name} {

    private $_params;
    /**
     * @param array $params
     */
    public function __construct(array $params = []) {
        $this->_params = $params;
    }

    public function show() : void {
        {data:body};
    }

    /**
     * Function for returning the updated time.
     * @return string
     */
    public function getUpdatedTime() : string {
        return '{data:tpl_last_updated}';
    }
}