<?php ?>
<?php



    session_start();

$_SESSION["lid"] = "52";//dev by malith
$_SESSION["luid"] = "52";
$_SESSION["cusymbol"] = "Rs.";
$_SESSION["uid"] = "0";
$_SESSION["guest"] = null;//dev by malith

// unset($_SESSION["guest"]);


date_default_timezone_set('Asia/Colombo');
?>
@extends('Templates/WiTemplate')

@section('title')
Enter Results
@stop

@section('head')

<script src="{{ asset('JS/ReportCalculations.js') }}"></script>

<script type="text/javascript">
    
    $(document).ready(function() {
    $('.input-text').change(function() {
        // Handler function for the change event
        console.log('Input value changed:', $(this).val());
    });
    });
    window.onload = loadDate;
    function loadDate() {
        document.getElementById('pdate').valueAsDate = new Date();
        $('#sNo').focus();

        if ($('#pxdate').val() !== '') {
            $('#sNo').val($('#pxsno').val());
            document.getElementById('pdate').valueAsDate = new Date($('#pxdate').val());
            search();
        } else if ($('#pxsno').val() !== '') {
            $('#sNo').val($('#pxsno').val());
            search();
        }

        opentab('pdatatable');
    }

    $(document).on('keydown keypress', '#sNo', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            search();
            return false;
        }
    });

    $(document).on('keydown keypress', 'textarea', function (event) {
        if (event.keyCode === 13) {
            event.stopPropagation();
        }
    });

    // $("input").change(function(){
    //     // Handler function for the change event
    //     console.log('Input value changed:', $(this).val());
    // });
    
    function validate() {
        var sNo = document.getElementById('sNo').value;

        var check = true;
        var regex2 = /^[a-zA-Z0-9]*$/;
        if (!regex2.test(sNo)) {
            alert("Enter Valid Sample Number!");
            check = false;
        }

        if (check) {
            search();
        }
    }
    var selectedGender = "";
    var PatientSugDataOblect;

    var a01 = "";
    var a02 = "";

    var al01 = "";
    var al02 = "";


    function search() {
        if (document.getElementById('sNo').value !== "") {
            var tableBody = "";
            var date = document.getElementById('pdate').value;
            var sNo = document.getElementById('sNo').value;

            var url = "SearchSampleByDtnSno?date=" + date + "&sno=" + sNo;

            $.ajax({
                type: 'POST',
                url: "SearchSampleByDtnSno",
                data: {'date': date, 'sno': sNo, '_token': $('input[name=_token]').val()},
                success: function (data) {
//                alert(data)
if (data === "    0") {
    $('#errormsg').html("Sample Not Found!");
    alert("Sample Not Found!");

    document.getElementById('fname').innerHTML = "";
    document.getElementById('lname').innerHTML = "";
    document.getElementById('age').innerHTML = "";
    document.getElementById('months').innerHTML = "";
    document.getElementById('days').innerHTML = "";
    document.getElementById('tpno').innerHTML = "";
    document.getElementById('refby').innerHTML = "";
    document.getElementById('adate').innerHTML = "";
//                        document.getElementById('arivaltime').innerHTML = "";
document.getElementById('status').innerHTML = "";
document.getElementById('fdate').innerHTML = "";
//                        document.getElementById('ftime').innerHTML = "";

if ($("#bdtime").length) {
    document.getElementById('bdtime').innerHTML = "";
}
if ($("#rctime").length) {
    document.getElementById('rctime').innerHTML = "";
}
document.getElementById('testD').innerHTML = "";
document.getElementById('resent').innerHTML = "";
document.getElementById('reverif').innerHTML = "";

document.getElementById('lpsid').value = "";
$('#lastlpsid').val("");

document.getElementById('nic').innerHTML = "";


} else {
    $('#errormsg').html("");
    var res = data.split("/&&");
    console.log(res);
    var pData = res[0];
//                    var tData = res[1];
//dev by malith
var htmlString = res[1];

// Regular expression to match id attributes
var idRegex = /id=['"]([^'"]+)['"]/g;

// Array to store id values
var idValues = [];

// Match id attributes in the HTML string and extract the id values
var match;
while ((match = idRegex.exec(htmlString)) !== null) {
    idValues.push(match[1]); // Push the id value into the array
}
var numbersOnlyArray = idValues.filter(function(value) {
    return /^\d+$/.test(value);
});
console.log(numbersOnlyArray);
//dev by malith
var lpsID = res[2];
var genderLB = res[3];
var enteredUser = res[4];



var histry_count = parseFloat(res[5]);

//dev by malith
// var refff1=res[15];
// var refff2=res[17];

// var refff3=res[16];
// var refff4=res[18];
var refff1 = [];//dev by malith
var refff2 = [];//dev by malith



// Assuming res is an array of values retrieved from the controller
// Iterate over the res array to separate the reference minimum and maximum values
for (var i = 15; i < res.length; i++) {
    // console.log((15 + res.length) / 2  - 1);
    if (i <=(((15 + res.length) / 2) - 1)) { //dev by malith
        refff1.push(res[i]);
    } else {
        refff2.push(res[i]);
    }
}

//dev by malith

//                    alert(histry_count);

if (histry_count > 1) {
    histry_count = histry_count - 1;
    $('#histry_available').html("<img style='float:left' src='images/history.png' width='28px'> <p style='font-family:arial; color:white; margin-top;0; margin-left:10; margin-right:0; padding:0; float:left'> History Available for " + histry_count + " Days</p>");
} else {
    $('#histry_available').html("");

}
//dev by malith
if (refff1 || refff2) {
    $('#ref_values').html("<button id='ref_values' class='btn' style='width: 180px;'>View Reference Values</button>");
    $('#ena-hist').html("<button id='ena-hist' class='btn' style='width: 100px;'>Enable History</button>");
} else {
    $('#ref_values').html("");
}
$(document).ready(function () {
    // Click event for the button
    $('#ref_values').one('click', function () {
        // Get the table element
        var table = $('#testD');

        // Add header for the new column
        var headerRow = table.find('tr').first();
        headerRow.append('<th><div>Reference Range</div></th>');

        // Loop through each row of the table
        table.find('tr').each(function () {
            // Find the ID of the current row
            var rowId = $(this).find('td input').attr('id');
            // console.log(rowId);
            if (rowId) {
                // Extract only the numbers from rowId
                var numericRowId = rowId.replace(/\D/g, '');

                console.log(typeof numericRowId == 'number');
                // Initialize matchFound flag
                var matchFound = false;

                // Iterate through numbersOnlyArray to find a matching value
                for (var i = 0; i < numbersOnlyArray.length; i++) {
                    // If a match is found
                    if (numbersOnlyArray[i] === numericRowId) {
                        matchFound = true;

                        // Get the corresponding refff1 and refff2 values
                        var refff1Value = refff1[i];
                        var refff2Value = refff2[i];

                        // Calculate the combined value
                        var combinedValue = refff1Value + '-' + refff2Value;

                        // Create a new cell with the combined value
                        var newCell = $('<td><div style="margin-left: -100px;">' + combinedValue + '</div></td>');

                        // Append the new cell to the current row
                        $(this).append(newCell);

                        // Exit the loop since a match is found
                        break;
                    }
                }

                // If no match is found, log an error message
                if (!matchFound) {
                    console.error("Row ID not found in numbersOnlyArray: " + numericRowId);
                }
            }
        });
    });
});

$(document).ready(function () {
    // Click event for the button
    $('#ena-hist').one('click', function () {
        // Get the table element
        var table = $('#testD');

        // Loop through each row of the table
        table.find('tr').each(function () {
            // Find the ID of the current row
            var rowId = $(this).find('td input').attr('id');
            if (rowId) {
                // Check if the rowId contains only numbers
                if (/^\d+$/.test(rowId)) {
                    // Create a new cell with the "View History" text
                    var newCell = $('<td><div style="margin-left: 0px;">View History</div></td>');

                    // Append the new cell to the current row
                    $(this).append(newCell);
                }
            }
        });
    });
});


//dev by malith







//dev by malith

selectedGender = genderLB;

//                    alert(pData)

var psData = pData.split("&");

document.getElementById('fname').innerHTML = psData[14] + ". " + psData[0];
document.getElementById('lname').innerHTML = psData[1];
document.getElementById('age').innerHTML = psData[2] + "Y ";
document.getElementById('months').innerHTML = psData[3] + "M ";
document.getElementById('days').innerHTML = psData[4] + "D ";
document.getElementById('gender').innerHTML = genderLB;
document.getElementById('tpno').innerHTML = psData[5];
document.getElementById('refby').innerHTML = psData[6];
document.getElementById('adate').innerHTML = psData[7] + " " + psData[8];
document.getElementById('status').innerHTML = psData[9];
document.getElementById('fdate').innerHTML = psData[10] + " " + psData[11];

if ($('#bdtime').length && psData[12] !== "") {
    $("#btnbd").css("background-color", "green");
    document.getElementById('bdtime').innerHTML = psData[12];
} else {
    $("#btnbd").css("background-color", "red");
}

if ($('#rctime').length && psData[13] !== "") {
    document.getElementById('rctime').innerHTML = psData[13];
    $("#btnrc").css("background-color", "green");
} else {
    $("#btnrc").css("background-color", "red");
}

if ($('#latime').length && psData[17] !== "") {
    document.getElementById('latime').innerHTML = psData[17];
    $("#btnbda").css("background-color", "green");
} else {
    $("#btnbda").css("background-color", "red");
}

if ($("#emailadd").length !== -1) {
    $("#emailadd").val(psData[15]);
}

$("#nic").html(psData[16]);



document.getElementById('testD').innerHTML = res[1];

document.getElementById('samep_oreps').innerHTML = res[6];

a01 = res[7];
a02 = res[8];

al01 = res[9];
al02 = res[10];

var confirmUser = res[11];

if(confirmUser == ""){
    confirmUser = enteredUser;
}

var VerifiedUser = res[12];

var bill_remark = res[13];

var accepted_user = res[14];






//                    document.getElementById('pending_reps').innerHTML = res[7];

document.getElementById('billremark').innerHTML = bill_remark;

document.getElementById('accepted_user').innerHTML = accepted_user;

document.getElementById('lpsid').value = lpsID;

document.getElementById('resent').innerHTML = enteredUser;

document.getElementById('confuser').innerHTML = confirmUser;

document.getElementById('reverif').innerHTML = VerifiedUser;

//                        document.getElementById('rephead').value = psData[12];

$('#lastlpsid').val(lpsID);

$('#form').find('*').filter(':input:visible:first').focus();

loadLISTable();

defactions();

// loadPendingTests();

//for centersamples
if(!isCenter(sNo)){
  loadCenterContact(sNo);  
}else{
   $("#adsms").val("");
}


}
}
});
} else {
    alert("Please enter a sample number!");
}
}

function loadCenterContact(sNo){

    var cd = sNo.substring(0, 2);

    

    $.ajax({
        url: "getCentertpno",
        type: 'POST',
        data: {'code': cd,'_token': $('input[name=_token]').val()},
        success: function (result) {
            $("#adsms").val(result);
        }
    }); 

}

function isCenter(str) {
  return /^\d{2}/.test(str);
}

