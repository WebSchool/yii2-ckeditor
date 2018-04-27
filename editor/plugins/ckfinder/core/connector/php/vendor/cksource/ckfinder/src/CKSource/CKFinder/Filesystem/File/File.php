<?php

/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2016, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

namespace CKSource\CKFinder\Filesystem\File;

use CKSource\CKFinder\Backend\Backend;
use CKSource\CKFinder\Cache\CacheManager;
use CKSource\CKFinder\CKFinder;
use CKSource\CKFinder\Config;
use CKSource\CKFinder\Filesystem\Path;

/**
 * The File class.
 *
 * Base class for processed files.
 */
abstract class File
{
    /**
     * File name.
     *
     * @var string $fileName
     */
    protected $fileName;

    /**
     * CKFinder configuration.
     *
     * @var Config $config
     */
    protected $config;

    /**
     * @var CKFinder $app
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param string   $fileName
     * @param CKFinder $app
     */
    public function __construct($fileName, CKFinder $app)
    {
        $this->fileName = $fileName;
        $this->config = $app['config'];
        $this->app = $app;
    }

    /**
     * Validates current file name.
     *
     * @return bool `true` if the file name is valid.
     */
    public function hasValidFilename()
    {
        return static::isValidName($this->fileName, $this->config->get('disallowUnsafeCharacters'));
    }

    /**
     * Returns current file name.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->fileName;
    }

    /**
     * Returns current file extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return strtolower(pathinfo($this->fileName, PATHINFO_EXTENSION));
    }

    /**
     * Returns a list of current file extensions.
     *
     * For example for a file named `file.foo.bar.baz` it will return an array containing
     * `['foo', 'bar', 'baz']`.
     *
     * @param null $newFileName the file name to check if it is different than the current file name (for example for validation of
     *                          a new file name in edited files).
     *
     * @return array
     */
    public function getExtensions($newFileName = null)
    {
        $fileName = $newFileName ?: $this->fileName;

        if (strpos($fileName, '.') === false) {
            return true;
        }

        $pieces = explode('.', $fileName);

        array_shift($pieces); // Remove file base name

        return array_map('strtolower', $pieces);
    }

    /**
     * Renames the current file by adding a number to the file name.
     *
     * Renaming is done by adding a number in parenthesis provided that the file name does
     * not collide with any other file existing in the target backend/path.
     * For example, if the target backend path contains a file named `foo.txt`
     * and the current file name is `foo.txt`, this method will change the current file
     * name to `foo(1).txt`.
     *
     * @param Backend $backend target backend
     * @param string  $path    target backend-relative path
     *
     * @return bool `true` if file was renamed.
     */
    public function autorename(Backend $backend = null, $path = '')
    {
        $filePath = Path::combine($path, $this->fileName);

        if (!$backend->has($filePath)) {
            return false;
        }

        $pieces = explode('.', $this->fileName);
        $basename = array_shift($pieces);
        $extension = implode('.', $pieces);

        $i = 0;
        while (true) {
            $i++;
            $this->fileName = "{$basename}-{$i}-.{$extension}";

            $filePath = Path::combine($path, $this->fileName);

            if (!$backend->has($filePath)) {
                break;
            }
        }

        return true;
    }

    /**
     * Check whether `$fileName` is a valid file name. Returns `true` on success.
     *
     * @param string $fileName
     * @param bool   $disallowUnsafeCharacters
     *
     * @return boolean `true` if `$fileName` is a valid file name.
     */
    public static function isValidName($fileName, $disallowUnsafeCharacters = true)
    {
        if (null === $fileName || !strlen(trim($fileName)) || substr($fileName, -1, 1) == "." || false !== strpos($fileName, "..")) {
            return false;
        }

        if (preg_match(',[[:cntrl:]]|[/\\\\:\*\?\"\<\>\|],', $fileName)) {
            return false;
        }

        if ($disallowUnsafeCharacters) {
            if (strpos($fileName, ";") !== false) {
                return false;
            }
        }

        if( File::secureName( $fileName, $disallowUnsafeCharacters ) != $fileName ){
            return false;
        }

        return true;
    }

    /**
     * Checks if the current file has an image extension.
     *
     * @return bool `true` if the file name has an image extension.
     */
    public function isImage()
    {
        $imagesExtensions = array('gif', 'jpeg', 'jpg', 'png', 'psd', 'bmp', 'tiff', 'tif',
            'swc', 'iff', 'jpc', 'jp2', 'jpx', 'jb2', 'xbm', 'wbmp');

        return in_array($this->getExtension(), $imagesExtensions);
    }

