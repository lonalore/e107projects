var e107 = e107 || {'settings': {}, 'behaviors': {}};

(function ($)
{
	'use strict';

	e107.callbacks = e107.callbacks || {};

	/**
	 * Initializes Isotope.
	 *
	 * @type {{attach: e107.behaviors.e107projectsIsotope.attach}}
	 */
	e107.behaviors.e107projectsIsotope = {
		attach: function (context, settings)
		{
			var selector = '.isotope-grid';

			$(selector, context).once('e107-projects-isotope').each(function ()
			{
				var $this = $(this);

				$this.isotope({
					itemSelector: '.grid-item',
					layoutMode: 'fitRows'
				});
			});
		}
	};

})(jQuery);
