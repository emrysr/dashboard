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
                <button id="open-widget-help-modal" role="button" class="btn d-none" onclick="readme.open(event);">
                  <svg class="icon icon-help"><use xlink:href="#icon-help"></use></svg>
                </button>
            </h3>
        </div>
        <div id="widget_options_body" class="modal-body"></div>
        <div class="modal-footer">
            <p class="pull-left"><small class="muted"><em class="widget-name"></em></small></p>
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
    .widget-help-modal{
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
    .slide-down-enter-active {
        animation: .6s cubic-bezier(0.2, 0.6, 0.3, 1) slide-down;
    }
    .slide-down-leave-active {
        animation: .4s slide-down reverse;
    }
    @keyframes slide-down {
        0% {
            top: -25%;
        }
        100% {
            top: 10%;
        }
    }
    .fade-enter-active {
        animation: fade-in .5s;
    }
    .fade-leave-active {
        animation: fade-in .5s reverse;
    }
    @keyframes fade-in {
        0% {
            opacity: 0;
        }
        100% {
            opacity: .8;
        }
    }
</style>

<div id="readme" v-cloak>
    <template>
        <transition name="slide-down">
        <div v-if="!hidden" @click.stop class="modal modal-wide widget-help-modal"
            tabindex="-1" role="dialog"
            aria-labelledby="widget-help-label" 
            aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" @click="close" aria-hidden="true">×</button>
                <h3 id="widget-help-label"><?php echo _('Readme') ?></h3>
            </div>
            <div class="modal-body" v-html="html"></div>
            <div class="modal-footer">
                <button class="btn btn-primary" @click="close" aria-hidden="true">Close</button>
            </div>
        </div>
        </transition>
        <transition name="fade">
            <div v-if="!hidden" class="modal-backdrop widget-help-backdrop"></div>
        </transition>
    </template>
</div>


<script src="<?php echo $path ?>Lib/vue.min.js"></script>
<script src="<?php echo $path ?>Modules/dashboard/lib/showdown.min.js"></script>
<script>
showdown.setFlavor('github');
var converter = new showdown.Converter();

function clearSelection() {
    if (window.getSelection) {
        window.getSelection().removeAllRanges();
    } else if (document.selection) {
        document.selection.empty();
    }
}

const readme = new Vue({
    el:'#readme',
    data: {
        file_path: '',
        hidden: true,
        widgets_path: 'Modules/dashboard/widget/',
        supported_images: ['.png','.gif','.jpg','.jpeg'],
        cache: {}
    },
    watch: {
        file_path: function(newValue) {
            this.setCache();
        },
        cache: {
            deep: true,
            handler: function(newValue) {
                // todo: set something
            }
        }
    },
    computed: {
        html: {
            get: function() {
                return this.cache[this.current_widget] || '';
            },
            set: function() {
                return true;
            }
        },
        base: function () {
            return path + this.widgets_path;
        },
        widget_base: function() { // widget directory
            var base = this.file_url.split('/').slice(0,-1).join('/')
            return  base !== '' ? base + '/': '';
        },
        file_url: function() { // full url
            return absolute(this.base, this.file_path)
        },
        file_name: function() {
            return this.file_path.split('?').slice(0,this.file_path.indexOf('?')>-1?-1:1).join('').split('/').pop().toLowerCase();
        },
        file_extention: function() {
            var extention = this.file_name.split('.').pop()
            return extention !== '' ? '.' + extention: '';
        },
        current_widget: function() {
            return this.file_path.toLowerCase().replace(this.file_name,'').split('/').filter(Boolean).pop();
        }
    },
    methods: {
        setCache: function() {
            if(!this.cache[this.current_widget]) {
                // if not already downloaded...
                if(this.supported_images.indexOf(this.file_extention) > -1) {
                    this.cache[this.current_widget] = this.getImageLink();
                    // show overlay
                    this.hidden = false;
                } else {
                    var vm = this;
                    $.get(this.file_url)
                    .done(function(html, status, xhr) {
                        var container = document.createElement("div");
                        var type = xhr.getResponseHeader("content-type") || "";
                        // convert from markdown format
                        if(type === 'text/markdown') {
                            html = converter.makeHtml(html);
                        }
                        // replace relative image/link paths
                        container.innerHTML = html;
                        container = vm.replaceReativeLinks(container);
                        if(vm.current_widget) {
                            vm.cache[vm.current_widget] = container.innerHTML;
                            // show overlay
                            vm.hidden = false;
                        }
                    });
                }
            }
        },
        getImageLink: function() {
            // use <img> tag if remote is image - link to fullsize
            var img = document.createElement("img");
            img.src = this.file_url;
            img.alt = this.widget;
            return '<a target="_blank" href="' + this.file_url + '">' + img.outerHTML + '</a>';
        },
        replaceReativeLinks: function(container) {
            var elements = container.querySelectorAll('a, img');
            var vm = this;
            elements.forEach(function(elem) {
                if(elem.tagName === 'A') {
                    elem.href = absolute(vm.widget_base, elem.getAttribute("href"));
                } else {
                    elem.src = absolute(vm.widget_base, elem.getAttribute("src"));
                }
            });
            return container;
        },
        /**
         * open the modal and get the widget's readme
         */
        open: function(event) {
            var button = event.currentTarget;
            event.stopPropagation();
            if(button.dataset.helpFile) {
                if(this.file_path === button.dataset.helpFile) {
                    this.hidden = false;
                } else {
                    this.file_path = button.dataset.helpFile;
                }
                clearSelection(); // fix bug with text being highlighted as modal fades in.
            }
        },
        close: function(event) {
            this.hidden = true;
            var button = event.currentTarget;
            event.stopPropagation();
            // this.file_path = '';
        }
    }
})

// hide on click
document.addEventListener('click', readme.close, false);


/**
 * add base url to relative path
 * 
 * checks for ../ and ./
 * returns path if not relative or empty
 * 
 * @param {String} base url used as the 'base' for relative url convertion
 * @param {String} path to be converted
 * @return {String}
 */
function absolute(base, path) {
    if(path.match(/^(https?|\/)/g)) return path;
    if(!path) return '';

    var stack = base.split("/"),
        parts = path.split("/");
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
