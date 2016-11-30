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
			var selector = '.ajax-action-button';

			$(selector, context).once('e107-projects-disable-submit-buttons').each(function ()
			{
				var $button = $(this);

				$button.on('click', function ()
				{
					var $this = $(this);

					if(!$this.hasClass('active'))
					{
						$this.attr('disabled', 'disabled');
						$this.toggleClass('active');
					}
				});
			});
		}
	};

	/**
	 * Initializes a click event callback on project submission buttons.
	 *
	 * @type {{attach: e107.behaviors.e107projectsDisableSubmitButtonsOnKeyup.attach}}
	 */
	e107.behaviors.e107projectsDisableSubmitButtonsOnKeyup = {
		attach: function (context, settings)
		{
			var selector = 'input.has-ajax-button';

			$(selector, context).once('e107-projects-disable-submit-buttons-on-keyup').each(function ()
			{
				var $input = $(this);
				var $button = $input.parent().find('button.e-ajax');

				$input.on($input.data('event'), function ()
				{
					if(!$button.hasClass('active'))
					{
						$button.attr('disabled', 'disabled');
						$button.toggleClass('active');
					}
				});
			});
		}
	};

})(jQuery);