function printReportWithHeading(skipREP) {

    if($("#blkrpfrdue").val() == "1"){

    $.ajax({
        url: "checkreppy",
        type: 'POST',
        data: {'date': $('#pdate').val(), 'sampleno': $('#sNo').val(),'_token': $('input[name=_token]').val()},
        success: function (result) {

            if(result === "pass"){

                printReportWithHeadingFwrd(skipREP);


            }else{
                alert("Patient has a due payment!");
            }

        }
    });

    }else{

        printReportWithHeadingFwrd(skipREP);

    }

    
}

function printReportWithHeadingFwrd(skipREP) {

    var x = "0";
    if(al01 == "1"){

        if(a01 == "Pass"){

            if(al02 == "1"){

                if(a02 == "Pass"){
                    x = "1";
                }

            }else{
                x = "1";
            }
        }

    }else{
        x = "1";
    }

    if(x == "1"){

        if ($('#lastlpsid').val() !== "") {
            if (skipREP || $('#status').html() !== "pending") {
                 
                if ($('#status').html() !== "Cancelled") {
                    var win = window.open("printreportWithHeading/" + $('#lastlpsid').val() + "&" + $('#rephead').val() + "&" + "PrintHeader", '_blank');
                    // win.print();
                    // setTimeout(function () {
                    //     win.close();
                    // }, 5000);

                    setTimeout(function () {
                        win.print();
                    }, 8000);


                    setTimeout(function () {
                        win.close();
                    }, 12000);
                } else {
                    alert("This invoie is cancelled!");
                }
            } else {
                alert("Please enter test results to continue!");
            }
        } else {
            alert("Please select a sample");
        }

    }else{
        alert("Please Confirm and Verify before print!");
    }

}

function loadPendingTests() {


    $.ajax({
        url: "loadPendingSamples_er",
        type: 'POST',
        success: function (result) {
//            alert(result)
$("#pending_reps").html(result);
}
});


}

function printReport(skipREP) {


    if($("#blkrpfrdue").val() == "1"){

    $.ajax({
        url: "checkreppy",
        type: 'POST',
        data: {'date': $('#pdate').val(), 'sampleno': $('#sNo').val(),'_token': $('input[name=_token]').val()},
        success: function (result) {

         if(result === "pass"){

            printReportFwrd(skipREP);


        }else{
            alert("Patient has a due payment!");
        }

        }
    });

    }else{

        printReportFwrd(skipREP);

    }

    
}

function printReportFwrd(skipREP){

    var x = "0";
    if(al01 == "1"){

        if(a01 == "Pass"){

            if(al02 == "1"){

                if(a02 == "Pass"){
                    x = "1";
                }

            }else{
                x = "1";
            }
        }

    }else{
        x = "1";
    }

    if(x == "1"){
        if ($('#lastlpsid').val() !== "") {
            if (skipREP || $('#status').html() === "Done") {
                if ($('#status').html() !== "Cancelled") {
                    var win = window.open("printreport/" + $('#lastlpsid').val() + "&" + $('#rephead').val(), '_blank');
                                    // win.print();
                                    // setTimeout(function () {
                                    //     win.close();
                                    // }, 8000);

                                    

                                    ReportCollect();

                                    setTimeout(function () {
                                        win.print();
                                    }, 8000);
                                    

                                    setTimeout(function () {
                                        win.close();
                                    }, 12000);

                                } else {
                                    alert("This invoie is cancelled!");
                                }
                            } else {
                                alert("Please enter test results to continue!");
                            }
                        } else {
                            alert("Please select a sample");
                        }
                    }else{
                        alert("Please Confirm and Verify before print!");
                    }

                }

    function submitForm() {

                    if ($('#status').html() !== "Cancelled") {

                        if ($('#btnsr').attr('disabled') !== "disabled") {

                            $("textarea").each(function (index) {
                                var value = $(this).val();
                                value = value.replace(new RegExp('\n', 'g'), '<br/>');
                                $(this).val(value);
                            });


                            if ($('#form')[0].checkValidity()) {
                                $('#errormsg').html("Loading...");
                                var form = $('#form');
                                $.ajax({
                                    url: form.attr('action'),
                                    type: 'POST',
                                    data: form.serialize(),
                                    success: function (result) {
                                        if (result === '1') {
                                            $('#errormsg').html("Result Entered!");

                                            $('#status').html("Done");

                            // if ($('#autosms').val() === "1") {
                                // sendSMSAgain();
                            // } 

                            search();
                        } else {

                        }
                    }
                });
                            } else {
                                alert("Please enter correct values!");
                            }

                        } else {
                            alert("Access Denied!");
                        }

                    } else {
                        alert("This invoice is Cancelled!");
                    }
                }

                $(document).keypress(function (e) {
                    if (e.which === 13) {
                        e.preventDefault();

                        if(document.getElementById("guestelement")){

                            if($("#btnsr").length){

                             submitForm(); 


                         }

                     }

                 }
             });

                function SavePrintReport() {
                    if ($('#status').html() !== "Cancelled") {
                        submitForm();
                        search();
                        printReport(true);
                    } else {
                        alert("This invoice is Cancelled!");
                    }
                }

                function LISGate() {

                    if ($('#LISWindow').height() <= 300) {
                        $('#LISWindow').height(300);
                        $('#LISWindowBtn').val("Close");
                    } else {
                        $('#LISWindow').height(25);
                        $('#LISWindowBtn').val("Open");
                    }


                }

                function loadLISTable() {



//                     var date = document.getElementById('pdate').value;
//                     var sNo = document.getElementById('sNo').value;
// //    sNo = parseInt(sNo);
// //    alert(sNo);

// $.ajax({
//     type: 'POST',
//     url: "SearchSampleLIS",
//     data: {'date': date, 'sno': sNo, '_token': $('input[name=_token]').val()},
//     success: function (data) {

//         var arr = data.split("###");

//         $('#LISWindow').height(25);

// //                    if (data !== "0") {
//     $('#LISTable').html(arr[0]);
//     $('#LISTabletno').html(arr[1]);


//     if (arr[2] !== "0") {
//         $('#lisc').html(" [ " + arr[2] + " ] ");
//     } else {
//         $('#lisc').html("");
//     }

// //                    } else {

// //                    }
// }
// });
}

function BloodDrew() {

    var date = document.getElementById('pdate').value;
    var sNo = document.getElementById('sNo').value;

    $.ajax({
        type: 'POST',
        url: "markblooddrew",
        data: {'date': date, 'sno': sNo, '_token': $('input[name=_token]').val()},
        success: function (data) {

            alert(data);

            search();
        }
    });
}

function labaccept() {

    var date = document.getElementById('pdate').value;
    var sNo = document.getElementById('sNo').value;

    $.ajax({
        type: 'POST',
        url: "markaccepttolab",
        data: {'date': date, 'sno': sNo, '_token': $('input[name=_token]').val()},
        success: function (data) {

            alert(data);

            search();
        }
    });
}

function ReportCollect() {

    if ($('#status').html() === "Done") {

        var date = document.getElementById('pdate').value;
        var sNo = document.getElementById('sNo').value;

        $.ajax({
            type: 'POST',
            url: "markreportcollected",
            data: {'date': date, 'sno': sNo, '_token': $('input[name=_token]').val()},
            success: function (data) {

                // alert(data);
                search();
            }
        });
    } else {
        alert("Sample not processed yet!");
    }
}

function setLISValue(id, val) {

    var TID = id.substring(5, id.length - 1);
    $('#' + TID).val(val);
}

function lipidCalculation(tcID, hdlID, ldlID, vldlID, trID, rfID) {
    var TC = parseFloat($('#' + tcID).val());
    var TR = parseFloat($('#' + trID).val());
    var HDL = parseFloat($('#' + hdlID).val());

    var LDL = TC - ((TR / 5) + HDL);
    var VLDL = (TC - LDL) - HDL;
    var RF = TC / HDL;

    $('#' + ldlID).val(LDL.toFixed(0));
    $('#' + vldlID).val(VLDL.toFixed(0));
    $('#' + rfID).val(RF.toFixed(1));

}
function lipidCalculation7(tcID, hdlID, ldlID, trID, rfID) {
    var TC = parseFloat($('#' + tcID).val());
    var TR = parseFloat($('#' + trID).val());
    var HDL = parseFloat($('#' + hdlID).val());

    var LDL = TC - ((TR / 5) + HDL);
    var RF = TC / HDL;

    $('#' + ldlID).val(LDL.toFixed(0));
    $('#' + rfID).val(RF.toFixed(2));

}
function lipidCalculation8(tcID, hdlID, ldlID, vldlID, trID, rfID, lhdl) {
    var TC = parseFloat($('#' + tcID).val());
    var TR = parseFloat($('#' + trID).val());
    var HDL = parseFloat($('#' + hdlID).val());

    var LDL = TC - ((TR / 5) + HDL);
    var VLDL = (TC - LDL) - HDL;
    var RF = TC / HDL;
    var lhDL = LDL / HDL;

    $('#' + ldlID).val(LDL.toFixed(0));
    $('#' + vldlID).val(VLDL.toFixed(0));
    $('#' + rfID).val(RF.toFixed(1));
    $('#' + lhdl).val(lhDL.toFixed(1));

}

function lipidCalculation32(tcID, hdlID, ldlID, vldlID, trID, rfID, lhdl) {
    var TC = parseFloat($('#' + tcID).val());
    var TR = parseFloat($('#' + trID).val());
    var HDL = parseFloat($('#' + hdlID).val());

    var LDL = TC - ((TR / 5) + HDL);
    var VLDL = (TC - LDL) - HDL;
    var RF = TC / HDL;
    var lhDL = LDL / HDL;

    $('#' + ldlID).val(LDL.toFixed(1));
    $('#' + vldlID).val(VLDL.toFixed(1));
    $('#' + rfID).val(RF.toFixed(1));
    $('#' + lhdl).val(lhDL.toFixed(1));

}

function lipidCalculation31(tcID, hdlID, ldlID, vldlID, trID, rfID, lhdl) {
    var TC = parseFloat($('#' + tcID).val());
    var TR = parseFloat($('#' + trID).val());
    var HDL = parseFloat($('#' + hdlID).val());

    var LDL = TC - ((TR / 5) + HDL);
    var VLDL = (TC - LDL) - HDL;
    var RF = TC / HDL;
    var lhDL = LDL / HDL;

    $('#' + ldlID).val(LDL.toFixed(1));
    $('#' + vldlID).val(VLDL.toFixed(1));
    $('#' + rfID).val(RF.toFixed(1));
    $('#' + lhdl).val(lhDL.toFixed(1));

}

function FBCCalculation(wbc, neu, lym, eos, mon, bas, neu2, lym2, eos2, mon2, bas2, rbc, hb, hct, mcv, mch, mchc) {

    var WBC = parseFloat($('#' + wbc).val());

    var NEU = parseFloat($('#' + neu).val());

    var LYM = parseFloat($('#' + lym).val());
    var EOS = parseFloat($('#' + eos).val());
    var MON = parseFloat($('#' + mon).val());
    var BAS = parseFloat($('#' + bas).val());

    var RBC = parseFloat($('#' + rbc).val());
    var HB = parseFloat($('#' + hb).val());
    var HCT = parseFloat($('#' + hct).val());

    $('#' + neu2).val((WBC / 100000 * NEU).toFixed(1));
    $('#' + lym2).val((WBC / 100000 * LYM).toFixed(1));
    $('#' + eos2).val((WBC / 100000 * EOS).toFixed(1));
    $('#' + mon2).val((WBC / 100000 * MON).toFixed(1));
    $('#' + bas2).val((WBC / 100000 * BAS).toFixed(1));

    $('#' + mcv).val(((HCT * 10) / RBC).toFixed(1));
    $('#' + mch).val(((HB * 10) / RBC).toFixed(1));
    $('#' + mchc).val(((HB * 100) / HCT).toFixed(1));

}

