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
    private const OPERATION_IF_ELSE = 0x05;

    private $_blocks = [];
    private $_blockId = 0;

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
                $i = $this->compileStatement($j, $buffer, $content, $max, $lastOperation);
            }
            // If-statements and loops
            else if ($i < ($max - 1) && ($content[$i] . $content[$i + 1]) == '{%') {
                $j = $i + 2;
                $i = $this->compileBlock($j, $buffer, $content, $max, $lastOperation);
            }
            else {
                switch ($lastOperation) {
                    case self::OPERATION_NONE:
                    case self::OPERATION_FUNCTION_CALL:
                    case self::OPERATION_IF_ELSE:
                    case self::OPERATION_LOOP:
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
        
        if (!empty($currentOperation) || !empty($this->_blocks))
            throw new Exception(sprintf('Unexpected end of template: %s.', $templateName));

        return $buffer;
    }

    private function compileStatement(int $start, string &$buffer, string $content, int $max, int &$lastOperation, bool $returnOnly = false) : int {
        $j = $start;

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
                        case self::OPERATION_IF_ELSE:
                        case self::OPERATION_FUNCTION_CALL:
                            if (!$returnOnly)
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
                    if ($match[1][0] == '_' && !$returnOnly)
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
                // 'X is not empty' (should only be present in if-statements)
                else if (preg_match('/^([a-zA-Z_]*) \Qis not empty\E$/', $currentOperation, $match)) {
                    $this->throwErrorIfNoMatch($match);
                    $buffer .= '!empty($this->_params[\'' . $match[1] . '\'])';
                }
                // 'X is empty'
                else if (preg_match('/^([a-zA-Z_]*) \Qis empty\E$/', $currentOperation, $match)) {
                    $this->throwErrorIfNoMatch($match);
                    $buffer .= 'empty($this->_params[\'' . $match[1] . '\'])';
                }
                // Array elements
                else if (preg_match('/^([a-zA-Z_]*)\.([a-zA-Z]*)$/', $currentOperation, $match)) {
                    $this->throwErrorIfNoMatch($match);

                    switch ($lastOperation) {
                        case self::OPERATION_NONE:
                        case self::OPERATION_LOOP:
                        case self::OPERATION_IF_ELSE:
                            if (!$returnOnly)
                                $buffer .= ' echo ';
                            break;
                        case self::OPERATION_HARDCODED_STRING:
                            $buffer .= '\', ';
                            break;
                        default:
                            $buffer .= ', ';
                    }

                    $buffer .= sprintf('$this->_params[\'%s\'][\'%s\']', $match[1], $match[2]);
                    $lastOperation = self::OPERATION_VARIABLE_CALL;
                }
                else
                    throw new Exception(sprintf('Invalid syntax at position %u. Operation: %s', $j, $currentOperation));

                $currentOperation = '';
                $done = true;
                $i = $j + 1;
            }
            else
                $currentOperation .= $content[$j];

            $j++;
        }

        return $j;
    }

    private function compileBlock(int $start, string &$buffer, string $content, int $max, int &$lastOperation) : int {
        $currentOperation = '';
        $j = $start;

        while ($j < ($max - 1) && ($content[$j] . $content[$j + 1]) != '%}') {
            $currentOperation .= $content[$j];
            $j++;
        }

        // The first keyword should be the type of statement (i.e. if, else, while, for)
        $statements = explode(' ', trim($currentOperation));
        $statement = $statements[0];

        $this->_blocks[$blockId = $this->_blockId++] = $statement;

        $condition = '';
        $statementConditions = trim(substr($currentOperation, strlen($statement) + 1)) . ' }}';
        $lastOperation = self::OPERATION_NONE;

        switch ($statement) {
            case 'if':
                $this->compileStatement(0, $condition, $statementConditions, strlen($statementConditions), $lastOperation, true);
                $blockOpening = $statement . ' (' . $condition . ') {';
                break;
            default:
                throw new Exception(sprintf('Unknown statement %s', $statement));
        }

        $j += 2;
        $blockEnclosed = '';
        $blockEnding = '';

        $lastOperation = self::OPERATION_NONE;

        $done = false;
        while ($j < ($max - 1) && !$done) {
            if (($content[$j] . $content[$j + 1]) != '{%' && ($content[$j] . $content[$j + 1]) != '{{') {
                switch ($lastOperation) {
                    case self::OPERATION_NONE:
                    case self::OPERATION_LOOP:
                    case self::OPERATION_IF_ELSE:
                    case self::OPERATION_FUNCTION_CALL:
                        $blockEnclosed .= ' echo \'';
                        $lastOperation = self::OPERATION_HARDCODED_STRING;
                        break;
                    case self::OPERATION_VARIABLE_CALL:
                        $blockEnclosed .= ', \'';
                        $lastOperation = self::OPERATION_HARDCODED_STRING;
                        break;
                }

                $blockEnclosed .= $content[$j];
            }
            else if (($content[$j] . $content[$j + 1]) == '{{') {
                $innerStatement = '';

                $k = $j + 2;
                $foundStatement = false;
                while ($k < ($max - 1) && !$foundStatement) {
                    if (($content[$k] . $content[$k + 1]) == '}}') {
                        $foundStatement = true;
                        $innerStatement .= '}}';
                    }
                    else
                        $innerStatement .= $content[$k];

                    $k++;
                }

                $j += $this->compileStatement(0, $blockEnclosed, $innerStatement, strlen($innerStatement), $lastOperation, true) + 2;
            }
            else {
                $subOperation = '';

                if ($lastOperation = self::OPERATION_HARDCODED_STRING) {
                    $blockEnclosed .= '\'; ';
                }

                $inBlock = true;

                while ($j < ($max - 1) && !$done) {
                    if (($content[$j] . $content[$j + 1]) != '%}' && ($content[$j] . $content[$j + 1] != '{%')) {
                        switch ($lastOperation) {
                            case self::OPERATION_NONE:
                            case self::OPERATION_LOOP:
                            case self::OPERATION_IF_ELSE:
                            case self::OPERATION_FUNCTION_CALL:
                                $blockEnclosed .= ' echo \'';
                                $lastOperation = self::OPERATION_HARDCODED_STRING;
                                break;
                            case self::OPERATION_VARIABLE_CALL:
                                $blockEnclosed .= ', \'';
                                $lastOperation = self::OPERATION_HARDCODED_STRING;
                                break;
                        }

                        $subOperation .= $content[$j];
                    }
                    else if (($content[$j] . $content[$j + 1] == '%}')) {
                        $subOperation = trim($subOperation);

                        if (substr($subOperation, 0, 3) == 'end' || substr($subOperation, 0, 4) == 'else') {
                            $subOperation = trim(substr($subOperation, 0, 6));

                            if ($subOperation == 'end' . $statement) {
                                $blockEnding = '}';
                                unset($this->_blocks[$blockId]);
                                $done = true;
                            }
                            else if ($subOperation == 'else') {
                                $blockEnclosed .= '} else { ';
                                $j += 2;
                            }
                            else
                                throw new Exception(sprintf('Unknown sub operation %s', $subOperation));
                        }
                        else {
                            $j = $this->compileBlock($j + 2, $blockEnclosed, $content, $max, $lastOperation) - 1;

                            // May or may not be a loop, but the result is the same
                            $lastOperation = self::OPERATION_LOOP;
                        }
                    }
                    else
                        $j++;

                    if (!$done)
                        $j++;
                }

                if (!$done)
                    $j += 2;
            }

            if (!$done)
                $j++;
        }

        switch ($lastOperation) {
            case self::OPERATION_VARIABLE_CALL:
                $buffer .= '; ';
                break;
            case self::OPERATION_HARDCODED_STRING:
                $buffer .= '\'; ';
                break;
        }

        $buffer .= $blockOpening . $blockEnclosed . $blockEnding;
        
        switch ($statement) {
            case 'if':
            case 'else':
            case 'elseif':
                $lastOperation = self::OPERATION_IF_ELSE;
                break;
            case 'while':
            case 'for':
            default:
                $lastOperation = self::OPERATION_LOOP;
        }

        return $j + 2;
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