var e107 = e107 || {'settings': {}, 'behaviors': {}};

(function ($)
{
	'use strict';

	e107.callbacks = e107.callbacks || {};

	/**
	 * Inactivate links.
	 *
	 * @type {{attach: e107.behaviors.e107projectsNoLink.attach}}
	 */
	e107.behaviors.e107projectsNoLink = {
		attach: function (context, settings)
		{
			// Location - Extended User Field.
			var selector = 'a.no-link';

			$(selector, context).once('e107-projects-no-link').each(function ()
			{
				var $this = $(this);
				$this.click(function() {
					return false;
				});
			});
		}
	};

})(jQuery);