function FBCCalculation19(wbc, neu, lym, eos, mon, bas, neu2, lym2, eos2, mon2, bas2, rbc, hb, hct, mcv, mch, mchc) {

    var WBC = parseFloat($('#' + wbc).val());

    var NEU = parseFloat($('#' + neu).val());

    var LYM = parseFloat($('#' + lym).val());
    var EOS = parseFloat($('#' + eos).val());
    var MON = parseFloat($('#' + mon).val());
    var BAS = parseFloat($('#' + bas).val());

    if (NEU !== "" && LYM !== "" && EOS !== "" && MON !== "") {

        $('#' + bas).val(100 - NEU - LYM - EOS - MON);

    }

    var RBC = parseFloat($('#' + rbc).val());
    var HB = parseFloat($('#' + hb).val());
    var HCT = parseFloat($('#' + hct).val());

    $('#' + neu2).val((WBC / 100 * NEU).toFixed(2));
    $('#' + lym2).val((WBC / 100 * LYM).toFixed(2));
    $('#' + eos2).val((WBC / 100 * EOS).toFixed(2));
    $('#' + mon2).val((WBC / 100 * MON).toFixed(2));
    $('#' + bas2).val((WBC / 100 * BAS).toFixed(2));

    $('#' + mcv).val(((HCT * 10) / RBC).toFixed(3));
    $('#' + mch).val(((HB * 10) / RBC).toFixed(3));
    $('#' + mchc).val(((HB * 100) / HCT).toFixed(3));

}

function FBCCalculation9(wbc, neu, lym, eos, mon, bas, neu2, lym2, eos2, mon2, bas2, rbc, hb, hct, mcv, mch, mchc) {

    var WBC = parseFloat($('#' + wbc).val());

    var NEU = parseFloat($('#' + neu).val());

    var LYM = parseFloat($('#' + lym).val());
    var EOS = parseFloat($('#' + eos).val());
    var MON = parseFloat($('#' + mon).val());
    var BAS = parseFloat($('#' + bas).val());

    var RBC = parseFloat($('#' + rbc).val());
    var HB = parseFloat($('#' + hb).val());
    var HCT = parseFloat($('#' + hct).val());

    $('#' + neu2).val((WBC * NEU / 100).toFixed(3));
    $('#' + lym2).val((WBC * LYM / 100).toFixed(3));
    $('#' + eos2).val((WBC * EOS / 100).toFixed(3));
    $('#' + mon2).val((WBC * MON / 100).toFixed(3));
    $('#' + bas2).val((WBC * BAS / 100).toFixed(3));

    $('#' + mcv).val(((HCT * 10) / RBC).toFixed(1));
    $('#' + mch).val(((HB * 10) / RBC).toFixed(1));
    $('#' + mchc).val(((HB * 100) / HCT).toFixed(1));

}

function FBCCalculation26(wbc, neu, lym, eos, mon, bas, neu2, lym2, eos2, mon2, bas2, rbc, hb, hct, mcv, mch, mchc) {

    var WBC = parseFloat($('#' + wbc).val());

    var NEU = parseFloat($('#' + neu).val());

    var LYM = parseFloat($('#' + lym).val());
    var EOS = parseFloat($('#' + eos).val());
    var MON = parseFloat($('#' + mon).val());
    var BAS = parseFloat($('#' + bas).val());

    var RBC = parseFloat($('#' + rbc).val());
    var HB = parseFloat($('#' + hb).val());
    var HCT = parseFloat($('#' + hct).val());

    $('#' + neu2).val((WBC * NEU / 100).toFixed(0));
    $('#' + lym2).val((WBC * LYM / 100).toFixed(0));
    $('#' + eos2).val((WBC * EOS / 100).toFixed(0));
    $('#' + mon2).val((WBC * MON / 100).toFixed(0));
    $('#' + bas2).val((WBC * BAS / 100).toFixed(0));

    $('#' + mcv).val(((HCT * 10) / RBC).toFixed(1));
    $('#' + mch).val(((HB * 10) / RBC).toFixed(1));
    $('#' + mchc).val(((HB * 100) / HCT).toFixed(1));

}

function HBCal(wbc, neu, lym, eos, mon, bas, neu2, lym2, eos2, mon2, bas2, rbc, hb, hct, mcv, mch, mchc) {

    var WBC = parseFloat($('#' + wbc).val());

    var NEU = parseFloat($('#' + neu).val());

    var LYM = parseFloat($('#' + lym).val());
    var EOS = parseFloat($('#' + eos).val());
    var MON = parseFloat($('#' + mon).val());
    var BAS = parseFloat($('#' + bas).val());

    var RBC = parseFloat($('#' + rbc).val());
    var HB = parseFloat($('#' + hb).val());
    var HCT = parseFloat($('#' + hct).val());

    $('#' + neu2).val((WBC * NEU / 100).toFixed(3));
    $('#' + lym2).val((WBC * LYM / 100).toFixed(3));
    $('#' + eos2).val((WBC * EOS / 100).toFixed(3));
    $('#' + mon2).val((WBC * MON / 100).toFixed(3));
    $('#' + bas2).val((WBC * BAS / 100).toFixed(3));

    $('#' + mcv).val(((HCT * 10) / RBC).toFixed(1));
    $('#' + mch).val(((HB * 10) / RBC).toFixed(1));
    $('#' + mchc).val(((HB * 100) / HCT).toFixed(1));

}

function HBCal(wbc, neu, lym, eos, mon, bas, neu2, lym2, eos2, mon2, bas2, rbc, hb, hct, mcv, mch, mchc) {

    var WBC = parseFloat($('#' + wbc).val());

    var NEU = parseFloat($('#' + neu).val());

    var LYM = parseFloat($('#' + lym).val());
    var EOS = parseFloat($('#' + eos).val());
    var MON = parseFloat($('#' + mon).val());
    var BAS = parseFloat($('#' + bas).val());

    var RBC = parseFloat($('#' + rbc).val());
    var HB = parseFloat($('#' + hb).val());
    var HCT = parseFloat($('#' + hct).val());

    $('#' + neu2).val((WBC * NEU / 100).toFixed(3));
    $('#' + lym2).val((WBC * LYM / 100).toFixed(3));
    $('#' + eos2).val((WBC * EOS / 100).toFixed(3));
    $('#' + mon2).val((WBC * MON / 100).toFixed(3));
    $('#' + bas2).val((WBC * BAS / 100).toFixed(3));

    $('#' + mcv).val(((HCT * 10) / RBC).toFixed(1));
    $('#' + mch).val(((HB * 10) / RBC).toFixed(1));
    $('#' + mchc).val(((HB * 100) / HCT).toFixed(1));

}

function HBCal18(rbc, hb, hct, mcv, mch, mchc) {

    var RBC = parseFloat($('#' + rbc).val());
    var HB = parseFloat($('#' + hb).val());
    var HCT = parseFloat($('#' + hct).val());

    $('#' + mcv).val(((HCT * 10) / RBC).toFixed(1));
    $('#' + mch).val(((HB * 10) / RBC).toFixed(1));
    $('#' + mchc).val(((HB * 100) / HCT).toFixed(1));

}

function TOTALPROTGLOB(prot, alb, glog, ratio) {

    var prot = parseFloat($('#' + prot).val());
    var alb = parseFloat($('#' + alb).val());
    var glob = prot - alb;

    $('#' + glog).val(glob.toFixed(1));
    $('#' + ratio).val((alb/glob).toFixed(1));

} 

function TOTALPROT45(prot, alb, glog) {

    var prot = parseFloat($('#' + prot).val());
    var alb = parseFloat($('#' + alb).val());
    var glob = prot - alb;

    $('#' + glog).val(glob.toFixed(1));

} 

function GFRCalculation(scELE, gfrELE) {

    var gender = $("#gender").html();
    var race = "Other";
    var creatinine = parseFloat($('#' + scELE).val());
    var age = parseFloat($("#age").html());

    var alpha;
    var kVal;
    if (gender === "Male") {
        alpha = -0.411;
        kVal = 0.9;
    } else {
        alpha = -0.329;
        kVal = 0.7;
    }

    var ifblack = 0;
    if (race === "black") {
        ifblack = 1;
    }

    var GFR;
    if (gender === "Male") {
        GFR = 141 * Math.pow(Math.min(creatinine / kVal, 1), alpha) * Math.pow(Math.max(creatinine / kVal, 1), -1.209) * Math.pow(0.993, age);
    } else {
        GFR = 141 * Math.pow(Math.min(creatinine / kVal, 1), alpha) * Math.pow(Math.max(creatinine / kVal, 1), -1.209) * Math.pow(0.993, age) * 1.018;
    }

    $("#" + gfrELE).val(Math.ceil(GFR));

}

function CreatinineWithumolAndGfrCalculation(scELE, scELEinMMOL, gfrELE) {

    var gender = $("#gender").html();
    var race = "Other";
    var creatinine = parseFloat($('#' + scELE).val());
    var age = parseFloat($("#age").html());

    var alpha;
    var kVal;
    if (gender === "Male") {
        alpha = -0.411;
        kVal = 0.9;
    } else {
        alpha = -0.329;
        kVal = 0.7;
    }

    var ifblack = 0;
    if (race === "black") {
        ifblack = 1;
    }

    var GFR;
    if (gender === "Male") {
        GFR = 141 * Math.pow(Math.min(creatinine / kVal, 1), alpha) * Math.pow(Math.max(creatinine / kVal, 1), -1.209) * Math.pow(0.993, age);
    } else {
        GFR = 141 * Math.pow(Math.min(creatinine / kVal, 1), alpha) * Math.pow(Math.max(creatinine / kVal, 1), -1.209) * Math.pow(0.993, age) * 1.018;
    }
    
    var creatinineuMOL = parseFloat($("#" + scELE).val() * 88.42);
    $("#" + scELEinMMOL).val(creatinineuMOL.toFixed(1));;
    $("#" + gfrELE).val(Math.ceil(GFR));
    

}

function creatinine_mgAndumol(scELE, scELEinMMOL) {

    var creatinineuMOL = parseFloat($("#" + scELE).val() * 88.42);
    $("#" + scELEinMMOL).val(creatinineuMOL.toFixed(1));;
    $("#" + gfrELE).val(Math.ceil(GFR));
    

}

function creatinine_micromol_to_mg(scELE, scELEinMicroMOL) {

var sceaVal = parseFloat($("#" + scELE).val() / 88.4);
$("#" + scELEinMicroMOL).val(sceaVal.toFixed(1));;



}

