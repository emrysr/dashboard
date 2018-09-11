/*
  All Emoncms code is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  Emoncms - open source energy visualisation
  Part of the OpenEnergyMonitor project:  http://openenergymonitor.org

  render.js goes through all the dashboard html elements that specify the dashboard widgets
  and inserts the dials, visualisations to be displayed inside the element.
  see designer.js for more information on the html element widget box model.

  render.js calls the render scripts of all the widgets which is where all the 
  individual widget render code is located.
*/

// Global page vars definition

// Array for all feed details by feed id
var associd = [];
var assocfeed = [];
// Array for smooth change values - creation of smooth dial widget
var assoc_curve = [];

var widgetcanvas = {};

var dialrate = 0.15;
var browserVersion = 999;
var fast_update_fps = 25;

// Get the device pixel ratio, falling back to 1.
var scale = window.devicePixelRatio || 1;

var Browser = {
  Version : function()
  {
    var version = 999;
    if (navigator.appVersion.indexOf("MSIE") != -1)
      version = parseFloat(navigator.appVersion.split("MSIE")[1]);
    return version;
  }
}

// populate widgets variable with *_widgetlist from all dashboards
function render_widgets_init(widget){
  for (z in widget){
    var fname = widget[z]+"_widgetlist";
    var fn = window[fname];
    $.extend(widgets,fn());
  }
}

//start dashboard init and update processes
function render_widgets_start(){
  update(true);

  browserVersion = Browser.Version();
  if (browserVersion < 9) dialrate = 0.4;

  for (z in widget){
    var fname = widget[z]+"_init";
    var fn = window[fname];
    fn();
  }

  setInterval(function() { update(false); }, 5000);
  gpu_fast_update();
  //setInterval(function() { fast_update(); }, 100);
}

// GPU friendly fast update loop
function gpu_fast_update() { 
  setTimeout( 
   function() {
      window.requestAnimationFrame(gpu_fast_update);
      fast_update();
    }
  , 1000/fast_update_fps);
};

// update function
function update(first_time){
  var query = path + "feed/list.json?userid="+userid;
  if (apikey) query += "&apikey="+apikey;
  $.ajax(
  {
    type: "GET",
    url : query,
    dataType : 'json',
    async: !first_time,
    success : function(data){ 
      for (z in data){
        associd[data[z]['id']] = data[z];
        assocfeed[data[z]['tag']+":"+data[z]['name']] = data[z]['id'];
      }
      if (!first_time){
        for (z in widget){
          var fname = widget[z]+"_slowupdate";
          var fn = window[fname];
          fn();
        }
      }
    },
    error : function(){
          for (z in widget){ 
          var fname = widget[z]+"_isnonetwork";
          var fn = window[fname];
             if(typeof(fn) == 'function') {
             fn();
             }
         }
    }
  });
}

function fast_update(){
  if (redraw){ 
    for (z in widget){
      // console.log('fast_update()', widget[z]+'_init')
      var fname = widget[z]+"_init";
      var fn = window[fname];
      fn();
    }
  }

  for (z in widget){
    var fname = widget[z]+"_fastupdate";
    var fn = window[fname];
    fn();
  }
  redraw = 0;
}

function curve_value(feed,rate){
  var val = 0;
  if (feed){
    if (assoc_curve[feed] === undefined) assoc_curve[feed] = 0;
    if (associd[feed] !== undefined) assoc_curve[feed] = assoc_curve[feed] + ((parseFloat(associd[feed]['value']) - assoc_curve[feed]) * rate);
    val = assoc_curve[feed] * 1;
  }
  return val;
}

function setup_widget_canvas(elementclass){
  $('.'+elementclass).each(function(index){
    var $this = $(this)
    var widgetId = $this.attr("id");
    var canvas = $this.children('canvas');
    var canvasid = "can-"+widgetId;

    // 1) Create canvas if it does not exist
    if (!canvas[0]){
      $this.html('<canvas id="'+canvasid+'"></canvas>');
      canvas = $this.children('canvas');
    }
    if(typeof designer != 'undefined'){
      var width = designer.boxlist[widgetId].width;
      var height = designer.boxlist[widgetId].height;
      
      widthIsInPixels = designer.boxlist[widgetId]["styleUnitWidth"] == 0
      heightIsInPixels = designer.boxlist[widgetId]["styleUnitHeight"] == 0
      
      designer.boxlist[widgetId]["width"] = width
      designer.boxlist[widgetId]["height"] = height
      // if not in design view derive diementions from bounding box (will not be resizable)
    } else {
      var rect = canvas[0].getBoundingClientRect();
      width = rect.width;
      height = rect.height;
      // widthIsInPixels = designer.boxlist[widgetId]["styleUnitWidth"] == 0
      // heightIsInPixels = designer.boxlist[widgetId]["styleUnitHeight"] == 0
    }

    // resize correction for specific widgts
    // @todo: directly modify the widget and remove this correction
    if(elementclass=='thresholds') {
      var rect = canvas[0].getBoundingClientRect();
      width = rect.width;
      height = rect.height;
    }

    // 2) Resize canvas if it needs resizing
    // make the canvas container relative
    $this[0].style.position = 'absolute'

    // offset the resized canvas to account for the scaling so that it sits top-left
    // calculate the values
    dpiWidth = width * scale
    dpiHeight = height * scale
    translate = [
      (0-dpiHeight)+'px',
      (0-dpiWidth)+'px'
    ].join(' ')
    // set the values to the css properties
    cssProperties = {
      transform: 'scale('+scale+') translate('+translate+')',
      transformOrigin: '0 0',
      left: '0',
      top: '0',
    // enlarge the widget by the a factor of "display resolution"
      width: width+'px',
      height: height+'px'
    }
    // set each property value to the object's css value
    // @note: jquery css() method calculates height & width differently
    for(property in cssProperties){
      canvas[0].style[property] = cssProperties[property]
    }
    // reduce the element using css to account for the display's resolution
    // @note: jquery height() & width() method calculates height & width differently
    canvas[0].width = dpiWidth
    canvas[0].height = dpiHeight
    
    var canvas = document.getElementById(canvasid);
    if (browserVersion != 999) {
      canvas[0].width  = dpiWidth;
      canvas[0].height = dpiHeight;
      if ( typeof G_vmlCanvasManager != "undefined") G_vmlCanvasManager.initElement(canvas);
    }
    // 3) Get and store the canvas context
    widgetcanvas[canvasid] = canvas.getContext("2d");
    widgetcanvas[canvasid].scale(scale, scale);
  });
}