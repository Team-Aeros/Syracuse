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

/**
 * Class TemplateManager
 * @package Syracuse\src\main\controllers
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class TemplateManager {

    /**
     * The path to the template directory
     */
    private $_templateDir;

    /**
     * The path to the cache dir
     */
    private $_cacheDir;

    /**
     * No previous operation
     */
    private const OPERATION_NONE = 0x00;

    /**
     * Hardcoded string
     */
    private const OPERATION_HARDCODED_STRING = 0x01;

    /**
     * Regular variable call
     */
    private const OPERATION_VARIABLE_CALL = 0x02;

    /**
     * Function call
     */
    private const OPERATION_FUNCTION_CALL = 0x03;

    /**
     * Loops
     */
    private const OPERATION_LOOP = 0x04;

    /**
     * If-else statements
     */
    private const OPERATION_IF_ELSE = 0x05;

    /**
     * The currently opened blocks
     */
    private $_blocks = [];

    /**
     * The amount of blocks that have been closed
     */
    private $_blockId = 0;

    /**
     * Whether or not we're in a foreach loop
     */
    private $_inForeachLoop;

    /**
     * Creates a new instance of the template manager
     */
    public function __construct() {
        $this->_templateDir = __DIR__;
        $this->_cacheDir = __DIR__;
    }

    /**
     * Function for compiling the given template.
     * @param string $templateName The name of the template (without the extension)
     * @return string The compiled temlpate
     * @throws Exception
     */
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

    /**
     * Compiles a single statement
     * @param int $start The current position
     * @param string $buffer A buffer to store the generated code in
     * @param string $content The content that needs to be parsed
     * @param int $max The content length
     * @param int $lastOperation The last operation
     * @param bool $returnOnly If set to true, variables will not be echoed (but returned instead)
     * @return int The new position
     * @throws Exception
     */
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

                    if (!$this->_inForeachLoop)
                        $buffer .= sprintf('$this->_params[\'%s\']', $match[0]);
                    else
                        $buffer .= '$' . $match[0];

                    $lastOperation = self::OPERATION_VARIABLE_CALL;
                }
                // External variables in foreach loops
                else if (preg_match('/^\Qexternal\E*\((.*)\)$/', $currentOperation, $match) && $this->_inForeachLoop) {
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

                    $buffer .= sprintf('$this->_params[%s]', $match[1]);

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

                    if (!$this->_inForeachLoop)
                        $buffer .= sprintf('$this->_params[\'%s\'][\'%s\']', $match[1], $match[2]);
                    else
                        $buffer .= sprintf('$%s[\'%s\']', $match[1], $match[2]);

                    $lastOperation = self::OPERATION_VARIABLE_CALL;
                }
                // Foreach loops
                else if (preg_match('/^([a-zA-Z_]*)\Q in \E([a-zA-Z]*)$/', $currentOperation, $match)) {
                    $this->throwErrorIfNoMatch($match);

                    $buffer .= sprintf('$this->_params[\'%s\'] as $%s', $match[2], $match[1]);
                }
                else if (preg_match('/^([a-zA-Z_]*)\,([a-zA-Z_]*)\Q in \E([a-zA-Z]*)$/', $currentOperation, $match)) {
                    $this->throwErrorIfNoMatch($match);

                    $buffer .= sprintf('$this->_params[\'%s\'] as $%s => $%s', $match[3], $match[1], $match[2]);
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

    /**
     * Compiles a block of code
     * @param int $start The current position
     * @param string $buffer A buffer to store the generated code in
     * @param string $content The content that needs to be parsed
     * @param int $max The content length
     * @param int $lastOperation The last operation
     * @return int The new position
     * @throws Exception
     */
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
            case 'for':
                $this->compileStatement(0, $condition, $statementConditions, strlen($statementConditions), $lastOperation, true);
                $blockOpening = ' foreach (' . $condition . ') {';
                $this->_inForeachLoop = true;
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
                                $this->_inForeachLoop = false;
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

    /**
     * Checks if a template needs to be recompiled and displays the result.
     * @param string $templateName The name of the template (without the .tpl extension)
     * @param array $data Template variables
     * @return void
     */
    public function getTemplate(string $templateName, array $data) : void {
        $templateNoCache = $this->_templateDir . '/' . $templateName . '.tpl';
        $cacheTemplate = false;

        if (file_exists($templateNoCache))
            $lastUpdated = filemtime($templateNoCache);
        else
            throw new Exception('Could not load template.');

        $path = $this->_cacheDir . '/' . $templateName . '.cached.php';
        if (!file_exists($path) || SYRACUSE_DEBUG)
            $cacheTemplate = true;

        if (file_exists($path))
            require $path;
        else
            throw new Exception('Could not load cache file.');

        $className = 'Syracuse\template\\' . $templateName;
        $template = new $className($data);

        /* Yes, this is not a very efficient solution, nor does it prevent browsers from saying 'This website is
           trying to refresh this page', but at least it works. */
        if ($template->getUpdatedTime() != $lastUpdated) {
            $this->updateCache($templateName, $this->compileTemplate($templateName), $lastUpdated);
            ob_clean();
            header('Location: #');
            exit;
        }

        $template->show();
        return;
    }

    /**
     * Writes the compiled template to cache
     * @param string $templateName The name of the template
     * @param string $body The template body
     * @param string $updated When the template was last updated
     * @return void
     */
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

    /**
     * Throws an error if no match was found
     * @param array $match An array of matches
     * @return void
     * @throws Exception
     */
    private function throwErrorIfNoMatch(array $match) : void {
        if (empty($match[0]))
            throw new Exception('Could not interpret match.');
    }

    /**
     * Sets the path to the template directory
     * @param string $templateDir The path to the template directory
     * @return void
     */
    public function setTemplateDir(string $templateDir) : void {
        $this->_templateDir = $templateDir;
    }

    /**
     * Sets the path to the cache directory
     * @param string $cacheDir The path to the cache directory
     * @return void
     */
    public function setCacheDir(string $cacheDir) : void {
        $this->_cacheDir = $cacheDir;
    }
}