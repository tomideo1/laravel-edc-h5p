<?php


namespace App\EdcH5pClasses;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LmsGamificationClass
{

    public function  create($content_id){
        DB::table('gamifications')->insert([
            'id' =>  Str::uuid()->toString(),
            'tenant_id' => Auth::user()->id,
            'content_id' =>  $content_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    public  function delete($content_id){
        DB::table('gamifications')
            ->where('content_id',$content_id)
            ->delete();
    }

}
