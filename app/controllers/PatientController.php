<?php

if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class PatientController extends Controller {

    public function registerPatient() {
        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];
        $cuSymble = $_SESSION['cuSymble'];
        $date = date('Y-m-d');
        $time = date('H:i:s');

        $fastingHours = "0";
        $refinv = "";
        $dob = "";

        $sampleNo = Input::get('sampleno');
        $status = 'pending';
        $refBy = Input::get('refby');
        if ($refBy == "") {
            $newRefBy = Input::get('newref');
            if ($newRefBy == "") {
                $refBy = false;
            } else {
                $result = DB::table('refference')->where('lid', $lid)->where('name', $newRefBy)->first();
                if (!empty($result)) {
                    $refBy = $result->idref;
                } else {
                    $refBy = DB::table('refference')->insertGetId(['lid' => $lid, 'name' => $newRefBy]);
                }
            }
        }

        //if not select value is 0
        $discountID = Input::get('discount');

        //invoic Details
        $tot = Input::get('tot');
        $gtot = Input::get('gtot');
        $paymeth = Input::get('paym');
        $paid = Input::get('payment');

        if ($paid == "0") {
            $paymentState = "Not Paid";
        } else if ($paid == $gtot) {
            $paymentState = "Payment Done";
            $paidDate = $date;
        } else if ($paid < $gtot) {
            $paymentState = "Pending Due";
            $paidDate = $date;
        } else {
            $paymentState = "Pending Due";
        }

        $ptype = Input::get('ptype');

        $selectedPID = Input::get('selectedpid');
        if ($selectedPID == "") {
            $fname = Input::get('fname');
            $lname = Input::get('lname');
            $gender = Input::get('gender');
            $years = Input::get('years');
            $months = Input::get('months');
            $dates = Input::get('dates');

            if (Input::get('pnno') == null) {
                $pnno = "";
            } else {
                $pnno = Input::get('pnno');
            }

            if (Input::get('address') == null) {
                $address = "";
            } else {
                $address = Input::get('address');
            }

            $initials = false;
            if (isset($_POST['initial'])) {
                $initials = $_POST['initial'];
            }

            $nic = "";


            $exsistPID = 0;
            //$result = DB::select("select b.pid from user a, patient b where a.uid = b.user_uid and a.fname = '" . $fname . "' and a.lname = '" . $lname . "' and a.tpno = '" . $pnno . "' and a.address = '" . $address . "'");

            $result = DB::select("select b.pid from user a, patient b where a.uid = b.user_uid and a.fname = '" . $fname . "' and a.lname = '" . $lname . "' and a.tpno = '" . $pnno . "' and a.address = '" . $address . "' and b.age = '" . $years . "' and b.months = '" . $months . "' and b.days = '" . $dates . "'");

            foreach ($result as $res) {
                $exsistPID = $res->pid;
            }

            if ($exsistPID == 0) {
                $userType = '2';
                //insert user
                $UID = $this->SaveUser($fname, null, $lname, $pnno, null, $address, null, $gender, $userType, '1', null, $nic);

                //insertPatient
                $PID = $this->SavePatient($UID, $years, $months, $dates, $initials, $dob);

                //insert patient into lps
                $lpsID = $this->insertPatientIntoLPS($PID, $lid, $date, $sampleNo, $time, $ptype, $refBy, $fastingHours, $refinv);

                //echo "Patient Added " . $lpsID;
            } else {

                //update patient details
                DB::statement("update user set nic = '" . $nic . "', address = '" . $address . "' where uid = (select user_uid from patient where pid = '" . $exsistPID . "')");

                //insert into lps
                $lpsID = $this->insertPatientIntoLPS($exsistPID, $lid, $date, $sampleNo, $time, $ptype, $refBy, $fastingHours, $refinv);
                //echo "Patient Added to exsist pid" . $lpsID;
            }
        } else {
            //update patient age
            $years = Input::get('years');
            $months = Input::get('months');
            $dates = Input::get('dates');
            $initials = "";
            $this->updateSelectedPatietAge($selectedPID, $years, $months, $dates, $initials);

            //insert if user selected a pid
            $lpsID = $this->insertPatientIntoLPS($selectedPID, $lid, $date, $sampleNo, $time, $ptype, $refBy, $fastingHours, $refinv);
            //echo "Patient Added to selected pid" . $lpsID;
        }

        //add Test to patient     
        $newPost = $_POST;
        unset($newPost['fname']);
        unset($newPost['lname']);
        unset($newPost['gender']);
        unset($newPost['years']);
        unset($newPost['months']);
        unset($newPost['dates']);
        unset($newPost['pnno']);
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
        unset($newPost['initial']);

        $testKeys = array_keys($newPost);
        for ($key = 0; $key < count($testKeys); $key++) {
            $test = $testKeys[$key];
            $arr = explode('-', $test);
            $testID = $arr[1];

            $this->addTestToPatient($lpsID, $testID, 'pending');
        }

        //get User Name
        $UID = Labuser::where('luid', '=', $_SESSION['luid'])->first()->user_uid;
        $User = User::find($UID);
        $UserName = $User->fname . " " . $User->lname;

        //Add invoice details 
        $IID = $this->addInvoice($lpsID, $tot, $discountID, $gtot, $paid, $date, $paymentState, $paymeth, $UserName, "0");


        //PrintReport
        $invoice = Input::get('invoice');
        if ($invoice == "on") {
            return View::make('Reports.Invoice')->with('IID', $IID)->with('lpsID', $lpsID);
            //echo $IID.",".$lpsID;
        } else {
            echo "1#@@#" . $sampleNo;
        }
    }

