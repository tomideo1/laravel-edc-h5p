<?php
namespace App\EdcH5pClasses;
use Djoudi\LaravelH5p\Eloquents\H5pContent as H5pContent;

class ContentClass
{
    public $content;
    public function __construct($content_id)
    {
        $this->content = $content_id;
    }


    public function processContent(){
        $contentObj  = H5pContent::where('id',$this->content)->first();
        $params = $contentObj->parameters;
        return $this->handleFileManager($params);
    }

    private function handleFileManager($params){
        dd(collect(json_decode($params,true))->pluck(''));

//        dd(preg_grep("/image-/i",));
//        $split_string = preg_split("/images/",$params);
//        if(!empty($split_string)){
//            foreach ($split_string as $str){
//                dd($str);
//            }
//        }
//        switch (true){
//            case 'image':
//                dd(preg_split("/images/",$params));
//            break;
//            case 'audio':
//                dd(preg_split("/audio/",$params));
//                break;
//            default:
//            break;
//        }

    }





}

