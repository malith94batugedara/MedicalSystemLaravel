<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class SampleController extends Controller {

    public function searchSample() {
        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];
        $_SESSION['smpno']= 24020610;//dev by malith

        $lpsidd = DB::select("SELECT l.lpsid FROM lps l where l.sampleNo = '" . $_SESSION['smpno'] . "'");//dev by malith

        $date = Input::get('date');
        $sNo = Input::get('sno');

        if ($sNo == "") {
            $sNo = "%";
        }
        if ($date == "") {
            $date = "%";
        }

        $repName = "";

        //load Editing privilages
        $editingPrivs = "readonly";
        $savingPrivs = "disabled=true";
        $auth1Privs = "disabled=true";
        $auth2Privs = "disabled=true";

        

        //Get Auth Levels
        $resultAL = DB::select("SELECT report_auth_1, report_auth_2 FROM configs where lab_lid = '" . $lid . "'");
        foreach ($resultAL as $resal) {
            $Auth_01 = $resal->report_auth_1;
            $Auth_02 = $resal->report_auth_2;
        }
        //

        // $resultEP = DB::select("SELECT * FROM privillages p where user_uid = (select user_uid from labUser where luid = '" . $luid . "') and options_idoptions = '12';");
        // foreach ($resultEP as $resep) { 
            $editingPrivs = "";
            $savingPrivs = "";
        // }

        // $resultEP = DB::select("SELECT * FROM privillages p where user_uid = (select user_uid from labUser where luid = '" . $luid . "') and options_idoptions = '20';");
        // foreach ($resultEP as $resep) {
            $auth1Privs = "";
        // }

        // $resultEP = DB::select("SELECT * FROM privillages p where user_uid = (select user_uid from labUser where luid = '" . $luid . "') and options_idoptions = '21';");
        // foreach ($resultEP as $resep) {
            $auth2Privs = "";
        // }
        //

        $authOne_color = "yellow";
        $authTwo_color = "yellow";
        
        $auth01Pass = "";
        $auth02Pass = "";

        $Auth_01_U = "";
        $Auth_02_U = "";

        $result = DB::select("select a.*,b.*,c.fname,c.lname,c.gender_idgender,c.tpno,c.nic,c.email, a.refference_idref,a.entered_uid from lps a,patient b, user c where a.patient_pid = b.pid and b.user_uid = c.uid and a.lab_lid='" . $lid . "' and a.lpsid in (select lpsid from lps where Lab_lid = '" . $lid . "' and date = '" . $date . "' and sampleNo = '" . $sNo . "')");
        foreach ($result as $res) {
            if ($res->refference_idref == null) {
                $refby = "None";
            } else {
                $resultxx = DB::select("select name from refference where idref = '" . $res->refference_idref . "' and lid = '" . $lid . "'");
                foreach ($resultxx as $resxx) {
                    $refby = $resxx->name;
                }
            }
            $lpsID = $res->lpsid;

            $Auth_01_U = $res->auth01_user;
            $Auth_02_U = $res->auth02_user;
            $Lab_accepted_user = $res->labaccepted_user;

            $resultxxg = DB::select("select gender from gender where idgender = '" . $res->gender_idgender . "'");
            foreach ($resultxxg as $resxxg) {
                $gender = $resxxg->gender;
            }

            $lpsSpecialNote = $res->specialnote;
            $lpsLabRemark = $res->report_remark;


            $pData = $res->fname . "&" . $res->lname . "&" . $res->age . "&" . $res->months . "&" . $res->days . "&" . $res->tpno . "&" . $refby . "&" . $res->date . "&" . $res->arivaltime . "&" . $res->status . "&" . $res->finishdate . "&" . $res->finishtime . "&" . $res->blooddraw . "&" . $res->repcollected . "&" . $res->initials . "&" . $res->email . "&" . $res->nic. "&" . $res->fastingtime;

            //get entered user
            $entered_user = "Pending...";
            $resultuser = DB::select("select fname,lname from user where uid = '" . $res->entered_uid . "'");
            foreach ($resultuser as $resus) {
                $entered_user = $resus->fname . " " . $resus->lname;
            }

            //get auth status
            if ($res->auth01 == "1") {
                $authOne_color = "green";
                $auth01Pass = "Pass";
            }
            
            if ($res->auth02 == "1") {
                $authTwo_color = "green";
                $auth02Pass = "Pass";
            }
        }

        if (isset($lpsID)) {
            $tData = "";
            $result0 = DB::select("select d.tgid,d.name from lps_has_test b,Lab_has_test c, Testgroup d where c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and b.lps_lpsid='" . $lpsID . "' and d.Lab_lid = '" . $lid . "' group by d.tgid");
            foreach ($result0 as $res0) {

                $tg_mame = $res0->name;


                if ($lid == "19") {
                    if (strpos($tg_mame, "-") !== false) {
                        $tg_mame = explode("-", $tg_mame)[0];
                    }
                }

                $tData .= "<tr><td colspan='3'><h4 style='margin-bottom:5px; margin-left:0px;'><b>" . $tg_mame . "</b></h4></td></tr>";
//                $tData .= "<tr><td><u>TEST</u></td><td><u>VALUE</u></td><td width='100'><u>UNIT</u></td><td>2020/12/01</td><td>2020/11/27</td></tr>";

                //define report Name~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                if ($res0->name == "Macroscopy") {
                    $repName = "Urine Full Report";
                }
                //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                //dev by malith
                $result2 = DB::select("select a.tid,a.name,c.measurement,c.status,b.value,c.valueformulars_fid,c.selactablevals from test a,lps_has_test b,Lab_has_test c where c.Testgroup_tgid = '" . $res0->tgid . "' and a.tid=b.test_tid and a.tid=c.test_tid and b.lps_lpsid='" . $lpsID . "' group by a.tid order by a.tid ASC;");
                foreach ($result2 as $res) {
                    $tid = $res->tid;
                    $name = $res->name;
                    $value = $res->value;

                    $selectableVals = $res->selactablevals;

                    //view default value if sample values not saved
                    if ($value === null) {
                        $resultx = DB::select("select defaultval from labtestingdetails where lab_lid = '" . $lid . "' and test_tid = '" . $tid . "'");
                        foreach ($resultx as $resx) {
                            $value = $resx->defaultval;
                        }
                    }

                    $mes = $res->measurement;
                    $vCode = $res->status;
                    $formular = "";
                    $formularID = $res->valueformulars_fid;
//                    $onkeyup = "";
//                    $formular = "";
//                    if ($formularID != null) {
//
                    $result2x = DB::select("select name, formular from valueformulars where fid = '" . $formularID . "' and lab_lid = '" . $lid . "'");
                    foreach ($result2x as $res2x) {
                        $formular = $res2x->formular;
//                            $formularName = $res2x->name;
                    }

//                    }
//                    $onkeyup = "onkeyup='".$formular."'";

                    $vCodeSp = explode('#', $vCode);

                    $tData .= "<tr>" . "<td>&nbsp;&nbsp;&nbsp;" . $tid . " " . $name . "</td>";
//                    $tData .= "<tr>" . "<td>&nbsp;&nbsp;&nbsp;" . $name . "</td>";
                    if ($vCodeSp[0] == "Integer") {
                        $tData .= "<td><input style='width:150px;' class='input-text' type='number' id='" . $tid . "' name='" . $tid . "' value='" . $value . "' Min='" . $vCodeSp[2] . "' Max='" . $vCodeSp[3] . "' onkeyup='" . $formular . "' " . $editingPrivs . "></td>";
                    } else if ($vCodeSp[0] == "Decimal") {
                        if ($vCodeSp[4] == "1") { 
                            $tData .= "<td><input style='width:150px;' class='input-text' type='number' id='" . $tid . "' name='" . $tid . "' value='" . $value . "' Min='" . $vCodeSp[2] . "' Max='" . $vCodeSp[3] . "' step='0.1' onkeyup='" . $formular . "' " . $editingPrivs . "></td>";
                        } else if ($vCodeSp[4] == "2") { 
                            $tData .= "<td><input style='width:150px;' class='input-text' type='number' id='" . $tid . "' name='" . $tid . "' value='" . $value . "' Min='" . $vCodeSp[2] . "' Max='" . $vCodeSp[3] . "' step='0.01' onkeyup='" . $formular . "' " . $editingPrivs . "></td>";
                        } else if ($vCodeSp[4] == "3") { 
                            $tData .= "<td><input style='width: 150px;' class='input-text' type='number' id='" . $tid . "' name='" . $tid . "' value='" . $value . "' Min='" . $vCodeSp[2] . "' Max='" . $vCodeSp[3] . "' step='0.001' onkeyup='" . $formular . "' " . $editingPrivs . "></td>";
                        }
                    } else if ($vCodeSp[0] == "String") {

                        $selectVals = "";
                        $selectListID = "";

                        if ($selectableVals && $lid != '41' && $lid != '18' && $lid != '44' && $lid != '31' && $lid != '42' && $lid != '45' && $lid != '28' && $lid != '32' && $lid != '26'&& $lid != '37'&& $lid != '29') {
                            $selectListID = "list='tst" . $tid . "'";
                            $selectVals = "<datalist id='tst" . $tid . "'>";

                            if($lid == '45'){

                                $resultsp = DB::select("SELECT distinct value FROM lps_has_test a,lps b where a.lps_lpsid= b.lpsid and a.test_tid = '" . $tid . "' and b.lab_lid = '" . $lid . "' group by value order by b.date DESC limit 20"); 

                            }else{ 

                                $resultsp = DB::select("SELECT distinct value FROM lps_has_test a,lps b where a.lps_lpsid= b.lpsid and a.test_tid = '" . $tid . "' and b.lab_lid = '" . $lid . "' group by value order by b.date DESC limit 10"); 

                            }
                            

                            

                            foreach ($resultsp as $resp) {
                                $selectVals .= "<option>" . $resp->value . "</option>";
                            }

                            $selectVals .= "</datalist>"; 
                        }
                        
                        if ( ($lid == '18' | $lid == '44' | $lid == '41' | $lid == '31' | $lid == '42' | $lid == '45' | $lid == '28' | $lid == '32' | $lid == '26' | $lid == '37' | $lid == '29') && $selectableVals) {
                            $selectListID = "list='tst" . $tid . "'";
                            $selectVals = "<datalist id='tst" . $tid . "'>";
                            $resultsp = DB::select("SELECT value FROM value_suggests where lhtid = (select lhtid from Lab_has_test where lab_lid = '".$lid."' and test_tid = '".$tid."')");
                            foreach ($resultsp as $resp) {
                                $selectVals .= "<option>" . $resp->value . "</option>";
                            }

                            $selectVals .= "</datalist>";
                        }
                        
                        $tData .= "<td><input type='text' style='width:150px;' class='input-text' id='" . $tid . "' name='" . $tid . "' value='" . $value . "' maxlength='" . $vCodeSp[1] . "' onkeyup='" . $formular . "' " . $selectListID . " " . $editingPrivs . ">" . $selectVals . "</td>";
                    } else if ($vCodeSp[0] == "Paragraph") {
                        $tData .= "<td><textarea class='text-area' rows='5' columns='60' id='" . $tid . "' name='" . $tid . "' style='width:400px;' onkeyup='test()' " . $editingPrivs . ">" . str_replace("<br/>", "\n", $value) . "</textarea>";
                    } else {
                        $tData .= "<td><input type='text' style='width:150px;' class='input-text' id='" . $tid . "' name='" . $tid . "' value='" . $value . "' onkeyup='" . $formular . "' " . $editingPrivs . "></td>";
                    }
                    
                    // $hisVal = $value + 10.9;
                    // $hisVal2 = $value + 8.2;
                    
                    $historyTD = "";
                    $historyTD2 = "";

//                    if($lid == "19"){
//                        $historyTD = "<div style='background-color:silver; width:70px; padding:2px; text-align:right'>".$hisVal."</div>";
//                        $historyTD2 = "<div style='background-color:silver; width:70px; padding:2px; text-align:right'>".$hisVal2."</div>";
//                    }

                    $tData .= "<td>" . $mes . "</td>" . "<td>" . $historyTD . "</td>" . "<td>" . $historyTD2 . "</td>" . "</tr>";
                }
            }

            //special note
            $resultsn = DB::select("select viewspecialnote,lab_report_remark from reportconfigs where lab_lid = '" . $lid . "'");
            foreach ($resultsn as $ressn) {
                if ($ressn->viewspecialnote == 1) {
                    $tData .= "<tr><td>&nbsp;</td></tr><tr><td>&nbsp;&nbsp; Special Note</td><td><input type='text' style='width:150px;' class='input-text' id='repnote' name='" . $lpsID . "sp' value = '" . $lpsSpecialNote . "' list='snotelist'> <datalist id='snotelist'>";

                    $resultsp = DB::select("SELECT specialnote FROM lps where lab_lid = '" . $lid . "' group by specialnote");
                    foreach ($resultsp as $resp) {
                        $tData .= "<option>" . $resp->specialnote . "</option>";
                    }

                    $tData .= "</datalist></td></tr>"; 
                }

                //report remark for lab view only
                if ($ressn->lab_report_remark == 1) {
                    $tData .= "<tr><td>&nbsp;</td></tr><tr><td>&nbsp;&nbsp; Report Remark</td><td><input type='text' style='width:150px;' class='input-text' id='repremark' name='" . $lpsID . "rm' value = '" . $lpsLabRemark . "' >";
                    $tData .= "</td></tr>"; 
                }
                //
            }

            

            //guest mode customized for Siyasi Lab~~~~~~~~~~~~~
            if($lid == "33"){

                if ($_SESSION["guest"] == null) {
                    $tData .= "<tr><td><input class='btn' type='reset' name='reset' value = 'Reset Fields'/></td><td><input id='btnsr' type='button' class='btn' style='width:180px; margin-left:0px;' onclick='submitForm()' name='save' value = 'Save Results' " . $savingPrivs . "/></td></tr><tr><td><input class='btn' type='button' name='printN' value = 'Print With Heading' onclick='printReportWithHeading(false)'/></td></tr>";

                    if($Auth_01 == "1"){
                        $tData .= "<tr><td></td><td><input id='btnao' type='button' class='btn' style='width:180px; margin-left:0px; background-color:".$authOne_color.";' onclick='auth_one(" . $lpsID . ")' name='authone' value = 'Confirm' " . $auth1Privs . "/></td></tr>";
                    }


                    if($Auth_02  == "1"){
                        $tData .= "<tr><td></td><td><input id='btnat' type='button' class='btn' style='width:180px; margin-left:0px; background-color:".$authTwo_color.";' onclick='auth_two(" . $lpsID . ")' name='authtwo' value = 'Verify' " . $auth2Privs . "/></td></tr>";
                    }

                }else{

                    if($savingPrivs == ""){
                        $tData .= "<tr><td><input class='btn' type='reset' name='reset' value = 'Reset Fields'/></td><td><input id='btnsr' type='button' class='btn' style='width:180px; margin-left:0px;' onclick='submitForm()' name='save' value = 'Save Results' " . $savingPrivs . "/></td></tr><tr><td><input class='btn' type='button' name='printN' value = 'Print With Heading' onclick='printReportWithHeading(false)'/></td></tr>";
                    }

                    if($Auth_01 == "1"){
                        $tData .= "<tr><td></td><td><input id='btnao' type='button' class='btn' style='width:180px; margin-left:0px; background-color:".$authOne_color.";' onclick='auth_one(" . $lpsID . ")' name='authone' value = 'Confirm' " . $auth1Privs . "/></td></tr>";
                    }


                    if($Auth_02  == "1"){
                        $tData .= "<tr><td></td><td><input id='btnat' type='button' class='btn' style='width:180px; margin-left:0px; background-color:".$authTwo_color.";' onclick='auth_two(" . $lpsID . ")' name='authtwo' value = 'Verify' " . $auth2Privs . "/></td></tr>";
                    }

                }
            //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            }else{

                if ($_SESSION["guest"] == null) {
                    
                    // if($auth01Pass != "Pass" && $auth02Pass != "Pass" && $entered_user == "Pending..."){
                    if($auth01Pass != "Pass" && $auth02Pass != "Pass"){

                        $tData .= "<tr><td><input class='btn' type='reset' name='reset' value = 'Reset Fields'/></td><td><input id='btnsr' type='button' class='btn' style='width:180px; margin-left:0px;' onclick='submitForm()' name='save' value = 'Save Results' " . $savingPrivs . "/></td>";

                    }

                    

                    if($Auth_01 == "1" && $entered_user != "Pending..."){

                        if($auth01Pass != "Pass"){

                            $tData .= "<tr><td></td><td><input id='btnao' type='button' class='btn' style='width:180px; margin-left:0px; background-color:".$authOne_color.";' onclick='auth_one(" . $lpsID . ")' name='authone' value = 'Confirm' " . $auth1Privs . "/></td></tr>";

                        }

                        
                    }


                    if($Auth_02  == "1" && $entered_user != "Pending..."){

                     if($auth02Pass != "Pass"){

                        $tData .= "<tr><td></td><td><input id='btnat' type='button' class='btn' style='width:180px; margin-left:0px; background-color:".$authTwo_color.";' onclick='auth_two(" . $lpsID . ")' name='authtwo' value = 'Verify' " . $auth2Privs . "/></td></tr>";

                     }
                        
                    }

                    if($Auth_01  == "1" && $Auth_02  != "1"  && $auth01Pass == "Pass"){

                        $tData .= "</tr><tr><td><input class='btn' type='button' name='printN' value = 'Print With Heading' onclick='printReportWithHeading(false)'/></td></tr>";

                    }

                    if($Auth_01  == "1" && $auth01Pass == "Pass" && $Auth_02  == "1" && $auth02Pass == "Pass"){

                        $tData .= "</tr><tr><td><input class='btn' type='button' name='printN' value = 'Print With Heading' onclick='printReportWithHeading(false)'/></td></tr>";

                    }

                    if($Auth_01 == "0" && $Auth_02  == "0"){
                        $tData .= "</tr><tr><td><input class='btn' type='button' name='printN' value = 'Print With Heading' onclick='printReportWithHeading(false)'/></td></tr>";
                    }


                }

            }

            

            $date_count = 0;
            // $resultTinfo = DB::select("select lpsid from lps where Lab_lid='" . $lid . "' and patient_pid = (select patient_pid from lps where lpsid='" . $lpsID . "') group by date");
            // foreach ($resultTinfo as $Tinfo) {
            //     $date_count++;
            // }

            $same_p_reps = "";

            // $result0x = DB::select("select d.tgid,d.name,e.sampleno,e.lpsid,e.status from lps_has_test b,Lab_has_test c, Testgroup d, lps e where e.lpsid=b.lps_lpsid and c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and b.lps_lpsid in (select lpsid from lps where date = '" . $date . "' and lab_lid = '" . $lid . "' and patient_pid = (select patient_pid from lps where lpsid = '" . $lpsID . "')) and b.lps_lpsid != '" . $lpsID . "' and d.Lab_lid = '" . $lid . "' group by d.tgid");

            // $result0x = DB::select("select e.auth02_user, e.repcollected,d.tgid,d.name,e.sampleno,e.lpsid,e.status from lps_has_test b,Lab_has_test c, Testgroup d, lps e where e.lpsid=b.lps_lpsid and c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and b.lps_lpsid in (select lpsid from lps where date = '" . $date . "' and lab_lid = '" . $lid . "' and patient_pid = (select patient_pid from lps where lpsid = '" . $lpsID . "')) and d.Lab_lid = '" . $lid . "' group by d.tgid");


            

            if($lid == "45"){

                $sno_mod = preg_replace('/[a-zA-Z]$/', '', $sNo);

                $result0x = DB::select("select e.auth02_user, e.repcollected,d.tgid,d.name,e.sampleno,e.lpsid,e.status from Testgroup d, lps e where e.testgroup_tgid = d.tgid and e.date = '" . $date . "' and e.Lab_lid = '" . $lid . "' and 

                    CASE
                        WHEN SUBSTRING(sampleno, LENGTH(sampleno), 1) NOT REGEXP '[0-9]' THEN SUBSTRING(sampleno, 1, LENGTH(sampleno) - 1)
                        ELSE sampleno
                        END = '" . $sno_mod . "'

                 ");

            }else{

                $result0x = DB::select("select e.auth02_user, e.repcollected,d.tgid,d.name,e.sampleno,e.lpsid,e.status from Testgroup d, lps e where e.testgroup_tgid = d.tgid and e.date = '" . $date . "' and e.Lab_lid = '" . $lid . "' and e.patient_pid = (select patient_pid from lps where lpsid = '" . $lpsID . "') ");

            }

            // foreach ($result0x as $res0x) {

            //     $color = "#FF9900";
            //     $print_check = "";
            //     if ($res0x->status == "Done") {
            //         $color = "#15BC15";
            //         $print_check = "<input type='checkbox' id='idf".$res0x->lpsid."' onclick='getLPS(".$res0x->lpsid.")'>";
            //     }

            //     if ($res0x->auth02_user != null) {
            //         $color = "#90EE90";
            //         $print_check = "<input type='checkbox' id='idf".$res0x->lpsid."' onclick='getLPS(".$res0x->lpsid.")'>";
            //     }

            //     if ($res0x->repcollected != null) {
            //         // $color = "#7FFFD4";
            //         $color = "#75bfa6";
            //         $print_check = "<input type='checkbox' id='idf".$res0x->lpsid."' onclick='getLPS(".$res0x->lpsid.")'>";
            //     }
                

            //     $same_p_reps .= "<tr class='preptd' style='cursor:pointer; background-color:" . $color . "; color:white;' id='smlp+" . $res0x->sampleno . "' ondblclick='openRep(id)'><td>" . $res0x->sampleno . "</td>" . "<td>" . $res0x->name . "</td>"."<td>".$print_check."</td>"."</tr>";
            // }


            // if($Auth_01_U != null){
            //     $resultTinfox = DB::select("SELECT fname, lname from user where uid = (select user_uid from labUser where luid = '" . $Auth_01_U . "')");
            //     foreach ($resultTinfox as $Tinfox) {
            //         $Auth_01_U = $Tinfox->fname. " " . $Tinfox->lname;           
            //     }
            // }

            // if($Auth_02_U != null){
            //     $resultTinfox = DB::select("SELECT fname, lname from user where uid = (select user_uid from labUser where luid = '" . $Auth_02_U . "')");
            //     foreach ($resultTinfox as $Tinfox) {
            //         $Auth_02_U = $Tinfox->fname. " " . $Tinfox->lname;           
            //     }
            // }

            $bill_remark = "";
            ////dev by malith
            // if (is_array($lpsidd)) {
            //     $lpsidd = implode("','", $lpsidd); // Assuming $lpsidd is an array of IDs
            // }
            $lpsidValue = $lpsidd[0]->lpsid;
            $refvall=DB::select("SELECT e.refference_min, e.refference_max 
            FROM labtestingdetails e,lps_has_test d WHERE e.test_tid = d.test_tid AND d.lps_lpsid = $lpsidValue");
            $all_reff1 = []; // Array to store all refference_min values
            $all_reff2 = []; // Array to store all refference_max values

            foreach ($refvall as $refval) {
                    $all_reff1[] = $refval->refference_min;
                    $all_reff2[] = $refval->refference_max;
            }
            // $resultTinfoxbr = DB::select("SELECT remark from invoice where lps_lpsid = '" . $lpsID . "' ");
            // foreach ($resultTinfoxbr as $Tinfoxbr) {
            //     $bill_remark = $Tinfoxbr->remark;           
            // }

            //get lab accepted user
            // if($Lab_accepted_user != null){
            //     $resultTinfox = DB::select("SELECT fname, lname from user where uid = (select user_uid from labUser where luid = '" . $Lab_accepted_user . "')");
            //     foreach ($resultTinfox as $Tinfox) {
            //         $Lab_accepted_user = $Tinfox->fname. " " . $Tinfox->lname;           
            //     }
            // }

            $reff1_string = implode("/&&", $all_reff1);
            $reff2_string = implode("/&&", $all_reff2);
            // $testt_id = DB::select("SELECT a.test_tid FROM lps_has_test a WHERE a.lps_lpsid = $lpsidValue");

            // Extract the 'test_tid' values from the objects and store them in an array
            // $testid_array = [];
            // foreach ($testt_id as $row) {
            //     $testid_array[] = $row->test_tid;
            // }
            
            // Use implode() to concatenate the 'test_tid' values into a string separated by "/&&"
            // $testid_string = implode("/&&", $testid_array);

            echo $pData . "&" . $repName . "/&&" . $tData . "/&&" . $lpsID . "/&&" . $gender . "/&&" . $entered_user . "/&&" . $date_count . "/&&" . $same_p_reps. "/&&" . $auth01Pass. "/&&" . $auth02Pass. "/&&" . $Auth_01. "/&&" . $Auth_02. "/&&" . $Auth_01_U. "/&&" . $Auth_02_U. "/&&" . $bill_remark. "/&&" . $Lab_accepted_user ."/&&". $reff1_string ."/&&". $reff2_string;
            //DEV BY MALITH
        } else {
            echo "0";
        }
    }

    function updateSample() {
        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];
        $lpsID = Input::get('lpsid');
        $sNote = Input::get($lpsID . "sp");
        $repRemark = Input::get($lpsID . "rm");

        $newState = "Done";

        //get for stock deduct
        $resultx1 = DB::select("select a.date, a.sampleno, b.age, b.months, b.days, c.gender_idgender from lps a, patient b, user c where c.uid = b.user_uid and a.patient_pid = b.pid and a.lpsid = '" . $lpsID . "'");
        foreach ($resultx1 as $resx1) {
            $sample_Numer = $resx1->sampleno;
            $sample_date = $resx1->date;

            $age = $resx1->age;
            $months = $resx1->months;
            $days = $resx1->days;

            $genderID = $resx1->gender_idgender;
        }
        //~~~

        $result = DB::select("select a.tid,a.name,b.state,b.value from test a,lps_has_test b where a.tid=b.test_tid and b.lps_lpsid='" . $lpsID . "'");
        foreach ($result as $res) {
            $tid = $res->tid;
            $value = Input::get($tid);
            $CurrentState = $res->state;

            //get advance ref on or off
            $ageGenderRef = 0;
            $LHTID = 0;

            $resultadvr = DB::select("select lhtid, advance_ref from Lab_has_test where Lab_lid = '" . $lid . "' and test_tid = '" . $tid . "'");
            foreach ($resultadvr as $ressdvr) {

                $ageGenderRef = $ressdvr->advance_ref;
                $LHTID = $ressdvr->lhtid;

            }

            $refmin = "";
            $refmax = "";

            if($ageGenderRef){


                //~~~~ OLD Method~~~~~
                // //make age to days
                // $ageFromDays = $age * 365;
                // $monthFromDays = $months * 30;
                // $age_days = $ageFromDays + $monthFromDays + $days;


                // $resultAR = DB::select("select min,max from reference_values where Lab_has_test_lhtid = '" . $LHTID . "' and age_range_id in (select id from age_range where min< '" . $age_days . "' and max >= '" . $age_days . "') and gender_idgender = '" . $genderID . "' ");
                // foreach ($resultAR as $resAR) {
                //     $refmin = $resAR->min;
                //     $refmax = $resAR->max;
                // }
                // if ($refMin == "") {
                //     $resultAR = DB::select("select min,max from reference_values where Lab_has_test_lhtid = '" . $LHTID . "' and age_range_id in (select id from age_range where min< '" . $age_days . "' and max >= '" . $age_days . "')");
                //     foreach ($resultAR as $resAR) {
                //         $refmin = $resAR->min;
                //         $refmax = $resAR->max;
                //     }
                // }

                //~~~~ NEW METHOD~~~~~

                $typeOftheAge = "";
                $ageValue = 0;
                if ($days != 0 && $months == 0 && $age == 0) {
                    $typeOftheAge = "days";
                    $ageValue = $days;
                } else if ($months != 0 && $age == 0) {
                    $typeOftheAge = "months";
                    $ageValue = $months;
                } else if ($age != 0) {
                    $typeOftheAge = "years";
                    $ageValue = $age;
                }

                $query = "select rangeMin,rangeMax from reference_values where Lab_has_test_lhtid = '" . $LHTID . "' and ageType = '".$typeOftheAge."' and ageMin <= '".$ageValue."' and ageMax >= '".$ageValue."' and gender_idgender = '" . $genderID . "'";

                $resultAR = DB::select("select rangeMin,rangeMax from reference_values where Lab_has_test_lhtid = '" . $LHTID . "' and ageType = '".$typeOftheAge."' and ageMin <= '".$ageValue."' and ageMax >= '".$ageValue."' and gender_idgender = '" . $genderID . "'");
                foreach ($resultAR as $resAR) {
                    $refmin = $resAR->rangeMin;
                    $refmax = $resAR->rangeMax;
                }

                //~~~~~~~~~~~~~~~~~~~~~~~~~


            }else{

            //get current refference range

                $resultxx = DB::select("select refference_min,refference_max from labtestingdetails where Lab_lid = '" . $lid . "' and test_tid = '" . $tid . "'");
                foreach ($resultxx as $resxx) {
                    $refmin = $resxx->refference_min;
                    $refmax = $resxx->refference_max;
                }
            //

            }

            DB::statement("update lps_has_test set value = '" . $value . "',refmin = '" . $refmin . "',refmax = '" . $refmax . "', state = '" . $newState . "' where test_tid = '" . $tid . "' and lps_lpsid= '" . $lpsID . "'");

            //CHECK FOR PREVILLAGES FOR STOCK CONTROL FEATURE
            $resultp = DB::select("select * from Lab_features where Lab_lid = '" . $_SESSION['lid'] . "' and features_idfeatures=(select idfeatures from features where name = 'Stock Management')");
            foreach ($resultp as $resp) {
                $featureExists = true;
            }
            if (isset($featureExists)) {

                //Get Material Consumption for each test
                $resultm = DB::select("select a.materials_mid,b.qty,b.unit,a.lmid, a.dedtype from Lab_has_materials a, test_Labmeterials b where a.lmid = b.Lab_has_materials_lmid and b.test_tid = '" . $tid . "' and a.Lab_lid = '" . $lid . "' order by dedtype DESC");
                foreach ($resultm as $resm) {
                    $lmid = $resm->lmid;
                    $qty = $resm->qty;

                    //$unit = $resm->unit;
                    //select deduction type and deduct stock
                    $deduct_type = $resm->dedtype;
                    switch ($deduct_type) {
                        case 1:
                            //deduct for test wise materials
                        if ($CurrentState != "Done") {
                            SampleController::deductStock($lmid, $qty, $lpsID);
                            
                        }

                        break;

                        case 2:
                            //deduct for sample
                        $resultx0 = DB::select("select stock_updated from lps where lpsid = '" . $lpsID . "' and stock_updated is null");
                        foreach ($resultx0 as $resx0) {
                            SampleController::deductStock($lmid, $qty, $lpsID);
                            DB::statement("update lps set stock_updated='1' where lpsID = '" . $lpsID . "'");
                        }

                        break;
                        case 3:
                        $new_sample_Numer = $sample_Numer;
                        if (ctype_alpha(substr($sample_Numer, -1))) {
                            $new_sample_Numer = substr($sample_Numer, 0, -1);
                        }

                            //deduct for bill
                        $allPending = true;
                        $resultx0 = DB::select("select stock_updated from lps where date = '" . $sample_date . "' and sampleno like '" . $new_sample_Numer . "' and Lab_lid = '" . $_SESSION['lid'] . "'");
                        foreach ($resultx0 as $resx0) {
                            if ($resx0->stock_updated != null) {
                                $allPending = false;
                            }
                        }

                        if ($allPending) {
                            SampleController::deductStock($lmid, $qty, $lpsID);
                        }

                        break;
                    }
                }
//                }
            }
            //
            //update lps_has_test status
            //.........
        }
        DB::statement("update lps set finishdate='" . date('Y-m-d') . "',finishtime='" . date('H:i') . "',specialnote='" . $sNote . "',report_remark='" . $repRemark . "' where lpsID = '" . $lpsID . "'");

        //get entered user
        $resultm2 = DB::select("select uid from user where uid = (select user_uid from labUser where luid = '" . $_SESSION['luid'] . "')");
        foreach ($resultm2 as $resm) {
            $uid = $resm->uid;
        }
        //

        DB::statement("update lps set status='Done', entered_uid = '" . $uid . "' where lpsid='" . $lpsID . "'");

        //return View::make('WienterResults')->with('msg', 'Result Added!')->with('lpsid', $lpsID);
        echo '1';
    }

    function deductStock($lmid, $qty, $lpsID) {

        //get Stock deduct department ID
        $did = "";
        $multiple_depts = "";
        $Resultd = DB::select("select deduct_stock_dept_id, multiple_departments from configs where Lab_lid = '" . $_SESSION['lid'] . "'");
        foreach ($Resultd as $resd) {                     
            $did = $resd->deduct_stock_dept_id;
            $multiple_depts = $resd->multiple_departments;
        }
                    //

        if($multiple_depts && $did != ""){
            //get oldest stock available from each material from related department
            $results = DB::select("select idstock,qty,usedqty from stock where department_did = '".$did."' and Lab_has_materials_lmid = '" . $lmid . "' and (qty-usedqty) > 0 order by expDate ASC");
        }else{
            //get oldest stock available from each material
            $results = DB::select("select idstock,qty,usedqty from stock where Lab_has_materials_lmid = '" . $lmid . "' and (qty-usedqty) > 0 order by expDate ASC");

        }

        $deducting_qty = $qty;

        foreach ($results as $ress) {
            // deduct stock from orderd materials for most resent exp. 
            if ($ress->qty >= $deducting_qty) {
                //stock update for each test
                if ($ress->usedqty != null) {
                    DB::statement("update stock set usedqty=usedqty+'" . $deducting_qty . "' where idstock='" . $ress->idstock . "'");
                } else {
                    DB::statement("update stock set usedqty='" . $deducting_qty . "' where idstock='" . $ress->idstock . "'");
                }
                SampleController::saveStockLog($ress->idstock, $lpsID, $deducting_qty);
                break;
            } else {
                //stock update for each test
                if ($ress->usedqty != null) {
                    DB::statement("update stock set usedqty=usedqty+'" . $ress->qty . "' where idstock='" . $ress->idstock . "'");
                } else {
                    DB::statement("update stock set usedqty='" . $ress->qty . "' where idstock='" . $ress->idstock . "'");
                }
                $deducting_qty = $deducting_qty - $ress->qty;
                SampleController::saveStockLog($ress->idstock, $lpsID, $deducting_qty);
            }

            
        }
    }

    function resetReport() {

        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];
        $lpsID = Input::get('lpsid');
        $remVals = Input::get('removeVals');

        if($lid == "42" || $lid == "45"){
            DB::statement("update lps set status='Accepted', auth01 = null, auth02 = null, entered_uid = null, auth01_user = null, auth02_user = null where lpsid='" . $lpsID . "'");
        }else{
            DB::statement("update lps set status='pending', auth01 = null, auth02 = null, entered_uid = null, auth01_user = null, auth02_user = null where lpsid='" . $lpsID . "'");
        }

        if($remVals == 1){
            DB::statement("update lps_has_test set value= null, state = 'pending', refmin = '', refmax = '' where lps_lpsid='" . $lpsID . "'");
        }else{
            DB::statement("update lps_has_test set state = 'pending', refmin = '', refmax = '' where lps_lpsid='" . $lpsID . "'");
        }
        

        $log_descreption = "Reset Report : LPS_ID : ".$lpsID;

        SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "Enter Result", "Reset Report", $log_descreption);

        echo 'Report Undone!';  

        // return $lpsID;
    }

    function saveStockLog($idstock, $lpsID, $deducting_qty) {
        return DB::statement("INSERT into lps_has_stock(stock_idstock,lps_lpsid,qty) values('".$idstock."','".$lpsID."','".$deducting_qty."')");
    }

    function updateSampleFromLIS() {
        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];
        $lpsID = Input::get('s');
    }

    function loadPendings() {
        $lid = $_SESSION['lid'];
        $date = date("Y-m-d");
        
        $same_p_reps = "";

        $result0x = DB::select("select d.tgid,d.name,e.sampleno,e.lpsid,e.status from lps_has_test b,Lab_has_test c, Testgroup d, lps e where e.lpsid=b.lps_lpsid and c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and e.date = '".$date."' and e.lab_lid = '" . $lid . "' and e.status != 'Done' group by e.lpsid ASC"); 
        
        foreach ($result0x as $res0x) {

            $color = "#FF9900";
            if ($res0x->status == "Done") {
                $color = "#15BC15";
            }

            $same_p_reps .= "<tr class='preptd' style='cursor:pointer; background-color:" . $color . "; color:white;' id='smlp+" . $res0x->sampleno . "' ondblclick='openRep(id)'><td>" . $res0x->sampleno . "</td>" . "<td>" . $res0x->name . "</td></tr>";
        }
        
        echo $same_p_reps;
    }

    function updateOPClient() {

        $lid = $_REQUEST['lid'];
        $pid = Input::get('pid');
        $lpsid = Input::get('lpsid');

        if (Input::get('client') != null) {
            $fname = Input::get("fname");
            $lname = Input::get("lname");
            $gen = Input::get("gender");
            $years = Input::get("years");
            $months = Input::get("months");
            $dates = Input::get("days");
            $pnno = Input::get("tpno");
            $initials = Input::get("initial");
            $nicno = Input::get("nic");

            $address = Input::get("address");
            $address = preg_replace('/[^A-Za-z0-9 !:,@#$^&*().]/u', ' ', strip_tags($address));

            $sno = Input::get("sampleno");

            //$mname = Input::get("mname");
            //$email = Input::get("email");
            //$hpno = Input::get("hpno");
            //Invoice Details
            $iid = Input::get("iid");
            $date = date('Y-m-d');

            $discount = Input::get('discount');

//            if ($discount != "") {
//                $result = DB::select("select did from Discount where name='" . $discount . "' and lab_lid='" . $lid . "'");
//                foreach ($result as $res) {
//                    $discount = $res->did;
//                }
//            }

            $total = Input::get("tot");
            $gtotal = Input::get("gtot");

            $pMethod = Input::get("paym");
            $payment = Input::get("payment");
            $invoiceDate = $date;
            $paidDate = "";

            $pstatus = "";
            if ($pstatus == "0") {
                $pstatus = "Not Paid";
            } else if ($payment == $gtotal) {
                $pstatus = "Payment Done";
                $paidDate = $invoiceDate;
            } else if ($payment == "0") {
                $pstatus = "Pending Due";
                $paidDate = $invoiceDate;
            } else {
                $pstatus = "Pending Due";
            }

            $PatientControlle = new PatientController();

            $mname = "";
            $email = "";
            $hpno = "";

            if ($gen == "1") {
                $gen = "Male";
            } else {
                $gen = "Female";
            }

            $x = $PatientControlle->updateExsistPatient($pid, $fname, $lname, $years, $months, $dates, $gen, $pnno, $address, $mname, $email, $hpno, $initials, $nicno);
            $y = $PatientControlle->updateInvoiceForLPS($iid, $total, $gtotal, $payment, $pMethod, $pstatus, $discount, $paidDate);

            //update sample Number
//            $sn = DB::statement("update lps set sampleNo = '" . $sno . "' where lpsid='" . $lpsid . "'");
            //update tests             
            $z = DB::statement("delete from lps_has_test where lps_lpsid='" . $lpsid . "'");

            $newPost = $_REQUEST;
            unset($newPost['pid']);
            unset($newPost['lpsid']);
            unset($newPost['client']);
            unset($newPost['fname']);
            unset($newPost['lname']);
            unset($newPost['gender']);
            unset($newPost['years']);
            unset($newPost['months']);
            unset($newPost['dates']);
            unset($newPost['tpno']);
            unset($newPost['address']);
            unset($newPost['refby']);
            unset($newPost['newref']);
            unset($newPost['ptype']);
            unset($newPost['sampleno']);
            unset($newPost['selectedpid']);
            unset($newPost['tot']);
            unset($newPost['discount']);
            unset($newPost['disPre']);
            unset($newPost['dc']);
            unset($newPost['gtot']);
            unset($newPost['paym']);
            unset($newPost['payment']);
            unset($newPost['invoice']);
            unset($newPost['submit']);
            unset($newPost['submited']);
            unset($newPost['lid']);
            unset($newPost['iid']);
            unset($newPost['luid']);

            $testKeys = array_keys($newPost);
            for ($key = 0; $key < count($testKeys); $key++) {
                $test = $testKeys[$key];
                $arr = explode('-', $test);
                $testGID = $arr[1];

//                $PatientControlle->addTestToPatient($lpsid, $testID, 'pending');
                $PatientControlle->addTestGroupToPatient($lpsid, $testGID, 'pending', $lid);
            }

            DB::statement("update lps set sampleNo='" . $sno . "' where lpsid='" . $lpsid . "'");

            echo "ok" . $discount . "#";

//            echo "update lps set sampleNo = '" . $sno . "' where lpsid='" . $lpsid . "'";
//            if ($x != 0 && $y != 0) {
//                echo "Details Updated!";
//            } else {
//                echo "Operation Error!";
//            }
        }
        ////else if (Input::get('submit') !== null && Input::get('submit') == "Remove Patient") {
//
//            $result = DB::select("select user_uid from patient where pid='" . $pid . "'");
//            foreach ($result as $res) {
//                $x = DB::statement("update user set status='0' where uid = '" . $res->user_uid . "'");
//            }
//            echo "User Deleted!";
//        }
    }

    function getTestbyDate() {

        $lid = Input::get('lid');
        $pid = Input::get('pid');
        $date = Input::get('date');
        $date2 = Input::get('date2');

        $test_group = Input::get('testg');


        if ($date == '0') {
            $result = DB::select("select lpsid,sampleNo,date from lps where patient_pid = '" . $pid . "' and Lab_lid ='" . $lid . "'");
        } else {
            $result = DB::select("select lpsid,sampleNo,date from lps where patient_pid = '" . $pid . "' and date between '" . $date . "' and '" . $date2 . "' and Lab_lid ='" . $lid . "'");
        }

        $testTable = "";

        $chart_dates = "";
        $chart_vals = "";

        $chart_refMin = "";
        $chart_refMax = "";
        $chart_mes = "";




        foreach ($result as $res) {

            $lpsID = $res->lpsid;
            $sampleNo = $res->sampleNo;
            $date = $res->date;


            $result20 = DB::select("select d.name as tgname, b.lps_lpsid from test a,lps_has_test b, Lab_has_test c, Testgroup d where c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and c.Lab_lid = '" . $lid . "' and a.tid=b.test_tid and b.lps_lpsid='" . $lpsID . "' and d.tgid like '" . $test_group . "' group by d.tgid");
            foreach ($result20 as $res20) {

                $tgname = $res20->tgname;
                $slpsID = $res20->lps_lpsid;

                $chart_dates .= $date . ",";

                $testTable .= "<tr><td><b><i>Date : " . $date . "</i></b></td><tr><tr><td><b><i>Sample No :</i></b></td><td><input type='text' class='input-text' name='sno' readonly='readonly' value='" . $sampleNo . "' style='width:76px'></td></tr>";

                $testTable .= "<tr><td><b><i>" . $tgname . "</i></b></td><td></td></tr>";


                $result2 = DB::select("select c.measurement,e.refference_min,e.refference_max, a.name,b.value,d.name as tgname from test a,lps_has_test b, Lab_has_test c, Testgroup d, labtestingdetails e where e.lab_lid = c.Lab_lid and e.test_tid = c.test_tid and c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and c.Lab_lid = '" . $lid . "' and a.tid=b.test_tid and b.lps_lpsid='" . $slpsID . "'");
                foreach ($result2 as $res2) {
                    $name = $res2->name;
                    $value = $res2->value;
                    $tgname = $res2->tgname;

                    $chart_vals .= $value . ",";

                    $chart_refMin = $res2->refference_min;
                    $chart_refMax = $res2->refference_max;
                    $chart_mes = $res2->measurement;

                    $testTable .= "<tr><td>&nbsp;&nbsp;" . $name . "</td>" . "<td><input class='input-text' type='text' name='" . $name . "' value='" . $value . "'></td></tr>";
                }

                $testTable .= "<tr><td>&nbsp;</td><td></td></tr>";
            }



            $testTable .= "<tr><td style='border-top: 2px black solid;'><input type='hidden' name='lpsid' value='" . $lpsID . "'></td><td></td></tr>";
        }
        echo $testTable . "##//##" . $chart_dates . "##//##" . $chart_vals . "##//##" . $chart_refMin . "##//##" . $chart_refMax . "##//##" . $chart_mes;
    }

    function updateTestResult() {
        $lpsid = Input::get('lpsid');
        if (Input::get('submit') == 'Update Test Details') {

            $newPost = $_POST;
            unset($newPost['sno']);
            unset($newPost['lpsid']);
            unset($newPost['submit']);

            $newState = "Updated";

            $keys = array_keys($newPost);
            for ($i = 0; $i < count($keys); $i++) {
                $tid = $keys[$i];
                $value = $newPost[$keys[$i]];

                DB::statement("update lps_has_test set value = '" . $value . "', state = '" . $newState . "' where test_tid = '" . $tid . "' and lps_lpsid= '" . $lpsid . "'");
            }

            DB::statement("update lps set sampleNo='" . Input::get('sno') . "' where lpsid='" . $lpsid . "'");

            return View::make('WiViewOP')->with('lpsid', $lpsid)->with('msg', 'Result Updated!');
        } elseif (Input::get('submit') == 'Delete Sample') {
            $result = DB::select("select patient_pid from lps where lpsid='" . $lpsid . "'");
            foreach ($result as $res) {
                $pid = $res->patient_pid;
            }

            $resultx = DB::select("select iid from invoice where lps_lpsid='" . $lpsid . "'");
            foreach ($resultx as $resx) {
                $IID = $resx->iid;
                DB::statement("delete from invoice_payments where invoice_iid='" . $IID . "'");
            }

            DB::statement("delete from invoice where lps_lpsid='" . $lpsid . "'");
            DB::statement("delete from lps_has_test where lps_lpsid='" . $lpsid . "'");

            DB::statement("update lps set date = '1993-01-01' where lpsid='" . $lpsid . "'");

//            DB::statement("delete from lps where lpsid='" . $lpsid . "'");

            $result2 = DB::select("select max(lpsid) as lpsid from lps where patient_pid = '" . $pid . "'");
            foreach ($result2 as $res2) {
                $lpsid = $res2->lpsid;
            }
            return View::make('WiViewOP')->with('lpsid', $lpsid)->with('msg', 'Result Updated!');
        }
    }

    public function ViewOPGET() {

        if (isset($_REQUEST['client'])) {
            $lid = $_REQUEST['lid'];

            $date = Input::get('date');
            $sNo = Input::get('sno');

            $data = "";

            $remark = "";

            $result = DB::select("select f.remark, a.arivaltime as atime,b.initials,b.pid,a.date,a.sampleNo,a.arivaltime as time,a.type,c.fname,c.lname,c.tpno,c.address,a.status,b.age,b.months,b.days,a.lpsid,f.iid,"
                . "c.gender_idgender, a.refference_idref as refby, f.total, f.Discount_did, f.gtotal,f.paymentmethod,f.paid,(f.gtotal-f.paid) as due, b.pid, a.lpsid, a.status, c.nic "
                . "from lps a,patient b, user c,usertype d,gender e,invoice f where f.lps_lpsid=a.lpsid and a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "' and a.date = '" . $date . "' and a.sampleno = '" . $sNo . "'");

            foreach ($result as $res) {

                //add test select code

                $sufix = explode($sNo, $res->sampleNo)[0];

                if (ctype_digit($sufix)) {

                } else {

                    if ($res->refby != "") {
                        $resultref = DB::select("select idref,name from refference where idref = '" . $res->refby . "'");
                        foreach ($resultref as $resref) {
                            $refby = $resref->idref . " : " . $resref->name;
                        }
                    } else {
                        $refby = "";
                    }

                    if ($res->Discount_did != "") {
                        $resultdis = DB::select("select did,name,value from Discount where did = '" . $res->Discount_did . "'");
                        foreach ($resultdis as $resdis) {
                            $discount = $resdis->did . " : " . $resdis->name . " : " . $resdis->value;
                        }
                    } else {
                        $discount = "";
                    }

                    $data .= $res->type . "#,#" . $res->fname . "#,#" . $res->lname . "#,#" . $res->age . "#,#" . $res->months . "#,#" .
                    $res->days . "#,#" . $res->gender_idgender . "#,#" . $refby . "#,#" . $res->tpno . "#,#" . $res->address . "#,#" . $res->total . "#,#" .
                    $res->gtotal . "#,#" . $discount . "#,#" . $res->paymentmethod . "#,#" . $res->paid . "#,#" . $res->due . "#,#" . $res->pid . "#,#" . $res->lpsid . "#,#" . $res->iid . "#,#" . $res->time;

                    $lpsID = $res->lpsid;
                    $initialsx = $res->initials;

                    $status = $res->status;
                    $aTime = $res->atime;

                    $aDate = $res->date;

                    $nic = $res->nic;
                    $remark = $res->remark;
                }
            }

            $tests = "";

            if (isset($lpsID)) {

                $sNo_for_dev = substr($sNo, 0, 2);

                if($lid == 26){
                // if($lid == 43){

                    if (ctype_digit($sNo_for_dev)) {
                        $result2 = DB::select("select d.testCode, d.tgid,d.name,d.price,d.testingtime,a.status,a.urgent_sample,a.lpsid,d.parameter_wise_barcode from lps a, Testgroup d where a.Testgroup_tgid = d.tgid and a.date = '" . $date . "' and a.patient_pid = '" . $res->pid . "' and a.arivaltime = '" . $aTime . "' and d.Lab_lid = '" . $lid . "' group by d.tgid");


                    } else { 
                        $result2 = DB::select("select d.testCode, d.tgid,d.name,d.price,d.testingtime,a.status,a.urgent_sample,a.lpsid,d.parameter_wise_barcode from lps a, Testgroup d,labbranches_has_Testgroup e, labbranches f where a.Testgroup_tgid = d.tgid and d.tgid = e.tgid and f.bid = e.bid and a.date = '" . $date . "' and a.patient_pid = '" . $res->pid . "' and a.arivaltime = '" . $aTime . "' and d.Lab_lid = '" . $lid . "' and f.bid = (select bid from labbranches where code = '".$sNo_for_dev."' and lab_lid = '" . $lid . "') group by d.tgid");


                    }


                }else if($lid == 43 | $lid == 45){
                    if (ctype_digit($sNo_for_dev)) {
                        $result2 = DB::select("select d.testCode, d.tgid,d.name,a.price,d.testingtime,a.status,a.urgent_sample,a.lpsid,d.parameter_wise_barcode from lps a, Testgroup d where a.Testgroup_tgid = d.tgid and a.date = '" . $date . "' and a.patient_pid = '" . $res->pid . "' and a.arivaltime = '" . $aTime . "' and d.Lab_lid = '" . $lid . "' group by d.tgid");


                    } else { 
                        $result2 = DB::select("select d.testCode, d.tgid,d.name,a.price,d.testingtime,a.status,a.urgent_sample,a.lpsid,d.parameter_wise_barcode from lps a, Testgroup d,labbranches_has_Testgroup e, labbranches f where a.Testgroup_tgid = d.tgid and d.tgid = e.tgid and f.bid = e.bid and a.date = '" . $date . "' and a.patient_pid = '" . $res->pid . "' and a.arivaltime = '" . $aTime . "' and d.Lab_lid = '" . $lid . "' and f.bid = (select bid from labbranches where code = '".$sNo_for_dev."' and lab_lid = '" . $lid . "') group by d.tgid");


                    }
                }else{

                    if (ctype_digit($sNo_for_dev)) {
                        $result2 = DB::select("select d.testCode, d.tgid,d.name,d.price,d.testingtime,a.status,a.urgent_sample,a.lpsid,d.parameter_wise_barcode from lps a, lps_has_test b,Lab_has_test c, Testgroup d where a.lpsid = b.lps_lpsid and c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and a.date = '" . $date . "' and a.patient_pid = '" . $res->pid . "' and a.arivaltime = '" . $aTime . "' and d.Lab_lid = '" . $lid . "' group by d.tgid");


                    } else { 
                        $result2 = DB::select("select d.testCode, d.tgid,d.name,e.price,d.testingtime,a.status,a.urgent_sample,a.lpsid,d.parameter_wise_barcode from lps a, lps_has_test b,Lab_has_test c, Testgroup d,labbranches_has_Testgroup e, labbranches f where d.tgid = e.tgid and f.bid = e.bid and a.lpsid = b.lps_lpsid and c.Testgroup_tgid = d.tgid and c.test_tid = b.test_tid and a.date = '" . $date . "' and a.patient_pid = '" . $res->pid . "' and a.arivaltime = '" . $aTime . "' and d.Lab_lid = '" . $lid . "' and f.bid = (select bid from labbranches where code = '".$sNo_for_dev."' and lab_lid = '" . $lid . "') group by d.tgid");


                    }


                }

                
                

                foreach ($result2 as $res2) {
                    if ($tests != "") {
                        $tests .= "#//#";
                    }

                    $time = "Depends";

                    if($lid == "43"){
                        $tests .= $res2->tgid . "###" . $res2->testCode ." ". $res2->name . "###" . $res2->price . "###" . $time;
                    }else{
                        $tests .= $res2->tgid . "###" . $res2->name . "###" . $res2->price . "###" . $time . "###" . $res2->status . "###" . $res2->urgent_sample ."###". $res2->lpsid . "###" . $res2->parameter_wise_barcode ;
                    }
                }

                $data .= "#,#" . $tests . "#,#" . $initialsx . "#,#" . $status . "#,#" . $aDate. "#,#" . $nic. "#,#" . $remark;
            } else {
                $data = "";
            }

            if ($data == "") {
                echo "No Data";
            } else {
                echo $data;
            }
        }
    }

    public function reportAuthentication() {
        $lid = $_SESSION['lid'];

        $auth = Input::get('auth');
        $lps = Input::get('id');

        if($auth == "1"){
            DB::statement("update lps set auth01 = '1', auth01_user = '".$_SESSION['luid']."' where lpsid='" . $lps . "'");
            echo "Report Confirmed! ".$lps."#";

        }else{
            DB::statement("update lps set auth02 = '1', auth02_user = '".$_SESSION['luid']."' where lpsid='" . $lps . "'");
            echo "Report Verified!";

        }

    }

    public function checkPaymentByLPS() {
        
        $date = Input::get('date');
        
        $sampleno = Input::get('sampleno');

        $lastChar = substr($sampleno, -1); 
        if (!is_numeric($lastChar)) { 
            $sampleno = substr($sampleno, 0, -1); 
        }

        $out = "";

        $result = DB::select("select SUM(a.amount) as paid, b.gtotal from invoice_payments a, invoice b where b.iid = a.invoice_iid and b.lps_lpsid = (select lpsid from lps where date = '".$date."' and sampleno = '".$sampleno."' and lab_lid = '".$_SESSION['lid']."')");
        foreach ($result as $res) {
            $total = $res->gtotal;
            $paid_total = $res->paid;

            if($paid_total >= $total){

                $out = "pass";

            }else{

                $out = "fail";

            }

        }

        echo $out;

    }

    public function enterBloodDrew() {
        $lid = $_SESSION['lid'];

        $date = Input::get('date');
        $sNo = Input::get('sno');

        DB::statement("update lps set blooddraw = '" . date("Y-m-d H:i:s") . "' where date='" . $date . "' and sampleNo='" . $sNo . "' and Lab_lid = '" . $lid . "'");

        echo "Marked as Blood Drew!";
    }

    public function acceptToLab() {
        $lid = $_SESSION['lid'];

        $date = Input::get('date');
        $sNo = Input::get('sno');

        $result = DB::select("SELECT fastingtime from lps where date='" . $date . "' and sampleNo='" . $sNo . "' and Lab_lid = '" . $lid . "' ");
        foreach ($result as $res) {

            if($res->fastingtime == null){

                if($lid == '45'){

                    DB::statement("update lps set fastingtime = '" . date("Y-m-d H:i:s") . "', labaccepted_user = '".$_SESSION['luid']."' where date='" . $date . "' and 
                        CASE
                        WHEN SUBSTRING(sampleno, LENGTH(sampleno), 1) NOT REGEXP '[0-9]' THEN SUBSTRING(sampleno, 1, LENGTH(sampleno) - 1)
                        ELSE sampleno
                        END = '" . $sNo . "'
                         and Lab_lid = '" . $lid . "' and status = 'Accepted'");

                }else{
                    DB::statement("update lps set fastingtime = '" . date("Y-m-d H:i:s") . "' where date='" . $date . "' and sampleNo='" . $sNo . "' and Lab_lid = '" . $lid . "'");
                }

                echo "Marked as Laboratory Accepted!";
            }else{

                echo "Already Accepted from Lab!";

            }

        }
        

        // echo "Marked as Laboratory Accepted!";
    }

    public function enterReportCollected() {
        $lid = $_SESSION['lid'];

        $date = Input::get('date');
        $sNo = Input::get('sno');

        DB::statement("update lps set repcollected = '" . date("Y-m-d H:i:s") . "' where date='" . $date . "' and sampleNo='" . $sNo . "' and Lab_lid = '" . $lid . "'");

        echo "Marked as Collected!";
    }

    public function acceptSample() {
        $lid = $_SESSION['lid'];

        $date = Input::get('date');
        $sNo = Input::get('sno');

        $result = DB::select("SELECT status from lps where date='" . $date . "' and sampleNo='" . $sNo . "' and Lab_lid = '" . $lid . "' ");
        foreach ($result as $res) {

            if($res->status == "pending"){

                DB::statement("update lps set status = 'Accepted', accept_time='".date("H:i:s")."' where date='" . $date . "' and sampleNo='" . $sNo . "' and Lab_lid = '" . $lid . "'");
                echo "Marked as Accepted!";

            }else{

                echo "Sample Already Accepted from the Lab!";

            }

        }

        

        
    } 

    public function checkAllLSPReady() {

    $out = "true";

    $lid = $_SESSION['lid'];

    $lpsid = Input::get('lpsid');

    if($lid == 45 || $lid == 31){

        $result = DB::select("SELECT auth02 from lps where date = (select date from lps where lpsid = '".$lpsid."') and patient_pid = (select patient_pid from lps where lpsid = '".$lpsid."') and Lab_lid = '".$lid."'");
        foreach ($result as $res) {
            $status = $res->auth02;

            if($status != "1"){

                $out = "false";

            }

        }

    }else{

        $result = DB::select("SELECT status from lps where date = (select date from lps where lpsid = '".$lpsid."') and patient_pid = (select patient_pid from lps where lpsid = '".$lpsid."') and Lab_lid = '".$lid."'");
        foreach ($result as $res) {
            $status = $res->status;

            if($status != "Done"){

                $out = "false";

            }

        }


    }



    return $out;
    
    } 


public function replaceTest() {
    $lid = $_SESSION['lid'];

    $date = Input::get('date');
    $sNo = Input::get('sno');
    $tgid = Input::get('tgid');

    DB::statement("delete from lps_has_test where lps_lpsid = (select lpsid from lps where date='" . $date . "' and sampleNo='" . $sNo . "' and Lab_lid = '" . $lid . "')");

    DB::statement("update lps set Testgroup_tgid = '".$tgid."' where date='" . $date . "' and sampleNo='" . $sNo . "' and Lab_lid = '" . $lid . "'");

    $result = DB::select("select test_tid from Lab_has_test where Lab_lid = '" .$lid. "' and Testgroup_tgid = '" .$tgid. "'");
    foreach ($result as $res) {
        $test_tid = $res->test_tid;

        DB::statement("insert into lps_has_test(lps_lpsid, test_tid, state, lisloaded) values ((select lpsid from lps where date='" . $date . "' and sampleNo='" . $sNo . "' and Lab_lid = '" . $lid . "'),'" . $test_tid. "','pending','0')");
    }


    echo "Test Changed!"; 
}

public function ChangeLPSData() {
    $lid = $_SESSION['lid'];

    $date = Input::get('date');
    $sNo = Input::get('sno');
    
    $nsno = Input::get('nsno');
    $ndate = Input::get('ndate');

    DB::statement("update lps set date = '".$ndate."', sampleNo = '".$nsno."' where date='" . $date . "' and sampleNo='" . $sNo . "' and Lab_lid = '" . $lid . "'");

   
    echo "Data Changed!"; 
}


}

?>
