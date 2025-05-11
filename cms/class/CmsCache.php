<?php

namespace App\Plugin\Cms;
if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', ROOT_PATH . 'static/');
}

class CmsCache
{

    public string $dirname = CACHE_PATH . LANG . DIRECTORY_SEPARATOR;
    public $filename;
    public $file;
    public $htmlFile;
    public $duration = CACHE_DURATION; // In seconds

    private $buffer = false;

    public function __construct($filename)
    {
        if (createFolder($this->dirname, 0755, true)) {
            createFile(CACHE_PATH . 'index.php', ['content' => DEFAULT_INDEX_CONTENT]);
        }
        $this->filename = $filename;
        $this->file = $this->dirname . $this->filename;
        $this->htmlFile = $this->dirname . substr($this->filename, 0, strrpos($this->filename, '.')) . '.html';
    }

    /**
     * Read from cache file
     * @return false|string
     */
    public function read(): false|string
    {

        if (file_exists($this->htmlFile)) {
            $lifetime = (time() - filemtime($this->htmlFile)) / 60;
            if ($lifetime > $this->duration) {
                return false;
            }

            return file_get_contents($this->htmlFile);
        }

        return false;
    }

    /**
     * write in cache file
     *
     * @param $content
     */
    public function write($content): void
    {
        file_put_contents($this->htmlFile, $content);
    }

    /**
     * Starting write in cache file if APPOE isn't in maintenance mode
     * @return bool
     */
    public function start(): bool
    {
        $maintenance = getOption('PREFERENCE', 'maintenance');
        $cacheProcess = getOption('PREFERENCE', 'cacheProcess');
        if ('false' === $cacheProcess || 'true' === $maintenance) {
            return false;
        }

        if ($content = $this->read()) {
            echo $content;

            return true;
        }
        $this->buffer = true;
        ob_start();

        return false;
    }

    /**
     * end writing in cache file
     * @return bool
     */
    public function end(): bool
    {
        if (!$this->buffer) {
            return false;
        }
        $content = ob_get_clean();
        echo $content;
        $this->write($content);

        return true;
    }

    /**
     * @param array|string $buffer
     * @return array|null|string|string[]
     */
    public function minifyHtml(array|string $buffer): array|string|null
    {
        $search = array('/<!--(.*)-->/Uis', '/[[:blank:]]+/');
        $replace = array('', ' ');
        return preg_replace($search, $replace, $buffer);
    }


    /**
     * delete cache file
     */
    public function delete(): void
    {

        if (file_exists($this->htmlFile)) {
            unlink($this->htmlFile);
        }
    }
}