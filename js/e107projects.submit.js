var e107 = e107 || {'settings': {}, 'behaviors': {}};

(function ($)
{
	'use strict';

	e107.callbacks = e107.callbacks || {};

	/**
	 * Initializes a click event callback on project submission buttons.
	 *
	 * @type {{attach: e107.behaviors.e107projectsDisableSubmitButtons.attach}}
	 */
	e107.behaviors.e107projectsDisableSubmitButtons = {
		attach: function (context, settings)
		{
			var selector = '.project-submission-button';

			$(selector, context).once('e107-projects-disable-submit-buttons').each(function ()
			{
				var $button = $(this);

				$button.on('click', function() {
					var $this = $(this);

					$this.attr('disabled', 'disabled');
					$this.toggleClass('active');
				});
			});
		}
	};

})(jQuery);
