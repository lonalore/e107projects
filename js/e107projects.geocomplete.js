var e107 = e107 || {'settings': {}, 'behaviors': {}};

(function ($)
{
	'use strict';

	e107.callbacks = e107.callbacks || {};

	/**
	 * Initializes GeoComplete widget.
	 *
	 * @type {{attach: e107.behaviors.e107projectsGeoComplete.attach}}
	 */
	e107.behaviors.e107projectsGeoComplete = {
		attach: function (context, settings)
		{
			// Location - Extended User Field.
			var selector = '#ue-user-plugin-e107projects-location';

			$(selector, context).once('e107-projects-geo-complete').each(function ()
			{
				var $this = $(this);
				$this.geocomplete();
			});
		}
	};

})(jQuery);
