<?php
/*
All Emoncms code is released under the GNU Affero General Public License.
See COPYRIGHT.txt and LICENSE.txt.

---------------------------------------------------------------------
Emoncms - open source energy visualisation
Part of the OpenEnergyMonitor project:
http://openenergymonitor.org
*/

global $session,$path;

load_language_files("Modules/vis/locale", "vis_messages");
load_language_files("Modules/dashboard/locale", "dashboard_messages");

if (!$dashboard['height']) $dashboard['height'] = 400;
if (!isset($dashboard['feedmode'])) $dashboard['feedmode'] = "feedid";
?>
    <script type="text/javascript"><?php require "Modules/dashboard/dashboard_langjs.php"; ?></script>
    <script type="text/javascript"><?php require "Modules/vis/vis_langjs.php"; ?></script>
    <link href="<?php echo $path; ?>Modules/dashboard/Views/js/widget.css?ver=<?php echo $js_css_version; ?>" rel="stylesheet">
    <link href="<?php echo $path; ?>Modules/dashboard/Views/dashboardeditor.css?ver=<?php echo $js_css_version; ?>" rel="stylesheet">

    <script type="text/javascript" src="<?php echo $path; ?>Lib/flot/jquery.flot.min.js"></script>
    <script type="text/javascript" src="<?php echo $path; ?>Modules/dashboard/dashboard.js?ver=<?php echo $js_css_version; ?>"></script>
    <script type="text/javascript" src="<?php echo $path; ?>Modules/dashboard/Views/js/widgetlist.js?ver=<?php echo $js_css_version; ?>"></script>
    <script type="text/javascript" src="<?php echo $path; ?>Modules/dashboard/Views/js/render.js?ver=<?php echo $js_css_version; ?>"></script>
    <script type="text/javascript" src="<?php echo $path; ?>Modules/feed/feed.js?ver=<?php echo $js_css_version; ?>"></script>

    <?php require_once "Modules/dashboard/Views/loadwidgets.php"; ?>

<div id="dashboardpage">
    <div id="widget_options" class="modal hide keyboard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel"><?php echo dgettext('dashboard_messages','Configure element'); ?>
            <!-- button shown if readme available -->
                <button id="open-widget-help-modal" role="button" class="btn d-none" 
                  data-target="#widget-help-modal"
                  data-toggle="modal"
                >
                  <svg class="icon icon-help"><use xlink:href="#icon-help"></use></svg>
                </button>
            </h3>
        </div>
        <div id="widget_options_body" class="modal-body"></div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo dgettext('dashboard_messages','Cancel'); ?></button>
            <button id="options-save" class="btn btn-primary"><?php echo dgettext('dashboard_messages','Save changes'); ?></button>
        </div>
    </div>
</div>

<div id="toolbox" style="cursor:move; text-align: center; background-color:#ddd; padding-left:5px; padding-right:5px; padding-bottom:15px; position:fixed;z-index:1; border-radius: 5px 5px 5px 5px; border-style:groove; width: 125px; height: auto; top: 5rem; right: 1rem;"><?php echo dgettext('dashboard_messages','Toolbox'); ?>
	<div id="separator" style="height:1.5px; background:#717171"></div>
	<div id="Buttons" style="position:relative; top:5px; cursor:pointer">
	<span id="dashboard-config-buttons">
	<button id="dashboard-config-button" style="padding:4px; float:left; width:31px" class="btn" href="#dashConfigModal" role="button" data-toggle="modal" title="<?php echo dgettext('dashboard_messages','Configure dashboard basic data'); ?>"><span><img src="<?php echo ($path.'Modules/dashboard/Views/icons/emon-icon-gear.png'); ?>"></span></button>
	<button id="undo-button" class="btn" style="padding:4px; float:left; width:31px" title="<?php echo dgettext('dashboard_messages','Undo last step'); ?>"><span><img src="<?php echo ($path.'Modules/dashboard/Views/icons/emon-icon-undo.png'); ?>"></span></button>
	<button id="redo-button" class="btn" style="padding:4px; float:left; width:31px" title="<?php echo dgettext('dashboard_messages','Redo last step'); ?>"><span><img src="<?php echo ($path.'Modules/dashboard/Views/icons/emon-icon-redo.png'); ?>"></span></button>
	<button id="view-mode" class="btn" style="float:left; padding:4px; width:31px" title="<?php echo dgettext('dashboard_messages','Return to view mode'); ?>" onclick="window.location.href='view?id=<?php echo $dashboard['id']; ?>'"><span><img src="<?php echo ($path.'Modules/dashboard/Views/icons/emon-icon-view.png'); ?>" ></span></button>
	</span>
	<span id="when-selected">
		<button id="options-button" class="btn" style="float:left; padding:4px; width:31px" data-toggle="modal" data-target="#widget_options" title="<?php echo dgettext('dashboard_messages','Configure selected item'); ?>"><span><img src="<?php echo ($path.'Modules/dashboard/Views/icons/emon-icon-tool.png'); ?>"></span></button>
		<button id="move-forward-button" class="btn" style="float:left; padding:4px; width:31px" title="<?php echo dgettext('dashboard_messages','Move selected item in front of other items'); ?>"><span><img src="<?php echo ($path.'Modules/dashboard/Views/icons/emon-icon-front.png'); ?>"></span></button>
		<button id="move-backward-button" class="btn" style="float:left; padding:4px; width:31px" title="<?php echo dgettext('dashboard_messages','Move selected item to back of other items'); ?>"><span><img src="<?php echo ($path.'Modules/dashboard/Views/icons/emon-icon-back.png'); ?>"></span></button>
		<button id="delete-button" class="btn btn-danger" style="float:left; padding:4px; width:31px" title="<?php echo dgettext('dashboard_messages','Delete selected items'); ?>"><span><img src="<?php echo ($path.'Modules/dashboard/Views/icons/emon-icon-delete.png'); ?>"></span></button>
	</span>
	<span id="widget-buttons" ></span>
	<span><button id="save-dashboard" class="btn btn-success" style="float:left; padding:2px; width:125px" title="<?php echo dgettext('dashboard_messages','Nothing to save'); ?>" ><?php echo dgettext('dashboard_messages','Not modified'); ?></button></span>
	</div>

