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
        a.CBB.customValidation(event);
    }, true);
    a.CBB.customValidation = function (e) {
        var msg = null, _this = null;
        if (e.srcElement != undefined) {
            _this = e.srcElement;
        }
        if (e.target != undefined) {
            _this = e.target;
        }
        if (_this != null) {
            if (_this.dataset['errMsgTo'] != undefined) {
                $(_this.dataset['errMsgTo']).attr({for : '', class: ''}).empty();
            }
            else {
                $('span[for="' + _this.name + '"]').remove();
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
            if (! _this.validity.valid) {
                if (_this.validity.typeMismatch) {
                    msg = _this.dataset['typemismatch'];
                } else if (_this.validity.badInput) {
                    msg = _this.dataset['valuemissing'];
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
                }
                else if (_this.validity.customError) {
                    msg = null;
                }
            }
            msg = msg != null ? msg : '';
            _this.setCustomValidity(msg);
            if (_this.validationMessage != undefined && _this.validationMessage != '') {
                if ($('[name="' + _this.name + '"]').length >= 1) {
                    if (_this.dataset['errMsgTo'] != undefined) {
                        $(_this.dataset['errMsgTo']).attr({for : _this.name, class: 'errmsg'}).append(_this.validationMessage);
                    }
                    else {
                        $('[name="' + _this.name + '"]').after($('<span>').attr({for : _this.name, class: 'errmsg'}).html(_this.validationMessage));
                    }
                }
            }
        }
    };
    a.addEventListener('error', function (e) {
        $.ajax({
            data: {msg: e.message, file: e.filename, line: e.lineno, col: e.colno, trace: e.error != undefined && e.error.stack != undefined ? e.error.stack : null},
            url: a.location.BASE + 'js-exceptions'
        });
        return true;
    });
    a.CBB.ajaxComplete = function (event, xhr, settings) {
        var data = xhr.responseJSON;
        a.CBB.location = xhr.getResponseHeader('location');
        if (a.CBB.location != null) {
            $('span#current-location').text(a.CBB.location);
            $('#edit-curernt-location').fadeOut('slow', function () {
                $('#display-curernt-location').fadeIn('fast');
            });
        }
        $('body').addClass('loaded');
        if (xhr.status === 401) {
            if ($('#login-modal').length) {
                $('#loginfrm #uname').val('');
                $('#loginfrm #password').val('');
                $('#login-modal').modal();
            }
        }
        else if (xhr.status === 307 || xhr.status === 308) {
            if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {
                if (notif !== undefined) {
                    notif({
                        msg: data.msg,
                        type: 'success',
                        position: 'right'
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
                        msg: data.msg,
                        type: 'success',
                        position: 'right'
                    });
                }
            }
        }
        else if (xhr.status === 208) {
            if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {
                if (notif !== undefined) {
                    notif({
                        msg: data.msg,
                        type: 'warning',
                        position: 'right'
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
                        msg: data.msg,
                        type: 'error',
                        position: 'right'
                    });
                }
            }
        }
        else if (xhr.status === 500 && window.CBB.DEBUG) {
            if (notif !== data) {
                notif({
                    msg: 'Something went wrong',
                    type: 'error',
                    position: 'right'
                });
            }
        }
    };
    if (! a.CBB.DEBUG) {
        a.CBB.console = a.console;
        a.console = undefined;
    }
})(this);
var CKEDITOR = CKEDITOR != undefined ? CKEDITOR : null;
var notif, CURFORM = null, CROPPED = false;
var Constants = {};
if (document.location.auto) {
    /*if (navigator.geolocation) {
     $.holdReady(true);
     navigator.geolocation.getCurrentPosition(function (d) {
     document.location.position = d.coords;
     $.ajaxSetup({
     dataType: 'JSON',
     method: 'POST',
     headers: {lat: document.location.position.latitude, lng: document.location.position.longitude}
     });
     $.holdReady(false);
     });
     }*/
    $.ajaxSetup({
        dataType: 'JSON',
        method: 'POST',
        cache: true,
        headers: {pincode: document.location.pincode}
    });
}
else {
    $.ajaxSetup({
        dataType: 'JSON',
        method: 'POST',
        cache: true,
        headers: {pincode: document.location.pincode}
    });
}
$(document).ajaxStart(function () {
    $('body').removeClass('loaded');
});
$(document).ajaxComplete(window.CBB.ajaxComplete);
$.extend({
    updateMeta: function (title, description, image, keys) {
        title = title || null;
        description = description || null;
        image = image || null;
        keys = keys || null;
        if (title) {
            $('meta[namr="title"],meta[property="og:title"]').attr('content', title);
        }
        if (description) {
            $('meta[namr="description"],meta[property="og:description"]').attr('content', description);
        }
        if (image) {
            $('meta[property="og:image"]').attr('content', image);
        }
        if (keys) {
            $('meta[namr="keywords"]').attr('content', image);
        }
    },
    getURLParams: function (url) {
        url = (url !== undefined) ? url : window.location.search;
        var params = {};
        url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (str, key, value) {
            params[decodeURI(key)] = decodeURI(value);
        });
        return params;
    },
    stringify: function stringify(obj) {
        var t = typeof (obj);
        if (t != 'object' || obj === null) {
            if (t == 'string')
                obj = '"' + obj + '"';
            return String(obj);
        } else {
            var n, v, json = [], arr = (obj && obj.constructor == Array);
            for (n in obj) {
                v = obj[n];
                t = typeof (v);
                if (obj.hasOwnProperty(n)) {
                    if (t == 'string')
                        v = '"' + v + '"';
                    else if (t == 'object' && v !== null)
                        v = jQuery.stringify(v);
                    json.push((arr ? '' : '"' + n + '":') + String(v));
                }
            }
            return (arr ? '[' : '{') + String(json) + (arr ? ']' : '}');
        }
    }
});
$.fn.extend({
    checkPincode: function (settings) {
        var _this = $(this);
        _this.settings = $.extend({}, {
            country: _this.closest('select.country'),
            region: _this.closest('select.region'),
            state: _this.closest('select.state'),
            district: _this.closest('select.district'),
            city: _this.closest('select.city'),
            callBack: null
        }, settings);
        _this.on('change', function () {
            if (_this.val() != '') {
                $.ajax({
                    url: document.location.USER + 'check-pincode',
                    data: {pincode: _this.val()},
                    success: function (op) {
                        _this.add($(_this.settings.country), op.country.id, op.country.value, true);
                        _this.add($(_this.settings.region), op.region.id, op.region.value, true);
                        _this.add($(_this.settings.state), op.state.id, op.state.value, true);
                        _this.add($(_this.settings.district), op.district.id, op.district.value, true);
                        $(_this.settings.city).empty();
                        $.each(op.cities, function (k, v) {
                            _this.add($(_this.settings.city), v.id, v.value, false);
                        });
                        if (_this.settings.callBack != null) {
                            _this.settings.callBack();
                        }
                    }
                });
            }
        });
        _this.add = function (ele, value, label, reset) {
            reset = reset || false;
            if (reset) {
                ele.empty();
            }
            ele.append($('<option>', {value: value}).text(label));
        }
    },
    resetForm: function () {
        var form = $(this);
        $.each($('input', form), function () {
            if (! $(this).hasClass('ignore-reset'))
            {
                switch ($(this).attr('type'))
                {
                    case 'text':
                    case 'password':
                    case 'textarea':
                    case 'hidden':
                    case 'number':
                    case 'tel':
                    case 'url':
                    case 'email':
                        $(this).val('');
                        break;
                    case 'radio':
                    case 'checkbox':
                        $(this).prop('checked', false);
                        break;
                    case 'file':
                        $('#' + $(this).attr('id') + '-preview').attr('src', $(this).data('default'));
                        break
                }
            }
        });
        $.each($('p.form-control-static', form), function () {
            if (! $(this).hasClass('ignore-reset'))
            {
                $(this).empty();
            }
        });
        $.each($('textarea', form), function () {
            $(this).val('');
            if (! $(this).hasClass('ignore-reset'))
            {
                if (CKEDITOR != undefined && CKEDITOR.instances[$(this).attr('id')] != undefined) {
                    CKEDITOR.instances[$(this).attr('id')].setData(CKEDITOR.instances[$(this).attr('id')].element.$.defaultValue);
                }
                else {
                    $(this).val(null);
                }
            }
        });
        $.each($('select', form), function () {
            if (! $(this).hasClass('ignore-reset'))
            {
                $(this).val('');
            }
        });
        return form;
    },
    appendLaravelError: function (error) {
        var form = this;
        if (error != undefined) {
alert("SDSD");return false;
            $.each(error, function (k, e) {
                //k = k.replace(/(\[\d*\])/ig, '[]');
                if ($('[name="' + k + '"]', form).data('err-msg-to') != undefined) {
                    $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : '', class: ''}).empty();
                }
                if ($('[name="' + k + '"]', form).hasClass('noValidate') == false)
                {
                    if ($('[name="' + k + '"]', form).length == 1) {
                        if ($('[name="' + k + '"]', form).data('err-msg-to') != undefined) {
                            /* display errmsg on single container */
                            $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : '', class: ''}).empty();
                            $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : k, class: 'errmsg'}).append(e);
                            $('[name="' + k + '"]', form).on('change', function () {
                                $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : '', class: ''}).empty();
                                //console.log($(this).data('this-or-that'));
                                if ($(this).data('this-or-that') != undefined) {
                                    $('span[for="' + $(this).data('this-or-that') + '"]', form).attr({for : '', class: ''}).empty();
                                }
                            });
                        }
                        else {
                            $('span[for="' + k + '"]', form).remove();
                            $('[name="' + k + '"]', form).after($('<span>').attr({for : k, class: 'errmsg'}).html(e)).on('change', function () {
                                $('span[for="' + $(this).attr('name') + '"]', form).remove();

                                if ($(this).data('this-or-that') != undefined) {
                                    $('span[for="' + $(this).data('this-or-that') + '"]', form).remove();
                                }
                            });
                        }
                    }
                    else if ($('[name="' + k + '"]', form).length > 1) { /* display errmsg for radio control */
                        if ($('[name="' + k + '"]', form).data('err-msg-to') != undefined) {
                            $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : '', class: ''}).empty();
                            $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : k, class: 'errmsg'}).append(e);
                            $('[name="' + k + '"]', form).on('change', function () {
                                $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : '', class: ''}).empty();
                                if ($(this).data('this-or-that') != undefined) {
                                    $('span[for="' + $(this).data('this-or-that') + '"]', form).attr({for : '', class: ''}).empty();
                                }
                            });
                        }
                        else
                        {
                            $('span[for="' + k + '"]', form).remove();
                            $('#' + k + '_errmsg', form).attr({for : k, class: 'errmsg'}).html(e);
                            $('[name="' + k + '"]', form).on('change', function () {
                                $('span[for="' + $(this).attr('name') + '"]', form).remove();
                                console.log($(this).data('this-or-that'));
                                if ($(this).data('this-or-that') != undefined) {
                                    $('span[for="' + $(this).data('this-or-that') + '"]', form).remove();
                                }
                            });
                        }
                    }
                }
            });
        }
        return form;
    },
    serializeObject: function ()
    {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name] !== undefined) {
                if (! o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    },
    addOptions: function (data, selected, reset, callback) {
        selected = selected || [];
        reset = reset || true;
        callback = callback || null;
        var _this = $(this);
        if (_this.attr('data-selected') != undefined) {
            selected = $.merge(_this.attr('data-selected').split(','), selected);
            _this.removeAttr('data-selected');
        }
        if (reset) {
            _this.empty();
        }
        $.each(data, function (k, e) {
            _this.append($('<option>', $.extend({}, {value: k}, (selected.indexOf(k) >= 0 ? {selected: selected} : {}))).text(e));
        });
        if (callback) {
            callback();
        }
        return _this;
    },
    setCountDown: function () {
        window.timers = [];
        var CURTimer = $(this);
        var countDownDate = new Date(CURTimer.data('expired_on')).getTime();
        window.timers[CURTimer.attr('id')] = setInterval(function () {
            var now = new Date().getTime(), distance = countDownDate - now, days = Math.floor(distance / (1000 * 60 * 60 * 24)), hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)), minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)), seconds = Math.floor((distance % (1000 * 60)) / 1000);
            CURTimer.text(days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's ');
            if (distance < 0) {
                clearInterval(window.timers[CURTimer.attr('id')]);
                CURTimer.text('EXPIRED');
            }
        }, 1000);
    }
});
if ($.fn.dataTable) {
    $.extend(true, $.fn.dataTable.defaults, {
        bPaginate: true,
        bInfo: true,
        bSort: true,
        processing: true,
        serverSide: true,
        bStateSave: true,
        bFilter: false,
        ordering: true,
        lengthChange: false,
        pagingType: 'input_page',
        sDom: 't' + '<"col-sm-6 bottom info align"li>r<"col-sm-6 info bottom text-right"p>',
        order: [[0, 'desc']],
        oLanguage: {
            sLengthMenu: '_MENU_',
            sInfo: '_START_ to _END_ of _TOTAL_'
        },
        ajax: {
            type: 'POST'
        }
    });
    $.fn.dataTable.ext.errMode = function (settings, techNote, message) {
        throw new Error(message);
        return true;
    };
}
$(document).ready(function () {
    $('body').addClass('loaded');
    if ($.fn.datepicker) {
        $('#from').datepicker().on('changeDate', function (date) {
            $('#to').datepicker('setStartDate', date.date);
        });
        $('#to').datepicker().on('changeDate', function (date) {
            $('#from').datepicker('setEndDate', date.date);
        });
    }
    var url = document.location.toString();
    if (url.match('#') && $.fn.tab) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
    }
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
    });
    $('img.editable-img').on('click', function (e) {
        e.preventDefault();
        $($(this).data('input')).trigger('click');
    });
    if (CKEDITOR != undefined && CKEDITOR) {
        $('.ckeditor').each(function () {
            var ele = $(this);

            var editor = CKEDITOR.instances[ele.attr('id')];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace(ele.attr('id'));

            // CKEDITOR.replace(ele.attr('id'));
            ele.on('change', function () {
                var ele = $(this);
                console.log(ele.attr('id'), ele.val());
                CKEDITOR.instances[ele.attr('id')].setData(ele.val());
            });
        });
        CKEDITOR.on('instanceReady', function (e) {
            e.editor.on('change', function () {
                e.editor.updateElement();
                document.getElementById($(e.editor.element.$).attr('id')).checkValidity();
                $(e.editor.element.$).trigger('input');
            });
        });
    }
    if ($.fn.iCheck) {
        $('input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        });
    }
    $(document).on('click', '.load-content', function (e) {
        e.preventDefault();
        var Cur = $(this);
        $.ajax({
            method: 'GET',
            url: Cur.attr('href'),
            cache: true,
            success: function (op) {
                op['url'] = Cur.attr('href');
                op['setContent'] = true;
                window.document.location.ChangeUrl(op.title, op.url, op);
                $('.xbp-title').html(op.title);
                $('.xbp-    icon-title').html([$('<i>', {class: 'fa fa-' + op.title_icon}), op.title]);
                $('#xbp-styles').html(op.styles);
                $('#xbp-breadcrumb').html(op.breadcrumb);
                $('#xbp-content').html(op.content);
                $('#xbp-scripts').html(op.scripts);
            }
        });
    });
    $('.logout').on('click', function (e) {
        e.preventDefault();
        var Cur = $(this);
        $.ajax({
            url: Cur.attr('href'),
            success: function (op) {
                document.location.href = op.url;
            }
        });
    });
});
function isNumberKeyy(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57 || charCode == 46)) {
        return false;
    }
    return true;
}
function alphaNumeric_withspace(e) {
    var code = e.charCode ? e.charCode : e.keyCode;
    if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code >= 48 && code <= 57) || code == 32 || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
        return true;
    }
    return false;
}
function alphaNumeric_specialchar(e) {
    var code = e.charCode ? e.charCode : e.keyCode;
    if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code >= 48 && code <= 57) || code == 32 || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 9 || code == 45 || code == 95 || code == 43 || code == 38 || code == 40 || code == 41 || code == 8 || (code == 46 && e.charCode == 0))) {
        return true;
    }
    return false;
}
function alphaBets(e) {
    var code = e.charCode ? e.charCode : e.keyCode;
    if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || code == 116 || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
        return true;
    }
    return false;
}
function alphaBets_withspace(e) {
    var code = e.charCode ? e.charCode : e.keyCode;
    if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 116 || code == 32 || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
        return true;
    }
    return false;
}
function validateRegno(e) {
    var code = e.charCode ? e.charCode : e.keyCode;
    if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code >= 48 && code <= 57) || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 9 || code == 8 || code == 45 || code == 92 || (code == 46 && e.charCode == 0))) {
        return true;
    }
    return false;
}
function alphaNumeric_withoutspace(e) {
    var code = e.charCode ? e.charCode : e.keyCode;
    if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code >= 48 && code <= 57) || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
        return true;
    }
    return false;
}
function isNumberKeydot(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31 && (charCode > 57 || charCode < 48 || charCode == 46)) {
        return false;
    }
    return true;
}

