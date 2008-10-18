/**
 *		@package		FV Community News
 *		@author			Frank Verhoeven
 *		@copyright		Copyright (c) 2008, Frank Verhoeven
 *		@version		1.0
 */

Event.observe(window, 'load', function() {
	Event.observe('fvCommunityNewsForm', 'submit', function(submission) {
		Effect.Appear('fvCommunityNewsLoader', {
			duration: 1.0,
			afterFinish: function() {
				fvCommunityNewsMakeRequest();
			}
		});
		
		Event.stop(submission);
	});
});

function fvCommunityNewsMakeRequest() {
	url = window.location.href + '?fvCommunityNewsAjaxRequest=true';
	
	new Ajax.Request (url, {
		method: 'post',
		
		parameters: $('fvCommunityNewsForm').serialize(true),
		
		onSuccess: function(response) {
			Effect.Fade('fvCommunityNewsLoader', {
				duration: 0.5,
				afterFinish: function() {
					fvCommunityNewsFetchResults(response);
				}
			});
			
		},
		
		onFailure: function() {
			Effect.Fade('fvCommunityNewsLoader', {
				afterFinish: function() {
					Effect.SwitchOff('fvCommunityNewsForm', {
						afterFinish: function() {
							$('fvCommunityNewsAjaxResponse').innerHTML = '<p>Submission failed, please try again later.</p>';
							Effect.Appear('fvCommunityNewsAjaxResponse');
						}
					});
				}
			});
		}
		
	 });
}

function fvCommunityNewsFetchResults(response) {
	var xml = response.responseXML;
	var status = xml.getElementsByTagName('status')[0].childNodes[0].nodeValue;
	var message = xml.getElementsByTagName('message')[0].childNodes[0].nodeValue;
	
	if ('error' == status) {
		$('fvCommunityNewsErrorResponse').innerHTML =  message;
	} else {
		Effect.SwitchOff('fvCommunityNewsForm', {
			afterFinish: function() {
				$('fvCommunityNewsAjaxResponse').innerHTML = '<p>' + message + '</p>';
				Effect.Appear('fvCommunityNewsAjaxResponse', {duration: 2.0});
			}
		});
	}
}

function fvCommunityNewsReloadCaptcha() {
	var element = 'fvCommunityNewsCaptchaImage';
	var oldSource  = $(element).readAttribute('src');
	var newSource = $(element).readAttribute('src') + '&amp;dummy=true';
	
	Effect.Appear('fvCommunityNewsCaptchaLoader', {duration: 0});
	
	new Effect.Opacity(element, {from: 1.0, to: 0.0, duration: 0.2,
		afterFinish: function() {
			$(element).writeAttribute({ src :  newSource }).writeAttribute({ '_src' :  oldSource });
			Event.observe(element, 'load', function() {
				Effect.Fade('fvCommunityNewsCaptchaLoader', {duration: 0});
				new Effect.Opacity(element, {from: 0.0, to: 1.0, duration: 0.2});
			});
		}
		
	});
	
	
	
	return false;
}