function GFRCalculation2(scELE, gfrELE) {

    var gender = $("#gender").html();
    var race = "Other";
    var creatinine = parseFloat($('#' + scELE).val());
    var age = parseFloat($("#age").html());

    var alpha;
    var kVal;
    if (gender === "Male") {
        alpha = -0.411;
        kVal = 0.9;
    } else {
        alpha = -0.329;
        kVal = 0.7;
    }

    var ifblack = 0;
    if (race === "black") {
        ifblack = 1;
    }

    var GFR;
    if (gender === "Male") {
        GFR = 141 * Math.pow(Math.min(creatinine / kVal, 1), alpha) * Math.pow(Math.max(creatinine / kVal, 1), -1.209) * Math.pow(0.993, age);
    } else {
        GFR = 141 * Math.pow(Math.min(creatinine / kVal, 1), alpha) * Math.pow(Math.max(creatinine / kVal, 1), -1.209) * Math.pow(0.993, age) * 1.018;
    }

    $("#" + gfrELE).val(Math.ceil(GFR));

}


function GFRCalculation3(scELE, gfrELE) {

var gender = $("#gender").html();
var race = "Other";
var creatinine = parseFloat($('#' + scELE).val());
var age = parseFloat($("#age").html());

var alpha;
var kVal;
if (gender === "Male") {
    alpha = -0.302;
    kVal = 0.9;
} else {
    alpha = -0.241;
    kVal = 0.7;
}

var ifblack = 0;
if (race === "black") {
    ifblack = 1;
}

var GFR;
if (gender === "Male") {
    GFR = 142 * Math.pow(Math.min(creatinine / kVal, 1), alpha) * Math.pow(Math.max(creatinine / kVal, 1), -1.200) * Math.pow(0.9938, age);
} else {
    GFR = 142 * Math.pow(Math.min(creatinine / kVal, 1), alpha) * Math.pow(Math.max(creatinine / kVal, 1), -1.200) * Math.pow(0.9938, age) * 1.012;
}

$("#" + gfrELE).val(Math.ceil(GFR));

}

function BUNCalculation(buELE, bunELE) {

    var bun = parseFloat($("#" + buELE).val() / 2.14);

    $("#" + bunELE).val(bun.toFixed(1));
}

function cortisolCalculation(val1,val2){
    var newVal =  parseFloat($("#" + val1).val() * 27.64);
    $("#" + val2).val(newVal.toFixed(1));
}

function sugarCalucation(val1,val2){
    var newVal =  parseFloat($("#" + val1).val() * 0.0555);
    $("#" + val2).val(newVal.toFixed(1));
}

function CalculateUForMAlbumin(umaELE, ucELE, uacELE) {

var uac = (parseFloat($("#" + umaELE).val()) / parseFloat($("#" + ucELE).val())) * 100;

$("#" + uacELE).val(uac.toFixed(2));
}

function CalculateUForMAlbumin32(umaELE, ucELE, uacELE) {

    var uac = (parseFloat($("#" + umaELE).val()) / parseFloat($("#" + ucELE).val())) * 1000;

    $("#" + uacELE).val(uac.toFixed(2));
}

function CalculateUAlbCreRatio19(uaELE, ucre, uacELE) {

    var uac = (parseFloat($("#" + uaELE).val()) / parseFloat($("#" + ucre).val())) * 1000;

    $("#" + uacELE).val(uac.toFixed(2));
}

function CalculateUAlbCreRatio28(uaELE, ucre, uacELE) {

    var uac = (parseFloat($("#" + uaELE).val()) / parseFloat($("#" + ucre).val()));

    $("#" + uacELE).val(uac.toFixed(2));
}


function CalculateUAlbCreRatio41(uaELE, ucre, uacELE) {

    var uac = ((parseFloat($("#" + uaELE).val()) * 100) / parseFloat($("#" + ucre).val()));

    $("#" + uacELE).val(uac.toFixed(2));
}

function CalculateUAlbuminCreRatio(umaELE, ucELE, uacELE) {

    var uac = (parseFloat($("#" + umaELE).val()) / parseFloat($("#" + ucELE).val())) * 1000;

    $("#" + uacELE).val(uac.toFixed(1));
}

function CalculateUAlbuminCreRatio30(umaELE, ucELE, uacELE) {

    var uac = (parseFloat($("#" + umaELE).val()) / parseFloat($("#" + ucELE).val())) * 100;

    $("#" + uacELE).val(uac.toFixed(1));
}

function calculateLeverProfile(tpELE, alELE, gloELE, agELE) {

    var glo = parseFloat($("#" + tpELE).val()) - parseFloat($("#" + alELE).val());
    var ag = parseFloat($("#" + alELE).val()) / glo;

    $("#" + gloELE).val(glo.toFixed(1));
    $("#" + agELE).val(ag.toFixed(2));

}

function calculateLeverProfileToTwoDecimalPoints(tpELE, alELE, gloELE, agELE) {

var glo = parseFloat($("#" + tpELE).val()) - parseFloat($("#" + alELE).val());
var ag = parseFloat($("#" + alELE).val()) / glo;

$("#" + gloELE).val(glo.toFixed(2));
$("#" + agELE).val(ag.toFixed(2));

}

function calculateRFT(tpELE, alELE, gloELE) {

    var glo = parseFloat($("#" + tpELE).val()) - parseFloat($("#" + alELE).val());
    var ag = parseFloat($("#" + alELE).val()) / glo;

    $("#" + gloELE).val(glo.toFixed(1));

}

function calculateBILDIRINDIR(btELE, bdELE, biELE) {

    var blin = parseFloat($("#" + btELE).val()) - parseFloat($("#" + bdELE).val());
    $("#" + biELE).val(blin.toFixed(2));
}

function calculateCorrectedCalsium(tcELE, saELE, ccELE) {
    var tc = parseFloat($("#" + tcELE).val());
    var sa = parseFloat($("#" + saELE).val());

    var cc = tc + 0.020 * (40 - sa);

    $("#" + ccELE).val(cc.toFixed(1));
}

function calculateCorrectedCalsiumTwoDecimal(tcELE, saELE, ccELE) {
    var tc = parseFloat($("#" + tcELE).val());
    var sa = parseFloat($("#" + saELE).val());

    var cc = tc + 0.020 * (40 - sa);

    $("#" + ccELE).val(cc.toFixed(2));
}

function calculateCorrectedCalsium28(tcELE, saELE, ccELE) {
    var tc = parseFloat($("#" + tcELE).val());
    var sa = parseFloat($("#" + saELE).val());

    var cc = tc + 0.8 * (4.0 - sa);

    $("#" + ccELE).val(cc.toFixed(2));
}

function calculateCorrectedCalsium31(tcELE, saELE, ccELE) {
    var tc = parseFloat($("#" + tcELE).val());
    var sa = parseFloat($("#" + saELE).val());

    var cc = tc + 0.020 * (4.0 - sa);

    $("#" + ccELE).val(cc.toFixed(1));
}

function UrineProteinExcretion(uVol, uProtein, excretion) {
    var vol = parseFloat($("#" + uVol).val());
    var protein = parseFloat($("#" + uProtein).val());

    var excre = (vol/1000)*protein;

    $("#" + excretion).val(excre.toFixed(1));
}

function calculateCorrectedCalsium18(tcELE, saELE, ccELE) {
    var tc = parseFloat($("#" + tcELE).val());
    var sa = parseFloat($("#" + saELE).val());

    var cc = (tc + 0.8) * (4 - sa);
//    var cc = ( 40 - sa * 10) / (40 + tc) 

$("#" + ccELE).val(cc.toFixed(2));
}

function urineProteinCreatinineRatio(uPro, uCrea, ratio) {
    var up = parseFloat($("#" + uPro).val());
    var uc = parseFloat($("#" + uCrea).val());

    var ur = (up/uc);


    $("#" + ratio).val(ur.toFixed(2));
}

function calculateUrineProtineRatio(upELE, upcELE, ucELE) {
    var upc = parseFloat($("#" + upELE).val()) / parseFloat($("#" + ucELE).val());
    $("#" + upcELE).val(upc.toFixed(2));
}

function calculateWBCDC(wbc, neu, lym, eos, mon, bas, neu2, lym2, eos2, mon2, bas2) {
    var WBC = parseFloat($('#' + wbc).val());
    var NEU = parseFloat($('#' + neu).val());
    var LYM = parseFloat($('#' + lym).val());
    var EOS = parseFloat($('#' + eos).val());
    var MON = parseFloat($('#' + mon).val());
    var BAS = parseFloat($('#' + bas).val());

    $('#' + neu2).val((WBC / 100 * NEU).toFixed(0));
    $('#' + lym2).val((WBC / 100 * LYM).toFixed(0));
    $('#' + eos2).val((WBC / 100 * EOS).toFixed(0));
    $('#' + mon2).val((WBC / 100 * MON).toFixed(0));
    $('#' + bas2).val((WBC / 100 * BAS).toFixed(0));
}

function calculateWBCDCWithACforTwoDecimal(wbc, neu, lym, eos, mon, bas, neu2, lym2, eos2, mon2, bas2) {
    var WBC = parseFloat($('#' + wbc).val());
    var NEU = parseFloat($('#' + neu).val());
    var LYM = parseFloat($('#' + lym).val());
    var EOS = parseFloat($('#' + eos).val());
    var MON = parseFloat($('#' + mon).val());
    var BAS = parseFloat($('#' + bas).val());

    

    var Neu_Ab = WBC * (NEU/100);
    var Lym_Ab = WBC * (LYM/100);
    var Mono_Ab = WBC * (MON/100);
    var Eos_Ab = WBC * (EOS/100);
    
   
    $('#' + neu2).val((WBC / 100 * NEU).toFixed(2));
    $('#' + lym2).val((WBC / 100 * LYM).toFixed(2));
    $('#' + eos2).val((WBC / 100 * EOS).toFixed(2));
    $('#' + mon2).val((WBC / 100 * MON).toFixed(2));
    // $('#' + bas2).val((WBC / 100 * BAS).toFixed(2));
    
    $('#' + bas).val((100 - (NEU + LYM + EOS + MON)).toFixed(1));
    $('#' + bas2).val((WBC - (Neu_Ab + Lym_Ab + Mono_Ab + Eos_Ab)).toFixed(2));
}

function calculateWBCDC9(wbc, neu, lym, eos, mon, bas, neu2, lym2, eos2, mon2, bas2) {
    var WBC = parseFloat($('#' + wbc).val());
    var NEU = parseFloat($('#' + neu).val());
    var LYM = parseFloat($('#' + lym).val());
    var EOS = parseFloat($('#' + eos).val());
    var MON = parseFloat($('#' + mon).val());
    var BAS = parseFloat($('#' + bas).val());

    $('#' + neu2).val((WBC / 100 * NEU).toFixed(3));
    $('#' + lym2).val((WBC / 100 * LYM).toFixed(3));
    $('#' + eos2).val((WBC / 100 * EOS).toFixed(3));
    $('#' + mon2).val((WBC / 100 * MON).toFixed(3));
    $('#' + bas2).val((WBC / 100 * BAS).toFixed(3));
}
function calculateSerumIron(siELE, uibcELE, tibcELE, transELE) {
    var si = parseFloat($('#' + siELE).val());
    var uibc = parseFloat($('#' + uibcELE).val());

    var tibc = si + uibc;

    $('#' + tibcELE).val(tibc.toFixed(1));
    $('#' + transELE).val(((si / tibc) * 100).toFixed(1));

}

