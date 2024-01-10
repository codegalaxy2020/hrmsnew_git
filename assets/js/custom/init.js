/*
    Author: Deep Basak
    Created ON: December 20, 2022
*/


//init flatpicker date
function initDatePicker(elemID = '') {
    flatpickr(elemID, {
        dateFormat: "d-m-Y"
    });
}

//init flatpicker timepicker
function initTimePicker(elemID = '', format24hr = true) {
    flatpickr(elemID, {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: format24hr
    });
}

$(document).ready(function() {
    //init select2
    // $('.select2').select2();
});

//init Ckeditor
function initCkeditor(elemId) {
    ClassicEditor
        .create(document.querySelector(elemId))
        .then(editor => {
            return editor;
        })
        .catch(error => {
            console.error(error);
        });
}

function multiSelectInit() {
    // multiple Remove CancelButton
    if ($(".multi-select").length) {
        $(".multi-select").each(function() {
            var multipleCancelButton = new Choices(
                this, {
                    removeItemButton: true,
                }
            );
        });
    }
}


function updateCsrf(data, callback = '') {
    $('#csrf_token_name').val(data.csrfName);
    $('#csrf_token_hash').val(data.csrfHash);
    if (callback != '') {
        callback();
    }
}

//reset from
function resetFrom(fromId) {
    $('#' + fromId)[0].reset();
}

/*
 * added_by: Deep Basak
 *	created_on: 12-11-2022
 * purpuse: print html data	
 */
function popup(data) {
    var frame1 = $('<iframe />');
    frame1[0].name = "frame1";
    frame1.css({ "position": "absolute", "top": "-1000000px" });
    $("body").append(frame1);
    var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
    frameDoc.document.open();
    //Create a new HTML document.
    frameDoc.document.write(data);
    frameDoc.document.close();
    setTimeout(function() {
        window.frames["frame1"].focus();
        window.frames["frame1"].print();
        frame1.remove();
    }, 500);
    return true;
}

/* 
	Make any dropdown Empty
*/
function makeDropdownEmpty(firstparam, callback = function() {}) {
    $("select[id='" + firstparam + "'] option:selected").removeAttr("selected");
    $('select[id="' + firstparam + '"]').prop("selectedIndex", "-1");
    callback();
}

/* 
 * initialize tinymce text editor plugin
 */
function initTinymce(id, placeholder = lang['add_comments'], initText = '', is_encrypted = 0, readonly = 0) {
    if (is_encrypted == 1) {
        initText = atob(initText);
    }
    if (tinymce.get(id) != "undefined") {
        tinymce.execCommand('mceRemoveEditor', true, id);
    }
    var useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;

    tinymce.init({
      selector: "#" + id,
      placeholder: placeholder,
      plugins:
        "print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen  link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons",
      imagetools_cors_hosts: ["picsum.photos"],
      menubar: "file edit view insert format tools table help",
      toolbar:
        "undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl",
      toolbar_sticky: true,
      autosave_ask_before_unload: true,
      autosave_interval: "30s",
      autosave_prefix: "{path}{query}-{id}-",
      autosave_restore_when_empty: false,
      autosave_retention: "2m",
      image_advtab: true,
      readonly: readonly,
      link_list: [
        { title: "My page 1", value: "https://www.tiny.cloud" },
        { title: "My page 2", value: "http://www.moxiecode.com" },
      ],
      image_list: [
        { title: "My page 1", value: "https://www.tiny.cloud" },
        { title: "My page 2", value: "http://www.moxiecode.com" },
      ],
      image_class_list: [
        { title: "None", value: "" },
        { title: "Some class", value: "class-name" },
      ],
      importcss_append: true,
      file_picker_callback: function (callback, value, meta) {
        /* Provide file and text for the link dialog */
        if (meta.filetype === "file") {
          callback("https://www.google.com/logos/google.jpg", {
            text: "My text",
          });
        }

        /* Provide image and alt text for the image dialog */
        if (meta.filetype === "image") {
          callback("myimage.jpg", { alt: "My alt text" });
        }

        /* Provide alternative source and posted for the media dialog */
        if (meta.filetype === "media") {
          callback("movie.mp4", {
            source2: "alt.ogg",
            poster: "https://www.google.com/logos/google.jpg",
          });
        }
      },
      templates: [
        {
          title: "New Table",
          description: "creates a new table",
          content:
            '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>',
        },
        {
          title: "Starting my story",
          description: "A cure for writers block",
          content: "Once upon a time...",
        },
        {
          title: "New list with dates",
          description: "New List with dates",
          content:
            '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>',
        },
      ],
      template_cdate_format: "[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]",
      template_mdate_format: "[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]",
      height: 270, //CR ID 16-22222
      image_caption: true,
      quickbars_selection_toolbar:
        "bold italic | quicklink h2 h3 blockquote quickimage quicktable",
      noneditable_noneditable_class: "mceNonEditable",
      toolbar_mode: "sliding",
      contextmenu: "link image imagetools table",
      skin: useDarkMode ? "oxide-dark" : "oxide",
      content_css: useDarkMode ? "dark" : "default",
      content_style:
        "body { font-family:Helvetica,Arial,sans-serif; font-size:14px }",
      setup: function (editor) {
        editor.on("init", function () {
          this.setContent(initText);
        });
      },
    });
}

function reInitTooltip() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
}

function reInitPopover() {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    })
}

//Linked Functions For Date Difference
function dateDiff(date) {
    date = date.split('-');
    var today = new Date();
    var year = today.getFullYear();
    var month = today.getMonth() + 1;
    var day = today.getDate();
    var yy = parseInt(date[0]);
    var mm = parseInt(date[1]);
    var dd = parseInt(date[2]);
    var years, months, days;
    months = month - mm;
    if (day < dd) {
        months = months - 1;
    }
    years = year - yy;
    if (month * 100 + day < mm * 100 + dd) {
        years = years - 1;
        months = months + 12;
    }
    days = Math.floor((today.getTime() - (new Date(yy + years, mm + months - 1, dd)).getTime()) / (24 * 60 * 60 * 1000));
    return {
        years: years,
        months: months,
        days: days
    };
}

//Remove the space from text field
//Added by Deep Basak On Febuary 21, 2023
function removeSpace(t){
	if(t.value.match(/\s/g)){
		t.value=t.value.replace(/\s/g,'');
	}
}

//String to uper case
//Added by Deep Basak On Febuary 21, 2023
function toAllUpercase(th){
	//return th.value.toUpperCase();
	$(th).val($(th).val().toUpperCase());
}
