@extends('Templates/WiTemplate')
<?php
if (!isset($_SESSION)) {
    session_start();
}

date_default_timezone_set('Asia/Colombo');
?>
@section('title')
View Patients
@stop

@section('head')
<script type="text/javascript">
    window.onload = loadDate;



    function loadDate() {


        $("#othFilters").hide();

        $('#tablebody2').hide();

        var d = new Date();
        d.toLocaleString('en-US', {timeZone: 'Asia/Colombo'});

        document.getElementById('pdate').valueAsDate = new Date();        
        document.getElementById('pdatex').valueAsDate = new Date();

        if(localStorage.getItem("user_selected_date1") !== null){
            document.getElementById('pdate').valueAsDate = new Date(localStorage.getItem("user_selected_date1"));
            document.getElementById('pdatex').valueAsDate =  new Date(localStorage.getItem("user_selected_date2"));
        }else{
            document.getElementById('pdate').valueAsDate = d; 
            document.getElementById('pdatex').valueAsDate = d;
        }

        
        if(localStorage.getItem("user_selected_dept") !== null && document.getElementById('viewtype').value == "dept"){
            document.getElementById('sampledept').value = localStorage.getItem("user_selected_dept");
            document.getElementById('depnamex').innerHTML = document.getElementById("sampledept").options[document.getElementById("sampledept").selectedIndex].text; 

        }


        
        if($("#lid_fr_chk").val() == "42"){
            $("#teststate").val("labview");
            $("#more").prop("checked", "checked");

        } 


        if(document.getElementById('viewtype').value == "accept"){
            $("#sNo").focus();
        }

        if(document.getElementById('viewtype').value == "dept"){

            search();
            
            setInterval(search, 60000); 
        }else{

            search();
        }

        


    }

    function validate() {
        var sNo = document.getElementById('sNo').value;
        var fname = document.getElementById('fname').value;
        var lname = document.getElementById('lname').value;
        var check = true;
        var regex = /^[a-zA-Z]*$/;

        if (check) {
            search();
        }

    }

    var searchedDate;
    var DataOblect;

    var sample_no_list = [];
    var stLps = [];

    var last_searched_sample_id = "";


    var selected_dept = "";

