var e107 = e107 || {'settings': {}, 'behaviors': {}};

(function ($)
{
	'use strict';

	e107.callbacks = e107.callbacks || {};

	/**
	 * Initializes NanoScroller.
	 *
	 * @type {{attach: e107.behaviors.e107projectsNanoScroller.attach}}
	 */
	e107.behaviors.e107projectsNanoScroller = {
		attach: function (context, settings)
		{
			var selector = '.nano';

			$(selector, context).once('e107-projects-nano-scroller').each(function ()
			{
				var $this = $(this);

				var maxHeight = 300;
				var listHeight = $this.find('.list-group').height();

				if(parseInt(listHeight) > maxHeight)
				{
					$this.height(maxHeight);
				}
				else
				{
					$this.height(listHeight);
				}

				$this.nanoScroller({
					sliderMaxHeight: maxHeight
				});
			});
		}
	};

})(jQuery);
