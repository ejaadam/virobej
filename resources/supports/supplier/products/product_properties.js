jQuery.fn.extend({
    loadPropertiesTree: function (properties, values) {
        var _this=this;
        if (!_this.length||!_this.is('ul')) {
            console.warn("Invalid Selector '"+_this.selector+"'");
            return false;
        }
        _this.options={
            properties: {
                url: '',
                data: {},
                success: null,
                checked: [],
                choosable: [],
                key_value: []
            },
            values: {
                url: '',
                parentKey: '',
                data: {},
                success: null,
                checked: []
            }
        };
        $.extend(_this.options.properties, properties);
        $.extend(_this.options.values, values);
        _this.loadProperties=function () {
            $.ajax({
                url: _this.options.properties.url,
                data: _this.options.properties.data,
                success: function (data) {
                    _this.addProperties($(_this.selector), data);
                }
            });
        };
        _this.addProperties=function (parentUL, data) {
            parentUL.empty();
            for (var key in data) {
                var item=data[key],
                        li=$('<li>');
                li.append($('<div>', {class: 'checkbox'})
                        .append($('<label>', {for : 'properties'+item.id})
                                .append($('<input>', {type: 'checkbox', class: 'properties-checkbox', id: 'properties'+item.id, 'data-choosable': ((item.property_type==Constants.PROPERTY_TYPE.PREDEFINED)?'#choosable':'#key_value')+item.id, 'data-property_id': item.id, 'data-property_type': item.property_type, name: 'properties['+item.id+'][property_id]', value: item.id}))
                                .append(item.label)
                                .append((item.property_type==Constants.PROPERTY_TYPE.PREDEFINED)
                                        ?$('<input>', {type: 'checkbox', class: 'pull-right', id: 'choosable'+item.id, style: 'display:none;', name: 'properties['+item.id+'][choosable]', value: 1})
                                        :$('<input>', {type: 'text', class: 'form-control', id: 'key_value'+item.id, style: 'display:none;', name: 'properties['+item.id+'][key_value]'}))
                                )
                        );
                parentUL.append(li);
                if (item.children!=undefined&&item.children.length) {
                    var $ul=$('<ul>', {style: 'list-style:none;', class: 'children'}).appendTo(li);
                    _this.addProperties($ul, item.children);
                }
                if (_this.options.properties.checked.indexOf(item.id)>=0) {
                    $('#properties'+item.id, parentUL).attr('checked', true);
                }
                if (_this.options.properties.choosable.indexOf(item.id)>=0) {
                    $('#choosable'+item.id, parentUL).attr('checked', true);
                }
                if (_this.options.properties.key_value[item.id]!=undefined) {
                    $('#key_value'+item.id, parentUL).val(_this.options.properties.key_value[item.id]);
                }
                $('#properties'+item.id, parentUL).trigger('change');
            }
        };
        _this.loadValues=function (parentUL, parentID) {
            _this.options.values.data[_this.options.values.parentKey]=parentID;
            $.ajax({
                url: _this.options.values.url,
                data: _this.options.values.data,
                success: function (data) {
                    return _this.addValues(parentUL, data);
                }
            });
        };
        _this.addValues=function (parentUL, data) {
            parentUL.empty();
            for (var key in data) {
                var item=data[key],
                        li=$('<li>');
                li.append($('<div>', {class: 'checkbox'})
                        .append($('<label>', {for : 'values'+item.id})
                                .append($('<input>', {type: 'checkbox', id: 'values'+item.id, name: 'values['+item.property_id+'][][value_id]', value: item.id}))
                                .append(item.label)
                                .attr(/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(item.label)?{style: 'background-color:'+item.label}:{})
                                )
                        );
                parentUL.append(li);
                if (_this.options.values.checked.indexOf(item.id)>=0) {
                    $('#values'+item.id, parentUL).attr('checked', true);
                }
            }
        };
        _this.update=function () {
            _this.loadProperties();
            if (_this.options.success!=null) {
                _this.options.success();
            }
        };
        _this.update();
        $(_this.selector).on('change', '.properties-checkbox', function () {
            var CurEle=$(this), CurEle_children=CurEle.closest('li').children('ul.children'), CurEleValues=CurEle.closest('li').children('ul.values');
            if (CurEle.is(':checked')) {
                CurEle_children.slideDown();
                if (CurEleValues.length<=0) {
                    CurEleValues=$('<ul>', {style: 'list-style:none;', class: 'values'})
                    CurEle.closest('div.checkbox').after(CurEleValues);
                    _this.loadValues(CurEleValues, CurEle.data('property_id'));
                }
                else {
                    CurEleValues.fadeIn();
                }
                $(CurEle.data('choosable')).fadeIn();
            }
            else {
                CurEle_children.slideUp();
                $(CurEle.data('choosable')).fadeOut();
                if (CurEleValues.length>0) {
                    CurEleValues.fadeOut();
                }
                $.each($(':checkbox', CurEle_children), function () {
                    $(this).removeAttr('checked');
                });
            }
        });
    }
});