function search() {

// var date = ;
// var date2 = ;

//store selected date in localstore 
localStorage.setItem("user_selected_date1", document.getElementById('pdate').value); 
localStorage.setItem("user_selected_date2", document.getElementById('pdatex').value); 

if(document.getElementById('viewtype').value == "dept"){
    localStorage.setItem("user_selected_dept", document.getElementById('sampledept').value); 
}

sample_no_list = [];

tableBody = "<tr class='viewTHead'>"
+ "<td width='100' height='28' class='fieldText'>Date</td>"
+ "<td width='200' height='28' class='fieldText'>Patient Name</td>"


//                + "<td width='159' height='28' class='fieldText'>First Name</td>"
//                + "<td width='152' class='fieldText'>Last Name</td>"
+ "<td width='60' class='fieldText'>Gender</td>"
+ "<td width='130' class='fieldText'>Age</td>"
+ "<td width='130' class='fieldText'>Contact</td>"
+ "<td width='44' class='fieldText'>S.No</td>"
+ "<td width='44' class='fieldText'>Test</td>";

if ($('#more').is(":checked")) {
    tableBody += "<td width='100' class='fieldText'>Refby</td>";
}

//                + "<td width='45' class='fieldText'>Type</td>"
tableBody += "<td width='60px' >Status</td>";

if ($('#more').is(":checked")) {
    tableBody += "<td width='45px' >Price</td>";
}


tableBody += "<td width='30' ></td>"
+ "<td width='30' ></td>"
+ "<td width='30' ></td>"
+ "</tr>";


var date = localStorage.getItem("user_selected_date1");
var date2 = localStorage.getItem("user_selected_date2");

var viewtype = document.getElementById('viewtype').value;




searchedDate = date;
var sNo = document.getElementById('sNo').value;
var fname = document.getElementById('fname').value;
var lname = document.getElementById('lname').value;
var type = document.getElementById('type').value;
var refby = document.getElementById('refby').value;


var more = "on";
if (!$('#more').is(":checked")) {
    more = "off";
}

var tstate;

if(viewtype == "accept"){

    tstate = "Accepted";
    more = "on";

}else if(viewtype == "dept"){

    tstate = "LabAccepted";
    more = "on";

}else{
    tstate = document.getElementById('teststate').value;
}



var dept = document.getElementById('sampledept').value;
var speci = document.getElementById('samplespec').value;





var branchCode = "";
if ($('#brcodex').length > 0) {
    branchCode = $('#brcodex').val();
}

var opt = $('option[value="' + $('#tgroup').val() + '"]'); 
var selectedTest = opt.length ? opt.attr('id') : '%';

if (document.getElementById('loadVal').value === "ok") {
    var url = "SearchPatientView?date=" + date + "&datex=" + date2 + "&sno=" + sNo + "&fname=" + fname + "&lname=" + lname + "&status=pending&type=" + type + "&refby=" + refby + "&testgroup=" + selectedTest + "&teststate=" + tstate + "&branchcode=" + branchCode + "&more=" + more+ "&dept=" + dept+ "&speci=" + speci;
} else {
    var url = "SearchPatientView?date=" + date + "&datex=" + date2 + "&sno=" + sNo + "&fname=" + fname + "&lname=" + lname + "&type=" + type + "&refby=" + refby + "&testgroup=" + selectedTest + "&teststate=" + tstate + "&branchcode=" + branchCode + "&more=" + more+ "&dept=" + dept+ "&speci=" + speci;
}

$.ajax({
    type: 'POST',
    url: url,
    success: function (data) {
        data = JSON.parse(data);
        DataOblect = data;
        var xy = 0;
        for (var i = 0; i < data.length; i++) {


            if(viewtype == "accept" && data[i].fastingtime !== null){

                continue;

            }


            xy += 1;
            var refbyID = data[i].refference_idref;
            var refby = $("#refby option[value=" + refbyID + "]").text();

            var x = data[i].status;

            var statusx = x.substr(0, 1).toUpperCase() + x.substr(1);

                    // alert(data[i].repcollected); 

                    if(data[i].repcollected == null){

                        if(data[i].fastingtime !== null){
                            statusx = "Lab Accepted";
                        }

                        if(data[i].auth02 == 1){
                            statusx = "Verified";
                        }


                    }else{
                        statusx = "Printed";
                    }

                    

                    var age_months = "";
                    if (data[i].months !== "0") {
                        age_months = "<td style='width: 30%'>" + data[i].months + "M</td>";
                    }

                    var age_days = "";
                    if (data[i].days !== "0") {
                        age_days = "<td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].days + "D</td>";
                    }

                    var age_table = "<table style='width: 100%' class='ageTable'><tr><td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].age + "Y</td>" + age_months + age_days + "</tr></table>"


                    var test_name = data[i].testname;

//                    alert(data[i].lab_lid);

                    if (data[i].lab_lid === 19) {
                        if (test_name.includes("-")) {
                            test_name = test_name.split("-")[0];
                        }
                    }

                    var row_color = "";
                    if(data[i].urgent_sample == 1){

                        row_color = "#FBA9AD";

                    }

                    sample_no_list.push(data[i].sampleNo);


                    if (!$('#more').is(":checked")) {


                        stLps.push(data[i].lpsid);

                    //                        tableBody += "<tr class='phistr' style='cursor:pointer;'><td>&nbsp;" + data[i].date + "</td><td>&nbsp;" + data[i].initials + " " + data[i].fname + " " + data[i].lname + "</td><td>&nbsp;" + data[i].gender + "</td><td><table style='width: 100%' class='ageTable'><tr><td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].age + "Y</td><td style='width: 30%'>" + data[i].months + "M</td><td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].days + "D</td></tr></table></td></td><td>&nbsp;" + data[i].tpno + "</td><td style='color:blue; font-weight:bold;'>&nbsp;" + data[i].sampleNo + "</td><td>&nbsp;" + data[i].testname + "</td><td>&nbsp;" + refby + "</td><td>&nbsp;" + data[i].type + "</td><td>&nbsp;" + statusx + "</td>";
                    tableBody += "<tr class='phistr' style='cursor:pointer; background-color: "+row_color+";' id='" + data[i].lpsid + "' onclick='selectRecord(id)'><td>" + data[i].date + "</td><td>" + data[i].initials + " " + data[i].fname + " " + data[i].lname + "</td><td>&nbsp;" + data[i].gender + "</td><td>" + age_table + "</td></td><td>&nbsp;" + data[i].tpno + "</td><td style='color:blue; font-weight:bold; size:14pt;' >&nbsp;" + data[i].sampleNo + "</td><td style='font-size:11pt;'>" + test_name + "</td>";

                    // tableBody += "<tr class='phistr' style='cursor:pointer; background-color: "+row_color+";' id='" + data[i].sampleNo + "#"+data[i].date+ "#"+data[i].tpno+"' onclick='selectRecord(id)'><td>" + data[i].date + "</td><td>" + data[i].initials + " " + data[i].fname + " " + data[i].lname + "</td><td>&nbsp;" + data[i].gender + "</td><td>" + age_table + "</td></td><td>&nbsp;" + data[i].tpno + "</td><td style='color:blue; font-weight:bold; size:14pt;' >&nbsp;" + data[i].sampleNo + "</td><td style='font-size:11pt;'>" + test_name + "</td>";

                    if(statusx == "Printed"){
                        tableBody += "<td style='background-color:#7FFFD4; text-align : center;'> &nbsp;" + statusx + "</td>";
                    }else if(statusx == "Lab Accepted"){
                        tableBody += "<td style='background-color:#A7BEFF; text-align : center;'> &nbsp;" + statusx + "</td>";
                    }else if(statusx == "Accepted"){
                        if($("#lid_fr_chk").val() == "44"){
                            tableBody += "<td style='background-color:#EBA7FF; text-align : center;'> &nbsp;Accepted</td>";
                        }else{
                            tableBody += "<td style='background-color:#EBA7FF; text-align : center;'> &nbsp;Barcoded</td>";   
                        } 
                        
                    }else if(statusx == "Done"){
                        tableBody += "<td style='background-color:#90EE90; text-align : center;'> &nbsp;" + statusx + "</td>";
                    }else if(statusx == "Pending"){
                        tableBody += "<td style='background-color:#FFFC44; text-align : center;'> &nbsp;" + statusx + "</td>";
                    }else if(statusx == "Verified"){
                        tableBody += "<td style='background-color:#C0FF00; text-align : center;'> &nbsp;" + statusx + "</td>";
                    }else{
                        tableBody += "<td>&nbsp;" + statusx + "</td>"; 


                    } 

                    } else {
                    //                        tableBody += "<tr class='phistr' style='cursor:pointer;'><td>&nbsp;" + data[i].date + "</td><td>&nbsp;" + data[i].initials + " " + data[i].fname + " " + data[i].lname + "</td><td>&nbsp;" + data[i].gender + "</td><td><table style='width: 100%' class='ageTable'><tr><td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].age + "Y</td><td style='width: 30%'>" + data[i].months + "M</td><td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].days + "D</td></tr></table></td></td><td>&nbsp;" + data[i].tpno + "</td><td style='color:blue; font-weight:bold;'>&nbsp;" + data[i].sampleNo + "</td><td>&nbsp;" + data[i].testname + "</td><td>&nbsp;" + refby + "</td><td>&nbsp;" + data[i].type + "</td><td>&nbsp;" + statusx + "</td><td align='right'>&nbsp;" + data[i].tgprice + "</td>";
                    tableBody += "<tr class='phistr' style='cursor:pointer; background-color: "+row_color+";' id='" + data[i].sampleNo + "#"+data[i].date+ "#"+data[i].tpno+"' onclick='selectRecord(id)'><td>" + data[i].date + "</td><td>" + data[i].initials + " " + data[i].fname + " " + data[i].lname + "</td><td>&nbsp;" + data[i].gender + "</td><td>" + age_table + "</td></td><td>&nbsp;" + data[i].tpno + "</td><td style='color:blue; font-weight:bold; size:14pt;'>&nbsp;" + data[i].sampleNo + "</td><td style='font-size:11pt;'>" + test_name + "</td><td style='font-size:11pt;'>&nbsp;" + refby + "</td>";

                    if(statusx == "Printed"){ 
                        tableBody += "<td style='background-color:#7FFFD4;'> &nbsp;" + statusx + "</td>";
                    }else if(statusx == "Verified"){
                        tableBody += "<td style='background-color:#90EE90;'> &nbsp;" + statusx + "</td>";
                    }else{
                        tableBody += "<td>&nbsp;" + statusx + "</td>";
                    } 

                    tableBody += "<td align='right'>&nbsp;" + data[i].tgprice + "</td>";


                    } 

                    var age_sms = "";
                    var months_sms = "";
                    var days_sms = "";

                    if (data[i].months !== 0) {
                        months_sms = data[i].months+" Months ";
                    }

                    if (data[i].days !== 0) {
                        days_sms = data[i].days + " Days ";
                    }

                    if (data[i].age !== 0) {
                        age_sms = data[i].age + " Years ";
                    }

                    var agefr_sms = age_sms+months_sms+days_sms;




                    if ($('#guestx').val()) {
                        tableBody += "<td width='30'><input type='button' class='btn' style='margin:0px;' name='submit' value='View' onclick='view(" + data[i].lpsid + ")'></td>";

                        if(document.getElementById("guestelement")){

                            if (data[i].lab_lid == "45") {
                    //                            tableBody += "<td><input type='button' class='btn' style='margin:0px;' name='submit' value='Add Sample' onclick='addSample(" + data[i].pid + ")'></td>";

                                if (statusx === "Lab Accepted" || data[i].status === "Done") {
                                    tableBody += "<td><input type='button' class='btn' id='" + data[i].sampleNo + "#" + data[i].date + "#" + data[i].initials +" " +data[i].fname+" " +data[i].lname + "#" + data[i].tpno+ "#" + agefr_sms +"#" + data[i].gender + "' style='margin:0px; background-color:#6D99FE;' name='submit' value='WorkSheet' onclick='WorkSheet(" + data[i].pid + ",id,`"+data[i].status+"`)'></td>";
                                } else {
                                    tableBody += "<td><input type='button' class='btn' id='" + data[i].sampleNo + "#" + data[i].date + "#" + data[i].initials +" " +data[i].fname+" " +data[i].lname + "#" + data[i].tpno+ "#" + agefr_sms +"#" + data[i].gender + "' style='margin:0px; background-color:#FEE87A;' name='submit' value='ACCEPT' onclick='WorkSheet(" + data[i].pid + ",id,`"+data[i].status+"`)'></td>";
                                }

                                if(sNo !== ""){

                                last_searched_sample_id = "" + data[i].pid + "," + data[i].sampleNo + "#" + data[i].date + "#" + data[i].initials +" " +data[i].fname+" " +data[i].lname + "#" + data[i].tpno+ "#" + agefr_sms +"#" + data[i].gender + ",`"+data[i].status+"`";

                                }

                            } else {

                                if (data[i].status === "Accepted" || statusx === "Lab Accepted" || data[i].status === "Done") {
                                    tableBody += "<td><input type='button' class='btn' id='" + data[i].sampleNo + "#" + data[i].date + "#" + data[i].initials +" " +data[i].fname+" " +data[i].lname + "#" + data[i].tpno+ "#" + agefr_sms +"#" + data[i].gender + "' style='margin:0px; background-color:#6D99FE;' name='submit' value='WorkSheet' onclick='WorkSheet(" + data[i].pid + ",id,`"+data[i].status+"`)'></td>";



                                } else {
                                    tableBody += "<td><input type='button' class='btn' id='" + data[i].sampleNo + "#" + data[i].date + "#" + data[i].initials +" " +data[i].fname+" " +data[i].lname + "#" + data[i].tpno+ "#" + agefr_sms +"#" + data[i].gender + "' style='margin:0px; background-color:#FEE87A;' name='submit' value='ACCEPT' onclick='WorkSheet(" + data[i].pid + ",id,`"+data[i].status+"`)'></td>";
                                }
                    //                            tableBody += "<td></td>";
                            }

                    }

                    }

                    tableBody += "<td><input type='button' class='btn' style='margin:0px; background-color:#00cc00;' id=" + data[i].sampleNo + " value='Results' onclick='goto(id)'></td></tr>";
                    //                alert(tableBody);
                    }
                    document.getElementById('pdataTable').innerHTML = tableBody;
                    $('#tablesummery').html("Sample Count : " + xy);

                    if (!$('#more').is(":checked")) {
                    search_without_formats();
                    }

                    }
});
}