function RestrictSpace(evt) {
    if (event.keyCode == 32) {
        return false;
    }
}
function selectallchk(evt) {
    if (evt.checked) {
        $('.checkbox').each(function () {
            this.checked = true;
        });
    } else {
        $('.checkbox').each(function () {
            this.checked = false;
        });
    }
}
function selectall() {
    /*
     if(evt.checked) {
     $('.checkbox').each(function() {
     this.checked = true;
     });
     }else{
     $('.checkbox').each(function() {
     this.checked = false;
     });
     }*/
}
function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}
function stripHtmlTags(string) {
    return string.replace(/(<([^>]+)>)/ig, '');
}
function addSlashes(string) {
    return string.replace(/\\/g, '\\\\').
            replace(/\u0008/g, '\\b').
            replace(/\t/g, '\\t').
            replace(/\n/g, '\\n').
            replace(/\f/g, '\\f').
            replace(/\r/g, '\\r').
            replace(/'/g, '\\\'').
            replace(/"/g, '\\"');
}
function stripSlashes(string) {
    return string.replace(/\\/g, '');
}
function checkformat(ele, doctypes, str) {
    /* ele=>element, doctypes=> jpg,jpeg,png, str=>'Please select valid file format' */

    txtext = ele;
    txtext = txtext.toString().toLowerCase();
    fformats = doctypes.split('|');
    if (fformats.indexOf(txtext) == - 1) {
        return false;
    } else {
        return true;
    }
}
function addDropDownMenu(arr, text) {
    arr = arr || [];
    text = text || false;
    var content = $('<div>', {class: 'btn-group'}).append($('<button>').attr({class: 'btn btn-xs btn-primary dropdown-toggle', 'data-toggle': 'dropdown'})
            .append([$('<i>', {class: 'fa fa-gear'}), $('<span>').attr({class: 'caret'})]),
            $('<ul>').attr({class: 'dropdown-menu pull-right', role: 'menu'}).append(function () {
        var options = [], data = {};
        $.each(arr, function (k, v) {
            if (! v.redirect) {
                v.class = v.class || (v.url ? 'actions' : 'show-modal');
            }
            v.url = v.url || '#';
            v.data = v.data || {};
            $.each(v.data, function (key, val) {
                data['data-' + key] = val;
            });
            options.push($('<li>').append($('<a>', {class: v.class}).attr($.extend({href: v.url}, data)).text(v.label)));
        });
        return options;
    }));
    return text ? content[0].outerHTML : content;
}

function addDropDownMenuActions(e, callback) {
    var Ele = e, data = Ele.data();
    callback = callback || null;
    if (Ele.data('confirm') == undefined || (Ele.data('confirm') != null && Ele.data('confirm') != '' && confirm(Ele.data('confirm')))) {
        if (data.confirm != undefined) {
            delete data.confirm;
        }
        $.ajax({
            url: Ele.attr('href'),
            data: data,
            success: function (data) {
                if (callback !== null) {
                    callback(data);
                }
            }
        });
    }
}

function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ('0' + parseInt(x).toString(16)).slice(- 2);
    }
    return '#' + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}
