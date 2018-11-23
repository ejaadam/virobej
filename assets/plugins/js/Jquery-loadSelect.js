var loadedSelectValues = [];
jQuery.fn.extend({
    loadSelect: function (e) {
        var o = this;
        if (! o.length || ! o.is("select"))
            return console.warn("Invalid Selector '" + o.selector + "'"), ! 1;
        o.xhr = ! 1, o.options = {
            values: [],
            url: "",
            key: "key",
            value: "value",
            optionData: [], //[{key: '', value: ''}]
            selected: ! 1,
            palceHolder: ! 0,
            firstOption: {
                key: "",
                value: "--Select--"
            },
            firstOptionSelectable: ! 1,
            dependingSelector: [], //['#input_id']
            notexistIn: [],
            copyTo: [], //[{selector: false, key: 'key', value: 'value',autoSelect:true}]
            data: {},
            cache: ! 0,
            success: null
        };
        for (var t in o.options)
            void 0 != o.data(t) && "" != o.data(t) && null != o.data(t) && (o.options[t] = o.data(t));
        if ($.extend(o.options, e), o.selector in loadedSelectValues || (loadedSelectValues[o.selector] = {
            parentValues: o.options.values,
            childernsValues: []
        }), o.update = function (e) {
            for (var t in o.options) {
                void 0 != o.attr('data-' + t) && "" != o.attr('data-' + t) && null != o.attr('data-' + t) && (o.options[t] = o.attr('data-' + t));
            }
            o.xhr && 4 != o.xhr.readyState && o.xhr.abort();
            var t = "";
            if (o.empty(), o.options.palceHolder) {
                o.html($("<option>").val(o.options.firstOption.key).text(o.options.firstOption.value).attr("hidden", ! o.options.firstOptionSelectable));
                for (var n in o.options.copyTo)
                    $(o.options.copyTo[n].selector).html($("<option>").val(o.options.firstOption.key).text(o.options.firstOption.value).attr("hidden", ! o.options.firstOptionSelectable))
            }
            for (var l in e)
                if (o.options.notexistIn.length <= 0 || o.options.notexistIn.length && o.options.notexistIn.indexOf(e[l][o.options.key]) <= - 1) {				
                    if (t = $("<option>").val(e[l][o.options.key]).text(e[l][o.options.value]), o.options.selected && (o.hasOwnProperty("multiple") && o.options.selected.indexOf(e[l][o.options.key].toString())>=0 || o.options.selected == e[l][o.options.key])? t.attr("selected", "selected"):'', o.options.optionData.length)
                        for (var i in o.options.optionData)
                            t.attr("data-" + o.options.optionData[i].key, e[l][o.options.optionData[i].value]);
                    if (o.append(t), o.options.selected && (o.hasOwnProperty("multiple") && o.options.selected.indexOf(e[l][o.options.key].toString()) || o.options.selected == e[l][o.options.key]) && o.trigger("change"), o.options.copyTo)
                        for (var n in o.options.copyTo)
                            t = $("<option>").val(e[l][o.options.copyTo[n].key]).text(e[l][o.options.copyTo[n].value]).attr("class", "pid_" + e[l][o.options.key]), $(o.options.copyTo[n].selector).append(t)
                }
            null != o.options.success && o.options.success()
        }, o.load = function () {
            var e = o.options.data;
            if (o.options.dependingSelector != [])
                for (var t in o.options.dependingSelector) {
                    var n = $(o.options.dependingSelector[t]).val(),
                            l = o.dependingSelectorKey(o.options.dependingSelector[t]);
                    e[l] = n
                }
				
		
				o.xhr = $.ajax({
					type: "POST",
					url: o.options.url,
					data: e,
					dataType: "JSON",
					beforeSend: function () {
						o.html('<option value="" hidden="hidden">Loading...</option>')
					},
					success: function (e) {
						if (o.options.dependingSelector.length > 0)
							for (var t in o.options.dependingSelector) {
								var n = $(o.options.dependingSelector[t]).val(),
										l = o.dependingSelectorKey(o.options.dependingSelector[t]);
								void 0 == loadedSelectValues[o.selector].childernsValues[l] && (loadedSelectValues[o.selector].childernsValues[l] = []), loadedSelectValues[o.selector].childernsValues[l][n] = e, o.update(loadedSelectValues[o.selector].childernsValues[l][n])
							}
						else
							loadedSelectValues[o.selector].parentValues = e, o.update(loadedSelectValues[o.selector].parentValues)
					}
				})
			
        }, o.dependingSelectorKey = function (e) {
            return e.substr(e.lastIndexOf("#") + 1)
        }, o.options.dependingSelector.length > 0) {
            if (o.options.palceHolder) {
                o.html($("<option>").val(o.options.firstOption.key).text(o.options.firstOption.value).attr("hidden", ! o.options.firstOptionSelectable));
                for (var t in o.options.copyTo)
                    $(o.options.copyTo[t].selector).html($("<option>").val(o.options.firstOption.key).text(o.options.firstOption.value).attr("hidden", ! o.options.firstOptionSelectable))
            }
            for (var t in o.options.dependingSelector)
                $(o.options.dependingSelector[t]).on("change", function () {
			
                    var e = $(this).val(),
                            t = $(this).attr('id');
                    0 == o.options.cache || 0 == loadedSelectValues[o.selector].childernsValues.length || void 0 == loadedSelectValues[o.selector].childernsValues[t] || 0 == loadedSelectValues[o.selector].childernsValues[t].length || void 0 != loadedSelectValues[o.selector].childernsValues[t] && void 0 == loadedSelectValues[o.selector].childernsValues[t][e] ? o.load() : o.update(loadedSelectValues[o.selector].childernsValues[t][e])
                })
        } else {
            if (o.html('<option value="" hidden="hidden">Loading...</option>'), o.options.palceHolder)
                for (var t in o.options.copyTo)
                    $(o.options.copyTo[t].selector).html($("<option>").val(o.options.firstOption.key).text(o.options.firstOption.value).attr("hidden", ! o.options.firstOptionSelectable));
            0 == o.options.cache || 0 == loadedSelectValues[o.selector].parentValues.length ? o.load() : o.update(loadedSelectValues[o.selector].parentValues)
        }
        o.options.copyTo && o.on("change", function () {
            for (var e in o.options.copyTo)
                o.options.copyTo[e].autoSelect ? ($("option:selected", o.options.copyTo[e].selector).removeAttr("selected"), $("option.pid_" + o.val(), o.options.copyTo[e].selector).attr("selected", "selected")) : "" != o.options.copyTo[e].selected && null != o.options.copyTo[e].selected && $(o.options.copyTo[e].selector).val(o.options.copyTo[e].selected)
        });
    }
});
