var e107 = e107 || {'settings': {}, 'behaviors': {}};

(function ($)
{
	'use strict';

	e107.Nodejs = e107.Nodejs || {
			'contentChannelNotificationCallbacks': {},
			'presenceCallbacks': {},
			'callbacks': {},
			'socket': false,
			'connectionSetupHandlers': {}
		};

	e107.Nodejs.callbacks.e107projectsNotify = {
		callback: function (message)
		{
			var msgData = {
				playsound: true,
				type: message.type,
				data: {
					subject: message.subject || '',
					body: message.markup
				}
			};

			e107.Nodejs.callbacks.nodejsNotify.callback(msgData);
		}
	};

})(jQuery);
