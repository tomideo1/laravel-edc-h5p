<?php
namespace App\EdcH5pClasses;
use Carbon\Carbon;
use Djoudi\LaravelH5p\Eloquents\H5pContent as H5pContent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ContentClass
{
    public $content;
    public function __construct($content_id)
    {
        $this->content = $content_id;
    }


    public function processCreatedContent() {
        $contentObj  = H5pContent::where('id',$this->content)->first();
        $params = $contentObj->parameters;
        return $this->handleFileManager($params);
    }

    private function handleFileManager($params)  {
        $path = storage_path('h5p/content/'.$this->content);
        $current_files =  $this->fetchAllStoredTempFiles();
        if($current_files === null){
            return;
        }
        if(!File::isDirectory($path)){
            File::makeDirectory($path,0777,true,true);
            $current_files =  $this->fetchAllStoredTempFiles();
            foreach ($current_files as $file){
                $current_file =  $this->fileTypeAnalyzer($file->path);
                if(empty($current_file)){
                    continue;
                }
                $newDir = $path.'/'.$current_file['type'];
                File::makeDirectory($newDir,0777,true,true);
                $search_path = storage_path('h5p/editor/'.$current_file['type'].'/'.$current_file['value']);
                if(!File::exists($search_path) ){
                    continue;
                }
                File::copy($search_path,$newDir .'/'.$current_file['value']);

            }
            File::put($path.'/content.json', $params);

        }else{
            foreach ($current_files as $file){
                $current_file =  $this->fileTypeAnalyzer($file->path);
                if(empty($current_file)){
                    continue;
                }
                $newDir = $path.'/'.$current_file['type']. '/';
                $search_path = storage_path('h5p/editor/'.$current_file['type'].'/'.$current_file['value']);
                if(!File::exists($search_path) ){
                    continue;
                }

                File::copy($search_path,$newDir .'/'.$current_file['value']);

            }
            File::put($path.'/content.json', $params);
        }
        $this->truncateTempFiles();
        return;


    }


    public  function processSavedContent()  {
        $contentObj  = H5pContent::where('id',$this->content)->first();
        $params = $contentObj->parameters;
        return $this->handleFileManager($params);
    }

    private function fetchAllStoredTempFiles(){
        return DB::table('h5p_tmpfiles')
            ->select('path')
            ->get();

    }

    private  function  truncateTempFiles() {
        return DB::table('h5p_tmpfiles')->truncate();
    }

    private  function fileTypeAnalyzer(string $path){
        $pathArray = explode('/',$path);
        $final_path = [];
        switch ($pathArray[6]){
            case 'images':
                $final_path = ["type" => "images","value" => $pathArray[7]];
                break;
            case 'audios':
                $final_path = ["type" => "audios","value" => $pathArray[7]];
                break;
            case  'videos':
                $final_path = ["type" => "videos","value" => $pathArray[7]];
                break;
            default:
                break;
        }
        return $final_path;
    }




}

