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