/*! AdminLTE app.js
 * ================
 * Main JS application file for AdminLTE v2. This file
 * should be included in all pages. It controls some layout
 * options and implements exclusive AdminLTE plugins.
 *
 * @Author  Almsaeed Studio
 * @Support <http://www.almsaeedstudio.com>
 * @Email   <abdullah@almsaeedstudio.com>
 * @version 2.3.8
 * @license MIT <http://opensource.org/licenses/MIT>
 */

//Make sure jQuery has been loaded before app.js
if (typeof jQuery === "undefined") {
  throw new Error("AdminLTE requires jQuery");
}

/* AdminLTE
 *
 * @type Object
 * @description $.AdminLTE is the main object for the template's app.
 *              It's used for implementing functions and options related
 *              to the template. Keeping everything wrapped in an object
 *              prevents conflict with other plugins and is a better
 *              way to organize our code.
 */
$.AdminLTE = {};

/* --------------------
 * - AdminLTE Options -
 * --------------------
 * Modify these options to suit your implementation
 */
$.AdminLTE.options = {
  //Add slimscroll to navbar menus
  //This requires you to load the slimscroll plugin
  //in every page before app.js
  navbarMenuSlimscroll: true,
  navbarMenuSlimscrollWidth: "3px", //The width of the scroll bar
  navbarMenuHeight: "200px", //The height of the inner menu
  //General animation speed for JS animated elements such as box collapse/expand and
  //sidebar treeview slide up/down. This options accepts an integer as milliseconds,
  //'fast', 'normal', or 'slow'
  animationSpeed: 500,
  //Sidebar push menu toggle button selector
  sidebarToggleSelector: "[data-toggle='offcanvas']",
  //Activate sidebar push menu
  sidebarPushMenu: true,
  //Activate sidebar slimscroll if the fixed layout is set (requires SlimScroll Plugin)
  sidebarSlimScroll: true,
  //Enable sidebar expand on hover effect for sidebar mini
  //This option is forced to true if both the fixed layout and sidebar mini
  //are used together
  sidebarExpandOnHover: false,
  //BoxRefresh Plugin
  enableBoxRefresh: true,
  //Bootstrap.js tooltip
  enableBSToppltip: true,
  BSTooltipSelector: "[data-toggle='tooltip']",
  //Enable Fast Click. Fastclick.js creates a more
  //native touch experience with touch devices. If you
  //choose to enable the plugin, make sure you load the script
  //before AdminLTE's app.js
  enableFastclick: false,
  //Control Sidebar Tree views
  enableControlTreeView: true,
  //Control Sidebar Options
  enableControlSidebar: true,
  controlSidebarOptions: {
    //Which button should trigger the open/close event
    toggleBtnSelector: "[data-toggle='control-sidebar']",
    //The sidebar selector
    selector: ".control-sidebar",
    //Enable slide over content
    slide: true
  },
  //Box Widget Plugin. Enable this plugin
  //to allow boxes to be collapsed and/or removed
  enableBoxWidget: true,
  //Box Widget plugin options
  boxWidgetOptions: {
    boxWidgetIcons: {
      //Collapse icon
      collapse: 'fa-minus',
      //Open icon
      open: 'fa-plus',
      //Remove icon
      remove: 'fa-times'
    },
    boxWidgetSelectors: {
      //Remove button selector
      remove: '[data-widget="remove"]',
      //Collapse button selector
      collapse: '[data-widget="collapse"]'
    }
  },
  //Direct Chat plugin options
  directChat: {
    //Enable direct chat by default
    enable: true,
    //The button to open and close the chat contacts pane
    contactToggleSelector: '[data-widget="chat-pane-toggle"]'
  },
  //Define the set of colors to use globally around the website
  colors: {
    lightBlue: "#3c8dbc",
    red: "#f56954",
    green: "#00a65a",
    aqua: "#00c0ef",
    yellow: "#f39c12",
    blue: "#0073b7",
    navy: "#001F3F",
    teal: "#39CCCC",
    olive: "#3D9970",
    lime: "#01FF70",
    orange: "#FF851B",
    fuchsia: "#F012BE",
    purple: "#8E24AA",
    maroon: "#D81B60",
    black: "#222222",
    gray: "#d2d6de"
  },
  //The standard screen sizes that bootstrap uses.
  //If you change these in the variables.less file, change
  //them here too.
  screenSizes: {
    xs: 480,
    sm: 768,
    md: 992,
    lg: 1200
  }
};

