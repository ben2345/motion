var MOTION = MOTION || {};

MOTION.loader = (function($){
	"use strict";
    var link;
    
    var $imgContainer, $loader, $skuContainer, $temperatureCpu, $temperatureGpu, $loadCpu, $nbFiles, $lastUpdateTime, $bitcoinRate, ajaxRequest, $temperatureBalcon, $temperatureSalon, $temperatureSalleDeBain, $;
 
	// Main init
	var _init = function($jq){
        $ = $jq;
        $imgContainer = $('#imgContainer');
        $skuContainer = $('#general-infos');
        $loader = $('#loader');
        $temperatureCpu = $('#temperatureCpu');
        $temperatureGpu = $('#temperatureGpu');
        $loadCpu = $('#loadCpu');
        $nbFiles = $('#nbFiles');
        $lastUpdateTime = $('#lastUpdateTime');
        $bitcoinRate = $('#bitcoinRate');
        $temperatureBalcon = $('#temperatureBalcon');
        $temperatureSalon = $('#temperatureSalon');
        $temperatureSalleDeBain = $('#temperatureSalleDeBain');
        eventsLoader.init();
        skuLoader.init();
   };
   
   var skuLoader = {
      init: function () {
         $.each($skuContainer.find('span'), function (key, span) {
            skuLoader.bindSkuRequest(span);
         });
      },
      bindSkuRequest: function (span) {
         if ($(span).data('delay')) {
            setInterval(function () {
               helper.ajaxRequest('getSku', $(span).attr('id'));
            }, $(span).data('delay'));
            $(span).on('click', function () {
               helper.ajaxRequest('getSku', $(span).attr('id'));
            });
         }
      },
   };
   
	var eventsLoader = {
        init: function () {
           helper.ajaxRequest('getFiles', 'all');
           eventsLoader.loader();
        },
        loader: function () {
           setInterval(eventsLoader.loadNewEvents, 60000);
        },
        loadNewEvents: function () {
         var min = (new Date()).getMinutes().toString().charAt(2 - 1);
         if ($.inArray(min, ["6", "1"]) !== -1) {
            helper.ajaxRequest('getFiles', 'latest');
         }
        },
    };

    var helper = {
        ajaxRequest: function (action, id) {
           helper.showLoader(true);
       
           ajaxRequest = $.ajax({
              url: 'ajax.php', type: "POST", async: true, cache: false,
              data: {action: action, id: id},
           });
     
           ajaxRequest.done(function (data, textStatus, jqXHR) {
              if (textStatus == "success") {
                 if (typeof data !== 'undefined') {
                    helper.showLoader(false);
                    if (data) {
                     var response = jQuery.parseJSON(data);
                     if (response) {
                          helper.display(response);
                     } 
                   }
                 }
              }
           });
           ajaxRequest.fail(function (jqXHR, textStatus, errorThrown) {
              console.log(jqXHR + textStatus + errorThrown);
           });
        },
        showLoader: function (show) {
           if ($loader.length) {
              if (show == true) {
                 $loader.addClass('loading');
              } else {
                 $loader.removeClass('loading');
              }
           }
       },
        
       display: function (response) {

          var skuBlock = $('#' + Object.keys(response)[0]);
          if (skuBlock.length) {
             skuBlock.html(response[Object.keys(response)[0]]);
          }
          if (response.last_update_time) {
            $lastUpdateTime.html(response.last_update_time);
          }
         if (response.event_files_infos) {

            $("html, body").animate({ scrollTop: 0 }, "slow");
            $nbFiles.html(response.nb_files);
            
            $.each(response.event_files_infos, function (key, value) {
               var image = new Image();
               image.src = value.url;
                
               var event = $('<div>', { "class": "event hidden" });
               var divInfos = $('<div>', { "class": "infos" });
               var h2 = $('<h2>').text(value.heure);
               var date = $('<span>', { "class": "date" }).text(value.date);
               var fileSize = $('<span>, { "class": "file-size" }').text(value.filesize);

               var progressBar = $('<div>', {
                  "class": "progress-bar",
                  "role": "progressbar",
                  "aria-valuenow": "0",
                  "aria-valuemin": "0",
                  "aria-valuemax": "100",
                  "data-current": "0"
               }).css({'width':'0%'});
               
               var progress = $('<div>', { "class": "progress" }).append(progressBar);

               var totalTime = $('<span>', { "class": "progression-total" }).text(' / '+ parseFloat(value.fileduration).toFixed(1) + ' s');
               var progressionTimer = $('<span>', { "class": "progression-timer"}).text('0');
               
               var progressDiv = $('<div>', {
                  "class": "progress-div",
               }).append(progressionTimer).append(totalTime).append(progress);

               helper.animateProgressBar(progressBar, value.fileduration, progressionTimer);
      
               var deleteLink = $('<a>', { 'class': 'delete', 'data-path': value.path }).text('🗑');
               deleteLink.on('click', function () {
                  helper.ajaxRequest('deleteFile', $(this).data('path'));
                  event.remove();
               });
               divInfos.append(h2);
               divInfos.append(date);
               divInfos.append(fileSize);
               divInfos.append(progressDiv);
               divInfos.append(deleteLink);
               event.append(divInfos);
               $imgContainer.prepend(event);
               
               image.onload = function () {
                  event.removeClass('hidden');
                  event.append(image);
                  // set interval to file duration
                  setInterval(function() {
                     helper.animateProgressBar(progressBar, value.fileduration, progressionTimer)
                  }, parseInt(value.fileduration * 1000));
               }
            });
          }
       },

      // init 
      animateProgressBar: function (progressBar, duration, progressionTimer) {
         // start
         progressBar.data('current', 0);
         
         var interval = window.setInterval(function () {
            helper.updateProgressBar(progressBar,duration,progressionTimer);
         }, 100);
 
         // stop 
         window.setTimeout(function(){
            clearInterval(interval);
         }, parseInt(duration*1000));
      },
      
      updateProgressBar: function (progressBar, duration, progressionTimer) {
         var newCurrent = parseFloat(progressBar.data('current')) + parseFloat('0.1');
         progressBar.data('current', newCurrent.toFixed(1));

         var percent = (((newCurrent.toFixed(1) - duration.toFixed(1)) / duration.toFixed(1)) * 100).toFixed(0);
         progressBar.css('width', parseFloat(percent) + parseFloat(100)+'%');
         progressionTimer.html(newCurrent.toFixed(1));
      },
    };

	// Public methods
	var _publics = {
		init: _init
	};
	return _publics;
})();

(function($){
	$(document).ready(function(){
		MOTION.loader.init($);
	});
})(jQuery);