//Functions~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    function SaveUser($fname, $mname, $lname, $mobNo, $hmno, $address, $email, $gender, $utype, $status, $LGID, $nic) {
        $User = new User;
        $User->fname = $fname;
        $User->mname = $mname;
        $User->lname = $lname;
        $User->tpno = $mobNo;
        $User->hpno = $hmno;
        $User->address = $address;
        $User->email = $email;
        $User->gender_idgender = $gender;
        $User->usertype_idusertype = $utype;
        $User->status = $status;
        $User->nic = $nic;
        $User->loginDetails_idloginDetails = $LGID;
        $User->save();
        return $User->uid;
    }

    function SavePatient($uid, $years, $months, $days, $initials, $dob) {
        if ($initials == false) {
            if ($dob == "") {
                $id = DB::table('patient')->insertGetId(['user_uid' => $uid, 'age' => $years, 'months' => $months, 'days' => $days]);
            } else {
                $id = DB::table('patient')->insertGetId(['user_uid' => $uid, 'age' => $years, 'months' => $months, 'days' => $days, 'dob' => $dob]);
            }
        } else {
            if ($dob == "") {
                $id = DB::table('patient')->insertGetId(['user_uid' => $uid, 'age' => $years, 'months' => $months, 'days' => $days, 'initials' => $initials]);
            } else {
                $id = DB::table('patient')->insertGetId(['user_uid' => $uid, 'age' => $years, 'months' => $months, 'days' => $days, 'initials' => $initials, 'dob' => $dob]);
            }
        }
        return $id;
    }

    function insertPatientIntoLPS($pid, $lid, $date, $sampleNo, $arrivalTime, $type, $refby, $FastingHours, $refinv) {
        $lpsStatus = "pending";

        if ($refby == false) {
            $id = DB::table('lps')->insertGetId(['patient_pid' => $pid, 'Lab_lid' => $lid, 'date' => $date, 'sampleNo' => $sampleNo, 'arivaltime' => $arrivalTime, 'status' => $lpsStatus, 'type' => $type, 'fastinghours' => $FastingHours, 'reference_in_invoice' => $refinv]);
        } else {
            $id = DB::table('lps')->insertGetId(['patient_pid' => $pid, 'Lab_lid' => $lid, 'date' => $date, 'sampleNo' => $sampleNo, 'arivaltime' => $arrivalTime, 'status' => $lpsStatus, 'type' => $type, 'refference_idref' => $refby, 'fastinghours' => $FastingHours, 'reference_in_invoice' => $refinv]);
        }

        return $id;
    }
    
    function insertPatientIntoLPSWithTGID($pid, $lid, $date, $sampleNo, $arrivalTime, $type, $refby, $FastingHours, $refinv, $testGID){
        $lpsStatus = "pending";
        $testPrice = "0";
        $testCost = "0";
        
        if(is_numeric(substr($sampleNo, 0, 2))){
            $result11 = DB::select("select price,cost from Testgroup where tgid = '".$testGID."' and Lab_lid = '".$lid."'");
            foreach ($result11 as $res) {
                $testPrice = $res->price;
                $testCost = $res->cost;
            }
        }else{
            $result10 = DB::select("select a.price as price, a.cost as cost FROM labbranches_has_Testgroup a, labbranches b where b.bid = a.bid and a.tgid = '".$testGID."' and b.code = '".substr($sampleNo, 0, 2)."' and b.Lab_lid = '".$lid."'");
            foreach ($result10 as $res) {
                $testPrice = $res->price;
                $testCost = $res->cost;
            }
        }
        
        
        if ($refby == false) {
            $id = DB::table('lps')->insertGetId(['patient_pid' => $pid, 'Lab_lid' => $lid, 'date' => $date, 'sampleNo' => $sampleNo, 'arivaltime' => $arrivalTime, 'status' => $lpsStatus, 'type' => $type, 'fastinghours' => $FastingHours, 'reference_in_invoice' => $refinv, 'Testgroup_tgid' => $testGID, 'price' => $testPrice, 'cost' => $testCost]);
        } else {
            $id = DB::table('lps')->insertGetId(['patient_pid' => $pid, 'Lab_lid' => $lid, 'date' => $date, 'sampleNo' => $sampleNo, 'arivaltime' => $arrivalTime, 'status' => $lpsStatus, 'type' => $type, 'refference_idref' => $refby, 'fastinghours' => $FastingHours, 'reference_in_invoice' => $refinv, 'Testgroup_tgid' => $testGID, 'price' => $testPrice, 'cost' => $testCost]);
        }

        return $id;
    }

    function addTestToPatient($lpsID, $testID, $status) {
        $id = DB::table('lps_has_test')->insertGetId(['lps_lpsid' => $lpsID, 'test_tid' => $testID, 'state' => $status, 'lisloaded' => '0']);
        return $id;
    }

    function addTestGroupToPatient($lpsID, $testGID, $status, $lid) {

        echo $lid;

        $result = DB::select("select test_tid from Lab_has_test where Testgroup_tgid = '" . $testGID . "' and Lab_lid = '" . $lid . "'");
        foreach ($result as $res) {

            $TestID = $res->test_tid;
            $this->addTestToPatient($lpsID, $TestID, $status);
        }
    }

    public function addInvoice($lpsID, $total, $discountID, $gtotal, $payment, $paidDate, $pStatus, $payMethod, $user, $BillCost) {
        if ($paidDate != "") {
            if ($discountID == 0) {
                $id = DB::table('invoice')->insertGetId(['lps_lpsid' => $lpsID, 'date' => $paidDate, 'total' => $total, 'gtotal' => $gtotal, 'paid' => $payment, 'status' => $pStatus, 'paymentmethod' => $payMethod, 'cashier' => $user, 'cost' => $BillCost]);
            } else {
                $id = DB::table('invoice')->insertGetId(['lps_lpsid' => $lpsID, 'date' => $paidDate, 'total' => $total, 'discount_did' => $discountID, 'gtotal' => $gtotal, 'paid' => $payment, 'status' => $pStatus, 'paymentmethod' => $payMethod, 'cashier' => $user, 'cost' => $BillCost]);
            }
        } else {
            if ($discountID == 0) {
                $id = DB::table('invoice')->insertGetId(['lps_lpsid' => $lpsID, 'total' => $total, 'gtotal' => $gtotal, 'paid' => $payment, 'status' => $pStatus, 'paymentmethod' => $payMethod, 'cashier' => $user, 'cost' => $BillCost]);
            } else {
                $id = DB::table('invoice')->insertGetId(['lps_lpsid' => $lpsID, 'total' => $total, 'discount_did' => $discountID, 'gtotal' => $gtotal, 'paid' => $payment, 'status' => $pStatus, 'paymentmethod' => $payMethod, 'cashier' => $user, 'cost' => $BillCost]);
            }
        }
        return $id;
    }

    public function addInvoicePayment($iid, $date, $amount, $paymethod, $uid) {
        $paymethod = "1";
        $id = DB::table('invoice_payments')->insertGetId(['invoice_iid' => $iid, 'date' => $date, 'amount' => $amount, 'user_uid' => $uid, 'paymethod' => $paymethod]);

        return $id;
    }

    function loadSuggestions() {
        $fname = Input::get('fname');
        $lname = Input::get('lname');
        $address = Input::get('address');

        $result = DB::select("select * from user a, patient b, lps c where b.pid = c.patient_pid and a.uid = b.user_uid and a.fname like '%" . $fname . "%' and a.lname like '%" . $lname . "%' and a.address like '%" . $address . "%' and c.Lab_lid = '" . $_SESSION['lid'] . "'");
        return json_encode($result);
    }

    function SearchPatientView() {
        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];

        if (Input::get('status') !== null && Input::get('status') == "all") {

        } else {
            $date = Input::get('date');
            $date2 = Input::get('datex');

            $sNo = Input::get('sno');
            $fname = Input::get('fname');
            $lname = Input::get('lname');
            $type = Input::get('type');
            $refby = Input::get('refby');
            $testgroup = Input::get('testgroup');

            $dept = Input::get('dept');
            $speci = Input::get('speci');

            $teststate = Input::get('teststate');

            $many_filters = Input::get('more');


            //get Branch Code for filtering
            $branchcode = Input::get('branchcode');
            if ($branchcode == "ALL") {
                $branchcode = "";
            }
            //not used. get it from here if want ($branchcode).

            $testStatus = "and a.status like '" . $teststate . "' ";

            if ($teststate == "Billed Only") {
                $testStatus = "and a.blooddraw IS NULL and a.repcollected IS NULL ";
            }

            if ($teststate == "Not Collected") {
                $testStatus = "and a.status like 'Done' and a.repcollected IS NULL ";
            }

            if($teststate == "labview"){
                $testStatus = "and (a.status like 'Done' OR a.status like 'Accepted') ";
            }

            if($teststate == "LabAccepted"){
                $testStatus = " and not a.fastingtime is null and a.auth02 is null and a.repcollected is null ";
            }

            if($teststate == "Verified"){
                $testStatus = " and not a.auth02 is null and a.repcollected is null ";  
            }

            if($teststate == "Printed"){
                $testStatus = " and not a.repcollected is null ";
            }

            $status = "";
            if (Input::get('status') != null) {
                $status = Input::get('status');
            }

            $additional_filter = "";

            

            if ($date == "") {
                $date = "%";
            }
            if ($sNo == "") {
                $sNo = "%";
            }
            if ($fname == "") {
                $fname = "%";
            } 
            if ($lname == "") {
                $lname = "%";
            }
            if ($type == "") {
                $type = "%";
            }


            if ($many_filters == "on") {


                if ($status != "") {
                    if ($refby == "0") {
                        $refby = "%";
                        $result = DB::select("select j.name as testname,a.date,a.sampleNo,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref,a.status,b.initials,c.tpno,a.lab_lid,a.price as tgprice, a.repcollected, a.auth02, fastingtime, a.urgent_sample  
                            from lps a,patient b, user c,usertype d,gender e,lps_has_test f,Lab_has_test i,Testgroup j
                            where j.tgid = i.Testgroup_tgid and i.Lab_lid = a.lab_lid and a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "'
                            and a.date between '" . $date . "' and '" . $date2 . "' and a.sampleNo like '" . $sNo . "%' and (c.fname like '" . $fname . "%' or c.lname like '" . $fname . "%') and a.status like '" . $teststate . "'
                            and c.tpno like '" . $lname . "%' and a.status='" . $status . "' and a.type like '" . $type . "' and d.type='Patient'
                            and (a.refference_idref like '" . $refby . "' or a.refference_idref) is null and i.testingcategory_tcid like '".$dept."' and i.testinginput_tiid like '".$speci."' 
                            and i.Testgroup_tgid like '" . $testgroup . "' ".$additional_filter." 
                            group by a.lpsid
                            order by a.lpsid DESC");
                    } else {
//                    $result = DB::select("select a.date,a.sampleNo,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref from lps a,patient b, user c,usertype d,gender e  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "' and a.date like '" . $date . "' and a.sampleNo like '" . $sNo . "' and c.fname like '" . $fname . "%' and c.lname like '" . $lname . "%' and a.status='" . $status . "' and a.type like '" . $type . "' and d.type='Patient' and a.refference_idref = '" . $refby . "' order by a.lpsid DESC");
                        $result = DB::select("select j.name as testname,a.date,a.sampleNo,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref,a.status,b.initials,c.tpno,a.lab_lid,a.price as tgprice, a.repcollected , a.auth02, fastingtime , a.urgent_sample  
                            from lps a,patient b, user c,usertype d,gender e,lps_has_test f,Lab_has_test i,Testgroup j
                            where j.tgid = i.Testgroup_tgid and i.Lab_lid = a.lab_lid and a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "'
                            and a.date between '" . $date . "' and '" . $date2 . "' and a.sampleNo like '" . $sNo . "%' and (c.fname like '" . $fname . "%' or c.lname like '" . $fname . "%') and a.status like '" . $teststate . "'
                            and c.tpno like '" . $lname . "%' and a.status='" . $status . "' and a.type like '" . $type . "' and d.type='Patient'
                            and a.refference_idref = '" . $refby . "' and i.testingcategory_tcid like '".$dept."' and i.testinginput_tiid like '".$speci."' 
                            and i.Testgroup_tgid like '" . $testgroup . "' ".$additional_filter." 
                            group by a.lpsid
                            order by a.lpsid DESC");
                    }
                    return json_encode($result);
                } else {
                    if ($refby == "0") {
                        $refby = "%";
                        $result = DB::select("select j.name as testname,a.date,a.sampleNo,a.type,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref,a.status,b.initials,c.tpno,a.lab_lid,a.price as tgprice, a.repcollected , a.auth02, fastingtime , a.urgent_sample  
                            from lps a,patient b, user c,usertype d,gender e,lps_has_test f,Lab_has_test i,Testgroup j
                            where j.tgid = i.Testgroup_tgid and f.lps_lpsid = a.lpsid and i.Lab_lid = a.lab_lid
                            and i.test_tid = f.test_tid 
                            and a.patient_pid = b.pid and b.user_uid = c.uid
                            and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "'
                            and a.date between '" . $date . "' and '" . $date2 . "' and a.sampleNo like '" . $sNo . "%' and (c.fname like '" . $fname . "%' or c.lname like '" . $fname . "%') " . $testStatus . "and c.tpno like '" . $lname . "%' and a.type like '" . $type . "' and d.type='Patient'
                            and (a.refference_idref like '" . $refby . "' or a.refference_idref is null)
                            and i.Testgroup_tgid like '" . $testgroup . "' and i.testingcategory_tcid like '".$dept."' and i.testinginput_tiid like '".$speci."' ".$additional_filter." 
                            group by a.lpsid
                            order by a.lpsid DESC");
                    } else {
                        $result = DB::select("select j.name as testname,a.date,a.sampleNo,a.type,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref,a.status,b.initials,c.tpno,a.lab_lid,a.price as tgprice, a.repcollected , a.auth02, fastingtime , a.urgent_sample  
                            from lps a,patient b, user c,usertype d,gender e,lps_has_test f,Lab_has_test i,Testgroup j
                            where j.tgid = i.Testgroup_tgid and f.lps_lpsid = a.lpsid and i.Lab_lid = a.lab_lid
                            and i.test_tid = f.test_tid 
                            and a.patient_pid = b.pid and b.user_uid = c.uid
                            and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "' ".$additional_filter."
                            and a.date between '" . $date . "' and '" . $date2 . "' and a.sampleNo like '" . $sNo . "%' and (c.fname like '" . $fname . "%' or c.lname like '" . $fname . "%') and a.status like '" . $teststate . "'
                            and c.tpno like '" . $lname . "%' and a.type like '" . $type . "' and d.type='Patient'
                            and a.refference_idref = '" . $refby . "' and i.testingcategory_tcid like '".$dept."' and i.testinginput_tiid like '".$speci."' 
                            and i.Testgroup_tgid like '" . $testgroup . "'
                            group by a.lpsid 
                            order by a.lpsid DESC");
                    }
                    return json_encode($result);
                }
            } else {

                $result = DB::select("select j.name as testname,a.date,a.sampleNo,a.type,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref,a.status,b.initials,c.tpno,a.lab_lid,a.price as tgprice, a.repcollected , a.auth02, fastingtime , a.urgent_sample  
                    from lps a,patient b, user c,usertype d,gender e,lps_has_test f,Lab_has_test i,Testgroup j
                    where j.tgid = i.Testgroup_tgid and f.lps_lpsid = a.lpsid and i.Lab_lid = a.lab_lid
                    and i.test_tid = f.test_tid 
                    and a.patient_pid = b.pid and b.user_uid = c.uid 
                    and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "' ".$additional_filter."
                    and a.date between '" . $date . "' and '" . $date2 . "' and a.sampleNo like '" . $sNo . "%' and d.type='Patient' and i.testingcategory_tcid like '".$dept."' and i.testinginput_tiid like '".$speci."' group by a.lpsid order by a.lpsid DESC");

                return json_encode($result);
            }
        }
    }

    function SearchSampleDashboard() { 
        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];

        if (Input::get('status') !== null && Input::get('status') == "all") {

        } else {
            $date = Input::get('date');
            $date2 = Input::get('datex');

            $sNo = Input::get('sno');
            $fname = Input::get('fname');
            $lname = Input::get('lname');
            $type = Input::get('type');
            $refby = Input::get('refby');
            $testgroup = Input::get('testgroup');

            $teststate = Input::get('teststate');

            $many_filters = Input::get('more');


            //get Branch Code for filtering
            $branchcode = Input::get('branchcode');
            if ($branchcode == "ALL") {
                $branchcode = "";
            }
            //not used. get it from here if want ($branchcode).

            $testStatus = "and (a.status='pending' OR a.status='Accepted')";

            // if ($teststate == "Billed Only") {
            //     $testStatus = "and a.blooddraw IS NULL and a.repcollected IS NULL ";
            // }

            // if ($teststate == "Not Collected") {
            //     $testStatus = "and a.status like 'Done' and a.repcollected IS NULL ";
            // }

            // if($teststate == "labview"){
            //     $testStatus = "and (a.status like 'Done' OR a.status like 'Accepted') ";
            // }

            $status = "";
            if (Input::get('status') != null) {
                $status = Input::get('status');
            }

            if ($date == "") {
                $date = "%";
            }
            if ($sNo == "") {
                $sNo = "%";
            }
            if ($fname == "") {
                $fname = "%";
            }
            if ($lname == "") {
                $lname = "%";
            }
            if ($type == "") {
                $type = "%";
            }

            //get exclude sample list
            $exclude = Input::get('exclude');
            $exclude_arr = explode(",",$exclude);
            
            $exclude_query = "";
            for($i=0; $i<count($exclude_arr); ++$i){
                $exclude_query .= " and not sampleno like '".$exclude_arr[$i]."%' ";
            }

            if($exclude_query != ""){
                $exclude_query = substr($exclude_query, 4);
            }

            if ($many_filters == "on") {


                if ($status != "") {
                    if ($refby == "0") {
                        $refby = "%";
                        $result = DB::select("select arivaltime,accept_time, j.name as testname,a.date,a.sampleNo,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref,a.status,b.initials,c.tpno,a.lab_lid,j.price as tgprice, a.repcollected 
                            from lps a,patient b, user c,usertype d,gender e,lps_has_test f,Lab_has_test i,Testgroup j
                            where j.tgid = i.Testgroup_tgid and i.Lab_lid = a.lab_lid and a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "'
                            and a.date between '" . $date . "' and '" . $date2 . "' and a.sampleNo like '" . $sNo . "%' and (c.fname like '" . $fname . "%' or c.lname like '" . $fname . "%') and a.status like '" . $teststate . "'
                            and c.tpno like '" . $lname . "%' and (a.status='pending' OR a.status='Accepted') and a.type like '" . $type . "' and d.type='Patient'
                            and (a.refference_idref like '" . $refby . "' or a.refference_idref) is null
                            and i.Testgroup_tgid like '" . $testgroup . "'
                            group by a.lpsid
                            order by a.lpsid ASC");
                    } else {
//                    $result = DB::select("select a.date,a.sampleNo,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref from lps a,patient b, user c,usertype d,gender e  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "' and a.date like '" . $date . "' and a.sampleNo like '" . $sNo . "' and c.fname like '" . $fname . "%' and c.lname like '" . $lname . "%' and a.status='" . $status . "' and a.type like '" . $type . "' and d.type='Patient' and a.refference_idref = '" . $refby . "' order by a.lpsid DESC");
                        $result = DB::select("select arivaltime,accept_time,j.name as testname,a.date,a.sampleNo,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref,a.status,b.initials,c.tpno,a.lab_lid,j.price as tgprice, a.repcollected 
                            from lps a,patient b, user c,usertype d,gender e,lps_has_test f,Lab_has_test i,Testgroup j
                            where j.tgid = i.Testgroup_tgid and i.Lab_lid = a.lab_lid and a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "'
                            and a.date between '" . $date . "' and '" . $date2 . "' and a.sampleNo like '" . $sNo . "%' and (c.fname like '" . $fname . "%' or c.lname like '" . $fname . "%') and a.status like '" . $teststate . "'
                            and c.tpno like '" . $lname . "%' and (a.status='pending' OR a.status='Accepted') and a.type like '" . $type . "' and d.type='Patient'
                            and a.refference_idref = '" . $refby . "'
                            and i.Testgroup_tgid like '" . $testgroup . "'
                            group by a.lpsid
                            order by a.lpsid ASC");
                    }
                    return json_encode($result); 
                } else {
                    if ($refby == "0") {
                        $refby = "%";
                        $result = DB::select("select arivaltime,accept_time,j.name as testname,a.date,a.sampleNo,a.type,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref,a.status,b.initials,c.tpno,a.lab_lid,j.price as tgprice, a.repcollected 
                            from lps a,patient b, user c,usertype d,gender e,lps_has_test f,Lab_has_test i,Testgroup j
                            where j.tgid = i.Testgroup_tgid and f.lps_lpsid = a.lpsid and i.Lab_lid = a.lab_lid
                            and i.test_tid = f.test_tid 
                            and a.patient_pid = b.pid and b.user_uid = c.uid
                            and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "'
                            and a.date between '" . $date . "' and '" . $date2 . "' and a.sampleNo like '" . $sNo . "%' and (c.fname like '" . $fname . "%' or c.lname like '" . $fname . "%') " . $testStatus . "and c.tpno like '" . $lname . "%' and a.type like '" . $type . "' and d.type='Patient'
                            and (a.refference_idref like '" . $refby . "' or a.refference_idref is null)
                            and i.Testgroup_tgid like '" . $testgroup . "'
                            group by a.lpsid
                            order by a.lpsid ASC");
                    } else {
                        $result = DB::select("select arivaltime,accept_time,j.name as testname,a.date,a.sampleNo,a.type,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref,a.status,b.initials,c.tpno,a.lab_lid,j.price as tgprice, a.repcollected 
                            from lps a,patient b, user c,usertype d,gender e,lps_has_test f,Lab_has_test i,Testgroup j
                            where j.tgid = i.Testgroup_tgid and f.lps_lpsid = a.lpsid and i.Lab_lid = a.lab_lid
                            and i.test_tid = f.test_tid 
                            and a.patient_pid = b.pid and b.user_uid = c.uid
                            and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "'
                            and a.date between '" . $date . "' and '" . $date2 . "' and a.sampleNo like '" . $sNo . "%' and (c.fname like '" . $fname . "%' or c.lname like '" . $fname . "%') " . $teststate . " and c.tpno like '" . $lname . "%' and a.type like '" . $type . "' and d.type='Patient'
                            and a.refference_idref = '" . $refby . "'
                            and i.Testgroup_tgid like '" . $testgroup . "'
                            group by a.lpsid 
                            order by a.lpsid ASC");
                    }
                    return json_encode($result);
                }
            } else {

                $result = DB::select("select arivaltime,accept_time,j.name as testname,a.date,a.sampleNo,a.type,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref,a.status,b.initials,c.tpno,a.lab_lid,j.price as tgprice, a.repcollected, a.urgent_sample, a.fastingtime 
                    from lps a,patient b, user c,usertype d,gender e,lps_has_test f,Lab_has_test i,Testgroup j
                    where j.tgid = i.Testgroup_tgid and f.lps_lpsid = a.lpsid and i.Lab_lid = a.lab_lid
                    and i.test_tid = f.test_tid 
                    and a.patient_pid = b.pid and b.user_uid = c.uid 
                    and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "'
                    and a.date between '" . $date . "' and '" . $date2 . "' and d.type='Patient' and (a.status='Accepted' and fastingtime is not null) and repcollected is null 
                    and (".$exclude_query.") 
                    group by a.lpsid
                    order by a.lpsid ASC");

                return json_encode($result); 

            }
        }
    }

    function SearchPatientView_WF() {
        $lid = $_SESSION['lid'];
        $luid = $_SESSION['luid'];

        $date = Input::get('date');
        $date2 = Input::get('datex');
        $sno = Input::get('sno');

        if($sno == ""){
            $sno = "%";
        }

        // $result = DB::select("select j.name as testname,j.tgid as testid,a.date,a.sampleNo,a.type,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref,a.status,b.initials,c.tpno,a.lab_lid,j.price as tgprice, a.price as billedprice
        //     from lps a,patient b, user c,usertype d,gender e,Testgroup j
        //     where j.tgid = a.Testgroup_tgid
        //     and a.patient_pid = b.pid
        //     and b.user_uid = c.uid
        //     and c.usertype_idusertype=d.idusertype
        //     and e.idgender = c.gender_idgender
        //     and a.lab_lid='" . $lid . "'
        //     and a.date between '" . $date . "' and '" . $date2 . "'
        //     group by a.lpsid
        //     order by a.lpsid DESC");

        $result = DB::select("SELECT j.name AS testname, j.tgid AS testid, a.date, a.sampleNo, a.type, b.pid, c.fname, c.lname, e.gender, b.age, b.months, b.days, c.tpno, c.address, a.lpsid, a.refference_idref, a.status, b.initials, c.tpno, a.lab_lid, j.price AS tgprice, a.price AS billedprice
FROM lps a
JOIN patient b ON a.patient_pid = b.pid
JOIN user c ON b.user_uid = c.uid
JOIN usertype d ON c.usertype_idusertype = d.idusertype
JOIN gender e ON e.idgender = c.gender_idgender
JOIN Testgroup j ON j.tgid = a.Testgroup_tgid
LEFT JOIN lps_has_test ab ON a.lpsid = ab.lps_lpsid
WHERE a.lab_lid = '" . $lid . "'
AND a.date BETWEEN '" . $date . "' AND '" . $date2 . "' AND a.sampleno like '".$sno."%' 
AND ab.lps_lpsid IS NULL
GROUP BY a.lpsid
ORDER BY a.lpsid DESC;");

        return json_encode($result);
    }

    function updateWoF() {

        $sno = explode("#", Input::get('sno'))[1];
        $tgid = explode("#", Input::get('sno'))[0];
        $date = explode("#", Input::get('sno'))[2];

        $result = DB::select("select test_tid from Lab_has_test where Lab_lid = '" . $_SESSION['lid'] . "' and Testgroup_tgid = '" . $tgid . "'");
        foreach ($result as $res) {

            $TestID = $res->test_tid;

            $exists = false;
            $resultx = DB::select("select * from lps_has_test where lps_lpsid = (select lpsid from lps where sampleno = '" . $sno . "' and lab_lid = '" . $_SESSION['lid'] . "' and date = '" . $date . "') and test_tid = '" . $TestID . "'");
            foreach ($resultx as $resx) {
                $exists = true;
            }

            if (!$exists) {
                DB::statement("insert into lps_has_test(lps_lpsid, test_tid, state, lisloaded) values ((select lpsid from lps where sampleno = '" . $sno . "' and lab_lid = '" . $_SESSION['lid'] . "' and date = '" . $date . "'),'" . $TestID . "','pending','0')");
            }
        }

        return "Format Updated!";
    }

    function deleteWoF() {
        $sno = explode("#", Input::get('sno'))[1];
        $date = explode("#", Input::get('sno'))[2];
        
        DB::statement("UPDATE lps set date = '0000-00-00' where sampleno = '" . $sno . "' and lab_lid = '" . $_SESSION['lid'] . "' and date = '" . $date . "'");

        return "Sample Deleted!";
    }


    function SearchPatientViewGET() {
        if (isset($_GET["client"])) {
            $lid = $_GET["lid"];

            $date = Input::get('date');
            $sNo = Input::get('sno');
            $fname = Input::get('fname');
            $lname = Input::get('lname');
            $type = Input::get('type');

            $status = "";
            if (Input::get('status') != null) {
                $status = Input::get('status');
            }


            if ($sNo == "") {
                $sNo = "%";
            }
            if ($fname == "") {
                $fname = "%";
            }
            if ($lname == "") {
                $lname = "%";
            }
            if ($type == "") {
                $type = "%";
            }

            $data = "";
            if ($status != "") {
                $result = DB::select("select a.date,a.sampleNo,a.type,c.fname,c.lname,a.status from lps a,patient b, user c,usertype d,gender e  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "' and a.date like '" . $date . "' and a.sampleNo like '" . $sNo . "%' and c.fname like '" . $fname . "%' and c.lname like '" . $lname . "%' and a.status='" . $status . "' and a.type like '" . $type . "' and d.type='Patient' order by a.lpsid DESC");
            } else {
                $result = DB::select("select a.date,a.sampleNo,a.type,c.fname,c.lname,a.status from lps a,patient b, user c,usertype d,gender e  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "' and a.date like '" . $date . "' and a.sampleNo like '" . $sNo . "%' and c.fname like '" . $fname . "%' and c.lname like '" . $lname . "%' and a.type like '" . $type . "' and d.type='Patient' order by a.lpsid DESC");
            }

            foreach ($result as $res) {
                if ($data != "") {
                    $data .= "#/#";
                }
                $data .= $res->date . "#,#" . $res->sampleNo . "#,#" . $res->type . "#,#" . $res->fname . "#,#" . $res->lname . "#,#" . $res->status;
            }

            if ($data == "") {
                echo "No Data";
            } else {
                echo $data;
            }
        }
    }

    function SearchPatientViewGETNew() {
        if (isset($_GET["client"])) {
            $lid = $_GET["lid"];

            $date = Input::get('date');
            $sNo = Input::get('sno');
            $fname = Input::get('fname');
            $lname = Input::get('lname');
            $type = Input::get('type');

            $status = "";
            if (Input::get('status') != null) {
                $status = Input::get('status');
            }


            if ($sNo == "") {
                $sNo = "%";
            }
            if ($fname == "") {
                $fname = "%";
            }
            if ($lname == "") {
                $lname = "%";
            }
            if ($type == "") {
                $type = "%";
            }

            $data = "";

            $date2 = Input::get('date2');

            $lnamePhase = " and c.lname like '" . $lname . "%' ";
            if($lid == "43"){
                $lnamePhase = " and c.tpno like '" . $lname . "%' ";
            }

            

            if($date2 != ""){

                // $date2 = Input::get('date2');

                

                if ($status != "") {
                    $result = DB::select("select a.date,a.sampleNo,a.type,c.fname,c.lname,a.status, f.gtotal,f.paid, f.gtotal-f.paid as due, f.cashier, a.arivaltime, a.patient_pid as patientId from lps a,patient b, user c,usertype d,gender e, invoice f  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and f.lps_lpsid = a.lpsid and a.lab_lid='" . $lid . "' and a.date between '" . $date . "' and '" . $date2 . "' and a.sampleNo like '" . $sNo . "%' and c.fname like '" . $fname . "%' ".$lnamePhase." and a.status='" . $status . "' and a.type like '" . $type . "' and d.type='Patient' group by f.iid order by a.lpsid DESC");
                } else {
                    $result = DB::select("select a.date,a.sampleNo,a.type,c.fname,c.lname,a.status, f.gtotal,f.paid, f.gtotal-f.paid as due, f.cashier, a.arivaltime, a.patient_pid as patientId from lps a,patient b, user c,usertype d,gender e, invoice f  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and f.lps_lpsid = a.lpsid and a.lab_lid='" . $lid . "' and a.date between '" . $date . "' and '" . $date2 . "' and a.sampleNo like '" . $sNo . "%' and c.fname like '" . $fname . "%' ".$lnamePhase." and a.type like '" . $type . "' and d.type='Patient' group by f.iid order by a.lpsid DESC");
                } 
 

            }else{

                if($lid == "45"){

                    if ($status != "") {
                        $result = DB::select("select a.date,a.sampleNo,a.type,c.fname,c.lname,a.status, f.gtotal,f.paid, f.gtotal-f.paid as due, f.cashier, a.arivaltime, a.patient_pid as patientId from lps a,patient b, user c,usertype d,gender e, invoice f, Testgroup g where a.Testgroup_tgid = g.tgid and a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and f.lps_lpsid = a.lpsid and a.lab_lid='" . $lid . "' and a.date like '" . $date . "' and a.sampleNo like '" . $sNo . "%' and c.fname like '" . $fname . "%' ".$lnamePhase." and a.status='" . $status . "' and a.type like '" . $type . "' and a.status = 'pending' and d.type='Patient' and (g.tgsection_id = '1' or g.tgsection_id = '5' or g.tgsection_id = '6') group by f.iid order by a.lpsid DESC");
                    } else {
                        $result = DB::select("select a.date,a.sampleNo,a.type,c.fname,c.lname,a.status, f.gtotal,f.paid, f.gtotal-f.paid as due, f.cashier, a.arivaltime, a.patient_pid as patientId from lps a,patient b, user c,usertype d,gender e, invoice f, Testgroup g where a.Testgroup_tgid = g.tgid and a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and f.lps_lpsid = a.lpsid and a.lab_lid='" . $lid . "' and a.status = 'pending' and a.date like '" . $date . "' and a.sampleNo like '" . $sNo . "%' and c.fname like '" . $fname . "%' ".$lnamePhase." and a.type like '" . $type . "' and d.type='Patient' and (g.tgsection_id = '1' or g.tgsection_id = '5' or g.tgsection_id = '6') group by f.iid order by a.lpsid DESC");
                    }

                }else{

                    if ($status != "") {
                        $result = DB::select("select a.date,a.sampleNo,a.type,c.fname,c.lname,a.status, f.gtotal,f.paid, f.gtotal-f.paid as due, f.cashier, a.arivaltime, a.patient_pid as patientId from lps a,patient b, user c,usertype d,gender e, invoice f  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and f.lps_lpsid = a.lpsid and a.lab_lid='" . $lid . "' and a.date like '" . $date . "' and a.sampleNo like '" . $sNo . "%' and c.fname like '" . $fname . "%' ".$lnamePhase." and a.status='" . $status . "' and a.type like '" . $type . "' and d.type='Patient' group by f.iid order by a.lpsid DESC");
                    } else {
                        $result = DB::select("select a.date,a.sampleNo,a.type,c.fname,c.lname,a.status, f.gtotal,f.paid, f.gtotal-f.paid as due, f.cashier, a.arivaltime, a.patient_pid as patientId from lps a,patient b, user c,usertype d,gender e, invoice f  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and f.lps_lpsid = a.lpsid and a.lab_lid='" . $lid . "' and a.date like '" . $date . "' and a.sampleNo like '" . $sNo . "%' and c.fname like '" . $fname . "%' ".$lnamePhase." and a.type like '" . $type . "' and d.type='Patient' group by f.iid order by a.lpsid DESC");
                    }

                }

                
            }

            foreach ($result as $res) {
                if ($data != "") {
                    $data .= "#/#";
                }
                $data .= $res->date . "#,#" . $res->sampleNo . "#,#" . $res->type . "#,#" . $res->fname . "#,#" . $res->lname . "#,#" . $res->status . "#,#" . $res->gtotal . "#,#" . $res->paid . "#,#" . $res->due. "#,#" . $res->cashier. "#,#" .$res->arivaltime . "#,#" . $res->patientId;
            }

            if ($data == "") {
                echo "No Data";
            } else {
                echo $data;
            }
        }
    }

    function SearchPatientViewGETNewTP() {
        if (isset($_GET["client"])) {
            $lid = $_GET["lid"];

            $date = Input::get('date');
            $sNo = Input::get('sno');
            $fname = Input::get('fname');
            $lname = Input::get('lname');
            
            $tpno = Input::get('tpno');
            $dateskip = Input::get('dateskip');
            
            $type = Input::get('type');

            $status = "";
            if (Input::get('status') != null) {
                $status = Input::get('status');
            }


            if ($sNo == "") {
                $sNo = "%";
            }
            if ($fname == "") {
                $fname = "%";
            }
            if ($lname == "") {
                $lname = "%";
            }

            if ($tpno == "") {
                $tpno = "%";
            }

            if ($type == "") {
                $type = "%";
            }

            $data = "";

            $lnamePhase = " and c.lname like '" . $lname . "%' ";
            
            $date_filter = " and a.date = '" . $date . "' ";
            if($dateskip == "1"){
                    $date_filter = "";
            }


                if ($status != "") {
                    $result = DB::select("select a.date,a.sampleNo,a.type,c.fname,c.lname,a.status, f.gtotal,f.paid, f.gtotal-f.paid as due, f.cashier,f.source from lps a,patient b, user c,usertype d,gender e, invoice f  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and f.lps_lpsid = a.lpsid and a.lab_lid='" . $lid . "' ".$date_filter." and a.sampleNo like '" . $sNo . "%' and c.fname like '" . $fname . "%' ".$lnamePhase."  and c.tpno like '" . $tpno . "%'  and a.status='" . $status . "' and a.type like '" . $type . "' and d.type='Patient' group by f.iid order by a.lpsid DESC");
                } else {
                    $result = DB::select("select a.date,a.sampleNo,a.type,c.fname,c.lname,a.status, f.gtotal,f.paid, f.gtotal-f.paid as due, f.cashier,f.source from lps a,patient b, user c,usertype d,gender e, invoice f  where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and f.lps_lpsid = a.lpsid and a.lab_lid='" . $lid . "' ".$date_filter." and a.sampleNo like '" . $sNo . "%' and c.fname like '" . $fname . "%' ".$lnamePhase."  and c.tpno like '" . $tpno . "%'  and a.type like '" . $type . "' and d.type='Patient' group by f.iid order by a.lpsid DESC");
                }
            

            foreach ($result as $res) {
                if ($data != "") {
                    $data .= "#/#";
                }
                $data .= $res->date . "#,#" . $res->sampleNo . "#,#" . $res->type . "#,#" . $res->fname . "#,#" . $res->lname . "#,#" . $res->status . "#,#" . $res->gtotal . "#,#" . $res->paid . "#,#" . $res->due. "#,#" . $res->cashier . "#,#" . $res->source;
            }

            if ($data == "") {
                echo "No Data";
            } else {
                echo $data;
            }
        }
    }



    function SearchPatientViewOneSample() {
        if (isset($_GET["client"])) {
            $lid = $_GET["lid"];

            $date = Input::get('date');
            $sNo = Input::get('sno');


            if ($sNo == "") {
                $sNo = "%";
            }

            $data = "";

//            $result = DB::select("select a.date,a.sampleNo,a.type,c.fname,c.lname,a.status from lps a,patient b, user c,usertype d,gender e, lps_has_test f   where a.patient_pid = b.pid and b.user_uid = c.uid and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "' and a.date like '" . $date . "' and a.sampleNo like '" . $sNo . "%' and c.fname like '" . $fname . "%' and c.lname like '" . $lname . "%' and a.status='" . $status . "' and a.type like '" . $type . "' and d.type='Patient' order by a.lpsid ASC");
            
            // if($lid = "43"){
                $result = DB::select("select a.repcollected, j.name as testname,a.date,a.sampleNo,a.type,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref,a.status,b.initials,c.tpno,a.lab_lid,j.price as tgprice
                from lps a,patient b, user c,usertype d,gender e,Testgroup j
                where j.tgid = a.Testgroup_tgid 
                and a.patient_pid = b.pid and b.user_uid = c.uid
                and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "'
                and a.date = '" . $date . "' and 

                CASE
                    WHEN SUBSTRING(sampleno, LENGTH(sampleno), 1) NOT REGEXP '[0-9]' THEN SUBSTRING(sampleno, 1, LENGTH(sampleno) - 1)
                    ELSE sampleno
                END = '" . $sNo . "'

                group by a.lpsid
                order by a.lpsid ASC");
            // }else{

            // $result = DB::select("select j.name as testname,a.date,a.sampleNo,a.type,b.pid,c.fname,c.lname,e.gender,b.age,b.months,b.days,c.tpno,c.address,a.lpsid,a.refference_idref,a.status,b.initials,c.tpno,a.lab_lid,j.price as tgprice
            //     from lps a,patient b, user c,usertype d,gender e,lps_has_test f,Lab_has_test i,Testgroup j
            //     where j.tgid = i.Testgroup_tgid and f.lps_lpsid = a.lpsid and i.Lab_lid = a.lab_lid
            //     and i.test_tid = f.test_tid 
            //     and a.patient_pid = b.pid and b.user_uid = c.uid
            //     and c.usertype_idusertype=d.idusertype and e.idgender = c.gender_idgender and a.lab_lid='" . $lid . "'
            //     and a.date = '" . $date . "' and a.sampleNo like '" . $sNo . "%'
            //     group by a.lpsid
            //     order by a.lpsid ASC");

            // }


            foreach ($result as $res) {
                if ($data != "") {
                    $data .= "#/#";
                }
                $data .= $res->sampleNo . "#,#" . $res->testname . "#,#" . $res->status. "#,#" . $res->repcollected;
            }

            if ($data == "") {
                echo "No Data";
            } else {
                echo $data;
            }
        }
    }

    function LoadLastPatient() {

        $lid = $_SESSION['lid'];

        $result = DB::select("select MAX(lpsid) as lpsid from lps where Lab_lid = '" . $lid . "'");
        foreach ($result as $res) {
            $lpsID = $res->lpsid;
        }

        if (isset($lpsID)) {
            $result = DB::select("select * from user a, patient b, lps c where a.uid = b.user_uid and c.patient_pid = b.pid and lpsid = '" . $lpsID . "'");
            return json_encode($result);
        } else {
            echo '0';
        }
    }

    function LoadLastPatientGET() {

        $lid = $_REQUEST['lid'];

        $result = DB::select("select MAX(lpsid) as lpsid from lps where Lab_lid = '" . $lid . "'");
        foreach ($result as $res) {
            $lpsID = $res->lpsid;
        }

        if (isset($lpsID)) {
            $result = DB::select("select * from user a, patient b, lps c where a.uid = b.user_uid and c.patient_pid = b.pid and lpsid = '" . $lpsID . "'");
            foreach ($result as $res) {
                $resss = $res->fname . "/" . $res->lname . "/" . $res->age . "/" . $res->months . "/" . $res->days . "/" . $res->tpno . "/" . $res->address . "/" . $res->gender_idgender . "/" . $res->pid;
            }
            return $resss;
        } else {
            echo '0';
        }
    }

    function updateSelectedPatietAge($selectedPID, $years, $months, $dates, $initials) {
        DB::statement("update patient set age = '" . $years . "', months='" . $months . "', days = '" . $dates . "', initials = '" . $initials . "' where pid = '" . $selectedPID . "'");
    }

    function manageOnePatient() {
        if ($_SESSION['luid'] !== null) {
            $lid = $_SESSION['lid'];
            $pid = Input::get('pid');
            $lpsid = Input::get('lpsid');

            if (Input::get('submit') !== null && Input::get('submit') == "Update Details") {
                $fname = Input::get("fname");
                $lname = Input::get("lname");
                $gen = Input::get("gender");
                $years = Input::get("years");
                $months = Input::get("months");
                $dates = Input::get("days");
                $pnno = Input::get("tpno");
                $address = Input::get("address");
                $initials = Input::get("ini");
                $nic = Input::get("nic");

                $mname = Input::get("mname");
                $email = Input::get("email");
                $hpno = Input::get("hpno");

                //Invoice Details
                $iid = Input::get("iid");
                $date = date('Y-m-d');

                $discountName = Input::get('dc');
                $discount = 0;

                if ($discountName != "") {
                    $result = DB::select("select did from Discount where name='" . $discountName . "' and lab_lid='" . $lid . "'");
                    foreach ($result as $res) {
                        $discount = $res->did;
                    }
                }

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

                $x = $this->updateExsistPatient($pid, $fname, $lname, $years, $months, $dates, $gen, $pnno, $address, $mname, $email, $hpno, $initials, $nic);
                $y = $this->updateInvoiceForLPS($iid, $total, $gtotal, $payment, $pMethod, $pstatus, $discount, $paidDate);

                //update refference for related lps
                $refName = Input::get("ref");
                $resultRef = DB::table('refference')->where('lid', $lid)->where('name', $refName)->first();
                if (!empty($resultRef)) {
                    $refBy = $resultRef->idref;
                } else {
                    $refBy = DB::table('refference')->insertGetId(['lid' => $lid, 'name' => $refName]);
                }

                //get all lps ids from arrival time to update all reffers
                $resultlp = DB::select("select lpsid from lps where arivaltime = (select arivaltime from lps where lpsid = '" . $lpsid . "') and lab_lid = '" . $lid . "' and date = (select date from lps where lpsid = '" . $lpsid . "')");
                foreach ($resultlp as $reslp) {
                    $z = DB::statement("update lps set refference_idref = '" . $refBy . "' where lpsid = '" . $reslp->lpsid . "'");
                }
                //
                //update sample fasting hours
                $fhour = Input::get("fhours");
                $z = DB::statement("update lps set fastinghours = '" . $fhour . "' where lpsid = '" . $lpsid . "'");
                //


                $log_descreption = "Patient Details Updated : ".$fname." # ".$lname." # ".$years." # ".$months." # ".$dates." # ".$gen." # ".$pnno." # ".$address." # ".$mname." # ".$email." # ".$hpno." # ".$initials." # ".$nic." # ".$iid." # ".$total." # ".$gtotal." # ".$payment." # ".$pMethod." # ".$pstatus." # ".$discount." # ".$paidDate;

                SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "Patient Details Page", "Update Details", $log_descreption);


                if ($x != 0 && $y != 0) {
                    return View::make('WiViewOP')->with('lpsid', $lpsid)->with('msg', 'Details Updated!');
                } else {
                    return View::make('WiViewOP')->with('lpsid', $lpsid)->with('msg', 'Operation Error!');
                }
            } else if (Input::get('submit') !== null && Input::get('submit') == "Remove Patient") {

                $result = DB::select("select user_uid from patient where pid='" . $pid . "'");
                foreach ($result as $res) {
                    $x = DB::statement("update user set status='0' where uid = '" . $res->user_uid . "'");
                }

                $log_descreption = "Patient Details Updated : PID: ".$pid;

                SystemLogs::saveChangeLog($_SESSION['lid'], $_SESSION['luid'], "Patient Details Page", "Remove Patient", $log_descreption);

                return View::make('WiViewOP')->with('lpsid', $lpsid)->with('msg', 'User Terminated!');
            }
        } else {
            return View::make('WiViewOP')->with('msg', 'Please Login!');
        }
    }

    function updateExsistPatient($pid, $fname, $lname, $age, $month, $days, $gender, $tpno, $address, $mname, $email, $hpno, $initials, $nic) {
        //update patient Table
        $this->updateSelectedPatietAge($pid, $age, $month, $days, $initials);

        //select User ID
        $result = DB::select("select user_uid from patient where pid = '" . $pid . "'");
        foreach ($result as $res) {
            $uid = $res->user_uid;
        }

        //select Gender
        $genID = 0;
        $result = DB::select("select * from gender where gender='" . $gender . "'");
        foreach ($result as $res) {
            $genID = $res->idgender;
        }

        //update User table
        $x = DB::statement("update user set fname='" . $fname . "', lname='" . $lname . "', tpno='" . $tpno . "', nic='" . $nic . "', address='" . $address . "', gender_idgender='" . $genID . "',mname = '" . $mname . "',email = '" . $email . "', hpno= '" . $hpno . "'  where uid = '" . $uid . "'");
        return $x;
    }

    function updateInvoiceForLPS($iid, $total, $gtotal, $paid, $paymentMethod, $status, $did, $paiddate) {
        if ($did == '0' | $did == '') {
            $x = DB::statement("update invoice set total = '" . $total . "',gtotal = '" . $gtotal . "',paid = '" . $paid . "',paymentmethod = '" . $paymentMethod . "',discount_did = NULL,status = '" . $status . "',paiddate = '" . $paiddate . "' where iid = '" . $iid . "'");
        } else {
            $x = DB::statement("update invoice set total = '" . $total . "',gtotal = '" . $gtotal . "',paid = '" . $paid . "',paymentmethod = '" . $paymentMethod . "',discount_did = '" . $did . "',status = '" . $status . "',paiddate = '" . $paiddate . "' where iid = '" . $iid . "'");
        }
        return $x;
    }

    function registerPatientViaClient() {
        if (isset($_REQUEST['submit']) && $_REQUEST['submit'] == "regp") {
            $lid = $_REQUEST['lid'];
            $luid = $_REQUEST['luid'];
            //$cuSymble = $_REQUEST['cuSymble'];
            $date = date('Y-m-d');
            $time = date('H:i:s');

            $sampleNo = Input::get('sampleno');
            $status = 'pending';
            $refBy = Input::get('refby');
            if ($refBy == "") {
                $newRefBy = Input::get('newref');
                if ($newRefBy == "") {
                    $refBy = false;
                } else {
                    $result = DB::table('refference')->where('lid', $lid)->where('name', $newRefBy)->first();
                    if (!empty($result)) {
                        $refBy = $result->idref;
                    } else {
                        $refBy = DB::table('refference')->insertGetId(['lid' => $lid, 'name' => $newRefBy]);
                    }
                }
            }

            $FastingHours = "0";
            if (isset($_REQUEST['hrs'])) {
                $FastingHours = $_REQUEST['hrs'];
            }

            $refinv = "";
            if (isset($_REQUEST['refinv'])) {
                $refinv = $_REQUEST['refinv'];
            }

            $dob = "";
            if (isset($_REQUEST['dob'])) {
                $dob = $_REQUEST['dob'];
            }

            $nic = "";
            if (isset($_REQUEST['nic'])) {
                $nic = $_REQUEST['nic'];
            }

            if (Input::get('address') == null) {
                $address = "";
            } else {
                $address = Input::get('address');
//                    $address = preg_replace('/[^A-Za-z0-9 !:,@#$^&*().]/u', ' ', strip_tags($address));
                $address = preg_replace('/[^A-Za-z0-9 !:,\/@#$^&*().]/u', ' ', strip_tags($address));
            }

            //if not select value is 0
            $discountID = Input::get('discount');

            //invoic Details
            $tot = Input::get('tot');
            $gtot = Input::get('gtot');
            $paymeth = Input::get('paym');
            $paid = Input::get('payment');

            if ($paid == "0") {
                $paymentState = "Not Paid";
            } else if ($paid == $gtot) {
                $paymentState = "Payment Done";
                $paidDate = $date;
            } else if ($paid < $gtot) {
                $paymentState = "Pending Due";
                $paidDate = $date;
            } else {
                $paymentState = "Pending Due";
            }

            $ptype = Input::get('ptype');

            $selectedPID = Input::get('selectedpid');
            if ($selectedPID == "") {
                $fname = Input::get('fname');
                $lname = Input::get('lname');
                $gender = Input::get('gender');
                $years = Input::get('years');
                $months = Input::get('months');
                $dates = Input::get('dates');
                $initial = Input::get('initial');

                if (Input::get('pnno') == null) {
                    $pnno = "";
                } else {
                    $pnno = Input::get('pnno');
                }




                $exsistPID = 0;
                $result = DB::select("select b.pid from user a, patient b where a.uid = b.user_uid and a.fname = '" . $fname . "' and a.lname = '" . $lname . "' and a.tpno = '" . $pnno . "' and (b.age='" . $years . "' and b.months='" . $months . "' and b.days = '" . $dates . "') and a.address = '" . $address . "'");
                foreach ($result as $res) {
                    $exsistPID = $res->pid;
                }

                if ($exsistPID == 0) {
                    $userType = '2';
                    //insert user
                    $UID = $this->SaveUser($fname, null, $lname, $pnno, null, $address, null, $gender, $userType, '1', null, $nic);

                    //insertPatient
                    $PID = $this->SavePatient($UID, $years, $months, $dates, $initial, $dob);

                    //insert patient into lps and add tests
                    $SnonCost = $this->saveLPSNaddTestToPatient($PID, $lid, $date, $sampleNo, $time, $ptype, $refBy, $tot, $discountID, $gtot, $paid, $paymentState, $paymeth, $luid, $FastingHours, $refinv);

                    $Sno = explode("###", $SnonCost)[0];
                    $BillCost = explode("###", $SnonCost)[1];

                    //echo "Patient Added " . $lpsID;
                } else {
                    //insert into lps and add tests
                    $SnonCost = $this->saveLPSNaddTestToPatient($exsistPID, $lid, $date, $sampleNo, $time, $ptype, $refBy, $tot, $discountID, $gtot, $paid, $paymentState, $paymeth, $luid, $FastingHours, $refinv);
                    $Sno = explode("###", $SnonCost)[0];
                    $BillCost = explode("###", $SnonCost)[1];

                    //echo "Patient Added to exsist pid" . $lpsID;
                }
            } else {
                //update patient age
                $years = Input::get('years');
                $months = Input::get('months');
                $dates = Input::get('dates');
                $initials = Input::get('initial');
                $this->updateSelectedPatietAge($selectedPID, $years, $months, $dates, $initials);

                //update patient details
                DB::statement("update user set nic = '" . $nic . "', address = '" . $address . "' where uid = (select user_uid from patient where pid = '" . $selectedPID . "')");

                //save LPS and add Test to patient     
                $SnonCost = $this->saveLPSNaddTestToPatient($selectedPID, $lid, $date, $sampleNo, $time, $ptype, $refBy, $tot, $discountID, $gtot, $paid, $paymentState, $paymeth, $luid, $FastingHours, $refinv);

                $Sno = explode("###", $SnonCost)[0];
                $BillCost = explode("###", $SnonCost)[1];

                //echo "Patient Added to selected pid" . $lpsID;
            }

            $result = DB::select("select lpsid from lps where sampleno='" . $Sno . "' and date = '" . $date . "'");
            foreach ($result as $res) {
                $lpsID = $res->lpsid;
            }

            //get User Name
            $UID = Labuser::where('luid', '=', $luid)->first()->user_uid;
            $User = User::find($UID);
            $UserName = $User->fname . " " . $User->lname;

            if ($BillCost == "") {
                $BillCost = 0;
            }

            //Add invoice details
            $IID = $this->addInvoice($lpsID, $tot, $discountID, $gtot, $paid, $date, $paymentState, $paymeth, $UserName, $BillCost);
            //save invoice payment table
            if ($paid == "0") {

            } else {
                $ipid = $this->addInvoicePayment($IID, $date, $paid, $paymeth, $UID);
            }


            //PrintReport
            if ($IID != 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
    }

    function saveLPSNaddTestToPatient($selectedPID, $lid, $date, $sampleNo, $time, $ptype, $refBy, $tot, $discountID, $gtot, $paid, $paymentState, $paymeth, $luid, $FastingHours, $refinv) {

        $SampleNoSufix = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

        $newPost = $_REQUEST;
        unset($newPost['initial']);
        if (isset($newPost['hrs'])) {
            unset($newPost['hrs']);
        }
        if (isset($newPost['refinv'])) {
            unset($newPost['refinv']);
        }
        if (isset($newPost['dob'])) {
            unset($newPost['dob']);
        }
        unset($newPost['submit']);
        unset($newPost['ptype']);
        unset($newPost['fname']);
        unset($newPost['lname']);
        unset($newPost['years']);
        unset($newPost['months']);
        unset($newPost['dates']);
        unset($newPost['gender']);
        unset($newPost['refby']);
        unset($newPost['sampleno']);
        unset($newPost['pnno']);
        unset($newPost['address']);
        unset($newPost['selectedpid']);
        unset($newPost['tot']);
        unset($newPost['gtot']);
        unset($newPost['discount']);
        unset($newPost['disPre']);
        unset($newPost['dc']);
        unset($newPost['paym']);
        unset($newPost['payment']);
        unset($newPost['submited']);
        unset($newPost['lid']);
        unset($newPost['luid']);
        unset($newPost['invoice']);
        unset($newPost['newref']);
        unset($newPost['nic']);
        unset($newPost['PHPSESSID']);
        unset($newPost['laravel_session']);

        $tg_cost = 0;

        $testKeys = array_keys($newPost);

        for ($key = 0; $key < count($testKeys); $key++) {
            $test = $testKeys[$key];

            $arr = explode('-', $test);
            if (count($arr) > 1) {

                $testGID = $arr[1];

                //get Fasting hours
                $FastingHours = $newPost[$test];

                //set sampleno without duplicate
                if ($key != 0) {
                    $sampleNoX = $sampleNo . $SampleNoSufix[$key];
                } else {
                    $sampleNoX = $sampleNo;
                }

                //insert if user selected a pid
//                $lpsID = $this->insertPatientIntoLPS($selectedPID, $lid, $date, $sampleNoX, $time, $ptype, $refBy, $FastingHours, $refinv);
                
                //insert patient with tgid
                $lpsID = $this->insertPatientIntoLPSWithTGID($selectedPID, $lid, $date, $sampleNoX, $time, $ptype, $refBy, $FastingHours, $refinv, $testGID);
                
                
                $this->addTestGroupToPatient($lpsID, $testGID, 'pending', $lid);

                //get test group cost
                $tg_cost += $this->getGTCost($testGID, $lid);

                
                //save lps costs~~~~~~~~~~~~~~~~~~

                //1.Identify the patient from main lab or center (lab branch)

                $SNOprefix = substr($sampleNoX,0,2);

                if(!is_numeric($SNOprefix)){
                    $branchCode = $SNOprefix;
                    $result = DB::select("select bid from labbranches where Lab_lid = '" . $lid . "' and code = '" . $branchCode . "'");
                    foreach ($result as $res) {
                        $branchSearch = "and bid = '".$res->bid."'";
                    }
                }else{
                    $branchSearch = "and bid is null";
                }

                //2.get related costs and save into lps_costs table
                
                $costFlag = true;
                $Resultcc = DB::select("select name,amount from test_costs where Testgroup_tgid = '".$testGID."' ".$branchSearch."");
                foreach ($Resultcc as $rescc) {
                    DB::statement("insert into lps_costs(lps_lpsid,name,amount) values('" . $lpsID . "','" . $rescc->name . "','" . $rescc->amount . "')");

                    $costFlag = false;
                }

                if($lid == "29"){
                    if($costFlag){

                        $query = "SELECT cost from Testgroup where Lab_lid = '".$lid."' and Testgroup_tgid = '".$testGID."'";

                        if(!is_numeric($SNOprefix)){
                            $query = "SELECT cost from labbranches_has_Testgroup where tgid = '".$testGID."' and bid = (select bid from labbranches where Lab_lid = '" . $lid . "' and code = '" . $branchCode . "')";
                        }

                        $Resultccx = DB::select($query);
                        foreach ($Resultccx as $resccx) {
                            DB::statement("insert into lps_costs(lps_lpsid,name,amount) values('" . $lpsID . "','TEST COST','" . $resccx->cost . "')");
                        }
                    }
                }
                
                //end~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

            }
        }
        return $sampleNo . "###" . $tg_cost;
    }

    function getGTCost($testGID, $lid) {
        $cost = 0;
        $result = DB::select("select cost from Testgroup where lab_lid = '" . $lid . "' and tgid = '" . $testGID . "'");
        foreach ($result as $res) {
            $cost = $res->cost;
        }
        return $cost;
    }

    function addSampleToPatient() {
        $pid = Input::get("pid");
        return View::make('WiaddPatient')->with('pid', $pid);
    }

    function searchcontacts() {
//        if(isset(Input::get("tp"))){
        $tp = Input::get("tp");
//        return View::make('WiaddPatient')->with('pid', $pid);
        echo "TP: " . $tp;
//        }
    }


    function statusProcess() {

        $doneList = [];
        
        $type = Input::get("type");
        $stLps = Input::get("stLps");

        for ($i=0; $i < count($stLps); $i++) { 

            $result = DB::select("SELECT a.date, c.tpno, a.sampleno from lps a, patient b, user c where a.patient_pid = b.pid and b.user_uid = c.uid and a.lpsid = '".$stLps[$i]."' group by b.pid");
            foreach ($result as $res) {
                $date = $res->date;
                $tpno = $res->tpno;
                $sno = $res->sampleno;

                $resultx = DB::select("SELECT msg FROM smslog where tpno = '". $tpno ."' and Lab_lid = '".$_SESSION['lid']."'");
                foreach ($resultx as $resx) {
                    if(str_contains($resx->msg,$date)){
                        array_push($doneList, $stLps[$i]);
                        // array_push($doneList, $sno."#".$date."#".$tpno);
                    }
                }

            }

        }
        

        return $doneList;
    }


}

?>