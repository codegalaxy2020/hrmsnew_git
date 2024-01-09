/**
 * From Submit Ajax Request 
 * By Deep
 * @param {*} url 
 * @param {*} elem 
 * @param {*} callback 
 */
 function ajaxFromSubmit(url, elem, callback, errCallBk = function(data) { errCllBkLogic(data); }) {
    var formData = new FormData(elem);
    formData.append([$('#csrf_token_name').val()], $('#csrf_token_hash').val());
    
    // console.log(formData);
    $.ajax({
        url: baseUrl + url,
        type: "POST",
        data: formData,
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function() {
            $('.page_loader').show();
        },
        success: function(data) {
            if (data.csrf) {
                csrf = data.csrf;
            } else {
                csrf = data;
            }
			updateCsrf(csrf, function() {
                if (data.status == "fail" || data.status == "0") {
                    errCallBk(data);
					$('.page_loader').hide();
                }
                else if(data.status == "2"){
                    //Confirm Message
					$('.page_loader').hide();
                    window[data.confirm.callbackfunction](data.confirm);
                } else {
                    $('.page_loader').hide();
                    callback(data);
                }
            });
        },
        error: function(errResp) {
            console.log(errResp);
            //alert("Fail")
        }
    });
}

function ajaxPostRequest(url, formData, callback, errCallBk = function(data) { errCllBkLogic(data); }) {
    formData[[$('#csrf_token_name').val()]] = $('#csrf_token_hash').val();
    $.ajax({
        url: baseUrl + url,
        type: "POST",
        data: formData,
        dataType: 'json',
        beforeSend: function() {
            $('.page_loader').show();
        },
        success: function(data) {
            if (data.csrf) {
                csrf = data.csrf;
            } else {
                csrf = data;
            }
			updateCsrf(csrf, function() {
                if (data.status == "fail" || data.status == "0") {
                    errCallBk(data);
					$('.page_loader').hide();
                } else {
                    validation(); //Initialized Validation
                    $('.page_loader').hide();
                    callback(data);
                }
            });
        },
        
        error: function(jqXHR, exception) {
            var msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status == 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status == 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            console.log(msg);
			$('.page_loader').hide();
        }
    });
}

function errCllBkLogic(data) {
    validation(); //Initialized Validation
    // $("body").removeClass("loading");
    // makeallEnabled();
    $('#global-loader').hide();
    swalErrMsg(data.error);
}