/* ------------------
 * - Implementation -
 * ------------------
 * The next block of code implements AdminLTE's
 * functions and plugins as specified by the
 * options above.
 */
$(function () {
  "use strict";

  //Fix for IE page transitions
  $("body").removeClass("hold-transition");

  //Extend options if external options exist
  if (typeof AdminLTEOptions !== "undefined") {
    $.extend(true,
      $.AdminLTE.options,
      AdminLTEOptions);
  }

  //Easy access to options
  var o = $.AdminLTE.options;

  //Set up the object
  _init();

  //Activate the layout maker
  $.AdminLTE.layout.activate();

  //Enable sidebar tree view controls
  if (o.enableControlTreeView) {
    $.AdminLTE.tree('.sidebar');
  }

  //Enable control sidebar
  if (o.enableControlSidebar) {
    $.AdminLTE.controlSidebar.activate();
  }

  //Add slimscroll to navbar dropdown
  if (o.navbarMenuSlimscroll && typeof $.fn.slimscroll != 'undefined') {
    $(".navbar .menu").slimscroll({
      height: o.navbarMenuHeight,
      alwaysVisible: false,
      size: o.navbarMenuSlimscrollWidth
    }).css("width", "100%");
  }

  //Activate sidebar push menu
  if (o.sidebarPushMenu) {
    $.AdminLTE.pushMenu.activate(o.sidebarToggleSelector);
  }

  //Activate Bootstrap tooltip
  if (o.enableBSToppltip) {
    $('body').tooltip({
      selector: o.BSTooltipSelector,
      container: 'body'
    });
  }

  //Activate box widget
  if (o.enableBoxWidget) {
    $.AdminLTE.boxWidget.activate();
  }

  //Activate fast click
  if (o.enableFastclick && typeof FastClick != 'undefined') {
    FastClick.attach(document.body);
  }

  //Activate direct chat widget
  if (o.directChat.enable) {
    $(document).on('click', o.directChat.contactToggleSelector, function () {
      var box = $(this).parents('.direct-chat').first();
      box.toggleClass('direct-chat-contacts-open');
    });
  }

  /*
   * INITIALIZE BUTTON TOGGLE
   * ------------------------
   */
  $('.btn-group[data-toggle="btn-toggle"]').each(function () {
    var group = $(this);
    $(this).find(".btn").on('click', function (e) {
      group.find(".btn.active").removeClass("active");
      $(this).addClass("active");
      e.preventDefault();
    });

  });
});

/* ----------------------------------
 * - Initialize the AdminLTE Object -
 * ----------------------------------
 * All AdminLTE functions are implemented below.
 */