</div>


<div id="page-container" style="height:<?php echo $dashboard['height']; ?>px; background-color:#<?php echo $dashboard['backgroundcolor']; ?>; position:relative;">
    <div id="page"><?php echo $dashboard['content']; ?></div>
    <canvas id="can" width="940px" height="<?php echo $dashboard['height']; ?>px" style="position:absolute; top:0px; left:0px; margin:0; padding:0;"></canvas>
</div>

<script type="application/javascript">
window.onload = addListeners();
var startx = 0, starty = 0;

function addListeners() {
  $("#toolbox").on("touchstart mousedown", null, null, mouseDown);
  $(window).on("touchend touchcancel mouseup", null, null, mouseUp);
}

function mouseUp() {
  $(window).off("touchmove mousemove", null, toolboxMove);
}

function mouseDown(e) {
  var toolbox = $('#toolbox');
  if (toolbox[0] === e.target) {
    var position = toolbox.position();
    startx = e.clientX - position.left;
    starty = e.clientY - position.top;
    $(window).on("touchmove mousemove", null, null, toolboxMove);
	e=e || window.event;
	pauseEvent(e);
  }
}

function pauseEvent(e){
    if(e.stopPropagation) e.stopPropagation();
    if(e.preventDefault) e.preventDefault();
    e.cancelBubble=true;
    e.returnValue=false;
    return false;
}

function toolboxMove(e) {
  var posx = e.clientX - startx;
  var posy = e.clientY - starty;
  if (posx < 0 ) posx = 0;
  if (posy < 50 ) posy = 50;

  $('#toolbox').css({position: 'absolute', left: posx+'px', top: posy+'px'});
  console.log("posx:" + posx + "posy:" + posy);
}
</script>

<script type="text/javascript" src="<?php echo $path; ?>Modules/dashboard/Views/js/designer.js"></script>
<script type="application/javascript">
    var dashid = <?php echo $dashboard['id']; ?>;
    var apikey = "";
    var feedlist = feed.list();
    var userid = <?php echo $session['userid']; ?>;
    var widget = <?php echo json_encode($widgets); ?>;
    var redraw = 0;
    var reloadiframe = -1; // force iframes url to recalculate for all vis widgets
    var _SI = designer.get_SI(); // get a list of International System of Units (SI)
    $('#can').width($('#dashboardpage').width());

    render_widgets_init(widget); // populate widgets variable

    designer.canvas = "#can";
    designer.grid_size = <?php echo $dashboard['gridsize']; ?>;
    designer.feedmode = "<?php echo $dashboard['feedmode']; ?>";
    console.log("designer.feedmode: "+designer.feedmode);
    designer.widgets = widgets;
    designer.init();

    render_widgets_start(); // start widgets refresh

    var lastsavecontent = $("#page").html();
    
    $("#save-dashboard").click(function (){
        var currentcontent = $("#page").html();

        var success = false;
        if (currentcontent === lastsavecontent) {
            // If it's not changed, just bypass actual saving and assume success
            success = true;
        } else {
            //recalculate the height so the page_height is shrunk to the minimum but still wrapping all components
            //otherwise a user can drag a component far down then up again and a too high value will be stored to db.
            designer.page_height = 0;
            designer.scan();
            designer.draw();
            console.log("Dashboard HTML content: " + currentcontent);
            var result=dashboard.setcontent(dashid,currentcontent,designer.page_height)
            success = result.success;
        }

        if (success) {
            $("#save-dashboard").attr('class','btn btn-success').text('<?php echo _("Saved") ?>');
            $("#save-dashboard").attr("title","<?php echo _("Items Saved") ?>");
            lastsavecontent = currentcontent;
        } else {
            alert('ERROR: Could not save Dashboard. '+result.message);
        }
    });

    $(window).resize(function(){
        designer.draw();
    });
