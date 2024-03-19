/**
 * Author: DEEP BASAK
 * IDE: VS Code
 */

/**
 * ADDED BY Deep Basak on January 11, 2024
 * @param {*} title 
 * @param {*} message 
 * @param {*} type 
 */
function SwalSuccess2(title, message, type){
    Swal.fire({
        title: title,
        text: message,
        icon: type
    });
}

/**
 * ADDED BY Deep Basak on January 11, 2024
 * @param {*} title 
 * @param {boolean} showDenyButton 
 * @param {boolean} showCancelButton 
 * @param {*} confirmButtonText 
 * @param {*} denyButtonText
 * @param {*} callback
 * @param {*} cancelCallback
 * @param {*} deniedCallback
 */
function warnMsg2(title, showDenyButton, showCancelButton, confirmButtonText, denyButtonText = "", callback = function() {}, cancelCallback = function() {}, deniedCallback = function() {}){
    Swal.fire({
        title: title,
        showDenyButton: showDenyButton,
        showCancelButton: showCancelButton,
        confirmButtonText: confirmButtonText,
        denyButtonText: denyButtonText
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            callback();
        }
        else if (result.isDenied) {
            deniedCallback();
        }
        else{
            if(cancelCallback!=''){
                cancelCallback();
            }else{
                return false;
            }
        }
        
    });
}


/**
 * ADDED BY Deep Basak on March 19, 2024
 * @param {*} msg 
 * @param {*} callback
 */
function swalErrMsg(msg, callback = function() {}) {

    if ((typeof msg) == "object") {
        var nwArr = [];
        $.each(msg, function(i, val) {
            nwArr.push(val);
        });
        msg = nwArr;
    }

    if ((typeof msg) == "string") {
        msg = [msg];
    }

    if (msg.length > 0) {
        msgString = "<ul>";
        var lenChar = 0;
        $.each(msg, function(i, val) {
            if (val != '') {
                msgString += "<li>" + val + "</li>";
                lenChar++;
            }
        });
        msgString += "</ul>";
        msgString = "<h4>" + lenChar + " errors occured.</h4><br>" + msgString;

        Swal.fire({
            title: msgString,
            confirmButtonColor: '#5156be',
        });

        $(".swal2-modal").css({ "background": "#FA4848" }); //Optional changes the color of the sweetalert 
        $(".swal2-title").css("color", "#ffffff");
        $(".swal2-title h4").css("color", "#ffffff");
        $(".swal2-container").css("z-index", "10000");      //CR BY DEEP BASAK on March 19, 2024
    } else {
        console.log('Array you sent was blank');
    }

    callback();
}