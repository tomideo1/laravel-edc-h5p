<?php

namespace Djoudi\LaravelH5p\Http\Controllers;

use App\Http\Controllers\Controller;
use Djoudi\LaravelH5p\Events\H5pEvent;
use Djoudi\LaravelH5p\LaravelH5p;
use H5PEditorEndpoints;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AjaxController extends Controller
{
    public function libraries(Request $request)
    {
        $machineName = $request->get('machineName');
        $major_version = $request->get('majorVersion');
        $minor_version = $request->get('minorVersion');

        $h5p = App::make('LaravelH5p');
        $core = $h5p::$core;
        $editor = $h5p::$h5peditor;

        if ($machineName) {
            $editor->ajax->action(H5PEditorEndpoints::SINGLE_LIBRARY, $machineName, $major_version, $minor_version, $h5p->get_language(), '', $h5p->get_h5plibrary_url('', true), '');
            // Log library load
            event(new H5pEvent('library', null, null, null, $machineName, $major_version.'.'.$minor_version));
        } else {
            // Otherwise retrieve all libraries
            $editor->ajax->action(H5PEditorEndpoints::LIBRARIES);
        }
    }

    public function singleLibrary(Request $request)
    {
        $h5p = App::make('LaravelH5p');
        $editor = $h5p::$h5peditor;
        $editor->ajax->action(H5PEditorEndpoints::SINGLE_LIBRARY, $request->get('_token'));
    }

    public function contentTypeCache(Request $request)
    {
        $h5p = App::make('LaravelH5p');
        $editor = $h5p::$h5peditor;
        $editor->ajax->action(H5PEditorEndpoints::CONTENT_TYPE_CACHE, $request->get('_token'));
    }

    public function libraryInstall(Request $request)
    {
        $h5p = App::make('LaravelH5p');
        $editor = $h5p::$h5peditor;
        $editor->ajax->action(H5PEditorEndpoints::LIBRARY_INSTALL, $request->get('_token'), $request->get('machineName'));
    }

    public function libraryUpload(Request $request)
    {
        $filePath = $request->file('h5p')->getPathName();
        $h5p = App::make('LaravelH5p');
        $editor = $h5p::$h5peditor;
        $editor->ajax->action(H5PEditorEndpoints::LIBRARY_UPLOAD, $request->get('_token'), $filePath, $request->get('contentId'));
    }

    public function files(Request $request)
    {
        $filePath = $request->file('file');
        $h5p = App::make('LaravelH5p');
        $editor = $h5p::$h5peditor;
        $editor->ajax->action(H5PEditorEndpoints::FILES, $request->get('_token'), $request->get('contentId'));
    }

    public function __invoke(Request $request)
    {
        return response()->json($request->all());
    }

    public function finish(Request $request)
    {
        $user_id = \Auth::id();
        //dd($request);

        

        if ((int)$user_id > 0) {

            $referer = app('Illuminate\Routing\UrlGenerator')->previous();

            //test si appel depuis mobile
            //'${Connexion.instance.urlBase}${Connexion.instance.urlH5P}/${act.h5pId}?l=${act.afll.learningpathId}&a=${act.id}&afll=${act.afll.id}&f=${act.afll.formationId}';          
            //$re = '/^((http|https):\/\/)([^:\/\s]+)(\/api\/h5p\/(?P<h5p_id>[\d]+)\?l=)(?P<learningpath_id>[\d]+)(\&a=)(?P<activity_id>[\d]+)(\&afll=)(?P<afll_id>[\d]+)(\&f=)(?P<formation_id>[\d]+)([\D]*)$/m';
            $re = '/^((http|https):\/\/)([^:\/\s]+)(\/api\/h5p\/(?P<h5p_id>[\d]+)\?)([\S]*)$/m';
            $find = preg_match_all($re, $referer, $matches, PREG_SET_ORDER, 0);

            if ($find !== false && $find > 0) {//cas de l'app mobile qui est appelant
               
                $formation_id = 0;
                $learningpath_id = 0;
                $activity_id = 0;

                $parts = explode('?', $referer);

                if(isset($parts[1])){

                    $paires = explode('&', $parts[1]);

                    foreach($paires as $paire){

                        list($prop, $value) = explode('=', $paire);

                        switch($prop){
                            case 'formation_id':
                            case 'f':
                                $formation_id = $value;
                                break;
                            case 'learningpath_id':
                            case 'l':
                                $learningpath_id = $value;
                                break;
                            case 'activity_id':
                            case 'a':
                                $activity_id = $value;
                                break;
                        }
                    }
                }

                $client = 'mobile';
            } else {//cas du client web browser                     
                //https://lmscc.test/data/formations/1/learningpaths/1/activityshow/1
               
                $re = '/^((http|https):\/\/)([^:\/\s]+)(\/data\/formations\/)(?P<formation_id>[\d]+)(\/learningpaths\/)(?P<learningpath_id>[\d]+)(\/activityshow\/)(?P<activity_id>[\d]+)([\D]*)$/m';

                $find = preg_match_all($re, $referer, $matches, PREG_SET_ORDER, 0);

                $formation_id = isset($matches[0]['formation_id']) ? $matches[0]['formation_id'] : 0;
                $learningpath_id = isset($matches[0]['learningpath_id']) ? $matches[0]['learningpath_id'] : 0;
                $activity_id = isset($matches[0]['activity_id']) ? $matches[0]['activity_id'] : 0;

                $client = 'browser';
            }

            if(config('app.env') !== 'production'){
                //Log::debug($client.'_'.$user_id.'_'.$formation_id.'_'.$learningpath_id.'_'.$activity_id.'_');
            } 

            $h5p_url = $request->input('object.id');
            $url_parts = explode('/', $h5p_url);//"http://lmscc.test/api/h5p/embed/13?subContentId=564bbacf-c83a-4511-9831-d8a4af1305eb"
            $h5p_ids = array_pop($url_parts);//13?subContentId=564bbacf-c83a-4511-9831-d8a4af1305eb
            $h5p_ids_parts = explode('?subContentId=', $h5p_ids);

            $h5p_id = $h5p_ids_parts[0];
            $h5p_id_subc = isset($h5p_ids_parts[1]) ? $h5p_ids_parts[1] : null;

            $previous_result = \Djoudi\LaravelH5p\Eloquents\H5pResult::where('content_id', $h5p_id)->where('subcontent_id', $h5p_id_subc)->where('user_id', $user_id)->first();

            $finished = false;
            if (/*$request->input('verb.id') == "http://adlnet.gov/expapi/verbs/answered" ||*/ $request->input('verb.id') == "http://adlnet.gov/expapi/verbs/completed") {
                $finished = true;
              
            }

            if ($request->input('result.completion') == 1){
                $finished = true;
            }

            $result = [
                'content_id' => $h5p_id,
                'subcontent_id' => $h5p_id_subc,
                'user_id' => $user_id,
                'score' => $request->has('result.score.raw') ? $request->input('result.score.raw') : 0,
                'max_score' => $request->has('result.score.max') ? $request->input('result.score.max') : 0,
                'opened' => $previous_result ? $previous_result->opened : now(),
                'finished' => $finished ? now() : null,
                'time' => round(str_replace(['PT', 'S'], '', $request->input('result.duration'))),
                'description' => $request->has('object.definition.description') ? json_encode($request->input('object.definition.description')) : ($request->has('object.definition.name') ? json_encode($request->input('object.definition.name') ): null),
                'correct_responses_pattern' => $request->has('object.definition.correctResponsesPattern') ? json_encode($request->input('object.definition.correctResponsesPattern')) : null,
                'response' => $request->has('result.response') ? json_encode($request->input('result.response')) : null,
                'additionals' => $request->has('object.definition.choices') ? json_encode($request->input('object.definition.choices')) : null,
                'formation_id' => $formation_id,
                'learningpath_id' => $learningpath_id,
                'activity_id' => $activity_id,
                'completion' => $finished ? 100 : 0
            ];
			
			if((int)$result['time'] == 0){//ne pas reset le temps deja pris en compte
				unset($result['time']);
			}
				

            if ($previous_result) {//maj
                $previous_result->update($result);

                
            } else {
                $previous_result = \Djoudi\LaravelH5p\Eloquents\H5pResult::create($result);
               
                
            }

            $remonteeParent = false;
            
            if ($request->input('context.contextActivities.parent') && isset($request->input('context.contextActivities.parent')[0]['id']) && !\Illuminate\Support\Str::contains($request->input('context.contextActivities.parent')[0]['id'], 'subContentId')){
                $remonteeParent = true;

                if($request->input('verb.id') == "http://adlnet.gov/expapi/verbs/attempted"){
                    //on parcourt le content.json, pour trouver les childs et peupler les results vierges
                    $exists = \Illuminate\Support\Facades\Storage::disk('h5p')->exists('content/'.$h5p_id.'/content.json');
                    if($exists){
                        $contents = \Illuminate\Support\Facades\Storage::disk('h5p')->get('content/'.$h5p_id.'/content.json');
                        $json = json_decode($contents);
                        if(isset($json->interactiveVideo)){
                            foreach($json->interactiveVideo->assets->interactions as $interaction){
                                if($interaction->libraryTitle == 'Multiple Choice'){
                                    $interaction->action->subContentId;
    
                                    $child = \Djoudi\LaravelH5p\Eloquents\H5pResult::firstOrCreate(['content_id' => $h5p_id, 'subcontent_id' => $interaction->action->subContentId, 'user_id' => $user_id], ['opened'=>now(), 'score'=>0, 'max_score'=>1]);
                                }
                            }
                        }
                    }
                   

                }
                
            } else {
                $remonteeParent = true;
                if($request->input('verb.id') == "http://adlnet.gov/expapi/verbs/answered"){
                    //on parcourt le content.json, pour trouver les childs et peupler les results vierges
                    $exists = \Illuminate\Support\Facades\Storage::disk('h5p')->exists('content/'.$h5p_id.'/content.json');
                    if($exists){
                        $contents = \Illuminate\Support\Facades\Storage::disk('h5p')->get('content/'.$h5p_id.'/content.json');
                        $json = json_decode($contents);

                        if(isset($json->presentation->slides)){
                            foreach($json->presentation->slides as $slide){
                                foreach($slide->elements as $element){
                                    if( Str::startsWith($element->action->library, 'H5P.SingleChoiceSet')){
                                        foreach($element->action->params->choices as $choice){
                                            $choice->subContentId;
                                            $child = \Djoudi\LaravelH5p\Eloquents\H5pResult::firstOrCreate(['content_id' => $h5p_id, 'subcontent_id' => $choice->subContentId, 'user_id' => $user_id], ['opened'=>now(), 'score'=>0, 'max_score'=>1]);
                        
                                        }
                                    }

                                }
                            }
                        }

                        if(isset($json->content)){
                            foreach($json->content as $content){
                                if (Str::startsWith($content->content->library, 'H5P.SingleChoiceSet')){
                                    foreach($content->content->params->choices as $choice){
                                        $choice->subContentId;
                                        $child = \Djoudi\LaravelH5p\Eloquents\H5pResult::firstOrCreate(['content_id' => $h5p_id, 'subcontent_id' => $choice->subContentId, 'user_id' => $user_id], ['opened'=>now(), 'score'=>0, 'max_score'=>1]);
                    
                                    }
                                }
                            }
                        }
                    }
                }
            }



            //remontée sur le parent
            if ($h5p_id_subc && $remonteeParent) {
                $parent = \Djoudi\LaravelH5p\Eloquents\H5pResult::firstOrCreate(['content_id' => $h5p_id, 'subcontent_id' => null, 'user_id' => $user_id], ['opened'=>now(), 'score'=>0, 'max_score'=>0]);
                if ($parent) {
                    $contents = \Djoudi\LaravelH5p\Eloquents\H5pResult::where('content_id', $h5p_id)->whereNotNull('subcontent_id')->where('user_id', $user_id)->get();

                    $data = [];//donnée pour maj parent
                    $data['content_id'] = $h5p_id;
                    $data['formation_id'] = $formation_id;
                    $data['learningpath_id'] = $learningpath_id;
                    $data['activity_id'] = $activity_id;
                    $data['score'] = 0;
                    $data['max_score'] = 0;
                    $data['finished'] = null;
                    $data['time'] = 0;
                    $nbSubContent = 0;
                    $nbSubContentComplete = 0;
                    foreach ($contents as $content) {
                        $nbSubContent++;
                        $data['score'] += $content->score;
                        $data['max_score'] += $content->max_score;
                        if ($content->finished) {
                            $nbSubContentComplete++;
                        }
                        
                        if ($content->finished && $data['finished'] != -1) {
                            if ($data['finished'] == null || $data['finished'] < $content->finished) {
                                $data['finished'] = $content->finished;
                            }   
                        } else {
                            $data['finished'] = -1;
                        }

                        $data['time'] = max($data['time'], $content->time);
                    }

                    if ($data['finished'] == -1) {
                        $data['finished'] = null;
                    }

                    if($nbSubContent > 0){
                        $data['completion'] = round($nbSubContentComplete / $nbSubContent * 100);
                    } else if ($data['finished'] != null) {
                        $data['completion'] = 100;
                    } else {
                        $data['completion'] = 0;
                    }
                   

                    $parent->update($data);
                    //if ($data['finished'] != -1 && $data['finished'] != null) {
                        event(new \Djoudi\LaravelH5p\Events\H5pResultEvent('result', 'finished', $data));
                    //}
                }
            } else {
                if($result['finished'] != null){
                    event(new \Djoudi\LaravelH5p\Events\H5pResultEvent('result', 'finished', $result));
                }
            }


            //event(new \Djoudi\LaravelH5p\Events\H5pResultEvent('test', 'debug', $result));
            

        } else {

        }
        
        
        return response()->json($request->all());
    }

    public function contentUserData(Request $request)
    {
        $retour = [];

        $user_id = \Auth::id();    
        
        if ((int)$user_id > 0) {
            // Query String Parameters.
            $content_id = $request->content_id;
            $data_type = strtolower($request->data_type);
            $sub_content_id = $request->sub_content_id;

            // Form Data.
            $data = $request->data;
            $preload = $request->preload;
            $invalidate = $request->invalidate;

            if ($data !== null && $preload !== null && $invalidate !== null) {
                if ($data === '0') { // Delete user data.             
                    \Djoudi\LaravelH5p\Eloquents\H5pContentsUserData::where('content_id', $content_id)
                    ->where('user_id', $user_id)
                    ->where('sub_content_id', $sub_content_id)
                    ->where('data_id', $data_type)
                    ->delete();

                } else { //create/update data
                    $contentUserData = \Djoudi\LaravelH5p\Eloquents\H5pContentsUserData::updateOrCreate([
                        'content_id' => $content_id,
                        'user_id' => $user_id,
                        'sub_content_id' => $sub_content_id,
                        'data_id' => $data_type
                    ],
                    [
                        'data' => $data,
                        'preload' => $preload,
                        'invalidate' => $invalidate
                    ]);
                }
                
            } else { //retrieve data        
                $contentUserData = \Djoudi\LaravelH5p\Eloquents\H5pContentsUserData::where('content_id', $content_id)
                    ->where('user_id', $user_id)
                    ->where('sub_content_id', $sub_content_id)
                    ->where('data_id', $data_type)
                    ->first();
            }

            if ($contentUserData) {
                $retour = [
                    'success' => 'success',
                    //'data' => [
                        'data' => $contentUserData->data,
                        'preload' => $contentUserData->preload,
                        'invalidate' => $contentUserData->invalidate,
                    //]
                              
                ];
            } 
        }
                
        return response()->json($retour);
    }

    public function dom(Request $request, $id = 0)
    {
        //dd('ici api h5p');
        $h5p = App::make('LaravelH5p');
        $core = $h5p::$core;

        $user = \Auth::user();

        

        //edition
        if ($id > 0) {
         
            //$settings = $h5p::get_core();
            
            $content = $h5p->get_content($id);
            $settings = $h5p::get_editor($content);
            $embed = $h5p->get_embed($content, $settings);
            $embed_code = $embed['embed'];
            $settings = $embed['settings'];
            $title = $content['title'];

            $parameters['params'] = json_decode($content['params']);
            $parameters['metadata'] = $content['metadata'];
            $parameters = \json_encode($parameters);
        } else {
            $settings = $h5p::get_editor();
            $content = null;
            $parameters = isset($content['params']) ? $content['params'] : '{}';
            // view Get the file and settings to print from
            
            $embed_code = '';
        }
        
        // Prepare form
        $library = isset($content['library']) ? \H5PCore::libraryToString($content['library']) : 0;
        $display_options = $core->getDisplayOptionsForEdit(isset($content['disable']) ? $content['disable'] : null);
        $title = isset($content['title']) ? $content['title'] : '';

      
       

        return [
            'settings' => $settings,
            'library' => $library,
            'parameters' => $parameters,
            'display_options' => $display_options,
            'embed_code' => $embed_code,
            'title' => $title,
            'user' => [
                'mail' => $user->email,
                'name' => $user->first_name.' '.$user->name
            ]
        ];
    }
}
