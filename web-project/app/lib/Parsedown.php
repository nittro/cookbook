<?php


namespace App;
use Nette\Application\LinkGenerator;
use Nette\Neon\Neon;


/**
 * Nittro Cookbook Parsedown extensions
 *
 * Processes [[Cookbook links]] and allows injecting
 * documented source code directly into Markdown documents
 * on compile time so that you don't need to keep the code
 * examples up to date by hand.
 *
 * Usage in Markdown:
 *
 * Load whole file:
 * >> file path/to/file.txt
 *
 * Load a section of file:
 * >> file path/to/file.txt, line 20 to 24
 *
 * Using from .. to .. pattern matching:
 * (note also that the comma after the path is optional)
 * >> file path/to/file.txt from "Chapter 1..." to "...and thus concludes this chapter."
 *
 * If the patterns start or end with three dots (...), they are
 * matched literally, otherwise they are matched as regular expressions:
 * >> file path/to/file.txt from /^chapter\s+1$/im to "/concludes (this|the) chapter/"
 *
 * Load a PHP class:
 * >> class My\Example\Class
 *
 * .. or just a method:
 * >> class My\Example\Class, method myExampleMethod
 *
 * Loading a Latte template may look a lot like
 * loading a regular file...
 * >> template some/layout/template.latte
 *
 * ... until you want to show just a specific block:
 * >> template some/layout/template.latte, block menu
 *
 *
 * All paths are considered to be relative from
 * the $basePath you provide in the constructor
 * and even classes are only loaded if the file
 * they are defined in lies within $basePath.
 */
class Parsedown extends \Parsedown
{

    private $linkGenerator;

    /** @var string */
    private $basePath;

    /**
     * @param LinkGenerator $linkGenerator
     * @param string $basePath
     */
    public function __construct(LinkGenerator $linkGenerator, $basePath)
    {
        $this->linkGenerator = $linkGenerator;
        $this->basePath = realpath($basePath);
        $this->InlineTypes['['][] = 'NetteLink';
        array_unshift($this->BlockTypes['>'], 'CodeExcerpt');
    }

    /**
     * @param array $Excerpt
     * @return array|null
     */
    protected function inlineNetteLink($Excerpt)
    {
        if (preg_match('/^\[\[([^]|]+)\|([^]]+)\]\]/', $Excerpt['text'], $m)) {
            @list ($action, $params) = explode(' ', $m[2], 2);
            $params = $params ? Neon::decode('{' . trim($params) . '}') : [];
            $url = $this->linkGenerator->link($action, $params);

            return [
                'extent' => strlen($m[0]),
                'element' => [
                    'name' => 'a',
                    'handler' => 'line',
                    'text' => $m[1],
                    'attributes' => [
                        'href' => $url,
                    ],
                ],
            ];
        }

        return null;
    }

    /**
     * @param array $Line
     * @return array|null
     */
    protected function blockCodeExcerpt($Line)
    {
        if (preg_match('/^>>[ ]*(file|class|template)[ ]+("[^"]+"|\'[^\']+\'|[^,\s]+)(?:(?:[ ]*,)?[ ]*(.+))?/', $Line['text'], $m)) {
            $type = $m[1];
            $target = trim($m[2], '"\'');
            $params = [];

            if (!empty($m[3]) && preg_match_all('/(line|from|to|method|block|snippet)[ ]+("[^"]+"|\'[^\']+\'|[^,\s]+)/', $m[3], $p, PREG_SET_ORDER)) {
                foreach ($p as $param) {
                    $params[$param[1]] = trim($param[2], '"\'');
                }
            }

            if ($type === 'file') {
                $contents = $this->loadFile($target, $params);
                $language = $this->extractLanguage($target);
            } else if ($type === 'class') {
                $contents = $this->loadClass($target, $params);
                $language = 'php';
            } else if ($type === 'template') {
                $contents = $this->loadTemplate($target, $params);
                $language = 'latte';
            } else {
                return null;
            }

            return $this->buildBlock($contents, $language);
        }

        return null;
    }

    /**
     * @param string $contents
     * @param string $language
     * @return array
     */
    private function buildBlock($contents, $language)
    {
        return [
            'element' => [
                'name' => 'pre',
                'handler' => 'element',
                'text' => [
                    'name' => 'code',
                    'attributes' => [
                        'class' => 'language-' . $language,
                    ],
                    'text' => htmlspecialchars($this->unindent($contents), ENT_QUOTES),
                ],
            ],
            'complete' => true,
        ];
    }