    /**
     * Secures the file name from unsafe characters.
     *
     * @param string $fileName
     * @param bool   $disallowUnsafeCharacters
     *
     * @return string
     */
    public static function secureName($fileName, $disallowUnsafeCharacters = true, $forceAscii = true)
    {
        $fileName = str_replace(array(":", "*", "?", "|", "/"), "_", $fileName);

        if ($disallowUnsafeCharacters) {
            $fileName = str_replace(";", "_", $fileName);
        }

        return self::convertToAscii( $fileName );
    }

    /**
     * для переименовывания файлов и каталогов
     */
    public static function convertToAscii( $str )
    {
        $UTF8_LOWER_ACCENTS = array(
            'à' => 'a', 'ô' => 'o', 'ď' => 'd', 'ḟ' => 'f', 'ë' => 'e', 'š' => 's', 'ơ' => 'o',
            'ß' => 'ss', 'ă' => 'a', 'ř' => 'r', 'ț' => 't', 'ň' => 'n', 'ā' => 'a', 'ķ' => 'k',
            'ŝ' => 's', 'ỳ' => 'y', 'ņ' => 'n', 'ĺ' => 'l', 'ħ' => 'h', 'ṗ' => 'p', 'ó' => 'o',
            'ú' => 'u', 'ě' => 'e', 'é' => 'e', 'ç' => 'c', 'ẁ' => 'w', 'ċ' => 'c', 'õ' => 'o',
            'ṡ' => 's', 'ø' => 'o', 'ģ' => 'g', 'ŧ' => 't', 'ș' => 's', 'ė' => 'e', 'ĉ' => 'c',
            'ś' => 's', 'î' => 'i', 'ű' => 'u', 'ć' => 'c', 'ę' => 'e', 'ŵ' => 'w', 'ṫ' => 't',
            'ū' => 'u', 'č' => 'c', 'ö' => 'oe', 'è' => 'e', 'ŷ' => 'y', 'ą' => 'a', 'ł' => 'l',
            'ų' => 'u', 'ů' => 'u', 'ş' => 's', 'ğ' => 'g', 'ļ' => 'l', 'ƒ' => 'f', 'ž' => 'z',
            'ẃ' => 'w', 'ḃ' => 'b', 'å' => 'a', 'ì' => 'i', 'ï' => 'i', 'ḋ' => 'd', 'ť' => 't',
            'ŗ' => 'r', 'ä' => 'ae', 'í' => 'i', 'ŕ' => 'r', 'ê' => 'e', 'ü' => 'ue', 'ò' => 'o',
            'ē' => 'e', 'ñ' => 'n', 'ń' => 'n', 'ĥ' => 'h', 'ĝ' => 'g', 'đ' => 'd', 'ĵ' => 'j',
            'ÿ' => 'y', 'ũ' => 'u', 'ŭ' => 'u', 'ư' => 'u', 'ţ' => 't', 'ý' => 'y', 'ő' => 'o',
            'â' => 'a', 'ľ' => 'l', 'ẅ' => 'w', 'ż' => 'z', 'ī' => 'i', 'ã' => 'a', 'ġ' => 'g',
            'ṁ' => 'm', 'ō' => 'o', 'ĩ' => 'i', 'ù' => 'u', 'į' => 'i', 'ź' => 'z', 'á' => 'a',
            'û' => 'u', 'þ' => 'th', 'ð' => 'dh', 'æ' => 'ae', 'µ' => 'u', 'ĕ' => 'e',
        );


        $str = str_replace( array_keys( $UTF8_LOWER_ACCENTS ), array_values( $UTF8_LOWER_ACCENTS ), $str );


        $UTF8_UPPER_ACCENTS = array(
            'À' => 'A', 'Ô' => 'O', 'Ď' => 'D', 'Ḟ' => 'F', 'Ë' => 'E', 'Š' => 'S', 'Ơ' => 'O',
            'Ă' => 'A', 'Ř' => 'R', 'Ț' => 'T', 'Ň' => 'N', 'Ā' => 'A', 'Ķ' => 'K',
            'Ŝ' => 'S', 'Ỳ' => 'Y', 'Ņ' => 'N', 'Ĺ' => 'L', 'Ħ' => 'H', 'Ṗ' => 'P', 'Ó' => 'O',
            'Ú' => 'U', 'Ě' => 'E', 'É' => 'E', 'Ç' => 'C', 'Ẁ' => 'W', 'Ċ' => 'C', 'Õ' => 'O',
            'Ṡ' => 'S', 'Ø' => 'O', 'Ģ' => 'G', 'Ŧ' => 'T', 'Ș' => 'S', 'Ė' => 'E', 'Ĉ' => 'C',
            'Ś' => 'S', 'Î' => 'I', 'Ű' => 'U', 'Ć' => 'C', 'Ę' => 'E', 'Ŵ' => 'W', 'Ṫ' => 'T',
            'Ū' => 'U', 'Č' => 'C', 'Ö' => 'Oe', 'È' => 'E', 'Ŷ' => 'Y', 'Ą' => 'A', 'Ł' => 'L',
            'Ų' => 'U', 'Ů' => 'U', 'Ş' => 'S', 'Ğ' => 'G', 'Ļ' => 'L', 'Ƒ' => 'F', 'Ž' => 'Z',
            'Ẃ' => 'W', 'Ḃ' => 'B', 'Å' => 'A', 'Ì' => 'I', 'Ï' => 'I', 'Ḋ' => 'D', 'Ť' => 'T',
            'Ŗ' => 'R', 'Ä' => 'Ae', 'Í' => 'I', 'Ŕ' => 'R', 'Ê' => 'E', 'Ü' => 'Ue', 'Ò' => 'O',
            'Ē' => 'E', 'Ñ' => 'N', 'Ń' => 'N', 'Ĥ' => 'H', 'Ĝ' => 'G', 'Đ' => 'D', 'Ĵ' => 'J',
            'Ÿ' => 'Y', 'Ũ' => 'U', 'Ŭ' => 'U', 'Ư' => 'U', 'Ţ' => 'T', 'Ý' => 'Y', 'Ő' => 'O',
            'Â' => 'A', 'Ľ' => 'L', 'Ẅ' => 'W', 'Ż' => 'Z', 'Ī' => 'I', 'Ã' => 'A', 'Ġ' => 'G',
            'Ṁ' => 'M', 'Ō' => 'O', 'Ĩ' => 'I', 'Ù' => 'U', 'Į' => 'I', 'Ź' => 'Z', 'Á' => 'A',
            'Û' => 'U', 'Þ' => 'Th', 'Ð' => 'Dh', 'Æ' => 'Ae', 'Ĕ' => 'E',
        );


        $str = str_replace( array_keys( $UTF8_UPPER_ACCENTS ), array_values( $UTF8_UPPER_ACCENTS ), $str );

        $arrSml = array(
            "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
            "Д"=>"D","Е"=>"E","Ё" => "E","Ж"=>"J","З"=>"Z","И"=>"I",
            "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
            "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
            "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
            "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
            "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
            "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё" => "e","ж"=>"j",
            "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
            "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
            "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
            "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
            "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
            "Ә" => "A", "Ғ" => "G", "Қ" => "K", "Ң" => "N", "Ө" => "O", "Ұ" => "Y", "Ү" => "Y", "Һ" => "Х", "І" => "I",
            "ә" => "a", "ғ" => "g", "қ" => "k", "ң" => "n", "ө" => "o", "ұ" => "y", "ү" => "y", "һ" => "x", "і" => "i",
            // \ / : * ? ! " < > | ,() . + % @ ; ‑
            " "=>"-",
            "\\"=>"-",
            "/"=>"-",
            ":"=>"-",
            "*"=>"-",
            "?"=>"-",
            "!"=>"-",
            "\""=>"-",
            "<"=>"-",
            ">"=>"-",
            "|"=>"-",
            ","=>"-",
            "("=>"-",
            ")"=>"-",
            "+"=>"-",
            "%"=>"-",
            "@"=>"-",
            ";"=>"-",
            "‑"=>"-",
            "_"=>"-",
        );

        $str = str_replace( array_keys( $arrSml ), array_values( $arrSml ), $str );

        $str = preg_replace("/[^A-Za-z0-9-_.]+/ui", "", $str);

        $str = substr( $str, 0, 240 );

        // если есть лишние точкм
        $name = pathinfo($str, PATHINFO_FILENAME);
        $ext = pathinfo($str, PATHINFO_EXTENSION);
        $name = str_replace( '.', '-', $name );

        return $name . (!empty($ext) ? '.' . $ext : '');
    }

    /**
     * @return CacheManager
     */
    public function getCache()
    {
        return $this->app['cache'];
    }
}