function search_without_formats() {
    tableBody = "<tr class='viewTHead'>"
    + "<td width='100' height='28' class='fieldText'>Date</td>"
    + "<td width='200' height='28' class='fieldText'>Patient Name</td>"
    + "<td width='60' class='fieldText'>Gender</td>"
    + "<td width='130' class='fieldText'>Age</td>"
    + "<td width='130' class='fieldText'>Contact</td>"
    + "<td width='44' class='fieldText'>S.No</td>"
    + "<td width='44' class='fieldText'>Test</td>";

    tableBody += "<td width='30' ></td>"
    + "<td width='30' ></td>"
    + "<td width='30' ></td>"
    + "</tr>";

    var date = document.getElementById('pdate').value;
    var date2 = document.getElementById('pdatex').value;
    var sNo = document.getElementById('sNo').value;

    var url = "SearchPatientView_WF?date=" + date + "&datex=" + date2+ "&sno=" + sNo;

    $.ajax({
        type: 'POST',
        url: url,
        success: function (data) {
            data = JSON.parse(data);
            DataOblect = data;
            var xy = 0;
            for (var i = 0; i < data.length; i++) {

                    xy += 1;
                    var refbyID = data[i].refference_idref;
                    var refby = $("#refby option[value=" + refbyID + "]").text();

                    var x = data[i].status;

                    var statusx = x.substr(0, 1).toUpperCase() + x.substr(1);

                    var age_months = "";
                    if (data[i].months !== "0") {
                        age_months = "<td style='width: 30%'>" + data[i].months + "M</td>";
                    }

                    var age_days = "";
                    if (data[i].days !== "0") {
                        age_days = "<td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].days + "D</td>";
                    }

                    var age_table = "<table style='width: 100%' class='ageTable'><tr><td style='width: 35%' bgcolor='#CCCCCC'>" + data[i].age + "Y</td>" + age_months + age_days + "</tr></table>"


                    var test_name = data[i].testname;

//                    if (data[i].lab_lid === 19) {
//                        if (test_name.includes("-")) {
//                            test_name = test_name.split("-")[0];
//                        }
//                    }

                    tableBody += "<tr class='phistr' style='cursor:pointer;'><td>" + data[i].date + "</td><td>" + data[i].initials + " " + data[i].fname + " " + data[i].lname + "</td><td>&nbsp;" + data[i].gender + "</td><td>" + age_table + "</td></td><td>&nbsp;" + data[i].tpno + "</td><td style='color:blue; font-weight:bold; size:14pt;' >&nbsp;" + data[i].sampleNo + "</td><td style='font-size:11pt;'>" + test_name + "</td><td>&nbsp;" + statusx + "</td>";
                    tableBody += "<td><input type='button' class='btn' style='margin:0px;' id=" + data[i].testid+"#"+data[i].sampleNo + "#" + data[i].date + " value='Update Format' onclick='updateFormat(id)'></td>";
                    tableBody += "<td><input type='button' class='btn' style='margin:0px;' id=" + data[i].testid+"#"+data[i].sampleNo + "#" + data[i].date + " value='Delete Sample' onclick='deleteSample(id)'></td></tr>";

}
var element = document.getElementById("pdataTable2");
if(typeof(element) !== 'undefined' && element !== null){
    document.getElementById('pdataTable2').innerHTML = tableBody; 
}

    $("#lblsampleswf").html("("+data.length+")");

}
});
}


