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
        $path = storage_path('/h5p/content/'.$this->content);
        if(!File::isDirectory($path)){
            File::makeDirectory($path,0777,true,true);
            $current_files =  $this->fetchAllStoredTempFiles();
            foreach ($current_files as $file){
                $current_file =  $this->fileTypeAnalyzer($file);
                dd($current_file);
            }


//            $contents = file_put_contents('content.json',$params);
//            Storage::put('./h5p/content/'.$this->content.'/', $params);
        }
        File::put($path.'/content.json', $params);
        $current_files =  $this->fetchAllStoredTempFiles();
        foreach ($current_files as $file){
            $current_file =  $this->fileTypeAnalyzer($file->path);
            dd($current_file);
        }
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
            ->where('created_at' ,'!=' , Carbon::now())
            ->get();

    }

    private  function fileTypeAnalyzer(string $path){
        return $path;
    }




}