    /**
     * @param string $path
     * @return string
     */
    private function extractLanguage($path)
    {
        if (preg_match('/\.(php|phtml|html|latte|js|css|less)$/i', $path, $ext)) {
            $language = strtolower($ext[1]);

            if ($language === 'phtml') {
                $language = 'php';
            }
        } else {
            $language = 'html';
        }

        return $language;
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    private function loadFile($path, array $params = [])
    {
        $source = file_get_contents($this->basePath . '/' . ltrim($path, '/'));

        if (isset($params['line']) && isset($params['to'])) {
            $offset = (int) $params['line'] - 1;
            $length = (int) $params['to'] - $offset;
            $source = explode("\n", $source);
            $source = array_slice($source, $offset, $length);
            $source = implode("\n", $source);
        } else if (isset($params['from'])) {
            $from = $this->findLineStart($source, $this->findPattern($source, $params['from']));

            if (isset($params['to'])) {
                $length = $this->findLineEnd($source, $this->findPattern($source, $params['to'], $from)) - $from + 1;
            } else {
                $length = null;
            }

            $source = substr($source, $from, $length);
        }

        return $source;
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    private function loadClass($name, array $params = [])
    {
        $reflection = new \ReflectionClass($name);
        $path = $reflection->getFileName();

        $len = strlen($this->basePath) + 1;

        if (substr($path, 0, $len) !== $this->basePath . '/') {
            throw new \RuntimeException("Class lies outside of base path");
        } else {
            $path = substr($path, $len);
        }

        if (isset($params['method'])) {
            $reflection = $reflection->getMethod($params['method']);
        }

        $params['line'] = $reflection->getStartLine();
        $params['to'] = $reflection->getEndLine();

        if ($comment = $reflection->getDocComment()) {
            $params['line'] -= substr_count($comment, "\n") + 1;
        }

        return $this->loadFile($path, $params);
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    private function loadTemplate($path, array $params = [])
    {
        $source = $this->loadFile($path);

        if (isset($params['block'])) {
            $type = 'block';
            $name = ltrim($params['block'], '#');
        } else if (isset($params['snippet'])) {
            $type = 'snippet';
            $name = $params['snippet'];
        }

        if (isset($type) && isset($name)) {
            if (!preg_match("/\\{{$type}\\s+#?{$name}\\s*(?:\\}|\\|)|<(\\S+)\\s+(?:[^>]+\\s+)n:{$type}=\"{$name}\"/", $source, $m, PREG_OFFSET_CAPTURE)) {
                throw new \RuntimeException("Block not found");
            }

            $start = $m[0][1];
            $end = $start + strlen($m[0][0]);
            $open = 1;

            if (!empty($m[1][0])) {
                $closing = "@<(/?){$m[1][0]}(\\s+|>)@";
            } else {
                $closing = "@\\{(/?)$type(?:\\s+|\\}|\\|)@";
            }

            do {
                if (preg_match($closing, $source, $m, PREG_OFFSET_CAPTURE, $end)) {
                    $end = $m[0][1] + strlen($m[0][0]);
                    $open += empty($m[1][0]) ? 1 : -1;
                } else {
                    $end = null;
                    break;
                }
            } while ($open > 0);

            $start = $this->findLineStart($source, $start);

            if ($end !== null) {
                $end = $this->findLineEnd($source, $end) - $start + 1;
            }

            $source = substr($source, $start, $end);
        }

        return $source;
    }

    /**
     * @param string $source
     * @return string
     */
    private function unindent($source)
    {
        if (preg_match_all('/^(?:[ ]*|\t*)(?=\S)/m', $source, $m, PREG_PATTERN_ORDER)) {
            $len = min(array_map('strlen', $m[0]));
            $char = $m[0][0];

            if ($len > 0) {
                return preg_replace('/^[' . $char . ']{' . $len . '}/m', '', $source);
            }
        }

        return $source;
    }

    /**
     * @param string $source
     * @param string $pattern
     * @param int $offset
     * @return int
     */
    private function findPattern($source, $pattern, $offset = 0)
    {
        if (preg_match('/^\.\.\.(.+)|(.+)\.\.\.$/', $pattern, $m)) {
            $offset = strpos($source, empty($m[1]) ? $m[2] : $m[1], $offset);
        } else if (preg_match($pattern, $source, $m, PREG_OFFSET_CAPTURE, $offset)) {
            $offset = $m[0][1];
        } else {
            $offset = false;
        }

        if ($offset === false) {
            throw new \RuntimeException("Pattern not found");
        }

        return $offset;
    }

    /**
     * @param string $source
     * @param int $offset
     * @return int
     */
    private function findLineStart($source, $offset)
    {
        while ($offset > 0) {
            if ($source[$offset - 1] === "\n") {
                return $offset;
            }

            $offset--;
        }

        return $offset;
    }

    /**
     * @param string $source
     * @param int $offset
     * @return int
     */
    private function findLineEnd($source, $offset)
    {
        if ($source[$offset] === "\n") {
            return $offset - 1;
        }

        $max = strlen($source) - 1;

        while ($offset < $max) {
            if ($source[$offset + 1] === "\n") {
                return $offset;
            }

            $offset++;
        }

        return $offset;
    }

}