function calculateFBCmmol(fbcELE, mmolELE) {
    var fbc = parseFloat($('#' + fbcELE).val());
    var mmol = fbc / 18;
    $('#' + mmolELE).val(mmol.toFixed(1));
}

function calculateSPGlobulin(pELE, alELE, bloELE) {
    var sp = parseFloat($('#' + pELE).val());
    var al = parseFloat($('#' + alELE).val());
    var glo = sp - al;
    $('#' + bloELE).val(glo.toFixed(1));
}

function calculateSPGlobulin(pELE, alELE, bloELE) {
    var sp = parseFloat($('#' + pELE).val());
    var al = parseFloat($('#' + alELE).val());
    var glo = sp - al;
    $('#' + bloELE).val(glo.toFixed(1));
}

function ptInr(test, control, inr) {
    var test_v = parseFloat($('#' + test).val());
    var control_v = parseFloat($('#' + control).val());
    var inr_v = test_v/control_v;
    $('#' + inr).val(inr_v.toFixed(2));
}

function ptInrWithISI(test, control, isi, inr) {
    var test_v = parseFloat($('#' + test).val());
    var control_v = parseFloat($('#' + control).val());
    var isi_v = parseFloat($('#' + isi).val());
    var inr_v = Math.pow(test_v/control_v,isi_v); 
    $('#' + inr).val(inr_v.toFixed(2));
}

function hb1cmmol(hb1cELE, mmELE) {
    var hbc = parseFloat($('#' + hb1cELE).val());

    var mml = (10.93 * hbc) - 23.50;

    $('#' + mmELE).val(mml.toFixed(1));
}

function calculateHBPerVal(hb1cELE, perELE) {
    var hbc = parseFloat($('#' + hb1cELE).val());

    var per = (hbc * 100) / 15;

    $('#' + perELE).val(per.toFixed(2));
}

function calculateDC(eleNUE, eleLym, eleMON, eleEOS, eleBAS) {
    var NEU = parseFloat($('#' + eleNUE).val());
    var LYM = parseFloat($('#' + eleLym).val());
    var EOS = parseFloat($('#' + eleMON).val());
    var MON = parseFloat($('#' + eleEOS).val());
    var BAS = parseFloat($('#' + eleBAS).val());


    $('#' + eleBAS).val((100 - (NEU + LYM + EOS + MON)).toFixed(0));

    //validate DC total - 100
    if ((NEU + LYM + EOS + MON + BAS) === 100) {
        $('#idpr').prop('disabled', false);
        $('#idsnp').prop('disabled', false);
        $('#btnsr').prop('disabled', false);
    } /*else {
        // alert("Please check DC values!");        
        $('#idpr').prop('disabled', true);
        $('#idsnp').prop('disabled', true);
        $('#btnsr').prop('disabled', true);
    }*/
}

function calculateDCToOneDecimal(eleNUE, eleLym, eleMON, eleEOS, eleBAS) {
    var NEU = parseFloat($('#' + eleNUE).val());
    var LYM = parseFloat($('#' + eleLym).val());
    var EOS = parseFloat($('#' + eleMON).val());
    var MON = parseFloat($('#' + eleEOS).val());
    var BAS = parseFloat($('#' + eleBAS).val());


    $('#' + eleBAS).val((100 - (NEU + LYM + EOS + MON)).toFixed(0));

    //validate DC total - 100
    if ((NEU + LYM + EOS + MON + BAS) === 100) {
        $('#idpr').prop('disabled', false);
        $('#idsnp').prop('disabled', false);
        $('#btnsr').prop('disabled', false);
    } /*else {
        // alert("Please check DC values!");        
        $('#idpr').prop('disabled', true);
        $('#idsnp').prop('disabled', true);
        $('#btnsr').prop('disabled', true);
    }*/
}

function check100AtBaso(eleNUE, eleLym, eleMON, eleEOS, eleBAS){
    var NEU = parseFloat($('#' + eleNUE).val());
    var LYM = parseFloat($('#' + eleLym).val());
    var EOS = parseFloat($('#' + eleMON).val());
    var MON = parseFloat($('#' + eleEOS).val());
    var BAS = parseFloat($('#' + eleBAS).val());

    //validate DC total - 100
    if ((NEU + LYM + EOS + MON + BAS) === 100) {
        $('#idpr').prop('disabled', false);
        $('#idsnp').prop('disabled', false);
        $('#btnsr').prop('disabled', false);
    }else{
        alert("Please check DC values!");
        
    }

    
}

function calculateDC35(eleNUE, eleLym, eleMON, eleEOS, eleBAS) {
    var NEU = parseFloat($('#' + eleNUE).val());
    var LYM = parseFloat($('#' + eleLym).val());
    var EOS = parseFloat($('#' + eleMON).val());
    var MON = parseFloat($('#' + eleEOS).val());
    var BAS = parseFloat($('#' + eleBAS).val());


    $('#' + eleBAS).val((100 - (NEU + LYM + EOS + MON)).toFixed(0));

    //validate DC total - 100
    // if ((NEU + LYM + EOS + MON + BAS) === 100) {
    //     $('#idpr').prop('disabled', false);
    //     $('#idsnp').prop('disabled', false);
    //     $('#btnsr').prop('disabled', false);
    // } else {
    //     // alert("Please check DC values!");        
    //     $('#idpr').prop('disabled', true);
    //     $('#idsnp').prop('disabled', true);
    //     $('#btnsr').prop('disabled', true);
    // }
}

function calculateDCWithAbosuluteCount(wbcCount, eleNUE, eleLym, eleMON, eleEOS, eleBAS, NeuAb, LymAb, MonoAb, EosAb, BasoAb) {
    var WBC = parseFloat($('#' + wbcCount).val());
    var NEU = parseFloat($('#' + eleNUE).val());
    var LYM = parseFloat($('#' + eleLym).val());
    var EOS = parseFloat($('#' + eleEOS).val());
    var MON = parseFloat($('#' + eleMON).val());
    var BAS = parseFloat($('#' + eleBAS).val());

    var Neu_Ab = WBC * (NEU/100);
    var Lym_Ab = WBC * (LYM/100);
    var Mono_Ab = WBC * (MON/100);
    var Eos_Ab = WBC * (EOS/100);

    

    $('#' + NeuAb).val((WBC * (NEU/100)).toFixed(0));
    $('#' + LymAb).val((WBC * (LYM/100)).toFixed(0));
    $('#' + MonoAb).val((WBC * (MON/100)).toFixed(0));
    $('#' + EosAb).val((WBC * (EOS/100)).toFixed(0));
    
    var bas = (100 - (NEU + LYM + EOS + MON));

    if(bas < 10){
        $('#' + eleBAS).val("0"+(100 - (NEU + LYM + EOS + MON)).toFixed(0));
    }else{
        $('#' + eleBAS).val((100 - (NEU + LYM + EOS + MON)).toFixed(0));
    }

    
    $('#' + BasoAb).val((WBC - (Neu_Ab + Lym_Ab + Mono_Ab + Eos_Ab)).toFixed(0));



    // //validate DC total - 100
    // if ((NEU + LYM + EOS + MON + BAS) === 100) {
    //     $('#idpr').prop('disabled', false);
    //     $('#idsnp').prop('disabled', false);
    //     $('#btnsr').prop('disabled', false);
    // } else {
    //     // alert("Please check DC values!");        
    //     $('#idpr').prop('disabled', true);
    //     $('#idsnp').prop('disabled', true);
    //     $('#btnsr').prop('disabled', true);
    // }
}

function calculateBasophilsAbsolute(wbcCount,eleBAS,BasoAb){
    var WBC = parseFloat($('#' + wbcCount).val());
    var BAS = parseFloat($('#' + eleBAS).val());

    $('#' + BasoAb).val((WBC * (BAS/100)).toFixed(0));
}

function calculateBasophilsAbsoluteWithDecimal(wbcCount,eleBAS,BasoAb){
    var WBC = parseFloat($('#' + wbcCount).val());
    var BAS = parseFloat($('#' + eleBAS).val());

    $('#' + BasoAb).val((WBC * (BAS/100)).toFixed(2));
}

function calculateDCWithAbosuluteCountWithDeciaml(wbcCount, eleNUE, eleLym, eleMON, eleEOS, eleBAS, NeuAb, LymAb, MonoAb, EosAb, BasoAb, rbc, hb, hct, mcv, mch, mchc) {
    var WBC = parseFloat($('#' + wbcCount).val());
    var NEU = parseFloat($('#' + eleNUE).val());
    var LYM = parseFloat($('#' + eleLym).val());
    var EOS = parseFloat($('#' + eleEOS).val());
    var MON = parseFloat($('#' + eleMON).val());
    var BAS = parseFloat($('#' + eleBAS).val());

    var rbc_val = parseFloat($('#' + rbc).val());
    var hb_val = parseFloat($('#' + hb).val());
    var hct_val = parseFloat($('#' + hct).val());

    var Neu_Ab = WBC * (NEU/100);
    var Lym_Ab = WBC * (LYM/100);
    var Mono_Ab = WBC * (MON/100);
    var Eos_Ab = WBC * (EOS/100); 

    $('#' + NeuAb).val((WBC * (NEU/100)).toFixed(2));
    $('#' + LymAb).val((WBC * (LYM/100)).toFixed(2));
    $('#' + MonoAb).val((WBC * (MON/100)).toFixed(2));
    $('#' + EosAb).val((WBC * (EOS/100)).toFixed(2));
    

    $('#' + eleBAS).val((100 - (NEU + LYM + EOS + MON)).toFixed(1));
    $('#' + BasoAb).val((WBC - (Neu_Ab + Lym_Ab + Mono_Ab + Eos_Ab)).toFixed(2));

    $('#' + mcv).val(((hct_val/rbc_val)*10).toFixed(1));
    $('#' + mch).val(((hb_val/rbc_val)*10).toFixed(1));
    $('#' + mchc).val(((hb_val/hct_val)*100).toFixed(1));


    // //validate DC total - 100
    // if ((NEU + LYM + EOS + MON + BAS) === 100) {
    //     $('#idpr').prop('disabled', false);
    //     $('#idsnp').prop('disabled', false);
    //     $('#btnsr').prop('disabled', false);
    // } else {
    //     // alert("Please check DC values!");        
    //     $('#idpr').prop('disabled', true);
    //     $('#idsnp').prop('disabled', true);
    //     $('#btnsr').prop('disabled', true);
    // }
}

function otherCalculationsInFBCLab39(rbc, hb, hct, mcv, mch, mchc) {
    
    var rbc_val = parseFloat($('#' + rbc).val());
    var hb_val = parseFloat($('#' + hb).val());
    var hct_val = parseFloat($('#' + hct).val());


    $('#' + mcv).val(((hct_val/rbc_val)*10).toFixed(1));
    $('#' + mch).val(((hb_val/rbc_val)*10).toFixed(1));
    $('#' + mchc).val(((hb_val/hct_val)*100).toFixed(1));


}