function selectRecord(sampleNO){


        var samplenox = sampleNO.split("#")[0];
        var date = sampleNO.split("#")[1];
        var ptp = sampleNO.split("#")[2];

        $("#repeatSno").html(samplenox); 
        $("#repeatDate").html(date); 
        $("#repeatTP").html(ptp); 

        $("#newsampleno").val(samplenox); 
        $("#newdate").val(date); 


    }


function shiftTests(){

        var x = confirm("Dou you want to change this test?");
            if (x){

                var sno = $("#repeatSno").html(); 
                var repeatDate = $("#repeatDate").html(); 
                var newTest = $("#newtestid").val(); 


                $.ajax({
                    url: "changeTest",
                    type: 'POST',
                    data: {'sno': sno, 'date': repeatDate, 'tgid': newTest,  '_token': $('input[name=_token]').val()},
                    success: function (result) {
                        alert(result);

                        search();
                    }
                });

            } 

    }

function WorkSheet(pid, id, status) {


    var arr = id.split("#");

    var sno = arr[0];
    var date = arr[1];
    var pname = arr[2];
    var tpno = arr[3];
    var age = arr[4];
    var gender = arr[5];


    if($("#lid_fr_chk").val() == "45"){

        var accept_flag = (status == "Done");

    }else{

        var accept_flag = (status == "Accepted" || status == "Done");

    }



    if(accept_flag){ 

        var x = confirm("Do you want to print patient worksheet?");
        if (x) {
                        //print worksheet

                        

                        var win = window.open("patientworksheet/" + sno + "/" + date, '_blank');
                        win.print();
                        setTimeout(function () {
                            win.close();
                            
                        }, 5000);
                    }

    }else{

                    // var x = confirm("Do you want to accept the sample?");
                    // if (x) {
            // update sample status

            if($("#lid_fr_chk").val() == "45"){

                $.ajax({
                    url: "markaccepttolab",
                    type: 'POST',
                    data: {'date': date, 'sno': sno, '_token': $('input[name=_token]').val()},
                    success: function (result) {



                        var x = confirm("Do you want to print patient worksheet?");
                        if (x) {
                            //print worksheet

                            

                            var win = window.open("patientworksheet/" + sno + "/" + date, '_blank');
                            win.print();
                            setTimeout(function () {
                                win.close();
                                
                            }, 5000);
                        }

                        //send Accept SMS
                        sendAcceptSMS(pname, tpno, age, gender, sno);

                        search();

                    }
                });

            }else{

                $.ajax({
                    url: "acceptsample",
                    type: 'POST',
                    data: {'pid': pid, 'sno': sno, 'date': date, '_token': $('input[name=_token]').val()},
                    success: function (result) {



                        var x = confirm("Do you want to print patient worksheet?");
                        if (x) {
                            //print worksheet

                            

                            var win = window.open("patientworksheet/" + sno + "/" + date, '_blank');
                            win.print();
                            setTimeout(function () {
                                win.close();
                                
                            }, 5000);
                        }

                        //send Accept SMS
                        sendAcceptSMS(pname, tpno, age, gender, sno);

                        search();

                    }
                });

            }
        // }

    }

    
}

