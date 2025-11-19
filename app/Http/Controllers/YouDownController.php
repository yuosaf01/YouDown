<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\YouTube\YouTubeDownloader;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class YouDownController extends Controller {

    public $hide_dash_videos = true;

    public function getURLs(Request $request) {

//        $url = 'https://youtu.be/BvtswduvEkA';

        $urls = $this->media_info($request['url']);

        return response()->json($urls[0]);

    }

    public function getVideo(Request $request) {

        $url = $request['url'];
        $filename = str_replace(' ','-',$request['name']).'.'.$request['type'];

        $tempPath = tempnam(sys_get_temp_dir(), $filename);

        $this->downloadFile($url,$tempPath);

        return response()->download($tempPath, $filename);

    }

    private function file_get_contents_curl( $url ) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
    private function downloadFile(  $url, $path )
    {
        $data = $this->file_get_contents_curl( $url );

        File::put($path,$data);

        return $path;
    }

    private function media_info($url){

        $yt = new YouTubeDownloader();
        $data = $yt->getDownloadLinks($url);

        $links = $data->getAllFormats();
        $video["title"] = $data->getInfo()->getTitle();
        $thumbnails = $data->getInfo()->getThumbnail()["thumbnails"];
        $video["thumbnail"] = $thumbnails[(count($thumbnails)-1)]["url"];

        $video["Video"] = [];
        $video["Audio"] = [];

//        $itags = [
//            18 => ['extension' => 'mp4','video' => ['width' => 640, 'height' => 360]],
//            22 => ['extension' => 'mp4','video' => ['width' => 1280, 'height' => 720]],
//            37 => ['extension' => 'mp4','video' => ['width' => 1920, 'height' => 1080]],
//            38 => ['extension' => 'mp4','video' => ['width' => 4096, 'height' => 3072]],
//            59 => ['extension' => 'mp4','video' => ['width' => 854, 'height' => 480]],
//            78 => ['extension' => 'mp4','video' => ['width' => 854, 'height' => 480]],
//            82 => ['extension' => 'mp4','video' => ['3d' => true, 'width' => 640, 'height' => 360]],
//            83 => ['extension' => 'mp4','video' => ['3d' => true, 'width' => 854, 'height' => 480]],
//            84 => ['extension' => 'mp4','video' => ['3d' => true, 'width' => 1280, 'height' => 720]],
//            85 => ['extension' => 'mp4','video' => ['3d' => true, 'width' => 1920, 'height' => 1080]],
//            133 => ['extension' => 'mp4','dash' => 'video','video' => ['width' => 426, 'height' => 240]],
//            134 => ['extension' => 'mp4','dash' => 'video','video' => ['width' => 640, 'height' => 360]],
//            135 => ['extension' => 'mp4','dash' => 'video','video' => ['width' => 854, 'height' => 480]],
//            136 => ['extension' => 'mp4','dash' => 'video','video' => ['width' => 1280, 'height' => 720]],
//            137 => ['extension' => 'mp4','dash' => 'video','video' => ['width' => 1920, 'height' => 1080]],
//            138 => ['extension' => 'mp4','dash' => 'video','video' => ['width' => 4096, 'height' => 2304]],
//            139 => ['extension' => 'm4a','dash' => 'audio','audio' => ['bitrate' => 48000, 'frequency' => 22050]],
//            140 => ['extension' => 'm4a','dash' => 'audio','audio' => ['bitrate' => 128000, 'frequency' => 44100]],
//            141 => ['extension' => 'm4a','dash' => 'audio','audio' => ['bitrate' => 256000, 'frequency' => 44100]],
//            160 => ['extension' => 'mp4','dash' => 'video','video' => ['width' => 256, 'height' => 144]],
//            264 => ['extension' => 'mp4', 'dash' => 'video', 'video' => ['width' => 2560, 'height' => 1440]],
//            266 => ['extension' => 'mp4', 'dash' => 'video', 'video' => ['width' => 3840, 'height' => 2160]],
//            298 => ['extension' => 'mp4', 'dash' => 'video', 'video' => ['width' => 1280, 'height' => 720]],
//            400 => ['extension' => 'mp4', 'dash' => 'video', 'video' => ['width' => 2560, 'height' => 1440]],
//            401 => ['extension' => 'mp4', 'dash' => 'video', 'video' => ['width' => 3840, 'height' => 2160]],
//        ];

        $itags = array(
//            5 => 'flv, video, 240p, audio',
//            6 => 'flv, video, 270p, audio',
//            13 => '3gp, video, audio',
//            17 => '3gp, video, 144p, audio',
            18 => 'mp4, video, 360p, audio',
            22 => 'mp4, video, 720p, audio',
//            34 => 'flv, video, 360p, audio',
//            35 => 'flv, video, 480p, audio',
//            36 => '3gp, video, audio',
            37 => 'mp4, video, 1080p, audio',
            38 => 'mp4, video, 3072p, audio',
            43 => 'webm, video, 360p, audio',
            44 => 'webm, video, 480p, audio',
            45 => 'webm, video, 720p, audio',
            46 => 'webm, video, 1080p, audio',
            59 => 'mp4, video, 480p, audio',
            78 => 'mp4, video, 480p, audio',
            82 => 'mp4, video, 360p, audio',
            83 => 'mp4, video, 480p, audio',
            84 => 'mp4, video, 720p, audio',
            85 => 'mp4, video, 1080p, audio',
            100 => 'webm, video, 360p, audio',
            101 => 'webm, video, 480p, audio',
            102 => 'webm, video, 720p, audio',
            91 => 'mp4, video, 144p, audio',
            92 => 'mp4, video, 240p, audio',
            93 => 'mp4, video, 360p, audio',
            94 => 'mp4, video, 480p, audio',
            95 => 'mp4, video, 720p, audio',
            96 => 'mp4, video, 1080p, audio',
            132 => 'mp4, video, 240p, audio',
            151 => 'mp4, video, 72p, audio',
//            133 => 'mp4, video, 240p',
//            134 => 'mp4, video, 360p',
//            135 => 'mp4, video, 480p',
//            136 => 'mp4, video, 720p',
//            137 => 'mp4, video, 1080p',
//            138 => 'mp4, video',
//            160 => 'mp4, video, 144p',
//            212 => 'mp4, video, 480p',
//            264 => 'mp4, video, 1440p',
//            298 => 'mp4, video, 720p',
//            299 => 'mp4, video, 1080p',
//            266 => 'mp4, video, 2160p',
//            139 => 'm4a, audio',
//            140 => 'm4a, audio',
//            141 => 'm4a, audio',
//            256 => 'm4a, audio',
//            258 => 'm4a, audio',
//            325 => 'm4a, audio',
//            328 => 'm4a, audio',
//            167 => 'webm, video, 360p',
//            168 => 'webm, video, 480p',
//            169 => 'webm, video, 720p',
//            170 => 'webm, video, 1080p',
//            218 => 'webm, video, 480p',
//            219 => 'webm, video, 480p',
//            278 => 'webm, video, 144p',
//            242 => 'webm, video, 240p',
//            243 => 'webm, video, 360p',
//            244 => 'webm, video, 480p',
//            245 => 'webm, video, 480p',
//            246 => 'webm, video, 480p',
//            247 => 'webm, video, 720p',
//            248 => 'webm, video, 1080p',
//            271 => 'webm, video, 1440p',
//            272 => 'webm, video, 2160p',
//            302 => 'webm, video, 720p',
//            303 => 'webm, video, 1080p',
//            308 => 'webm, video, 1440p',
//            313 => 'webm, video, 2160p',
//            315 => 'webm, video, 2160p',
//            171 => 'webm, audio',
//            172 => 'webm, audio',
//            249 => 'webm, audio',
//            250 => 'webm, audio',
//            251 => 'webm, audio',
//            394 => 'video',
//            395 => 'video',
//            396 => 'video',
//            397 => 'video',
        );

        foreach ($links as $link) {

            if (isset($itags[$link->itag])) {

                $itag = $itags[$link->itag];

                $pieces = explode(" ", $itag);
                $file_size = $this->getFileSize($link->url);

                if ($file_size !== false) {
                    $video["Video"][] = [
                        "url" => $link->url,
                        "type" => str_replace(',','',$pieces[0]),
                        "itag" => $link->itag,
                        "quality" => str_replace(',','',$pieces[2]),
                        "size" => $file_size
                    ];
                }

//                $is_audio = (isset($itag["video"]) != "") ? false : true;
//                $is_dash = isset($itag["dash"]) != "";
//                $type = ($is_audio && $itag["extension"] == "webm") ? "mp3" : $itag["extension"];
//                $file_size = $this->getFileSize($link->url);
//                $quality = (!$is_audio) ? $itag["video"]["height"] . "p" : $this->format_bitrate($itag["audio"]["bitrate"]);

//                if (!$is_dash && !$is_audio) {
//
//                    array_push($video["Video"],[
//                        "url" => $link->url,
//                        "type" => $type,
//                        "itag" => $link->itag,
//                        "quality" => $quality,
//                        "mute" => $is_dash,
//                        "size" => $file_size
//                    ]);

//                    if ($is_dash && $this->hide_dash_videos) {
//                        $is_hidden = true;
//                    } else {
//
//                        $typeInArray = $type === "m4a" ? "Audio" :  "Video";
//
//                    }

//                }
            }
        }
        return [$video,$data];
    }

    private function getFileSize($url) {

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);

        $data = curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($ch);

        if ($size <= 0) {
            return false;
        }

        switch ($size) {
            case $size < 1024:
                return $size . ' B';
                break;
            case $size < 1048576:
                return round($size / 1024, 2) . ' KB';
                break;
            case $size < 1073741824:
                return round($size / 1048576, 2) . ' MB';
                break;
            case $size < 1099511627776:
                return round($size / 1073741824, 2) . ' GB';
                break;
        }

        return $size . ' B';
    }
    private function format_bitrate($bitrate) {

        if ($bitrate >= 1073741824) {
            $bitrate = number_format($bitrate / 1073741824, 2) . ' GB';
        } elseif ($bitrate >= 1048576) {
            $bitrate = number_format($bitrate / 1048576, 2) . ' MB';
        } elseif ($bitrate >= 1024) {
            $bitrate = number_format($bitrate / 1024, 2) . ' KB';
        } elseif ($bitrate > 1) {
            $bitrate = $bitrate . ' bytes';
        } elseif ($bitrate === 1) {
            $bitrate = $bitrate . ' byte';
        } else {
            $bitrate = '0 bytes';
        }
        $kb = (int)$bitrate;
        return $kb . ' kbps';
    }

}
