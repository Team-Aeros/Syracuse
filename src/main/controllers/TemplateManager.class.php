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

use Exception;

class TemplateManager {

    private $_templateDir;
    private $_cacheDir;

    private const OPERATION_NONE = 0x00;
    private const OPERATION_HARDCODED_STRING = 0x01;
    private const OPERATION_VARIABLE_CALL = 0x02;
    private const OPERATION_FUNCTION_CALL = 0x03;
    private const OPERATION_LOOP = 0x04;

    public function __construct() {
        $this->_templateDir = __DIR__;
        $this->_cacheDir = __DIR__;
    }

    private function compileTemplate(string $templateName) : string {
        $filename = $this->_templateDir . '/' . $templateName . '.tpl';
        $file = @fopen($filename, 'r');

        if (!$file)
            throw new Exception('Could not compile template.');

        $content = fread($file, filesize($filename));
        $buffer = '';
        $max = strlen($content);

        $lastOperation = self::OPERATION_NONE;

        for ($i = 0; $i < $max; $i++) {
            // Simple operations, like {{ i_am_an_operation() }}
            if ($content[$i] == '{' && $i < ($max - 1) && $content[$i + 1] == '{') {
                $j = $i + 2;
                $currentOperation = '';
                $done = false;

                while ($j < $max - 1 && !$done) {
                    if (($content[$j] . $content[$j + 1]) == '}}' || ($content[$j - 1] . $content[$j]) == '}}') {
                        $currentOperation = trim($currentOperation);

                        // Regular variables
                        if (preg_match('/^[a-zA-Z_]*$/', $currentOperation, $match)) {
                            $this->throwErrorIfNoMatch($match);

                            switch ($lastOperation) {
                                case self::OPERATION_NONE:
                                case self::OPERATION_LOOP:
                                    $buffer .= ' echo ';
                                    break;
                                case self::OPERATION_HARDCODED_STRING:
                                    $buffer .= '\', ';
                                    break;
                                default:
                                    $buffer .= ', ';
                            }

                            $buffer .= sprintf('$this->_params[\'%s\']', $match[0]);
                            $lastOperation = self::OPERATION_VARIABLE_CALL;
                        }
                        // Functions
                        else if (preg_match('/^([a-zA-Z_].+)*\((.*)\)$/', $currentOperation, $match)) {
                            $this->throwErrorIfNoMatch($match);

                            switch ($lastOperation) {
                                case self::OPERATION_HARDCODED_STRING:
                                    $buffer .= '\'; ';
                                    break;
                                case self::OPERATION_VARIABLE_CALL:
                                    $buffer .= '; ';
                                    break;
                            }

                            $arguments = [];
                            foreach (explode(',', $match[2]) as $argument) {
                                if (preg_match('/^\'[a-zA-Z].+\'$/', $argument) || is_numeric($argument))
                                    $arguments[] = $argument;
                                else
                                    $arguments[] = sprintf('$this->_params[\'%s\']', $argument);
                            }

                            // A really cheap hack, I know, I know
                            if ($match[1][0] == '_')
                                $match[1] = 'echo ' . $match[1];

                            $buffer .= sprintf('%s;', $match[1] . '(' . implode(', ', $arguments) . ')');
                            $lastOperation = self::OPERATION_FUNCTION_CALL;
                        }
                        // Filters
                        else if (preg_match('/^([a-zA-Z_]*)\|([a-zA-Z_]*)$/', $currentOperation, $match)) {
                            $this->throwErrorIfNoMatch($match);

                            if (empty($match[1]) || empty($match[2]))
                                throw new Exception('Invalid function name');

                            switch ($lastOperation) {
                                case self::OPERATION_HARDCODED_STRING:
                                    $buffer .= '\'; ';
                                    break;
                                case self::OPERATION_VARIABLE_CALL:
                                    $buffer .= '; ';
                                    break;
                            }

                            $buffer .= sprintf('echo %s($this->_params[\'%s\']);', $match[2], $match[1]);
                            $lastOperation = self::OPERATION_FUNCTION_CALL;
                        }
                        else
                            throw new Exception(sprintf('Invalid syntax at position %u', $j));

                        $currentOperation = '';
                        $done = true;
                        $i = $j + 1;
                    }
                    else
                        $currentOperation .= $content[$j];

                    $j++;
                }
            }
            else {
                switch ($lastOperation) {
                    case self::OPERATION_NONE:
                    case self::OPERATION_FUNCTION_CALL:
                        $buffer .= 'echo \'';
                        break;
                    case self::OPERATION_VARIABLE_CALL:
                        $buffer .= ', \'';
                        break;
                }

                $buffer .= $content[$i] == '\'' ? '\\' . $content[$i] : $content[$i];
                $lastOperation = self::OPERATION_HARDCODED_STRING;
            }
        }

        if ($lastOperation == self::OPERATION_HARDCODED_STRING)
            $buffer .= '\'';

        fclose($file);
        
        if (!empty($currentOperation))
            throw new Exception(sprintf('Unexpected end of template: %s.', $templateName));

        return $buffer;
    }

    public function getTemplate(string $templateName, array $data) : void {
        $templateNoCache = $this->_templateDir . '/' . $templateName . '.tpl';

        if (file_exists($templateNoCache))
            $lastUpdated = filemtime($templateNoCache);
        else
            throw new Exception('Could not load template.');

        $path = $this->_cacheDir . '/' . $templateName . '.cached.php';
        if (!file_exists($path) || SYRACUSE_DEBUG)
            $this->updateCache($templateName, $this->compileTemplate($templateName), $lastUpdated);

        if (file_exists($path))
            require $path;
        else
            throw new Exception('Could not load cache file.');

        $className = 'Syracuse\template\\' . $templateName;
        $template = new $className($data);

        if ($template->getUpdatedTime() == $lastUpdated) {
            $template->show();
            return;
        }
    }

    private function updateCache(string $templateName, string $body, string $updated) : void {
        $path = __DIR__ . '/../../BaseTemplate.example.php';
        $baseTemplate = @fopen($path, 'r');

        if (!$baseTemplate)
            throw new Exception('Could not open base template.');

        $baseTemplateBody = fread($baseTemplate, filesize($path));
        fclose($baseTemplate);

        $cachedTemplate = @fopen($this->_cacheDir . '/' . $templateName . '.cached.php', 'w');

        if (!$cachedTemplate)
            throw new Exception(sprintf('Could not create cache file for template %s', $templateName));

        $cachedTemplateBody = str_replace([
            '{data:tpl_name}',
            '{data:body}',
            '{data:tpl_last_updated}'
        ], [
            $templateName,
            $body,
            $updated
        ], $baseTemplateBody);

        fwrite($cachedTemplate, $cachedTemplateBody);
        fclose($cachedTemplate);
    }

    private function throwErrorIfNoMatch(array $match) : void {
        if (empty($match[0]))
            throw new Exception('Could not interpret match.');
    }

    public function setTemplateDir(string $templateDir) : void {
        $this->_templateDir = $templateDir;
    }

    public function setCacheDir(string $cacheDir) : void {
        $this->_cacheDir = $cacheDir;
    }
}