<?php

namespace App\Http\Controllers\YouTube\Utils;

use App\Http\Controllers\YouTube\Models\StreamFormat;
use App\Http\Controllers\YouTube\DownloadOptions;

class SerializationUtils
{
    public static function optionsToArray(DownloadOptions $downloadOptions)
    {
        return array_map(function (StreamFormat $link) {
            return $link->toArray();
        }, $downloadOptions->getAllFormats());
    }

    public static function optionsFromArray($array)
    {
        $links = array();

        foreach ($array as $item) {
            $links[] = new StreamFormat($item);
        }

        return new DownloadOptions($links);
    }

    public static function optionsFromFile($path)
    {
        $contents = @file_get_contents($path);

        if ($contents) {
            $json = json_decode($contents, true);

            if ($json) {
                return self::optionsFromArray($json);
            }
        }

        return null;
    }
}