function confirmDC(eleNUE, eleLym, eleMON, eleEOS, eleBAS) {
    var NEU = parseFloat($('#' + eleNUE).val());
    var LYM = parseFloat($('#' + eleLym).val());
    var EOS = parseFloat($('#' + eleMON).val());
    var MON = parseFloat($('#' + eleEOS).val());
    var BAS = parseFloat($('#' + eleBAS).val());

    //validate DC total - 100 
    if ((NEU + LYM + EOS + MON + BAS) === 100) {
        $('#idpr').prop('disabled', false);
        $('#idsnp').prop('disabled', false);
        $('#btnsr').prop('disabled', false);
    } else {
        alert("Please check DC values!");
        $('#idpr').prop('disabled', true);
        $('#idsnp').prop('disabled', true);
        $('#btnsr').prop('disabled', true);
    }
}

function calculateDCGML(eleNUE, eleLym, eleMON, eleEOS) {
    var NEU = parseFloat($('#' + eleNUE).val());
    var LYM = parseFloat($('#' + eleLym).val());
    var EOS = parseFloat($('#' + eleEOS).val());
    var MON = parseFloat($('#' + eleMON).val());

    $('#' + eleMON).val("0" + (100 - (NEU + LYM + EOS)).toFixed(0));

}

function confirmDCGML(eleNUE, eleLym, eleMON, eleEOS) {
    var NEU = parseFloat($('#' + eleNUE).val());
    var LYM = parseFloat($('#' + eleLym).val());
    var EOS = parseFloat($('#' + eleMON).val());
    var MON = parseFloat($('#' + eleEOS).val());

    //validate DC total - 100 
    if ((NEU + LYM + EOS + MON) === 100) {
        $('#idpr').prop('disabled', false);
        $('#idsnp').prop('disabled', false);
        $('#btnsr').prop('disabled', false);
    } else {
        $('#idpr').prop('disabled', true);
        $('#idsnp').prop('disabled', true);
        $('#btnsr').prop('disabled', true);
    }
}

function SICfromCPC(fbcSIC) {
//    var CIS = parseFloat($('#' + fbcSIC).val());
//    var newval = CIS / 2;
//    $('#' + fbcSIC).val(newval.toFixed(2));
}

function sendSMSAgain() {
    if ($('#status').html() !== "pending") {
        if ($('#tpno').html() !== "") {
            var i = confirm("Are you sure you want to send the SMS?");
            if (i) {
                var tpno = $('#tpno').html();
                var sNo = $('#sNo').val();
                var date = $('#pdate').val();
                var msgType = "ReportReady";
                var name = $('#fname').html() + " " + $('#lname').html();

//                alert(tpno + " " + msgType+" "+sNo);

$.ajax({
    type: 'POST',
    url: "sendsms",
    data: {'tp': tpno, 'sno': sNo, 'type': msgType, 'name': name, 'date': date, '_token': $('input[name=_token]').val()},
    success: function (data) {
        var win = window.open(data);
        setTimeout(function () {
            win.close();
        }, 5000);
    }
});
}

} else {
    alert("Please enter contact number to send SMS!");
}

} else {
    alert("Report is not ready to send SMS!");
}
}

function sendEmailAgain() {
    if ($('#status').html() !== "pending") {
        if ($('#emailadd').val() !== "") {
            var i = confirm("Are you sure you want to send the Email?");
            if (i) {
                var email = $('#emailadd').val();
                var contact = $('#tpno').html();
                var sNo = $('#sNo').val();
                var msgType = "ReportReady";
                var name = $('#fname').html() + " " + $('#lname').html();
                var sampledate = $('#pdate').val();

//                alert(contact)

$.ajax({
    type: 'POST',
    url: "sendemail",
    data: {'email': email, 'sno': sNo, 'type': msgType, 'name': name, 'sdate': sampledate, 'tpno': contact, '_token': $('input[name=_token]').val()},
    success: function (data) {
        alert(data);
//                        var win = window.open(data);
//                        setTimeout(function () {
//                            win.close();
//                        }, 3000);
}
});
}

} else {
    alert("Please enter email address to send Email!");
}

} else {
    alert("Report is not ready to send Email!");
}

}


function sendSMSReport() {
    if ($('#status').html() !== "pending") {
        if ($('#tpno').html() !== "") {
            var i = confirm("Are you sure you want to send the SMS report?");
            if (i) {
                var tpno = $('#tpno').html();
                var sNo = $('#sNo').val();
                var date = $('#pdate').val();

                var msgType = "ReportReady";
                var name = $('#fname').html() + " " + $('#lname').html();

                var form = $('#form');
                var testDetails = form.serialize();

//                alert(testDetails)

$.ajax({
    type: 'POST',
    url: "sendsms?" + testDetails,
    data: {'tp': tpno, 'sno': sNo, 'type': msgType, 'name': name, 'date': date, '_token': $('input[name=_token]').val()}, testDetails,
    success: function (data) {
        var win = window.open(data);
        setTimeout(function () {
            win.close();
        }, 5000);
    }
});
}

} else {
    alert("Please enter contact number to send SMS!");
}

} else {
    alert("Report is not ready to send SMS report!");
}
}

function sendEMSMS() {
    if ($('#status').html() !== "pending") {
        if ($('#tpno').html() !== "") {
            var i = confirm("Are you sure you want to send the emergency alert?");
            if (i) {
                var tpno = $('#tpno').html();
                var sNo = $('#sNo').val();
                var date = $('#pdate').val();

                var msgType = "ReportEmergency";
                var name = $('#fname').html() + " " + $('#lname').html();

                var form = $('#form');
                var testDetails = form.serialize();

//                alert(testDetails)

$.ajax({
    type: 'POST',
    url: "sendsms?" + testDetails,
    data: {'tp': tpno, 'sno': sNo, 'type': msgType, 'name': name, 'date': date, '_token': $('input[name=_token]').val()}, testDetails,
    success: function (data) {
        var win = window.open(data);
        setTimeout(function () {
            win.close();
        }, 5000);
    }
});
}

} else {
    alert("Please enter contact number to send SMS!");
}

} else {
    alert("Report is not ready to send SMS report!");
}
}

function defactions() {
    $("input").on("keydown", function (event) {

        if (event.keyCode === 38) {
            event.preventDefault();
            var fields = $(this).parents('form:eq(0),body').find('button,input,textarea,select');
            var index = fields.index(this);
            if (index > -1 && (index + 1) < fields.length) {
                fields.eq(index - 1).focus();
            }

        }
        if (event.keyCode === 40) {
            event.preventDefault();
            var fields = $(this).parents('form:eq(0),body').find('button,input,textarea,select');
            var index = fields.index(this);
            if (index > -1 && (index + 1) < fields.length) {
                fields.eq(index + 1).focus();
            }

        }
    });
}

function viewPatient() {
    window.location = "viewOP?lpsid=" + $('#lastlpsid').val();
}


function openpanel() {

    if ($("#patientD").css('display') === 'none') {
        $('#patientD').css("display", "block");
        $('#patientDHead').css("display", "block");
    } else {
        $('#patientD').css("display", "none");
        $('#patientDHead').css("display", "none");
    }

}

function opentab(element) {
    $("#pdatatable").hide();
    $("#timetable").hide();
    $("#smstable").hide();

    $("#" + element).show();
}

function openRep(lpsid) {

    var sno = lpsid.split("+")[1];

    window.location = "enterresults?pdate=" + $('#pdate').val() + "&psno=" + sno;
}

function viewRightPanel() {
    $("#rightPanel").hide();
}

function bulk_print() {
    alert("Printing... Please wait...");
}



var merged_list = [];
function getLPS(id) {

    // alert(id);

    if ($("#idf" + id).prop("checked")) {
        merged_list.push(id);
       // alert(merged_list);
   } else {
    merged_list.indexOf(id) !== -1 && merged_list.splice(merged_list.indexOf(id), 1);
               // alert(merged_list);

           }

       }

       function printMerged() {

        var repList = merged_list.toString();

    // alert(merged_list.length); 

    if (merged_list.length > 1) {

        var win = window.open("printreport/" + repList + "&" + $('#rephead').val(), '_blank');
        win.print();
        setTimeout(function () {
            win.close();
        }, 8000);

        merged_list = [];


    } else {
        alert("Please select at least 2 samples!");
    }


}

function auth_one(id){

    $.ajax({
        type: 'POST',
        url: "reportauth?",
        data: {'auth': '1','id': id, '_token': $('input[name=_token]').val()},
        success: function (data) {
            alert(data);
            search();
        }
    });

}

function auth_two(id){

    $.ajax({
        type: 'POST',
        url: "reportauth?",
        data: {'auth': '2','id': id, '_token': $('input[name=_token]').val()},
        success: function (data) {
            alert(data);

            //send SMS for patient automated
            if($("#lid_fr_sms").val() == 44 || $("#lid_fr_sms").val() == 45|| $("#lid_fr_sms").val() == 31){

                if ($('#autosms').val() === "1") {
                    sendReportSMSAuto();

                }


            }

            search();
        }
    });

}

function sendReportSMSAuto(){

    var lpsid = $("#lpsid").val();

    $.ajax({
        type: 'POST',
        url: "autosmscheck?",
        data: {'lpsid': lpsid, '_token': $('input[name=_token]').val()},
        success: function (data) {
            // alert(data);

            if(data == "true"){
                sendSMSAgain();

                // if($("#lid_fr_sms").val() == 44){

                //     sendCenterSMSAuto();

                // }

                

                // alert("SMS Sent!");

            }

        }
    }); 

}

function resetReport(){

    var i = confirm("Are you sure you want to Reset this Report?");
    if (i) {

        var lpsid = $("#lpsid").val();

        var remVals = 0;
        var i = confirm("Do you need to Remove All Values?");
        if (i) {    
            remVals = 1;
        }

        $.ajax({
            type: 'POST',
            url: "resetReport?", 
            data: {'lpsid': lpsid,'removeVals': remVals, '_token': $('input[name=_token]').val()},
            success: function (data) {
                alert(data);
                search();
            }
        }); 

    }

}

function sendSMSAgain(additional) {
    if ($('#status').html() !== "pending" && $('#status').html() !== "Accepted") {
        if ($('#tpno').html() !== "") {
            var i = confirm("Are you sure you want to send the SMS?");
            if (i) {

                var tpno = $('#tpno').html();
                if(additional){
                    tpno = $('#adsms').val();
                }                
                
                var sNo = $('#sNo').val();
                var date = $('#pdate').val();
                var msgType = "ReportReady";
                var name = $('#fname').html() + " " + $('#lname').html();

//                alert(tpno + " " + msgType+" "+sNo);

$.ajax({
    type: 'POST',
    url: "sendsms",
    data: {'tp': tpno, 'sno': sNo, 'type': msgType, 'name': name, 'date': date, '_token': $('input[name=_token]').val()},
    success: function (data) {
        var win = window.open(data); 
        setTimeout(function () {
            win.close();
        }, 5000);
    }
});

            if(additional){
                
            }else{
                if($("#lid_fr_sms").val() == 44){

                    sendCenterSMSAuto();

                }
            }

} 

} else {
    alert("Please enter contact number to send SMS!");
}

} else {
    alert("Report is not ready to send SMS!");
}
}

