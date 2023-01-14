<?php

namespace Enzim\Lib\TikaWrapper;

use Symfony\Component\Process\Process;
use SplFileInfo;

class TikaWrapper {

    /**
     * @param string $option
     * @param string $fileName
     * @return string
     * @throws \RuntimeException
     */
    protected static function run($option, $fileName){
        $file = new SplFileInfo($fileName);
        $tikaPath = __DIR__ . "/../vendor/";
        $shellCommand = 'java -jar tika-app-1.14.jar ' . $option . ' "' . $file->getRealPath() . '"';

        $process = Process::fromShellCommandline($shellCommand);
        $process->setWorkingDirectory($tikaPath);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }

    /**
     * @param string $fileName
     * @return int
     */
    public static function getWordCount($fileName){
        $text = self::getText($fileName);

        return count(
            preg_split('#[\s,]+#', trim($text))
        );
    }
    
    /**
     * @param string $fileName
     * @return int|null
     */
    public static function getPageCount($fileName)
    {
        $metaData = self::getMetaData($fileName);

        $metaData = explode("\n", $metaData);

        $arr = [];
        foreach ($metaData as $data) {
            $el = explode(':', $data);

            if ($el[0]) {
                $arr[trim($el[0])] = trim($el[1] ?? '');
            }
        }

        return $arr['Page-Count']
            ? (int) $arr['Page-Count']
            : null;
    }

    /**
     * Options
     */

    /**
     * @param $filename
     * @return string
     */
    public static function getXHTML($filename){
        return self::run("--xml", $filename);
    }

    /**
     * @param $filename
     * @return string
     */
    public static function getHTML($filename){
        return self::run("--html", $filename);
    }

    /**
     * @param string $filename
     * @return string
     */
    public static function getText($filename) {
        return self::run("--text", $filename);
    }

    /**
     * @param $filename
     * @return string
     */
    public static function getTextMain($filename){
        return self::run("--text-main", $filename);
    }

    /**
     * @param $filename
     * @return string
     */
    public static function getMetadata($filename){
        return self::run("--metadata", $filename);
    }

    /**
     * @param $filename
     * @return string
     */
    public static function getJson($filename){
        return self::run("--json", $filename);
    }

    /**
     * @param $filename
     * @return string
     */
    public static function getXmp($filename){
        return self::run("--xmp", $filename);
    }

    /**
     * @param $filename
     * @return string
     */
    public static function getLanguage($filename){
        return self::run("--language", $filename);
    }

    /**
     * @param $filename
     * @return string
     */
    public static function getDocumentType($filename){
        return self::run("--detect", $filename);
    }

}
