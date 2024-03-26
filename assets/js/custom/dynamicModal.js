/* 
common function for opening modal by Deep On 22/09/2022
arguments: modalId => accepts string, expects id of the modal to open
returnts: Nothing
*/
function holdModal(modalId,callback = '') {
	$('#' + modalId).modal('show');
	if(callback != ''){
		callback();
	}
}  

/* 
common function for closing modal by Deep On 22/09/2022
arguments: modalId => accepts string, expects id of the modal to close
returnts: Nothing
*/
function closeModal(modalId, callback = ''){
	$('#' + modalId).modal('toggle');
	if(callback != ''){
		callback();
	}
}

//Added by DEEP BASAK on March 19, 2024
function dynamicModalSize(modalId, modalRemoveClass, modalSize){
	$('#' + modalId).find('.modal-dialog').removeClass(modalRemoveClass);
	$('#' + modalId).find('.modal-dialog').addClass(modalSize);
}