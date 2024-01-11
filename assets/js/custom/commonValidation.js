function initiateIsRequired(elm) {
    let html = '<span class="required">*</span>';
    if (
        $(elm).parent().children(":first-child").next().attr("class") != "required"
    ) {
        $(html).insertAfter($(elm).parent().children(":first-child"));
    }
    // $(elm).parent().children(':last-child').append(html);
    // nwElm.insertAdjacentElement('afterbegin',html);
}

function initiateMinValCheck(elm, minVal) { }

function initiateMaxValCheck(elm, maxVal) { }

/***************************
# Deep
***************************/

$(document).ready(function () {
    validation();
});

function validation() {
    //Name
    $("input[type=name]").on("keyup", function () {
        this.value = this.value
            .replace(/[^a-zA-Z ]/g, "")
            .replace(/\b[a-z]/g, (match) => match.toUpperCase());
    });
    //OnlyText
    $("input[type=textonly]").on("keyup", function () {
        this.value = this.value.replace(/[^a-zA-Z ]/g, "");
    });

    //code
    $("input[type=code]").on("keyup", function () {
        this.value = this.value.replace(/[^a-zA-Z0-9_/]/g, "").toUpperCase();
    });

    //code
    $("input[type=code_low]").on("keyup", function () {
        this.value = this.value.replace(/[^a-zA-Z0-9_/%.]/g, "");
    });

    //Alpha Numeric
    $("input[type=alphanumeric]").on("keyup", function () {
        this.value = this.value.replace(/[^a-zA-Z0-9 ]/g, "");
    });

    //Numeric
    $(document).on("keyup", "input[type=numeric]", function () {
        this.value = Math.round(
            this.value.replace(/[^0-9]/g, "").replace(/(\..*?)\..*/g, "$1")
        );
    });

    //Float
    $(document).on("keyup", "input[type=float]", function () {
        this.value = this.value
            .replace(/[^0-9.]/g, "")
            .replace(/(\..*?)\..*/g, "$1");
    });

    $(document).on("change", "input[type=float]", function () {
        if (this.value != "") {
            this.value = parseFloat(this.value).toFixed(2);
        } else {
            this.value = parseFloat(0).toFixed(2);
        }
    });

    //Phone
    $("input[type=phone]").attr("maxlength", 10);
    $("input[type=phone]").on("keyup", function () {
        this.value = this.value
            .replace(/[^0-9.]/g, "")
            .replace(/(\..*?)\..*/g, "$1");
    });

    //Land Phone
    $("input[type=land-phone]").attr("maxlength", 20);
    $("input[type=land-phone]").on("keyup", function () {
        this.value = this.value
            .replace(/[^0-9.]/g, "")
            .replace(/(\..*?)\..*/g, "$1");
    });

    //Zip code
    $("input[type=zip]").attr("maxlength", 6);
    $("input[type=zip]").on("keyup", function () {
        this.value = this.value
            .replace(/[^0-9.]/g, "")
            .replace(/(\..*?)\..*/g, "$1");
    });

    //Email
    $("input[type=email]").on("keyup", function () {
        this.value = this.value.replace(/[^a-z0-9@.]/g, "").toLowerCase();
    });
    $("input[type=email]").on("change", function () {
        if (!(this.value.includes("@") && this.value.includes("."))) {
            errorMsg("Email is not valid.");
            this.value = "";
        }
    });
}

/***************************
# Deep
***************************/


