

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