function sendCenterSMSAuto() {

    if ($('#adsms').val() !== "") {


        var tpno = $('#adsms').val();                               
        var patient_tpno = $('#tpno').html();                               

        var sNo = $('#sNo').val();
        var date = $('#pdate').val();
        var msgType = "ReportReady";
        var name = $('#fname').html() + " " + $('#lname').html();

        $.ajax({
            type: 'POST', 
            url: "sendsms",
            data: {'tp': tpno, 'sno': sNo, 'type': msgType, 'name': name, 'date': date, 'ptp': patient_tpno, '_token': $('input[name=_token]').val()},
            success: function (data) {
                var win = window.open(data);
                setTimeout(function () {
                    win.close();
                }, 5000);
            }
        });


    } else {
        alert("Please enter contact number to send SMS!");
    }


}




</script>

@stop

@section('body')

<?php
//load Editing privilages
// $editingPrivs = "disabled=true";
// $resultEP = DB::select("SELECT * FROM privillages p where user_uid = (select user_uid from labUser where luid = '" . $_SESSION['luid'] . "') and options_idoptions = '12';");
// foreach ($resultEP as $resep) {
    $editingPrivs = "";
// }

//report reset privilages
// $resetReport = "disabled=true";
// $resultEP = DB::select("SELECT * FROM privillages p where user_uid = (select user_uid from labUser where luid = '" . $_SESSION['luid'] . "') and options_idoptions = '25';");
// foreach ($resultEP as $resep) {
    $resetReport = "";
// }

//
?>

<blockquote>
    <h2 class="pageheading">Enter Testing Results</h2>
    <br/>
    <p class="tableHead">Search Sample</p>  

    <table width="100%">
        <tr>
            <td width="220">Date :
                <?php if ($_SESSION["guest"] == null) { ?>
                    <input type="date" name="date" id="pdate" class="input-text"> 
                <?php } else { ?>
                    <input type="date" name="date" id="pdate" class="input-text" disabled="disabled"> 
                <?php } ?>
            </td>
            <td>Sample NO : 
                <?php if ($_SESSION["guest"] == null) { ?>
                    <input type="text" name="sampleNo" class="input-text" id="sNo" style="width: 160px"> 
                <?php } else { ?>
                    <input type="text" name="sampleNo" class="input-text" id="sNo" style="width: 160px" disabled="disabled">
                <?php } ?>
            </td>
            <td width="150">
                <?php if ($_SESSION["guest"] == null) { ?>
                    <input type="button" style="margin:0; " name="search" class="btn" id="search" value="Search Sample" onclick="validate();">
                <?php } ?>
            </td>
            <td id="errormsg" style="color: blue;">{{ $msg or '' }}</td>
            <td align='right'> 
                <?php if ($_SESSION["guest"] == null) { ?>
                    <?php if ($_SESSION["lid"] != "12") { ?>
                        <!-- <input id="idsnp" type="button" class="btn" style="background-color: #00cc00; color: white; float: right; margin-left: 0px; margin-right: 0px;" value="Save and Print" onclick="SavePrintReport();" <?php echo $editingPrivs; ?> > -->
                    <?php } ?>
                <?php } ?>

                <img src="{{ asset('images/newprint.png') }}" width="50px">

                <input id="idpr"type="button" class="btn" style="float: right; margin-left: 0px" value="Print Report" onclick="printReport(false);">
            </td>



        </tr>


        <tr> 
            <?php if ($_SESSION["guest"] == null) { ?>
                <td width='10%'>
                    <?php
                    // $resultc = DB::select("select enableblooddrew, enablecollected from reportconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
            //         foreach ($resultc as $resc) {
            //             if ($resc->enableblooddrew == 1) {

            //                 if($_SESSION["lid"] == 44 || $_SESSION["lid"] == 45){

            //                 ?><input type="button" id="btnbda" class="btn" style="float: left; margin: 0px; width: 160px; background-color: #d43f3a; color: white;" value="Accept To Lab" onclick="labaccept()">
            //                 <?php

            //             } 

            //         ?><input type="button" id="btnbd" class="btn" style="float: left; margin: 0px; width: 160px; background-color: #d43f3a; color: white;" value="Blood Drew" onclick="BloodDrew()">
            //         <?php
            //     }
            // }
            ?> 
        </td>
        <td width='10%'>
            <?php
            // $resultc = DB::select("select enableblooddrew, enablecollected from reportconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
            // foreach ($resultc as $resc) {
            //     if ($resc->enablecollected == 1) {
            //         ?>
            //         <input type="button" id="btnrc" class="btn" style="float: left; margin: 0px; width: 180px; background-color: #d43f3a; color: white;" value="Report Collected" onclick="ReportCollect()"></td>
            //         <?php
            //     }
            // }
            ?>


            <td>

            <?php } ?>

            <?php if ($_SESSION["guest"] == null) { ?>

                <input type="hidden" id="lid_fr_sms" value="<?php echo $_SESSION["lid"]; ?>" />

                <table>
                    <?php
                    // $result1 = DB::select("select * from Lab_features where lab_lid = '" . $_SESSION["lid"] . "' and features_idfeatures = (select idfeatures from features where name = 'SMS')");
                    if (true) {
                        ?>
                        <tr>
                            <td class="form-label" style="width: 100px;">SMS Alert</td><td><div id="sst"></div><img src="{{ asset('images/sendsms.png') }}" width="32px" onclick="sendSMSAgain()" style="cursor: pointer;">
                                
                            </td>

                            <td>&nbsp;</td>

                            <td width="200">Bill Remark</td>
                            <td width="100%" id="billremark" style="color: red; font-weight:bold;"></td>
                        </tr>
                        <?php
                    }
                    ?> 
                </table>

            <?php }else{
                ?>
                       <td width="100%" id="billremark" hidden></td> 
                <?php
            } ?>
        </td>
    </tr>


</table>

<style>
.tabstyle1{            
    padding: 5px;
    cursor: pointer;
    font-size: 10pt;
    border-right: #ECECEC solid medium;
    background-color: #31708f;
    text-align: center;
}

.tabstyle1:hover{            
    background-color: #BDCDF9;
}
</style>

<table id="Samples" width="100%">
    <tr class="viewTHead">

        <!--            <td width="1%"> <span onclick="openpanel()"> << </span> </td>-->

        <td width="30%" id="patientDHead">

            <table width="100%">

                <tbody>
                    <tr>
                        <td><div id="tab1" class="tabstyle1" onclick="opentab('pdatatable')">Patient Details</div>    </td>
                        <td><div id="tab2" class="tabstyle1" onclick="opentab('timetable')">Timeline</div>    </td>
                        <td><div id="tab3" class="tabstyle1" onclick="opentab('smstable')">SMS / Email</div>   </td>
                    </tr>
                </tbody>
            </table>

        {{-- new dev added by malith --}}
        </td><td width="70%"><table width='100%'><tr><td width='100'>Testing Details</td> <td width='230' id="histry_available"></td><td width='120' id="ref_values"></td><td width='80' id="ena-hist"></td>
        </tr></table> </td>{{-- new dev added by malith --}}
    </tr>
    <tr> 
        <!--<td style="background-color: #cfdefd;"></td>-->
        <td valign="top" id="patientD" style="background-color: #cfdefd; height: 500px; overflow-y: visible; ">

            <table width='100%' id="pdatatable" cellpadding="5">
                <?php
//                    $result1x = DB::select("select * from Lab_features where lab_lid = '" . $_SESSION["lid"] . "' and features_idfeatures = (select idfeatures from features where name = 'Patient Image')");
//                    if (!empty($result1x)) {
                ?>
                <tr>
                    <td colspan="2" align='center'>
                        <img id="pimage" width="150" src="{{ asset('images/patient_image.png') }}" >
                    </td>
                </tr>

                <?php
//                    }
                ?>

                <tr>
                    <td style="padding: 10px; font-size: 14pt; background-color:#191970; color: white;"  width="125" colspan="2" class="Normaltext" width="263"> <span id="fname"></span> <span id="lname"></span> </td>
                </tr>

                <tr>
                    <td class="form-label">Age</td><td> <table><tr><td class="Normaltext" id="age"></td><td class="Normaltext" id="months"></td><td class="Normaltext" id="days"></td></tr></table> </td>
                </tr>
                <tr>
                    <td class="form-label">Gender</td><td class="Normaltext" id="gender"></td>
                </tr>
                <tr>
                    <td class="form-label">TP No</td><td class="Normaltext" id="tpno"></td>
                </tr>
                <tr>
                    <td class="form-label">NIC NO</td><td class="Normaltext" id="nic"></td>
                </tr>
                <tr>
                    <td class="form-label">Referred By</td><td class="Normaltext" id="refby"></td>
                </tr>
                <tr>
                    <td class="form-label">Status</td><td class="Normaltext" id="status"></td>
                </tr>


                <?php if ($_SESSION["guest"] == null) { ?>
                    <tr>
                        <td class="form-label">More Details </td><td> <button style="margin:0;" onclick="viewPatient()" class="btn">View Patient</button>   </td>
                    </tr>


                    <tr>
                        <td class="form-label"></td><td> <button style="margin:0;" onclick="resetReport()" class="btn" <?php echo $resetReport; ?> >Reset Report</button>   </td>
                    </tr>



                <?php } ?>

            </table> 


            <table id="timetable" width='100%' cellpadding="5">

                <tr>
                    <td class="form-label">Arrival Date</td><td class="Normaltext" id="adate"></td>
                </tr>
<!--                    <tr>
                        <td class="form-label">Arrival Time</td><td class="Normaltext" id="arivaltime"></td>
                    </tr> -->

                    <?php
                    // $resultc = DB::select("select enableblooddrew, enablecollected from reportconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
                    // foreach ($resultc as $resc) {
                    //     if ($resc->enableblooddrew == 1) {
                    //         ?>
                    //         <tr>
                    //             <td class = "form-label">Blood Drew Time</td><td class="Normaltext" id = "bdtime"></td>
                    //         </tr>
                    //         <?php
                    //     }
                    // }
                    ?>

                    <tr>
                        <td class="form-label">Lab Accepted </td><td class="Normaltext"><div id="latime"></div></td>
                    </tr>

                    <tr>
                        <td class="form-label">Finished Date</td><td class="Normaltext" id="fdate"></td>
                    </tr>
