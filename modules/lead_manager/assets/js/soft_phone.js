"use strict";
var staffPhoneNumber=$("#staffPhoneNumber").val();
var incomingCallFrom='';
var holdStatus = false;
var device = null;
var acceptCall =function (phoneNumber) {
	$(".ringing").removeClass("-ringing");
	setTimeout(function () {
		$(".ringing").addClass("-flip");
		$(".speaking").removeClass("flipback");
	}, 0);
	$("#caller-info").html(phoneNumber);
};
var acceptedDialledCall = function (phoneNumber) {
	$(".dialing").removeClass("-dialing");
	setTimeout(function () {
		$(".dialing").addClass("-flip");
		$(".speaking").removeClass("flipback");
	}, 0);
	$("#caller-info").html(phoneNumber);
};

$("#refuse").click(function () {
	$(".ringing").removeClass("-ringing");
	$(".ringing").addClass("-drop");

	setTimeout(function () {
		$(".ringing").addClass("-fadeout");
	}, 0);

	setTimeout(function () {
		$(".ringing").addClass("-ringing").removeClass("-fadeout");
	}, 10000);
});

var dropLiveCall = function(){
	$(".speaking").addClass("-drop");
	setTimeout(function () {
		$(".ringing").removeClass("-flip");
		$(".speaking")
		.addClass("flipback")
		.removeClass("hold")
		.removeClass("-drop");
	}, 2000);
};

var dropRingingCall = function(){
	$(".ringing").addClass("-drop");
	setTimeout(function () {
		$(".ringing").removeClass("-drop");
		$(".ringing").css('display','');
	}, 2000);
};

var onMuteChange = function (muted) {
	if (muted) {
		$(".fa-ban").addClass("hidden");
		$("#eq").removeClass("hidden");
	} else {
		$(".fa-ban").removeClass("hidden");
		$("#eq").addClass("hidden");
	}
};

$( function() {
	$( "#soft-phone-draggable" ).draggable();
} );

function dialPhone(phone,leadId,callerIdNumber) { 
	if(phone == ''){
		alert('please enter valid phone number');
	}else{
		let params = {"phoneNumber": phone, "leadId": leadId,"callerIdNumber":callerIdNumber};
		device.connect(params);
		$("#dialing-soft-phone").find('.details span').text(phone);
		$("#dialing-soft-phone").show();	
	}
}
$("#disconnect").click(function(){
	dropLiveCall();
	$("#dialing-soft-phone").css('display','');
})
function updateCallStatus(status) {
	console.log(status);	
}
function setupClient() {
	$.post(admin_url+'lead_manager/call_control/generateClientToken', {
		forPage: window.location.pathname,
	}).done(function (data) {
		device = new Twilio.Device();
		let obj = JSON.parse(data);
		device.setup(obj.token, { debug: true });
		setupHandlers(device);
	}).fail(function () {
		updateCallStatus("Could not get a token from server!");
	});
};
function setupHandlers(device) {
	device.on('ready', function (_device) {
		updateCallStatus("Ready");
	});
	device.on('error', function (error) {
		updateCallStatus("ERROR: " + error.message);
		dropLiveCall();
		/*alert("ERROR: " + error.message);*/
		if(error.message == 'JWT token expired' || error.message == 'Invalid JWT token'){
			location.reload();
			//setupClient
		}
	});
	device.on('connect', function (connection) {
		setTimeout(function () {
			if( connection.status() == "open" ){
				updateCallStatus("In call with " + connection.message.phoneNumber);
				acceptedDialledCall(connection.message.phoneNumber);
			}
		}, 1000);
		$(".sound").click(function () {
			let mute = connection.isMuted();
			connection.mute(mute ? false : true);
			onMuteChange(mute);
		});
		$("#drop").click(function () {
			connection.disconnect();
			dropLiveCall();
			dropRingingCall();
		})
	});
	device.on('disconnect', function(connection) {
		dropLiveCall();
		updateCallStatus("Ready");
	});
	
	device.on('incoming', function(connection) {
		let callerId = connection.parameters.From;
		let callSid = connection.parameters.CallSid;
		let acallerId = connection.parameters.To;
		updateCallStatus("incoming call from=ani= "+acallerId);
		getFromNumberByChildCallSid(callSid, staffPhoneNumber);
		connection.accept(function() {
			updateCallStatus("In call with customer "+incomingCallFrom);
		});
		connection.reject(function() {
			dropLiveCall();
			updateCallStatus("incoming Call Rejected!");
		});
		/*connection.cancel(function() {
			dropLiveCall();
			updateCallStatus("incoming Call Cancelled!");
		});*/
		connection.ignore(function() {
			dropRingingCall();
			updateCallStatus("incoming Call Ignored!");
		});
		connection.disconnect(function() {
			dropLiveCall();
			updateCallStatus("incoming Call disconnected");
		});
		$("#accept").click(function() {
			connection.accept();
			acceptCall(incomingCallFrom);
		});
		$("#refuse").click(function() {
			connection.reject();
			dropRingingCall();
			updateCallStatus("incoming Call Ignored by agent");
		});
	});
}
function getFromNumberByChildCallSid(childSid, staffNumber) {
	$.post(admin_url+'lead_manager/call_control/getFromNumberByChildCallSid', {
		'CallSid': childSid,
	}).done(function (resp) {
		resp = JSON.parse(resp)
		if(staffNumber === resp.to){
			incomingCallFrom = resp.from;
			$("#ringing-soft-phone").find('#calling-info p').html(resp.from);
			$("#ringing-soft-phone").show();
		}
	}).fail(function () {
		console.log('failed!')
	});
}
$(document).ready(function(){
	setupClient();
})