</script>





<!-- Help Modal -->
<style>
    #open-widget-help-modal {
        position: absolute;
        margin: 0 .4em;
    }
    #widget-help-modal{
        z-index:1051;
    }
    .widget-help-backdrop {
        z-index:1050;
        background-color: #FFF;
    }
    @media(min-width:767px) {
        .modal.modal-wide {
            width: 750px;
            margin-left: -375px;
        }
    }
</style>

<div id="widget-help-modal" class="modal modal-wide hide fade" 
    tabindex="-1" role="dialog"
    aria-labelledby="widget-help-label" 
    aria-hidden="true"
>
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="widget-help-label">Readme</h3>
  </div>
  <div class="modal-body"></div>
  <div class="modal-footer">
    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>


<script src="<?php echo $path ?>Modules/dashboard/lib/showdown.min.js"></script>
<script>
$(function() {
    showdown.setFlavor('github');
    var converter = new showdown.Converter();

    /**
     * load helpfile as markdown and render as html in modal overlay
     * supply url in opening button data. eg: [data-help-file="README.md"]
     * if directly linking to image, <img> tag created to display the image
     * image paths (src) are re-written to be absolute urls
     */
    $('#widget-help-modal').on('show', function(event) {
        var $modal = $(this)

        // $backdrop property not available initially
        setTimeout(function() {
            $modal.data('modal').$backdrop.addClass('widget-help-backdrop');
        }, 0);
        var button = $('[data-target="#' + $modal.attr('id') + '"]');
        var modalBody = $modal.find('.modal-body');
        var file_path = button.data('help-file');
        var base = path + 'Modules/dashboard/widget/';
        var file_url = absolute(base, file_path);

        var widget_base = file_url.split('/').slice(0,-1).join('/') + '/';
        var supported_images = ['.png','.gif','.jpg','.jpeg'];
        var file_name = file_path.split('?').slice(0,file_path.indexOf('?')>-1?-1:1).join('').split('/').pop().toLowerCase();
        var file_extention = '.' + file_name.split('.').pop();
        // if button links to remote file...
        if(file_path) {
            if(supported_images.indexOf(file_extention) > -1) {
            // use <img> tag if remote is image - link to fullsize 
                $('<img>').appendTo(modalBody).attr('src', file_path).wrap('<a target="_blank" href="' + file_url + '"></a>');
            } else {
                // download <html>
                $.get(file_url, function(html, status, xhr) {
                    var type = xhr.getResponseHeader("content-type") || "";
                    if(type === 'text/markdown') {
                        // convert to html if help text is markdown
                        html = converter.makeHtml(html);
                    }
                    modalBody.html(html);
                    // replace relative image/link paths
                    modalBody.find('a,img').each(function(index, elem) {
                        var $elem = $(elem);
                        var absolute_url;
                        if(elem.tagName === 'A') {
                            absolute_url = absolute(widget_base, $elem.attr('href'));
                            $elem.attr('href', absolute_url);
                        } else {
                            absolute_url = absolute(widget_base, $elem.attr('src'));
                            $elem.attr('src', absolute_url);
                        }
                    });
                });
            }
        }
    })
});

/**
 * add base path to relative string
 * checks for ../ and ./
 * returns original if no relative url passed
 * @param {String} base url used as the 'base' for relative url convertion
 * @param {String} relative path to be converted
 * @return {String}
 */
function absolute(base, relative) {
    if(relative.match(/^(https?|\/)/g)) return relative;
    var stack = base.split("/"),
        parts = relative.split("/");
    stack.pop(); // remove current file name (or empty string)
                 // (omit if "base" is the current folder without trailing slash)
    for (var i=0; i<parts.length; i++) {
        if (parts[i] == ".")
            continue;
        if (parts[i] == "..")
            stack.pop();
        else
            stack.push(parts[i]);
    }
    return stack.join("/");
}
</script>

<svg aria-hidden="true" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <defs>
        <symbol id="icon-help" viewBox="0 0 32 32">
            <!--title>help</title-->
            <path d="M20.063 15c0.75-0.75 1.25-1.813 1.25-3 0-2.938-2.375-5.313-5.313-5.313s-5.313 2.375-5.313 5.313h2.625c0-1.438 1.25-2.688 2.688-2.688s2.688 1.25 2.688 2.688c0 0.75-0.313 1.375-0.813 1.875l-1.625 1.688c-0.938 1-1.563 2.313-1.563 3.75v0.688h2.625c0-2 0.625-2.75 1.563-3.75zM17.313 25.313v-2.625h-2.625v2.625h2.625zM16 2.688c7.375 0 13.313 5.938 13.313 13.313s-5.938 13.313-13.313 13.313-13.313-5.938-13.313-13.313 5.938-13.313 13.313-13.313z"></path>
        </symbol>
    </defs>
</svg>