function sendAcceptSMS(pname, tpno, age, gender, sno) {

    let chr = sno[sno.length - 1];
    if (parseInt(chr) >= 0 && parseInt(chr) <= 9) {

        if($("#lid_fr_chk").val() == "44"){

            var msgType = "acceptsms";


            $.ajax({
                type: 'POST',
                url: "sendsms",
                data: {'tpno': tpno, 'type': msgType, 'name': pname,'age': age,'gender': gender,'sno': sno, '_token': $('input[name=_token]').val()},
                success: function (data) {
                    var win = window.open(data);
                    setTimeout(function () {
                        win.close();
                    }, 5000);
                } 
            });


        }

    }



}


function addSample(pid) {
    window.location = "addpatientto?pid=" + pid;
}

function view(lpsid) {
    window.location = "viewOP?lpsid=" + lpsid;
}

function goto(sno) {
    window.location = "enterresults?pdate=" + searchedDate + "&psno=" + sno;
}

function printPatientTable() {

        //hide buttons
        $('.btn').hide();

        var body = $("#pdataTable").html();
        var date = document.getElementById('pdate').value;
        var newWin = window.open('', 'Print-Window');
        newWin.document.open();
        newWin.document.write("<html><head><title>MLWS - Print</title><head><body onload='window.print()'><h2>MLWS Patient Details</h2><p>Date : " + date + "</p><div style='width:800px'><hr/><br/><table>" + body + "</table></div><br/><hr/><p style='font-size:12px' align='right'>Generated By MLWS. Powered by Appex Solutions. www.appexsl.com</p><style>table, td, th {border: 1px solid black;} table {border-collapse: collapse;}</style></body></html>");
        newWin.document.close();
        setTimeout(function () {
            newWin.close();
            $('.btn').show();
        }, 2000);

    }

    function setBranchCode() {
        var branchCode = "";
        if ($('#brcodex').val() === "ALL") {
            branchCode = "";
        } else {
            branchCode = $('#brcodex').val();
        }
        $('#sNo').val(branchCode);
    }

    function moreSettings() {
        if ($("#othFilters").is(":hidden")) {
            $("#othFilters").show();
        } else {
            $("#othFilters").hide();
        }

    }

    function requestStatusSMS(){

        if($("#smsSt").is(":checked")){

            $("#emailSt").prop("checked", false);

            getStatusReport("sms");

        }else{

            for (let i = 0; i < stLps.length; i++) {
                $("#"+stLps[i]).css("background-color", "white");

            }


        }

        

    }

    function showSamplesWF(){
        if ($('#tablebody2').is(':hidden')) {
            $('#tablebody2').show();
            $('#btnsampleswf').val("Hide List");
        }else{
            $('#tablebody2').hide();
            $('#btnsampleswf').val("View List");
        }
    }

    function requestStatusEmail(){

        if($("#emailSt").is(":checked")){

            $("#smsSt").prop("checked", false);
        }

        getStatusReport("em");

    }

    function getStatusReport(type){

        $.ajax({
            url: "getStatus",
            type: 'POST',
            data: {'submit': 'getStatus', 'type': type, 'stLps': stLps, '_token': $('input[name=_token]').val()},
            success: function(resultx) {


                var arr = resultx.toString().split(",");



                for (let i = 0; i < arr.length; i++) {
                    $("#"+resultx[i]).css("background-color", "#90EE90");

                }

            }
        });



    }

    function changeLPSData(){
        var x = confirm("Dou you want to change this details?");
            if (x){

                var sno = $("#repeatSno").html(); 
                var oDate = $("#repeatDate").html(); 

                var newSampleno = $("#newsampleno").val(); 
                var newDate = $("#newdate").val(); 


                $.ajax({
                    url: "changelpsdata",
                    type: 'POST',
                    data: {'sno': sno, 'date': oDate, 'nsno': newSampleno, 'ndate': newDate,  '_token': $('input[name=_token]').val()},
                    success: function (result) {
                        alert(result);

                        search();
                    }
                });

            } 
    }

    function searchAfterAccept(){

        search();

        $("#sNo").val("");

    }

    // $(document).on('keydown keyup', '#sNo', function (e) {
    //     if (e.which === 13) {
    //     e.stopPropagation();

    //         search(); 
    //     }


    // });

    document.addEventListener("keyup", function(event) {
        event.stopPropagation();

        if(event.key == "Enter" && event.srcElement.id == "sNo"){
            search(); 
            
            //auto accept if in accept mode    

            if(document.getElementById('viewtype').value == "accept"){

                $.ajax({
                    url: "markaccepttolab",
                    type: 'POST',
                    data: {'date': document.getElementById('pdate').value, 'sno': document.getElementById('sNo').value, '_token': $('input[name=_token]').val()},
                    success: function (result) {



                        // var x = confirm("Do you want to print patient worksheet?");
                        // if (x) {
                        //     //print worksheet

                            

                        //     var win = window.open("patientworksheet/" + sno + "/" + date, '_blank');
                        //     win.print();
                        //     setTimeout(function () {
                        //         win.close();
                                
                        //     }, 5000);
                        // }

                        setTimeout(searchAfterAccept, 3000);


                        


                    }
                });

            }
        }

    });

    $(document).on('keydown keypress', '#fname', function (e) {
        if (e.which === 13) {
            search();
        }
    });

    $(document).on('keydown keypress', '#lname', function (e) {
        if (e.which === 13) {
            search();
        }
    });