<!--                    <tr>
                        <td class="form-label">Finished Time</td><td class="Normaltext" id="ftime"></td>
                    </tr>-->

                    <?php
                    // $resultc = DB::select("select enableblooddrew, enablecollected, lab_report_remark from reportconfigs where lab_lid = '" . $_SESSION['lid'] . "'");
                    // foreach ($resultc as $resc) {
                    //     if ($resc->enablecollected == 1) {
                    //         ?>
                    //         <tr>
                    //             <td class = "form-label">Report Collected</td><td class="Normaltext" id = "rctime"></td>
                    //         </tr> 
                    //         <?php
                    //     }
                    // }
                    ?>

                    <tr>
                        <td class="form-label">Report Name</td><td><input type="text" class="input-text" id="rephead"></td>
                    </tr>

                    

                    <tr>
                        <td class="form-label">Result Entered </td><td><div id="resent"></div></td>
                    </tr>

                    <tr>
                        <td class="form-label">Result Confirmed </td><td><div id="confuser"></div></td> 
                    </tr>

                    <tr>
                        <td class="form-label">Result Verified </td><td><div id="reverif"></div></td>
                    </tr>

                    <tr>
                        <td class="form-label">Accepted User</td><td><div id="accepted_user"></div></td>
                    </tr>



                </table>

                <table id="smstable" width='100%' cellpadding="5">

                    <?php if ($_SESSION["guest"] == null) { ?>

                        <?php
                        // $result1 = DB::select("select * from Lab_features where lab_lid = '" . $_SESSION["lid"] . "' and features_idfeatures = (select idfeatures from features where name = 'SMS Reporting')");
                        if (true) {
                            ?>
                            <tr>
                                <td class="form-label">SMS Reporting</td><td><div id="esst"></div>
                                    <img src="{{ asset('images/smsrep.png') }}" width="32px" onclick="sendSMSReport()" style="cursor: pointer;">

                                    <?php
//                                $result1e = DB::select("SELECT isactiveauto FROM sms_profile where lab_lid = '" . $_SESSION["lid"] . "'");
//                                foreach ($result1e as $ress) {
//                                    echo "<input type='hidden' id='autosms' value='" . $ress->isactiveauto . "'>";
//                                }
                                    ?>
                                </td>

                            </tr>

                            <tr>
                                <td class="form-label">Additional SMS (Link)</td><td><div id="esst"></div>
                                    <input type="text" id="adsms" class="input-text"/> <br/>
                                    <img src="{{ asset('images/sendsms.png') }}" width="32px" onclick="sendSMSAgain(true)" style="cursor: pointer;">

                                    
                                </td>

                            </tr>

                            <tr>
                                <td class="form-label">Emergency SMS</td><td><div id="esst"></div>
                                    <img src="{{ asset('images/emsms.png') }}" width="32px" onclick="sendEMSMS()" style="cursor: pointer;">

                                    <?php
//                                $result1e = DB::select("SELECT isactiveauto FROM sms_profile where lab_lid = '" . $_SESSION["lid"] . "'");
//                                foreach ($result1e as $ress) {
//                                    echo "<input type='hidden' id='autosms' value='" . $ress->isactiveauto . "'>";
//                                }
                                    ?>
                                </td>

                            </tr>
                            <?php
                        }
                        ?> 

                        <?php
                        // $result1 = DB::select("select * from Lab_features where lab_lid = '" . $_SESSION["lid"] . "' and features_idfeatures = (select idfeatures from features where name = 'Email')");
                        if (true) {
                            ?>
                            <tr>
                                <td class="form-label">Email Reporting</td><td><div id="esst"></div>
                                    <input type="text" id="emailadd" class="input-text"/> <br/>
                                    <img src="{{ asset('images/sendemail.png') }}" width="32px" onclick="sendEmailAgain()" style="cursor: pointer;">

                                    <?php
//                                $result1e = DB::select("SELECT isactiveauto FROM sms_profile where lab_lid = '" . $_SESSION["lid"] . "'");
//                                foreach ($result1e as $ress) {
//                                    echo "<input type='hidden' id='autosms' value='" . $ress->isactiveauto . "'>";
//                                }
                                    ?>
                                </td>

                            </tr>
                            <?php
                        }
                        ?> 

                    <?php } ?>

                </table>



            </td>


            <td style="vertical-align: top">
                <form action="UpdateTestResults" id="form" method="POST">
                    <table id="testD">

                    </table>
                    <input type="hidden" id="lpsid" name="lpsid" value="">
                </form> 


                <?php if ($_SESSION["guest"] == null) { ?>

                    <?php
                    // $result1 = DB::select("select * from Lab_features where lab_lid = '" . $_SESSION["lid"] . "' and features_idfeatures = (select idfeatures from features where name = 'LIS Support')");
//                foreach ($result1 as $res1) {
//                    
//                }
                    if (true) {
                        ?>
                        <div id="LISWindow" style=" z-index: 1; width: 400px; height: 25px; background-color: #BDCDF9; position: absolute; top: 130px; right: 5px; overflow-y: scroll; border-width: 2px; border-color:  #0015B0; border-style: solid; border-radius: 10px;">
                            <h4 style="float: left; color: #001092; font-weight: bold;">LIS Results <span style="font-family: sans-serif; color: greenyellow" id="lisc"></span></h4>

                            <input type="button" class="btn" style="margin: 0px; float: right;" id="LISWindowBtn" value="Open" onclick="LISGate()">    
                            <br/>
                            <p id="LISTabletno"></p> 

                            <hr style="margin-top: 5px;"/>   

                            <table width="100%" id="LISTable" class="container">

                            </table>

                        </div> 

                        <style>
                            /*                        body {
                                                        font-family: sans-serif;
                                                        font-weight: 300;
                                                        line-height: 1.42em;
                                                        color:#A7A1AE;
                                                        background-color:#1F2739;
                                                        }*/

                            /*                        h1 {
                                                        font-size:3em; 
                                                        font-weight: 300;
                                                        line-height:1em;
                                                        text-align: center;
                                                        color: #4DC3FA;
                                                        }*/

                            /*                        h2 {
                                                        font-size:1em; 
                                                        font-weight: 300;
                                                        text-align: center;
                                                        display: block;
                                                        line-height:1em;
                                                        padding-bottom: 2em;
                                                        color: #FB667A;
                                                        }*/

                            /*                        h2 a {
                                                        font-weight: 700;
                                                        text-transform: uppercase;
                                                        color: #FB667A;
                                                        text-decoration: none;
                                                        }*/

                                                        .blue { color: #185875; }
                                                        .yellow { color: #FFF842; }

                                                        .container th h1 {
                                                            font-weight: bold;
                                                            font-size: 1em;
                                                            text-align: left;
                                                            /*color: #185875;*/
                                                        }

                                                        .container td {
                                                            font-weight: normal;
                                                            font-size: 1em;
                                                            -webkit-box-shadow: 0 2px 2px -2px #0E1119;
                                                            -moz-box-shadow: 0 2px 2px -2px #0E1119;
                                                            box-shadow: 0 2px 2px -2px #0E1119;
                                                        }

                                                        .container {
                                                            font-family: sans-serif;
                                                            text-align: left;
                                                            overflow: hidden;
                                                            width: 80%;
                                                            margin: 0 auto;
                                                            display: table;
                                                            padding: 0 0 8em 0;
                                                        }

                                                        .container td, .container th {
                                                            padding-bottom: 2%;
                                                            padding-top: 2%;
                                                            padding-left:2%;  
                                                        }

                                                        /* Background-color of the odd rows */
                                                        .container tr:nth-child(odd) {
                                                            background-color: #ECECEC;
                                                        }

                                                        /* Background-color of the even rows */
                                                        .container tr:nth-child(even) {
                                                            background-color: #C6C6C6;
                                                        }

                                                        .container th {
                                                            background-color: #ECECEC;
                                                        }

                                                        .container td:first-child { color: #001092; }

                                                        .container tr:hover { 
                                /*                            background-color: #464A52;
                                                            -webkit-box-shadow: 0 6px 6px -6px #0E1119;
                                                            -moz-box-shadow: 0 6px 6px -6px #0E1119;
                                                            box-shadow: 0 6px 6px -6px #0E1119;*/
                                                        }

                                                        .container td:hover {
                                                            background-color: #FFF842;
                                                            color: #0015B0;
                                                            font-weight: bold;

                                /*                            box-shadow: #7F7C21 -1px 1px, #7F7C21 -2px 2px, #7F7C21 -3px 3px, #7F7C21 -4px 4px, #7F7C21 -5px 5px, #7F7C21 -6px 6px;
                                                            transform: translate3d(6px, -6px, 0);
                                
                                                            transition-delay: 0s;
                                                            transition-duration: 0.4s;
                                                            transition-property: all;
                                                            transition-timing-function: line;*/
                                                        }

                            /*                        @media (max-width: 800px) {
                                                        .container td:nth-child(4),
                                                        .container th:nth-child(4) { display: none; }
                                                        }*/


                                                        .preptd td{
                                                            padding: 5px;
                                                        }

                                                        .preptd:hover{ 
                                                            background-color: #C6C6C6;
                                                        }

                                                        #rightPanel{
                                                            z-index: 0;
                                                        }

                                                    </style>

                                                    <?php
                                                }
                                                ?>

                                            <?php } ?>
                                        </td>



                                        <?php
                                        if (isset($lpsid)) {
                                            ?>
                                            <input type="hidden" id="lastlpsid" value="{{ $lpsid }}">
                                            <?php
                                        } else {
                                            ?>
                                            <input type="hidden" id="lastlpsid" value="">
                                            <?php
                                        }
                                        ?>

                                    </tr>            
                                </table>



                                
                                <!--pending report table-->



                                <div id="rightPanel" style="width:285px; height: 630px; position: absolute; top: 247px; right: 8px; background-color: #cfdefd; overflow-y: scroll" >
                                    <div style="width:100%; height: 25px; background-color: #31708f; color: white; padding-left: 5px; padding-top: 3px;">Other Reports of Patient Session &nbsp; <span style="cursor: pointer;" onclick="viewRightPanel()"> [X] </span></div>

                                    <table width='100%' border='1' style="border-collapse: collapse; border: #185875 solid 1px; font-family: Arial;">
                                        <thead>
                                            <tr>
                                                <th width='40%'></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="samep_oreps">





                                        </tbody>
                                    </table>

                                    <?php if ($_SESSION["guest"] == null) { ?>
                                        <!-- <input type="button" value="Bulk Print" class="btn" style="margin:0; float: left;" onclick="bulk_print()">  --> 
                                        <input type="button" value="Merged Print" class="btn" style="margin:0; float: right;" onclick="printMerged()"> 

                                        <div id="guestelement"></div>

                                    <?php } ?>

                                    <br/>
                                    <br/>

                                    <div style="width:100%; height: 25px; background-color: #31708f; color: white; padding-left: 5px; padding-top: 3px;">Pending Reports - Today <input type="button" class="btn" value="View" onclick="loadPendingTests();"></div>

                                    <table width='100%' border='1' style="border-collapse: collapse; border: #185875 solid 1px; font-family: Arial;">
                                        <thead>
                                            <tr>
                                                <th width='40%'></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="pending_reps">





                                        </tbody>
                                    </table>


                                </div>  

                                <!--pending report table-->

                                

                                <?php
                                if (isset($_GET['psno'])) {
                                    ?>
                                    <input type="hidden" id="pxsno" value="<?php echo $_GET['psno']; ?>"> 
                                    <?php
                                } else {
                                    ?>
                                    <input type="hidden" id="pxsno" value="">
                                    <?php
                                }

                                if (isset($_GET['pdate'])) {
                                    ?>
                                    <input type="hidden" id="pxdate" value="<?php echo $_GET['pdate']; ?>">
                                    <?php
                                } else {
                                    ?>
                                    <input type="hidden" id="pxdate" value="">
                                <?php } ?>



                                <!-- config from block report for due patients -->

                                <?php

                                $brfdp = 0;

                                // $resultcr = DB::select("select block_report_fordue from configs where Lab_lid = '" . $_SESSION['lid'] . "'");
                                // foreach ($resultcr as $rescr) {
                                //     $brfdp = $rescr->block_report_fordue;
                                // }
                                ?>

                                <input type="hidden" id="blkrpfrdue" value="{{$brfdp}}">

                            </blockquote>
                            @stop