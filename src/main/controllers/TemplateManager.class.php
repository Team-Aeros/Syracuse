<?php

/**
 * Syracuse
 *
 * @version     1.0 Beta 1
 * @author      Aeros Development
 * @copyright   2017-2018 Syracuse
 * @since       1.0 Beta 1
 *
 * @license     MIT
 */

namespace Syracuse\src\main\controllers;

class TemplateManager {

    private $_templateDir;
    private $_cacheDir;

    public function __construct() {
        $this->_templateDir = __DIR__;
        $this->_cacheDir = __DIR__;
    }

    private function compileTemplate(string $templateName) : string {
        $file = @fopen($this->_templateDir . '/' . $templateName, 'r');

        if (!$content)
            throw new Exception('Could not compile template.');

        $content = fread($content);
        $buffer = '';
        $max = count($content);

        for ($i = 0; $i < $max; $i++) {
            if ($content[$i] == '{' && $i < $max && $content[$i + 1] == '{') {
                $j = $i;

                while ($j < $max) {
                    $j++;

                    if ($content[$j] . $content[$j + 1] != '}}') {
                        $currentOperation = trim($currentOperation);

                        if (preg_match('/^[a-zA-Z_]*$/', $currentOperation, $match)) {
                            $this->throwErrorIfNoMatch($match);
                            $content .= sprintf('$this->_params[\'%s\']', $match[0]);
                        }
                        else
                            throw new Exception(sprintf('Invalid syntax at character %u', $j));

                        $currentOperation = '';
                        continue;
                    }

                    else
                        $currentOperation .= $content[$j];
                }
                
                $i = $j + 1;
            }
            else
                $buffer .= $content[$i];
        }

        fclose($file);
        
        if (!empty($currentOperation))
            throw new Exception(sprintf('Unexpected end of template: %s.', $templateName));

        return $buffer;
    }

    public function getTemplate(string $templateName, array $data) {
        $templateNoCache = $this->_templateDir . '/' . $templateName . '.tpl';

        if (file_exists($templateNoCache))
            $lastUpdated = filemtime($templateNoCache);
        else
            throw new Exception('Could not load template.');

        if ($path = file_exists($this->_cacheDir . '/' . $templateName . '.cached.php')) {
            require $path;

            $className = 'Syracuse\template\\' . $templateName;
            $template = new $className($data);

            if ($template->getUpdatedTime() == $lastUpdated) {
                $template->show();
                die;
            }
        }

        $this->compileTemplate($templateName);
    }

    private function throwErrorIfNoMatch(array $match) : void {
        if (empty($match[0]))
            throw new Exception('Could not interpret match.', implode('<br />', debug_backtrace()));
    }

    public function setTemplateDir(string $templateDir) : void {
        $this->_templateDir = $templateDir;
    }

    public function setCacheDir(string $cacheDir) : void {
        $this->_cacheDir = $cacheDir;
    }
}