</script>

<style>
#pdataTable tr:nth-child(even){background-color: white;}

#pdataTable tr:hover {background-color: lightgray;}

#pdataTable td{
    padding-left: 5px;
    padding-right: 5px;
}
</style>

@stop

@section('body')
<?php
if (isset($_SESSION['lid']) & isset($_SESSION['luid'])) {
    ?>
    <blockquote>

        <?php
        if(isset($option)){
            if($option == "accept"){

                ?>
                <h3 class="pageheading">Sample Accept</h3>
                <input type="hidden" id="viewtype" value="accept">
                <?php

            }else if($option == "dept"){

                ?>
                <h3 class="pageheading">Department Area - <span id="depnamex"></span></h3>
                <input type="hidden" id="viewtype" value="dept">
                <?php

            }else{

                ?>
                <h3 class="pageheading">View Patients</h3>
                <input type="hidden" id="viewtype" value="view">
                <?php
            }
        }else{
            ?>
                <h3 class="pageheading">View Patients</h3>
                <input type="hidden" id="viewtype" value="view">
                <?php
        }
        ?>
        
        <br/>

        <table border="0">

            <tbody>
                <tr>

                    <td>Date :<input type="date" name="date" id="pdate" class="input-text" style="width: 125px;"> <input type="date" name="datex" id="pdatex" class="input-text" style="width: 125px;"></td>


                    <td>Sample NO : 
                        <?php
                        if ($_SESSION["guest"] == null) {
                            $selectedBranchCode = "";
                        } else {
                            $selectedBranchCode = $_SESSION["userbranch"];
                        }
                        ?>

                        <?php
                        if ($_SESSION["guest"] == null) {
                            ?>
                            <input type="text" name="sampleNo"  class="input-text" id="sNo" style="width: 100px" pattern="[A-Za-z0-9]{1,10}" title="Minimum one charactor, Maximum 10 charactors and excluding symbols." value="<?php echo $selectedBranchCode; ?>">
                            <?php
                        } else {
                            ?>
                            <input type="text" name="sampleNo"  class="input-text" id="sNo" style="width: 100px" pattern="[A-Za-z0-9]{1,10}" title="Minimum one charactor, Maximum 10 charactors and excluding symbols." value="<?php echo $selectedBranchCode; ?>" disabled="disabled">
                            <?php
                        }
                        ?>
                    </td>


                    <td>
                        <?php
                        $result1x = DB::select("select * from Lab_features where lab_lid = '" . $_SESSION["lid"] . "' and features_idfeatures = (select idfeatures from features where name = 'Branch Handeling')");
                        if (!empty($result1x)) {
                            ?> 
                            Branch :  
                            <select class="select-basic" id="brcodex" style="width: 150px;" onchange="setBranchCode()"> 


                                <?php
                                if ($_SESSION["guest"] == null) {
                                    ?>
                                    <option value="ALL"></option> 
                                    <?php
                                    $rs = DB::select("select name, code from labbranches where lab_lid = '" . $_SESSION['lid'] . "'");
                                    foreach ($rs as $rsb) {
                                        ?>
                                        <option value="{{ $rsb->code }}">{{ $rsb->name }}</option>
                                        <?php
                                    }
                                } else {
                                    $rs = DB::select("select name, code from labbranches where lab_lid = '" . $_SESSION['lid'] . "' and code='" . $selectedBranchCode . "'");
                                    foreach ($rs as $rsb) {
                                        ?>
                                        <option value="{{ $rsb->code }}">{{ $rsb->name }}</option>
                                        <?php
                                    }
                                }
                                ?> 

                            </select>
                            <?php
                        }
                        ?>


                    </td>
                    <td> 
                        <input type="button" name="search" class="btn" id="search" value="Search" onclick="validate();" style="margin-right: 0px; margin-left: 0px; width: 100px; float: left;"> 
                        &nbsp;&nbsp;&nbsp; <div id="tablesummery" style="float: right; padding-top: 15px;"></div>
                    </td>

                    <td>
                        &nbsp;&nbsp;&nbsp; <input type="checkbox" id="more" name="more" onchange="moreSettings()"> More Options
                    </td>

                </tr>
            </tbody>
        </table>


        <table id="othFilters">
            <tr>


                <td>Patient Name :
                    <input type="text" name="searchfname" class="input-text" style="width: 100px" id="fname" pattern="[A-Za-z]{1,40}" title="Valid name excluding digits."></td>
                    <td>Contact : 
                        <input type="text" name="searchlname" class="input-text" style="width: 100px" id="lname" pattern="[A-Za-z]{1,40}" title="Valid name excluding digits."></td>
                        <td><?php if ($_SESSION["guest"] == null) {
                            ?>
                            Referred By : 
                            <select id="refby" class="select-basic" style="width: 150px;" class="select-basic">
                                <option value="0">All</option>
                                <?php
                                $refferenceResult = DB::select("Select * from refference where lid = '" . $_SESSION['lid'] . "' order by name");
                                foreach ($refferenceResult as $result) {
                                    ?>
                                    <option value="<?php echo $result->idref; ?>"><?php echo $result->name; ?></option>
                                    <?php
                                }
                                ?>
                            </select>

                        <?php } else { ?>
                            <input type="hidden" id="refby" value="0"/>
                        <?php } ?>    
                    </td>

                    <td> &nbsp; &nbsp; Specimen : &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;
                        
                    </td> 
                    <td>
                        <select id="samplespec" name="samplespec" class="select-basic" style="width: 145px;">
                            <option value="%">All</option>
                                <?php
                                $refferenceResult = DB::select("Select * from testinginput order by name ASC");
                                foreach ($refferenceResult as $result) {
                                    ?>
                                    <option value="<?php echo $result->tiid; ?>"><?php echo $result->name; ?></option>
                                    <?php
                                }
                                ?>
                        </select> 
                    </td>



                </tr>

                <tr>

                    <td>
                        Status : &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        <select id="teststate" name="teststate" class="select-basic" style="width: 120px; margin-left: 5px;">
                            <option value="%">All</option>
                            <option value="pending">Pending</option>
                            <option value="Accepted">Barcoded</option>
                            <option value="LabAccepted">Lab Accepted Only</option>
                            <option value="Accepted">Accepted</option>
                            <option value="Done">Done</option>
                            <option value="Verified">Verified Only</option>
                            <option value="Printed">Printed</option>
                            <option value="Billed Only">Billed Only</option>
                            <option value="Not Collected">Not Collected</option>                        
                            <option value="Cancelled">Cancelled</option>
                            <option value="labview">Lab View</option>

                        </select>
                    </td>
                    <td>
                        Testing : 
                        <input id="tgroup" list="testgroups" style="width: 100px; margin-left: 2px;" class="input-text"> 
                        <datalist id="testgroups">
                            <?php
                            $Result = DB::select("select c.name,c.tgid from test a, Lab_has_test b,Testgroup c where a.tid = b.test_tid and b.testgroup_tgid = c.tgid and b.lab_lid='" . $_SESSION['lid'] . "' group by c.name order by c.name");
                            foreach ($Result as $res) {
                                $tgid = $res->tgid;
                                $group = $res->name;
                                ?>
                                <option id="{{ $tgid }}" value="{{ $group }}"/>
                                <?php
                            }
                            ?>
                        </datalist>
                    </td>

                    <td>Type &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;
                        : <select id="type" name="type" class="select-basic" style="width: 150px;">
                            <option></option>
                            <option>In</option>
                            <option>Out</option>
                        </select>
                    </td>

                    <td> &nbsp; &nbsp; Department : &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;
                        
                    </td>

                    <td>
                        <select id="sampledept" name="sampledept" class="select-basic" style="width: 145px;">
                            <option value="%">All</option>
                            <?php
                                $refferenceResult = DB::select("Select * from testingcategory order by name ASC");
                                foreach ($refferenceResult as $result) {
                                    ?>
                                    <option value="<?php echo $result->tcid; ?>"><?php echo $result->name; ?></option>
                                    <?php
                                }
                                ?>
                            
                        </select>
                    </td>

                    <td>
                        <input type="checkbox" id="smsSt" onchange="requestStatusSMS()" /> SMS Status
                    </td>

                    <td>
                        &nbsp; &nbsp;
                        <!-- <input type="checkbox" id="emailSt" onchange="requestStatusEmail()" /> Email Status -->
                    </td>


                </tr>
            </table>



            <div class="tableBody" style="height:500px">
                <form action="selectOP" method="POST">
                    <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="pdataTable" width="100%" border="0" cellspacing="2" cellpadding="0">
                        <tr class="viewTHead">


                    </tr>
                </table> 
            </form> 
        </div>

        <hr>

        <table width="100%">
            <tr>
                <td>
                    <input type="button" class="btn" value="Print Patient List" onclick="printPatientTable()">
                </td>
                
                <td style="text-align:right;"> 

                    <?php

                    if($_SESSION['lid'] == 45 || $_SESSION['lid'] == 44){

                    ?>
                    
                    <table>
                        <tr>
                            <td><div class="colorbox" style="background-color:#FFFC44;"></div> <div style="float: right;">Pending</div></td>
                            <td><div class="colorbox" style="background-color:#EBA7FF;"></div> <div style="float: right;">Barcoded</div></td> 
                            <td><div class="colorbox" style="background-color:#A7BEFF;"></div> <div style="float: right;">Accepted in Lab</div></td>
                            <td><div class="colorbox" style="background-color:#90EE90;"></div> <div style="float: right;">Report Entered (Done)</div></td>
                            <td><div class="colorbox" style="background-color:#C0FF00;"></div> <div style="float: right;">Report Verified</div></td>
                            <td><div class="colorbox" style="background-color:#7FFFD4;"></div> <div style="float: right;">Printed</div></td>
                        </tr>
                    </table> 

                    <?php } ?>

                    
                </td>
            </tr>
        </table>

        <style>
            .colorbox{

                width: 25px;
                height: 25px;
                border-radius: 5px;
                float: left;
                margin-right: 10px;
                margin-left: 10px;

            }
        </style>



        <hr>

        <?php

        $editingPrivs = false;

        $resultEP = DB::select("SELECT * FROM privillages p where user_uid = (select user_uid from labUser where luid = '" . $_SESSION['luid'] . "') and options_idoptions = '19';");
        foreach ($resultEP as $resep) {
            $editingPrivs = true;
        }

        if($editingPrivs){

        ?>

        <br>

        <b>Sample Options </b>

        <br>
        <br>

        <table>


            <tr>
                <td> 
                    <div style="font-family:sans-serif; font-weight:bold; color: blue;" id="repeatSno"></div>  
                    <div style="font-family:sans-serif; font-weight:bold; color: blue;" id="repeatDate"></div> 
                    <div style="font-family:sans-serif; font-weight:bold;" id="repeatTP"></div> 
                </td>
                


            </tr>



            </table>

            <br>



            <b>Change Test Group</b>

            <br>
            <table>
                <tr>
                    
                    <td>New Test Name</td>
                

                    <td> 

                        <select id="newtestid" class="input-text">

                            <?php

                            $Result = DB::select("select c.name,c.tgid from test a, Lab_has_test b,Testgroup c where a.tid = b.test_tid and b.testgroup_tgid = c.tgid and b.lab_lid='" . $_SESSION['lid'] . "' group by c.name order by c.name");
                            foreach ($Result as $res) {

                                ?>

                                <option value="<?php echo $res->tgid;?>"><?php echo $res->name;?></option>

                                <?php

                            }

                            ?>

                        </select>

                    </td>

                    <td>
                        <input type="button" id="shiftbtn" class="btn" onclick="shiftTests()" value="Replace Test">
                    </td>

                </tr>
            </table>

            <br>


            <table>
                <tr>
                    
                    <td>Change Date and Sampleno</td>
                

                    <td> 

                        <table>
                            <tr>
                                <td> &nbsp; &nbsp; &nbsp; &nbsp;New Sample NO : <input type="text" id="newsampleno" class="input-text" /></td>
                                <td>New Date : <input type="date" id="newdate" class="input-text" /></td>
                                <td><input type="button" value="Change Details" class="btn" onclick="changeLPSData()" /</td>
                            </tr>
                        </table>

                        
                        



                    </td>

                    <td>
                    </td>

                </tr>
            </table>


        <?php } ?>
        

        <?php if ($_SESSION["guest"] == null) { ?>
            <br/>
            <hr/>


            <h3>Samples without Report Formats <span id="lblsampleswf"></span> <input type="button" id="btnsampleswf" onclick="showSamplesWF()" value="View List"> </h3>
            
            <div id="tablebody2">
                <form action="selectOP">
                    <table style="font-family: Futura, 'Trebuchet MS', Arial, sans-serif; font-size: 13pt;" id="pdataTable2" width="100%" border="0" cellspacing="2" cellpadding="0">

                    </table>
                </form>
            </div>

            <div id="guestelement"></div>

        <?php } ?>

        <input type="hidden" id="lid_fr_chk" value="<?php echo $_SESSION['lid']; ?>">
        

        <?php
        if (isset($_GET['load'])) {
            ?>
            <input type="hidden" id="loadVal" name="loadVal" value="ok"/>
            <?php
        } else {
            ?>
            <input type="hidden" id="loadVal" name="loadVal" value=""/>
            <?php
        }
        ?>

        <?php if ($_SESSION["guest"] == null) { ?>
            <input type="hidden" id="guestx" value="false"/>
        <?php } else { ?>
            <input type="hidden" id="guestx" value="true"/>  
        <?php } ?>

    </blockquote> 
    <?php
}
?>


@stop


