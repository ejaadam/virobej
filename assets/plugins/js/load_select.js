$('#country').loadSelect({
                    firstOption: {key: '', value: '--Select--'},
                    firstOptionSelectable: false,
                    url: window.location.ADMIN + 'affiliates/get_newtork',
                    key: 'id',
                    value: 'mrbusiness_name',
                    selected: data.details.supplier_id,
                    success: function () {
                        // $('#store_id').loadSelect({
                            // firstOption: {key: '', value: '--Select--'},
                            // firstOptionSelectable: true,
                            // url: window.location.ADMIN + 'affiliates/get_stores',
                            // key: 'store_id',
                            // value: 'store_name',
                            // dependingSelector: ['#merchant_id'],
                            // selected: data.details.store_id
                        // });
                    }
                });