function _init() {
  'use strict';
  /* Layout
   * ======
   * Fixes the layout height in case min-height fails.
   *
   * @type Object
   * @usage $.AdminLTE.layout.activate()
   *        $.AdminLTE.layout.fix()
   *        $.AdminLTE.layout.fixSidebar()
   */
  $.AdminLTE.layout = {
    activate: function () {
      var _this = this;
      _this.fix();
      _this.fixSidebar();
      $('body, html, .wrapper').css('height', 'auto');
      $(window, ".wrapper").resize(function () {
        _this.fix();
        _this.fixSidebar();
      });
    },
    fix: function () {
      // Remove overflow from .wrapper if layout-boxed exists
      $(".layout-boxed > .wrapper").css('overflow', 'hidden');
      //Get window height and the wrapper height
      var footer_height = $('.main-footer').outerHeight() || 0;
      var neg = $('.main-header').outerHeight() + footer_height;
      var window_height = $(window).height();
      var sidebar_height = $(".sidebar").height() || 0;
      //Set the min-height of the content and sidebar based on the
      //the height of the document.
      if ($("body").hasClass("fixed")) {
        $(".content-wrapper, .right-side").css('min-height', window_height - footer_height);
      } else {
        var postSetWidth;
        if (window_height >= sidebar_height) {
          $(".content-wrapper, .right-side").css('min-height', window_height - neg);
          postSetWidth = window_height - neg;
        } else {
          $(".content-wrapper, .right-side").css('min-height', sidebar_height);
          postSetWidth = sidebar_height;
        }

        //Fix for the control sidebar height
        var controlSidebar = $($.AdminLTE.options.controlSidebarOptions.selector);
        if (typeof controlSidebar !== "undefined") {
          if (controlSidebar.height() > postSetWidth)
            $(".content-wrapper, .right-side").css('min-height', controlSidebar.height());
        }

      }
    },
    fixSidebar: function () {
      //Make sure the body tag has the .fixed class
      if (!$("body").hasClass("fixed")) {
        if (typeof $.fn.slimScroll != 'undefined') {
          $(".sidebar").slimScroll({destroy: true}).height("auto");
        }
        return;
      } else if (typeof $.fn.slimScroll == 'undefined' && window.console) {
        window.console.error("Error: the fixed layout requires the slimscroll plugin!");
      }
      //Enable slimscroll for fixed layout
      if ($.AdminLTE.options.sidebarSlimScroll) {
        if (typeof $.fn.slimScroll != 'undefined') {
          //Destroy if it exists
          $(".sidebar").slimScroll({destroy: true}).height("auto");
          //Add slimscroll
          $(".sidebar").slimScroll({
            height: ($(window).height() - $(".main-header").height()) + "px",
            color: "rgba(0,0,0,0.2)",
            size: "3px"
          });
        }
      }
    }
  };

  /* PushMenu()
   * ==========
   * Adds the push menu functionality to the sidebar.
   *
   * @type Function
   * @usage: $.AdminLTE.pushMenu("[data-toggle='offcanvas']")
   */
  $.AdminLTE.pushMenu = {
    activate: function (toggleBtn) {
      //Get the screen sizes
      var screenSizes = $.AdminLTE.options.screenSizes;

      //Enable sidebar toggle
      $(document).on('click', toggleBtn, function (e) {
        e.preventDefault();

        //Enable sidebar push menu
        if ($(window).width() > (screenSizes.sm - 1)) {
          if ($("body").hasClass('sidebar-collapse')) {
            $("body").removeClass('sidebar-collapse').trigger('expanded.pushMenu');
          } else {
            $("body").addClass('sidebar-collapse').trigger('collapsed.pushMenu');
          }
        }
        //Handle sidebar push menu for small screens
        else {
          if ($("body").hasClass('sidebar-open')) {
            $("body").removeClass('sidebar-open').removeClass('sidebar-collapse').trigger('collapsed.pushMenu');
          } else {
            $("body").addClass('sidebar-open').trigger('expanded.pushMenu');
          }
        }
      });

      $(".content-wrapper").click(function () {
        //Enable hide menu when clicking on the content-wrapper on small screens
        if ($(window).width() <= (screenSizes.sm - 1) && $("body").hasClass("sidebar-open")) {
          $("body").removeClass('sidebar-open');
        }
      });

      //Enable expand on hover for sidebar mini
      if ($.AdminLTE.options.sidebarExpandOnHover
        || ($('body').hasClass('fixed')
        && $('body').hasClass('sidebar-mini'))) {
        this.expandOnHover();
      }
    },
    expandOnHover: function () {
      var _this = this;
      var screenWidth = $.AdminLTE.options.screenSizes.sm - 1;
      //Expand sidebar on hover
      $('.main-sidebar').hover(function () {
        if ($('body').hasClass('sidebar-mini')
          && $("body").hasClass('sidebar-collapse')
          && $(window).width() > screenWidth) {
          _this.expand();
        }
      }, function () {
        if ($('body').hasClass('sidebar-mini')
          && $('body').hasClass('sidebar-expanded-on-hover')
          && $(window).width() > screenWidth) {
          _this.collapse();
        }
      });
    },
    expand: function () {
      $("body").removeClass('sidebar-collapse').addClass('sidebar-expanded-on-hover');
    },
    collapse: function () {
      if ($('body').hasClass('sidebar-expanded-on-hover')) {
        $('body').removeClass('sidebar-expanded-on-hover').addClass('sidebar-collapse');
      }
    }
  };

  /* Tree()
   * ======
   * Converts the sidebar into a multilevel
   * tree view menu.
   *
   * @type Function
   * @Usage: $.AdminLTE.tree('.sidebar')
   */
  $.AdminLTE.tree = function (menu) {
    var _this = this;
    var animationSpeed = $.AdminLTE.options.animationSpeed;
    $(document).off('click', menu + ' li a')
      .on('click', menu + ' li a', function (e) {
        //Get the clicked link and the next element
        var $this = $(this);
        var checkElement = $this.next();

        //Check if the next element is a menu and is visible
        if ((checkElement.is('.treeview-menu')) && (checkElement.is(':visible')) && (!$('body').hasClass('sidebar-collapse'))) {
          //Close the menu
          checkElement.slideUp(animationSpeed, function () {
            checkElement.removeClass('menu-open');
            //Fix the layout in case the sidebar stretches over the height of the window
            //_this.layout.fix();
          });
          checkElement.parent("li").removeClass("active");
        }
        //If the menu is not visible
        else if ((checkElement.is('.treeview-menu')) && (!checkElement.is(':visible'))) {
          //Get the parent menu
          var parent = $this.parents('ul').first();
          //Close all open menus within the parent
          var ul = parent.find('ul:visible').slideUp(animationSpeed);
          //Remove the menu-open class from the parent
          ul.removeClass('menu-open');
          //Get the parent li
          var parent_li = $this.parent("li");

          //Open the target menu and add the menu-open class
          checkElement.slideDown(animationSpeed, function () {
            //Add the class active to the parent li
            checkElement.addClass('menu-open');
            parent.find('li.active').removeClass('active');
            parent_li.addClass('active');
            //Fix the layout in case the sidebar stretches over the height of the window
            _this.layout.fix();
          });
        }
        //if this isn't a link, prevent the page from being redirected
        if (checkElement.is('.treeview-menu')) {
          e.preventDefault();
        }
      });
  };

  /* ControlSidebar
   * ==============
   * Adds functionality to the right sidebar
   *
   * @type Object
   * @usage $.AdminLTE.controlSidebar.activate(options)
   */
  $.AdminLTE.controlSidebar = {
    //instantiate the object
    activate: function () {
      //Get the object
      var _this = this;
      //Update options
      var o = $.AdminLTE.options.controlSidebarOptions;
      //Get the sidebar
      var sidebar = $(o.selector);
      //The toggle button
      var btn = $(o.toggleBtnSelector);

      //Listen to the click event
      btn.on('click', function (e) {
        e.preventDefault();
        //If the sidebar is not open
        if (!sidebar.hasClass('control-sidebar-open')
          && !$('body').hasClass('control-sidebar-open')) {
          //Open the sidebar
          _this.open(sidebar, o.slide);
        } else {
          _this.close(sidebar, o.slide);
        }
      });

      //If the body has a boxed layout, fix the sidebar bg position
      var bg = $(".control-sidebar-bg");
      _this._fix(bg);

      //If the body has a fixed layout, make the control sidebar fixed
      if ($('body').hasClass('fixed')) {
        _this._fixForFixed(sidebar);
      } else {
        //If the content height is less than the sidebar's height, force max height
        if ($('.content-wrapper, .right-side').height() < sidebar.height()) {
          _this._fixForContent(sidebar);
        }
      }
    },
    //Open the control sidebar
    open: function (sidebar, slide) {
      //Slide over content
      if (slide) {
        sidebar.addClass('control-sidebar-open');
      } else {
        //Push the content by adding the open class to the body instead
        //of the sidebar itself
        $('body').addClass('control-sidebar-open');
      }
    },
    //Close the control sidebar
    close: function (sidebar, slide) {
      if (slide) {
        sidebar.removeClass('control-sidebar-open');
      } else {
        $('body').removeClass('control-sidebar-open');
      }
    },
    _fix: function (sidebar) {
      var _this = this;
      if ($("body").hasClass('layout-boxed')) {
        sidebar.css('position', 'absolute');
        sidebar.height($(".wrapper").height());
        if (_this.hasBindedResize) {
          return;
        }
        $(window).resize(function () {
          _this._fix(sidebar);
        });
        _this.hasBindedResize = true;
      } else {
        sidebar.css({
          'position': 'fixed',
          'height': 'auto'
        });
      }
    },
    _fixForFixed: function (sidebar) {
      sidebar.css({
        'position': 'fixed',
        'max-height': '100%',
        'overflow': 'auto',
        'padding-bottom': '50px'
      });
    },
    _fixForContent: function (sidebar) {
      $(".content-wrapper, .right-side").css('min-height', sidebar.height());
    }
  };

  /* BoxWidget
   * =========
   * BoxWidget is a plugin to handle collapsing and
   * removing boxes from the screen.
   *
   * @type Object
   * @usage $.AdminLTE.boxWidget.activate()
   *        Set all your options in the main $.AdminLTE.options object
   */
  $.AdminLTE.boxWidget = {
    selectors: $.AdminLTE.options.boxWidgetOptions.boxWidgetSelectors,
    icons: $.AdminLTE.options.boxWidgetOptions.boxWidgetIcons,
    animationSpeed: $.AdminLTE.options.animationSpeed,
    activate: function (_box) {
      var _this = this;
      if (!_box) {
        _box = document; // activate all boxes per default
      }
      //Listen for collapse event triggers
      $(_box).on('click', _this.selectors.collapse, function (e) {
        e.preventDefault();
        _this.collapse($(this));
      });

      //Listen for remove event triggers
      $(_box).on('click', _this.selectors.remove, function (e) {
        e.preventDefault();
        _this.remove($(this));
      });
    },
    collapse: function (element) {
      var _this = this;
      //Find the box parent
      var box = element.parents(".box").first();
      //Find the body and the footer
      var box_content = box.find("> .box-body, > .box-footer, > form  >.box-body, > form > .box-footer");
      if (!box.hasClass("collapsed-box")) {
        //Convert minus into plus
        element.children(":first")
          .removeClass(_this.icons.collapse)
          .addClass(_this.icons.open);
        //Hide the content
        box_content.slideUp(_this.animationSpeed, function () {
          box.addClass("collapsed-box");
        });
      } else {
        //Convert plus into minus
        element.children(":first")
          .removeClass(_this.icons.open)
          .addClass(_this.icons.collapse);
        //Show the content
        box_content.slideDown(_this.animationSpeed, function () {
          box.removeClass("collapsed-box");
        });
      }
    },
    remove: function (element) {
      //Find the box parent
      var box = element.parents(".box").first();
      box.slideUp(this.animationSpeed);
    }
  };
}

