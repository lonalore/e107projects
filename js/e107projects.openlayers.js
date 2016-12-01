var e107 = e107 || {'settings': {}, 'behaviors': {}};

(function ($)
{
	'use strict';

	e107.callbacks = e107.callbacks || {};

	e107.OpenLayers = e107.OpenLayers || {};

	e107.Nodejs = e107.Nodejs || {
			'contentChannelNotificationCallbacks': {},
			'presenceCallbacks': {},
			'callbacks': {},
			'socket': false,
			'connectionSetupHandlers': {}
		};

	e107.Nodejs.callbacks.e107projectsMapPopup = {
		callback: function (message)
		{

		}
	};

	/**
	 * Resize Map's canvas.
	 *
	 * @type {{attach: e107.behaviors.e107projectsResizeMapCanvas.attach}}
	 */
	e107.behaviors.e107projectsResizeMapCanvas = {
		attach: function (context, settings)
		{
			$("#header", context).once('e107-projects-resize-map-canvas').each(function ()
			{
				e107.callbacks.e107projectsResizeMapCanvas();
			});
		}
	};

	/**
	 * Initializes OpenLayers.
	 *
	 * @type {{attach: e107.behaviors.e107projectsOpenLayers.attach}}
	 */
	e107.behaviors.e107projectsOpenLayers = {
		attach: function (context, settings)
		{
			$("#commitMap", context).once('e107-projects-open-layers').each(function ()
			{
				var style = new ol.style.Style({
					fill: new ol.style.Fill({
						color: 'rgba(6, 120, 190, 1)'
					}),
					stroke: new ol.style.Stroke({
						color: 'rgba(6, 71, 113, 1)',
						width: 1
					}),
					text: new ol.style.Text({
						font: '12px Calibri,sans-serif',
						fill: new ol.style.Fill({
							color: '#000'
						}),
						stroke: new ol.style.Stroke({
							color: '#fff',
							width: 3
						})
					})
				});

				var vectorLayer = new ol.layer.Vector({
					source: new ol.source.Vector({
						url: 'https://openlayers.org/en/v3.19.1/examples/data/geojson/countries.geojson',
						format: new ol.format.GeoJSON()
					}),
					style: function(feature, resolution) {
						style.getText().setText('');
						return style;
					}
				});

				e107.OpenLayers = new ol.Map({
					target: 'commitMap',
					layers: [
						vectorLayer
					],
					controls: ol.control.defaults({
						attributionOptions: ({
							collapsible: false
						})
					}).extend([
						// new ol.control.ZoomSlider(),
						// new ol.control.OverviewMap(),
						// new ol.control.ScaleLine(),
						new ol.control.FullScreen()
					]),
					view: new ol.View({
						center: ol.proj.transform([30, 30], 'EPSG:4326', 'EPSG:3857'),
						zoom: 2.5
					}),
					interactions: ol.interaction.defaults({
						mouseWheelZoom: false
					})
				});

				if(e107.settings.e107projects.locations)
				{
					$.each(e107.settings.e107projects.locations, function() {
						e107.callbacks.e107projectsSetMarker(this);
					});
				}
			});

			$(window).resize(function ()
			{
				e107.callbacks.e107projectsWaitForFinalEvent(function ()
				{
					e107.callbacks.e107projectsResizeMapCanvas();
				}, 300, 'e107projectsResizeMapCanvas');
			});
		}
	};

	e107.callbacks.e107projectsSetMarker = function(location)
	{
		var marker = document.createElement('img');
		marker.src = e107.settings.e107projects.marker;
		marker.width = 5;
		marker.height = 5;
		marker.style = 'margin: 0; padding: 0;';
		
		var position = ol.proj.transform(
			[parseFloat(location.lon), parseFloat(location.lat)],
			'EPSG:4326',
			'EPSG:3857'
		);

		// http://openlayers.org/en/v3.5.0/apidoc/ol.Overlay.html
		e107.OpenLayers.addOverlay(new ol.Overlay({
			position: position,
			positioning: 'center-center',
			offset: [0, 0],
			element: marker
		}));
	};

	e107.callbacks.e107projectsResizeMapCanvas = function ()
	{
		var $canvas = $('#header');
		var $window = $(window);
		var $prologue = $('.prologue-container');

		var canvasHeight = $window.height() - 50;
		var canvasWidth = $window.width();
		var prologueTop = (canvasHeight / 2) - ($prologue.height());

		if(canvasWidth >= 768)
		{
			canvasHeight -= 50;
		}

		$canvas.height(canvasHeight);
		$canvas.width(canvasWidth);
		$prologue.css('top', parseInt(prologueTop) + 'px');

		if(typeof e107.OpenLayers.updateSize === 'function')
		{
			e107.OpenLayers.updateSize();
		}
	};

	e107.callbacks.e107projectsWaitForFinalEvent = (function ()
	{
		var timers = {};
		return function (callback, ms, uniqueId)
		{
			if(!uniqueId)
			{
				uniqueId = "Don't call this twice without a uniqueId";
			}
			if(timers[uniqueId])
			{
				clearTimeout(timers[uniqueId]);
			}
			timers[uniqueId] = setTimeout(callback, ms);
		};
	})();

})(jQuery);
