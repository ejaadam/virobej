function sendTokenToServer(newToken) {
    var token = window.document.querySelector('meta[name="X-FCM-ID"]').content;
    if (newToken !== token) {
        $.ajax({
            data: {fcm_registration_id: token},
            url: window.location.API.BASE + 'update-notification-token'
        });
    }
}
$.fn.extend({
    notification: function (data) {
        var _this = $(this);
        _this.count = 0;
        _this.data = [];
        _this = $.extend({}, _this, data);
		console.log( _this );
        _this.getData = function () {
            if (_this.data.length <= 0) {
                $.ajax({
                    url: window.location.API.BASE + 'get-notifications',
                    success: function (data) {
                        _this.data = data.notifications;
                        _this.count = data.count;
                        _this.print();
                    }
                });
            }
        };
        _this.updateData = function (data) {
            _this.data = data.notifications;
            _this.count = data.count;
            _this.print();
        };
        _this.addNotification = function (data) {
            if (_this.count < 5) {
                _this.data.push(data);
            }
            else {
                _this.data[5] = data;
            }
            _this.count ++;
            _this.print();
        };
        _this.print = function () {
            $('span.count', _this).html(_this.count);
            $('ul.list', _this).empty();
            if (_this.count > 0) {
                $('span.count', _this).show();
                $.each(_this.data, function (k, e) {
                    $('ul.list', _this).append($('<li>').append([
                        $('<h3>').append([$('<span>', {class: 'small_info'}).html(e.created_on), $('<a>', {class: 'read-notification', href: e.click_action, 'data-id': e.id}).html(e.title)]),
                        $('<p>').html(e.body)
                    ]));
                });
            }
            else {
                $('span.count', _this).hide();
                $('ul.list', _this).append($('<li>').append('No New Notifications'));
            }
        };
        return _this;
    }
});
$(document).ready(function () {
    var N = $('#user-notifications').notification(window.TSP.data.notifications);
    if (window.firebase != undefined && window.firebase != null) {
        window.firebase.initializeApp({
            apiKey: "AIzaSyAMyyuEPU7w7oX7Xm1WLY_dRESujhmuDRg",
            authDomain: "telserra-5a0ea.firebaseapp.com",
            databaseURL: "https://telserra-5a0ea.firebaseio.com",
            projectId: "telserra-5a0ea",
            storageBucket: "telserra-5a0ea.appspot.com",
            messagingSenderId: "1005064225363"
        });
        var messaging = window.firebase.messaging();
        messaging.requestPermission()
                .then(function () {
                    console.log('Notification permission granted.');
                    messaging.getToken()
                            .then(function (currentToken) {
                                if (currentToken) {
                                    sendTokenToServer(currentToken);
                                } else {
                                    console.log('No Instance ID token available. Request permission to generate one.');
                                }
                            })
                            .catch(function (err) {
                                console.log('An error occurred while retrieving token. ', err);
                            });
                })
                .catch(function (err) {
                    console.log('Unable to get permission to notify.', err);
                });
        messaging.onTokenRefresh(function () {
            messaging.getToken()
                    .then(function (refreshedToken) {
                        console.log('Token refreshed.');
                        sendTokenToServer(refreshedToken);
                    })
                    .catch(function (err) {
                        console.log('Unable to retrieve refreshed token ', err);
                    });
        });
        messaging.onMessage(function (payload) {
            N.addNotification(payload.data);
            notif({
                msg: payload.data.body,
                type: "success",
                position: "right"
            });
        });
    }
	
    $(document.body).on('click', '.read-notification', function (e) {
        e.preventDefault();
        var Curele = $(this);
        $.ajax({
            data: {id: Curele.data('id')},
            url: window.location.API.BASE + 'mark-notification-read',
            success: function (data) {
                if (Curele.attr('href') !== '' && Curele.attr('href') !== '#') {
                    document.location.href = Curele.attr('href');
                }
                else {
                    N.updateData(data);
                }
            }
        });
    });
});