/* ------------------
 * - Custom Plugins -
 * ------------------
 * All custom plugins are defined below.
 */

/*
 * BOX REFRESH BUTTON
 * ------------------
 * This is a custom plugin to use with the component BOX. It allows you to add
 * a refresh button to the box. It converts the box's state to a loading state.
 *
 * @type plugin
 * @usage $("#box-widget").boxRefresh( options );
 */
var notif, CURFORM = null, CROPPED = false;
var Constants = {}; 
 
(function ($) {

  "use strict";

  $.fn.boxRefresh = function (options) {

    // Render options
    var settings = $.extend({
      //Refresh button selector
      trigger: ".refresh-btn",
      //File source to be loaded (e.g: ajax/src.php)
      source: "",
      //Callbacks
      onLoadStart: function (box) {
        return box;
      }, //Right after the button has been clicked
      onLoadDone: function (box) {
        return box;
      } //When the source has been loaded

    }, options);

    //The overlay
    var overlay = $('<div class="overlay"><div class="fa fa-refresh fa-spin"></div></div>');

    return this.each(function () {
      //if a source is specified
      if (settings.source === "") {
        if (window.console) {
          window.console.log("Please specify a source first - boxRefresh()");
        }
        return;
      }
      //the box
      var box = $(this);
      //the button
      var rBtn = box.find(settings.trigger).first();

      //On trigger click
      rBtn.on('click', function (e) {
        e.preventDefault();
        //Add loading overlay
        start(box);

        //Perform ajax call
        box.find(".box-body").load(settings.source, function () {
          done(box);
        });
      });
    });

    function start(box) {
      //Add overlay and loading img
      box.append(overlay);

      settings.onLoadStart.call(box);
    }

    function done(box) {
      //Remove overlay and loading img
      box.find(overlay).remove();

      settings.onLoadDone.call(box);
    }

  };

})(jQuery);

