
(function ($) {
    ns.initResultsListener = function () {
        
        H5P.externalDispatcher.on('xAPI', function (event) {
            console.log(event.data.statement);
        
            if(event.data.statement.result || event.data.statement.verb.id == "http://adlnet.gov/expapi/verbs/attempted"){
        
                var idCurrent = event.data.statement.object.id;
        
                var idParent = (event.data.statement.context.contextActivities.parent) ? event.data.statement.context.contextActivities.parent[0].id : null;
        
                var idCurrent_split = idCurrent.split('?');
        
                //if(idCurrent_split[0] == idParent || idParent == null){//on enregistre les event de l'activité ou des sous activités, mais pas des sous-sousactivités...
                    
                    axios.post(H5PIntegration.ajax.setFinished, event.data.statement)
                    .then((response) => {
                        // handle success
                        console.log(response);
                    })
                    .catch(function (error) {
                        // handle error
                        console.log(error);
                    });
                /*} else {
                    console.log('event ignore');
                }*/
        
               
        
            }
        });
       

        //tests video
        var iframeH5P = document.getElementsByClassName('h5p-iframe')[0].contentWindow.H5P;

        //iframeH5P.externalDispatcher.on('domChanged', function (e) {
    
            if(iframeH5P && iframeH5P.instances[0]){
                var iframeVideo = iframeH5P.instances[0].video;
                iframeVideo.on('stateChange', function (event) { 
                    switch (event.data) {
                        case iframeH5P.Video.ENDED:
                            console.log('Video ended after ' + iframeVideo.getCurrentTime() + ' seconds!');          
                            // Start over again?
                            //iframeVideo.play();
                        
                            if (iframeVideo.getDuration() > 15) {
                               // iframeVideo.seek(10);
                            }
                        break;
                    
                        case iframeH5P.Video.PLAYING:
                            console.log('Playing'); 
                            break;
                    
                        case iframeH5P.Video.PAUSED:
                            console.log('Why you stop?');
                            //iframeVideo.setPlaybackRate(1.5); // Go fast
                            break;
                    
                        case iframeH5P.Video.BUFFERING:
                            console.log('Wait on your slow internet connection...');
                            break;
                    }
                });
            }
        //});    
    }





    $(document).ready(ns.initResultsListener);


})(H5P.jQuery);
