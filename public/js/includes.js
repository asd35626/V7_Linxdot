window.FlashCanvasOptions = {
  swfPath: '/js/lib/FlashCanvas/bin/'
};
yepnope([
  // Libs
  // '/js/lib/jquery/jquery-1.7.1.min.js',
  '/js/lib/flotr2/lib/bean.js',
  '/js/lib/flotr2/lib/underscore-min.js',
  {
  test : (navigator.appVersion.indexOf("MSIE") != -1  && parseFloat(navigator.appVersion.split("MSIE")[1]) < 9),
    // Load for IE < 9
    yep : [
      '/js/lib/FlashCanvas/bin/flashcanvas.js',
      /*'../lib/flotr2/lib/excanvas.js',*/
      '/js/lib/flotr2/lib/base64.js'
    ]
  },
  '/js/lib/flotr2/lib/canvas2image.js',
  /*'../lib/flotr2/lib/canvastext.js',*/
  '/js/lib/bonzo/bonzo.min.js',

  // Flotr
  '/js/lib/flotr2/js/Flotr.js',
  '/js/lib/flotr2/js/DefaultOptions.js',
  '/js/lib/flotr2/js/Color.js',
  '/js/lib/flotr2/js/Date.js',
  '/js/lib/flotr2/js/DOM.js',
  '/js/lib/flotr2/js/EventAdapter.js',
  '/js/lib/flotr2/js/Graph.js',
  '/js/lib/flotr2/js/Axis.js',
  '/js/lib/flotr2/js/Series.js',
  '/js/lib/flotr2/js/Text.js',
  '/js/lib/flotr2/js/types/lines.js',
  '/js/lib/flotr2/js/types/bars.js',
  '/js/lib/flotr2/js/types/points.js',
  '/js/lib/flotr2/js/plugins/selection.js',
  '/js/lib/flotr2/js/plugins/legend.js',
  '/js/lib/flotr2/js/plugins/hit.js',
  '/js/lib/flotr2/js/plugins/crosshair.js',
  '/js/lib/flotr2/js/plugins/labels.js',
  '/js/lib/flotr2/js/plugins/legend.js',
  '/js/lib/flotr2/js/plugins/titles.js',
  {
    test : ('ontouchstart' in window),
    nope : [
      '/js/lib/flotr2/js/plugins/handles.js'
    ]
  },

  // Visualization
  '/js/Envision.js',
  '/js/Visualization.js',
  '/js/Component.js',
  '/js/Interaction.js',
  '/js/Preprocessor.js',
  '/js/templates/namespace.js',
  '/js/templates/Finance.js',
  '/js/templates/TimeSeries.js',
  '/js/templates/Zoom.js',
  '/js/actions/namespace.js',
  '/js/actions/hit.js',
  '/js/actions/selection.js',
  '/js/actions/zoom.js',
  '/js/adapters/namespace.js',
  '/js/adapters/flotr/namespace.js',
  '/js/adapters/flotr/defaultOptions.js',
  '/js/adapters/flotr/Child.js',
  '/js/adapters/flotr/lite-lines.js',
  '/js/adapters/flotr/whiskers.js',
  '/js/components/namespace.js',
  '/js/components/QuadraticDrawing.js',

 // { complete: startChartProcess }
  { complete: example }
]);