/*
 * EXPLICIT BOX CONTROLS
 * -----------------------
 * This is a custom plugin to use with the component BOX. It allows you to activate
 * a box inserted in the DOM after the app.js was loaded, toggle and remove box.
 *
 * @type plugin
 * @usage $("#box-widget").activateBox();
 * @usage $("#box-widget").toggleBox();
 * @usage $("#box-widget").removeBox();
 */
(function ($) {

  'use strict';

  $.fn.activateBox = function () {
    $.AdminLTE.boxWidget.activate(this);
  };

  $.fn.toggleBox = function () {
    var button = $($.AdminLTE.boxWidget.selectors.collapse, this);
    $.AdminLTE.boxWidget.collapse(button);
  };

  $.fn.removeBox = function () {
    var button = $($.AdminLTE.boxWidget.selectors.remove, this);
    $.AdminLTE.boxWidget.remove(button);
  };

})(jQuery);

/*
 * TODO LIST CUSTOM PLUGIN
 * -----------------------
 * This plugin depends on iCheck plugin for checkbox and radio inputs
 *
 * @type plugin
 * @usage $("#todo-widget").todolist( options );
 */

(function ($) {

  'use strict';	
  $.fn.todolist = function (options) {
    // Render options
    var settings = $.extend({
      //When the user checks the input
      onCheck: function (ele) {
        return ele;
      },
      //When the user unchecks the input
      onUncheck: function (ele) {
        return ele;
      }
    }, options);

    return this.each(function () {

      if (typeof $.fn.iCheck != 'undefined') {
        $('input', this).on('ifChecked', function () {
          var ele = $(this).parents("li").first();
          ele.toggleClass("done");
          settings.onCheck.call(ele);
        });

        $('input', this).on('ifUnchecked', function () {
          var ele = $(this).parents("li").first();
          ele.toggleClass("done");
          settings.onUncheck.call(ele);
        });
      } else {
        $('input', this).on('change', function () {
          var ele = $(this).parents("li").first();
          ele.toggleClass("done");
          if ($('input', ele).is(":checked")) {
            settings.onCheck.call(ele);
          } else {
            settings.onUncheck.call(ele);
          }
        });
      }
    });
  };
  
	
  
}(jQuery));


