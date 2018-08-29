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
var dpr = window.devicePixelRatio || 1;

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
  
    var widgetId = $(this).attr("id");
    var canvas = $(this).children('canvas');
    var canvasid = "can-"+widgetId;

    // 1) Create canvas if it does not exist
    if (!canvas[0]){
      $(this).html('<canvas id="'+canvasid+'"></canvas>');
      canvas = $(this).children('canvas');
    }
    
    var rect = canvas[0].getBoundingClientRect();
    var width = rect.width;
    var height = rect.height;

    // 2) Resize canvas if it needs resizing
    // canvas[0].style.backgroundColor = 'red';
    canvas[0].style.transform = "scale("+(1/dpr)+") translate("+(0-width)+"px, "+(0-height)+"px)"
    canvas[0].width = width * dpr;
    canvas[0].height = height * dpr;
    canvas[0].style.width = (100 * dpr)+"%;"
    canvas[0].style.height = (100 * dpr)+"%";

    var canvas = document.getElementById(canvasid);
    if (browserVersion != 999) {
      canvas.setAttribute('width', width * dpr);
      canvas.setAttribute('height', height * dpr);
      if ( typeof G_vmlCanvasManager != "undefined") G_vmlCanvasManager.initElement(canvas);
    }
    // 3) Get and store the canvas context
    widgetcanvas[canvasid] = canvas.getContext("2d");
    widgetcanvas[canvasid].scale(dpr, dpr);
  });
}