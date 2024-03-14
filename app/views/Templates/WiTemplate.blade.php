<?php
//if (session_id() == '') {
//    session_start();
//}

if (!isset($_SESSION)) {
    session_start();
}

    
    // unset($_SESSION["guest"]);

if (isset($_SESSION['lid']) & isset($_SESSION['luid'])) {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <!--        <link href="CSS/n_list.css" rel="stylesheet" type="text/css">
                        <link href="CSS/workUI.css" rel="stylesheet" type="text/css">
                        <link href="CSS/Stylie.css" rel="stylesheet" type="text/css">
                        <script type="text/javascript" src="JS/jquery-3.1.0.js"></script>-->

            <link href="{{ asset('CSS/n_list.css') }}" rel='stylesheet' type='text/css' />
            <link href="{{ asset('CSS/workUI.css') }}" rel='stylesheet' type='text/css' />
            <link href="{{ asset('CSS/Stylie.css') }}" rel='stylesheet' type='text/css' />
            <script src="{{ asset('JS/jquery-3.1.0.js') }}"></script>

            <title>@yield('title')</title>        

            <script type="text/javascript">
    $(document).ready(function () {
        $('#naviItemPane').hide();
        $('#loading').hide();
    });

    $(document).ajaxStart(function () {
        $('#loading').show();
    }).ajaxStop(function () {
        $('#loading').hide();
    });

    function showNaviPane() {
        if ($('#naviItemPane').is(":visible")) {
            $('#naviItemPane').hide();
        } else {
            $('#naviItemPane').show();
        }
    }

    function naviDrop(x, y) {
        var panel = document.getElementById(x);
        var maxHeight = y;
        if (panel.style.height === maxHeight) {
            panel.style.height = "0px";

        } else {
            panel.style.height = maxHeight;
        }

    }

    function back() {
        window.history.back();
    }
    function forward() {
        window.history.forward()
    }

    function navigate(element) {
        window.location = element;

    }
            </script>

            <style type="text/css">
                .settingPanelItem{
                    background-color: #ccccff;
                    color: #ffffff;
                    border-right: 15px;
                }

            </style>


            @yield('head')


        </head>  
        <body bgcolor="#EEEEEE" style="margin:0px">

            <table width="100%">
                <tr>
                    <td>
                        <table class="headTable" style="margin:0px" width="100%" border="0">
                            <tr style="margin:0px" >

                                <?php
                                $lid = $_SESSION['lid'];
                                $luid = $_SESSION['luid'];
                                // $cuSymble = $_SESSION['cuSymble'];

                                $labresults = DB::select("select name,logo from Lab where lid = '" . $lid . "'");
                                foreach ($labresults as $result) {
                                    $labname = $result->name;
                                    $Llogo = $result->logo;
                                    if ($Llogo == "none") {
                                        $Llogo = "images/defaultlablogo.jpg";
                                    }
                                }

                                // $userresults = DB::select("select fname,lname from user where uid=(select user_uid from labUser where luid='" . $luid . "')");
                                // foreach ($userresults as $result) {
                                //     $username = $result->fname . " " . $result->lname;
                                // }

                                $PM = false;
                                $TM = false;
                                $SM = false;
                                $MM = false;
                                $SpM = false;
                                $FM = false;
                                $communication = false;
                                $EM = false;
                                $myp = false;
                                $labp = false;
                                $labConfig = false;
                                $expenses = false;                                
                                $login_log = false;
                                $finance_reports = false;


                                // $prevresults = DB::select("select name from options where idoptions in (select options_idoptions from privillages where user_uid = (select user_uid from labUser where luid = '" . $luid . "'))");
                                // foreach ($prevresults as $result) {
                                //     if ($result->name == "Patient Management") {
                                //         $PM = true;
                                //     } else if ($result->name == "Test Management") {
                                //         $TM = true;
                                //     } else if ($result->name == "Stock Management") {
                                //         $SM = true;
                                //     } else if ($result->name == "Material Management") {
                                //         $MM = true;
                                //     } else if ($result->name == "Supplier Management") {
                                //         $SpM = true;
                                //     } else if ($result->name == "Finance Management") {
                                //         $FM = true;
                                //     } else if ($result->name == "Communication") {
                                //         $communication = true;
                                //     } else if ($result->name == "Employee Management") {
                                //         $EM = true;
                                //     } else if ($result->name == "Myprofile Management") {
                                //         $myp = true;
                                //     } else if ($result->name == "Laboratory Profile") {
                                //         $labp = true;
                                //     } else if ($result->name == "Laboratory Configurations") {
                                //         $labConfig = true;
                                //     }else if ($result->name == "Login Log") {
                                //         $login_log = true;
                                //     }else if ($result->name == "Finance Reports") {
                                //         $finance_reports = true;
                                //     }
                                // }

                                //select lab features
                                // $featuresQ = DB::select("select * from Lab_features where Lab_lid = '" . $lid . "' and features_idfeatures = (select idfeatures from features where name = 'Expenses Handling')");
                                // foreach ($featuresQ as $resultf) {
                                //     $expenses = true;
                                // }
                                ?>


                                
                                
                                <?php
                                // if (!isset($_SESSION["guest"]) && $_SESSION["guest"] == null) {
                                    ?>
                                    <td width="50" valign="bottom">                   



                                        <button onClick="naviDrop('settingspanal', '200px')" class="btn" style="height: 55px; margin-left: 0px;">
                                            Settings
                                        </button>

                                        <div id="settingspanal">
                                            <div style="padding: 10px;">
                                                <table align="right">
                                                    
                                                    
                                                    

                                
    

                                                </table>

                                            </div>
                                        </div>



                                    </td>
                                    <td width="50" valign="bottom">
                                        <button onClick="showNaviPane()" class="btn" style="height: 55px; margin-left: -4px;">
                                            Navigation
                                        </button>
                                    </td>

                                    <?php
                                // }
                                ?>

                            </tr>
                        </table>                        
                    </td>
                </tr>
                <tr>                    
                    <td>
                        <table width="100%" border="0" style="margin-top:-3px;">
                            <tr valign="top"> 
                                <td width="82%"> 


                                    @yield('body')



                                </td>

                                <td>
                                    <div id="naviPanal" class="naviPanal" style="z-index: 1500;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr align="center" valign="top">
                                                <td width="33%" height="30">
                                                    <?php
                                                // if (!isset($_SESSION["guest"]) && $_SESSION["guest"] == null) {
                                                    ?>
                                                    <!-- <a href="#" onClick="history.go(-1);
                                                            return false;"><img src="{{ asset('images/Back_Icon.png') }}" height="30px"></a> -->  
                                                <?php //}else{ ?>
                                                    <a href="#" onClick="window.location.href = 'viewptients';"><img src="{{ asset('images/Back_Icon.png') }}" height="30px"></a>  
                                                <?php //} ?>    
                                                </td>
                                                <?php
                                                // if (!isset($_SESSION["guest"]) && $_SESSION["guest"] == null) {
                                                    ?>
                                                    <td width="34%">
                                                        <a href="wimain"><img src="{{ asset('images/Home_Icon.png') }}" height="30px"></a>
                                                    </td>
                                                    
                                                    <td width="33%"><a href="#" onClick="history.go(1);
                                                        return false;"><img src="{{ asset('images/Forward_Icon.png') }}" height="30px"></a></td>
                                                    <?php
                                                //}
                                                ?>
                                                    
                                                
                                            </tr>
                                        </table>
                                    </div>

                                    <?php
                                    // if (!isset($_SESSION["guest"]) && $_SESSION["guest"] == null) {
                                        ?>

                                    
                                        <?php
                                    // }
                                    ?>
                                </td>


                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
    </html>
    <?php
} else {
    echo "<br/><center>Please login to continue!<a href='memberarea'> Click Here</a></center>";
}
?>

