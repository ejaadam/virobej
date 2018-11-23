
function sendTokenToServer(newToken) {
    var token = window.document.querySelector('meta[name="X-FCM-ID"]').content;
    if (newToken !== token) {	
        $.ajax({
            data: {fcm_registration_id: newToken},
			url: window.location.SELLER + 'update-notification-token',         
        });
    }
}
$.fn.extend({
    notification: function (data) {
        var _this = $(this);
        _this.count = 0;
        _this.data = [];
        _this = $.extend({}, _this, data);	
        _this.getData = function () {
            if (_this.data.length <= 0) {
                $.ajax({
                    url: window.location.SELLER + 'get-notifications',
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
                        $('<h3>').append([$('<a>', {class: (e.is_read == 0?'read-notification':''),href:e.click_action,'data-id':e.id}).html(e.title)]),
                        $('<p>').html(e.body),
                        $('<p>',{class:'large_info'}).html(e.created_on),                       
                        (e.is_read == 0) ? $('<i>',{class:'icon-exclamation-sign indicator'}).html(''):'',
                    ]));
                });
            }
            else {
                $('span.count', _this).hide();
                $('ul.list', _this).append($('<li>').append('No New Notifications'));
            }
        };
		_this.getData();
        return _this;
    }
});
$(function () {	    
    var N = $('#user-notifications').notification(window.TSP.data.notifications);
    $.getScript("https://www.gstatic.com/firebasejs/4.5.0/firebase.js", function () {
        if (firebase != undefined && firebase != null) {		
            firebase.initializeApp({
                apiKey: "AIzaSyCXgMnHknh9Nys-RplG0yPw2D7yMKuEs2I",
				authDomain: "virob-77a48.firebaseapp.com",
				databaseURL: "https://virob-77a48.firebaseio.com",
				projectId: "virob-77a48",
				storageBucket: "virob-77a48.appspot.com",
				messagingSenderId: "1017218485630"
            });		
            var messaging = firebase.messaging();
            messaging.requestPermission()
                    .then(function () {				    	
                        console.log('Notification permission granted.');
                        messaging.getToken()
                                .then(function (currentToken) {
                                    if (currentToken) {
                                        console.log(currentToken);
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
                    type: 'success',
                    position: 'right'
                });
            });
        }
    });
	$('.notification_dropdown .dropdown_items').on('click','a.read-notification',function(e) {
    //$(document).on('click', '#user-notifications a', function (e) {
		e.preventDefault();     
        var Curele = $(this);	
        $.ajax({          
	    	data: {id: Curele.data('id')},
            url: window.location.SELLER + 'notification-read',
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