var CKEDITOR = CKEDITOR != undefined ? CKEDITOR : null;
var notif, CURFORM = null, CROPPED = false;
var Constants = {};
$.ajaxSetup({
    dataType: 'JSON',
    method: 'POST',
    cache: true,
});

(function (a) {
    a.CBB = {DEBUG: true, data: {}};
    a.CBB.loaderImg = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    a.location.CurPage = null;
    a.location.auto = true;
    a.location.PINCODE = null;
    a.location.position = {latitude: null, longitude: null};
    a.location.BASE = $('base').attr('href');
    a.location.ADMIN = a.location.BASE + 'admin/';
    a.location.MERCHANT = a.location.BASE + 'merchant/';
    a.location.DSA = a.location.BASE + 'dsa/';
    a.location.USER = a.location.BASE;
    a.location.RETAILER = a.location.BASE + 'retailer/';
    a.location.AddToUrl = function (title, url) {
        if (typeof (a.history.pushState) !== undefined) {
            var href = a.location.href, c_url = (href.indexOf('?') > 1) ? href.substring(0, href.indexOf('?')) : href;
            a.location.CurPage = {page: title, url: c_url + ((url !== '') ? '?' + url : '')};
            a.document.title = title;
            a.history.pushState(a.location.CurPage, a.location.CurPage.title, a.location.CurPage.url);
        }
    };
    a.location.ChangeUrl = function (title, url, op) {
        op = op || null;
        if (typeof (a.history.pushState) !== undefined) {
            a.location.CurPage = op != null ? op : {page: title, url: url};
            a.document.title = title;
            a.history.pushState(a.location.CurPage, a.location.CurPage.title, a.location.CurPage.url);
        }
    };
    a.location.GoToPrevious = function () {
        if (a.location.CurPage !== null) {
            a.document.title = a.location.CurPage.title;
            a.history.pushState(a.location.CurPage, a.location.CurPage.title, a.location.CurPage.url);
            a.location.CurPage = null;
        }
    };
    $(a).on('popstate', function (e) {
        if (e.originalEvent.state !== null && e.originalEvent.state.setContent) {
            a.document.title = e.originalEvent.state.page;
            $('.xbp-title').html(e.originalEvent.state.title);
            $('.xbp-icon-title').html([$('<i>', {class: 'fa fa-' + e.originalEvent.state.title_icon}), e.originalEvent.state.title]);
            $('#xbp-styles').html(e.originalEvent.state.styles);
            $('#xbp-breadcrumb').html(e.originalEvent.state.breadcrumb);
            $('#xbp-content').html(e.originalEvent.state.content);
            $('#xbp-scripts').html(e.originalEvent.state.scripts);
            //a.history.pushState(e.originalEvent.state, e.originalEvent.state.page, e.originalEvent.state.url);
        }
    });
    a.Error.stackTraceLimit = a.Infinity;
    a.location.PINCODE = (a.localStorage.getItem('location_settings') !== null && a.localStorage.getItem('location_settings') !== undefined && a.localStorage.getItem('location_settings') !== 'undefined') ? a.localStorage.getItem('location_settings') : null;
    a.location.setLocationSetings = function (status, pincode) {
        localStorage.setItem('location_settings', {auto: status, pincode: pincode});
        a.location.auto = status;
        a.location.PINCODE = pincode;
    };
    a.document.addEventListener('invalid', function (event) {
        event.preventDefault();
        a.CBB.customValidation(event);
    }, true);
    a.document.addEventListener('input', function (event) {
        a.CBB.customValidation(event);
    }, true);
    a.document.addEventListener('change', function (event) {
		console.log(event);
        a.CBB.customValidation(event);
    }, true);
    if (a.document.querySelector('[type="checkbox"]') != null) {
        a.document.querySelector('[type="checkbox"]').addEventListener('click', function (event) {
            a.CBB.customValidation(event);
        }, true);
    }
    a.CBB.customValidation = function (e) {
        var msg = null, _this = null;
        if (e.srcElement != undefined) {
            _this = e.srcElement;
        }
        if (e.target != undefined) {
            _this = e.target;
        }
		console.log(e);
        if (_this != null) {
            $(':submit', _this.form).attr('disabled', true);
            if (_this.dataset['errMsgTo'] != undefined) {
                $(_this.dataset['errMsgTo'], _this.form).attr({for : '', class: ''}).empty();
            }
            else {
                $('span[for="' + _this.name + '"]', _this.form).remove();
            }
            if (_this.getAttribute('type') == 'file' && _this.getAttribute('accept') != undefined && _this.getAttribute('accept') != '') {
                if (! ((new RegExp('(.*?)(' + ((_this.getAttribute('accept').replace(/\./g, '')).split(',')).join('|') + ')$')).test(_this.value))) {
                    msg = _this.dataset['typemismatch'];
                    $('#' + _this.id + '-preview').attr('src', _this.dataset['default']);
                }
                else if (_this.files && _this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#' + _this.id + '-preview').attr('src', reader.result);
                    }
                    reader.readAsDataURL(_this.files[0]);
                }
            }
            $('[data-requiredwith]', _this.form).each(function (k, element) {
                var fields = $(element).data('requiredwith').split(',');
                var passed = 0;
                var checking_fields = [];
                for (var i in fields) {
                    var field = fields[i].split(':');
                    checking_fields.push(field[0]);
                    if (field[0] == _this.name && (field[1] == undefined && _this.checked || (field[1] != undefined && field[1] == _this.value))) {
                        passed ++;
                    }
                }
                if (checking_fields.indexOf(_this.name) >= 0) {
                    if (passed == fields.length) {
                        $(element).attr('required', true);
                    }
                    else {
                        $(element).removeAttr('required');
                    }
                }
            });
            return a.CBB.confirmInput(e, _this);
        }
        return false;
    };
    a.CBB.confirmInput = function (e, _this) {
        if (e.type == 'input' && e.target.name == _this.name && ! _this.validity.badInput && ! _this.validity.patternMismatch && ! _this.validity.rangeOverflow && ! _this.validity.rangeUnderflow && ! _this.validity.stepMismatch && ! _this.validity.tooLong && ! _this.validity.tooShort && ! _this.validity.typeMismatch && ! _this.validity.valueMissing && _this.dataset['confirm'] != undefined && _this.value != $('[name="' + _this.dataset['confirm'] + '"]', _this.form).val()) {
            _this.dataset['customerror'] = _this.dataset['confirmErr'];
            _this.setCustomValidity(_this.dataset['confirmErr']);
            return  a.CBB.updateErrMsg(_this, false);
        }
        else {
            return a.CBB.checkExist(e, _this);
        }
    };
    a.CBB.checkExist = function (e, _this) {
        if (e.type == 'change' && e.target.name == _this.name && ! _this.validity.badInput && ! _this.validity.patternMismatch && ! _this.validity.rangeOverflow && ! _this.validity.rangeUnderflow && ! _this.validity.stepMismatch && ! _this.validity.tooLong && ! _this.validity.tooShort && ! _this.validity.typeMismatch && ! _this.validity.valueMissing && _this.dataset['checkExist'] != undefined) {
            var v = _this.dataset['checkExistData'].split(',');
            var data = {};
            for (var field in v) {
                var f = v[field].split(':');
                data[f[0]] = $(f[1]).val();
            }
            $.ajax({
                url: _this.dataset['checkExist'],
                data: data,
                success: function () {
                    _this.dataset['customerror'] = '';
                    _this.setCustomValidity('');
                    return a.CBB.updateErrMsg(_this, true);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.responseJSON.error[_this.name] != undefined) {
                        _this.dataset['customerror'] = jqXHR.responseJSON.error[_this.name].join(',');
                        _this.setCustomValidity(_this.dataset['customerror']);
                        return a.CBB.updateErrMsg(_this, false);
                    }
                }
            });
        }
        else if (_this.dataset['checkExist'] != undefined) {
            _this.setCustomValidity('');
            return a.CBB.updateErrMsg(_this, false);
        }
        else {
            _this.dataset['customerror'] = '';
            _this.setCustomValidity('');
            return a.CBB.updateErrMsg(_this, true);
        }
    };
    a.CBB.updateErrMsg = function (_this, enableFormSubmit) {
        var msg = null;
        if (! _this.validity.valid) {
            if (_this.validity.typeMismatch) {
                msg = _this.dataset['typemismatch'];
            } else if (_this.validity.badInput) {
                msg = _this.dataset['badinput'];
            } else if (_this.validity.patternMismatch) {
                msg = _this.dataset['patternmismatch'];
            } else if (_this.validity.rangeOverflow) {
                msg = _this.dataset['toolong'];
            } else if (_this.validity.rangeUnderflow) {
                msg = _this.dataset['tooshort'];
            } else if (_this.validity.stepMismatch) {
                msg = _this.dataset[''];
            } else if (_this.validity.tooLong) {
                msg = _this.dataset['toolong'];
            } else if (_this.validity.tooShort) {
                msg = _this.dataset['tooshort'];
            } else if (_this.validity.valueMissing) {
                msg = _this.dataset['valuemissing'];
            } else if (_this.validity.customError) {
                msg = _this.dataset['customerror'];
            }
            msg = msg != null ? msg : '';
            _this.setCustomValidity(msg);
        }
        if (_this.validationMessage != undefined && _this.validationMessage != '') {
            if ($('[name="' + _this.name + '"]', _this.form).length >= 1) {
                if (_this.dataset['errMsgTo'] != undefined) {
                    $(_this.dataset['errMsgTo'], _this.form).attr({for : _this.name, class: 'errmsg'}).append(_this.validationMessage);
                }
                else {
                    $('[name="' + _this.name + '"]', _this.form).after($('<span>').attr({for : _this.name, class: 'errmsg'}).html(_this.validationMessage));
                }
            }
            $(':submit', _this.form).attr('disabled', true);
            return false;
        }
        if (enableFormSubmit && _this.form.checkValidity()) {
            $(':submit', _this.form).removeAttr('disabled');
            return true;
        }
        else {
            $(':submit', _this.form).attr('disabled', true);
            return false;
        }
    };

    a.addEventListener('error', function (e) {
        console.log('error')
		if (notif !== undefined) {
			notif({
				width: 500,
				msg: "Please try again later",
				type: 'success',
				position: 'right',
				timeout: 6000,
			});
		}
    });

    a.CBB.ajaxComplete = function (event, xhr, settings) {
        var data = xhr.responseJSON; 
		console.log(xhr.status)
        $('body').addClass('loaded');
        if (xhr.status === 307 || xhr.status === 308) {
            if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {
                if (notif !== undefined) {
                    notif({
                        width: 500,
                        msg: data.msg,
                        type: 'success',
                        position: 'right',
                        timeout: 6000,
                    });
                }
            }
            if (data !== undefined && data.url !== undefined) {
                setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);
            }
        }
        else if (xhr.status === 200) {
            if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {
                if (notif !== undefined) {
                    notif({
                        width: 500,
                        msg: data.msg,
                        type: 'success',
                        position: 'right',
                        timeout: 6000,
                    });
                }
            }
        }
        else if (xhr.status === 208 || xhr.status == 205) {
            if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {
                if (notif !== undefined) {
                    notif({
                        width: 500,
                        msg: data.msg,
                        type: 'warning',
                        position: 'right',
                        timeout: 6000,
                    });
                }
            }
        }
        else if (xhr.status === 422 || xhr.status === 400 || xhr.status === 404) {
			
            if (CURFORM != undefined && CURFORM !== null && data.error !== undefined && data.error !== null) {
                CURFORM.appendLaravelError(data.error);
                CURFORM = null;
            }
            else if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {
                if (notif !== undefined) {
                    notif({
                        width: 500,
                        msg: data.msg,
                        type: 'error',
                        position: 'right',
                        timeout: 6000,
                    });
                }
            }
        }
        else if (xhr.status === 500 && window.CBB.DEBUG) {
            if (notif !== data) {
                notif({
                    width: 500,
                    msg: 'Something went wrong',
                    type: 'error',
                    position: 'right',
                    timeout: 6000,
                });
            }
        }
    };
    
	if (! a.CBB.DEBUG) {
        a.CBB.console = a.console;
        a.console = undefined;
    }
})(this);

