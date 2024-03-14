<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}"> 
        <title>Test Management</title>
        <link href="{{ asset('CSS/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{ asset('CSS/generic.css')}}" rel="stylesheet">
        <link href="{{ asset('CSS/HomePage.css')}}" rel="stylesheet">
        <link href="{{ asset('CSS/js-image-slider.css')}}" rel="stylesheet">
        <link href="{{ asset('CSS/n_list.css')}}" rel="stylesheet">
        <link href="{{ asset('CSS/outPage.css')}}" rel="stylesheet">
        <link href="{{ asset('CSS/outTMP1.css')}}" rel="stylesheet">
        <link href="{{ asset('CSS/ReportStyles.css')}}" rel="stylesheet">
        <link href="{{ asset('CSS/Stylie.css')}}" rel="stylesheet">
        <link href="{{ asset('CSS/workUI.css')}}" rel="stylesheet">
        <!-- DataTables CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css"/>
        <style>
            .arrow1{
                cursor: pointer;
            }
            .arrow2{
                cursor: pointer;
            }
            .arrow3{
                cursor: pointer;
            }
            .arrow-up {
            transform: rotateX(180deg);
            }
            .mySection{
                display: none;
            }
            .mySection2{
                display: none;
            }
            .mySection3{
                display: none;
            }
            .mySection4{
                display: none;
            }
            .mySection5{
                display: none;
            }
            .header-background{
                background-color: #000000;
            }
            .top{
                margin-top: 30px;
            }
            #noResults {
            display: none;
            color: red;
            text-align: center;
            }
            .analy{
                display: none;
            }
            .addana{
                display: none;
            }
            .highlighted {
                color: blue;
            }
            #myTable2 {
                display: none;
            }
            #myTable3 {
                display: none;
            }
            #myTable4 {
                display: none;
            }
            #myTable5 {
                display: none;
            }
            #myTable6 {
                display: none;
            }
            #myTable7 {
                display: none;
            }
            #myTable8 {
                display: none;
            }
            #myTable9 {
                display: none;
            }
            #myTable10 {
                display: none;
            }
            #myTable11 {
                display: none;
            }
        </style>
	</head>
	<body style="background-color:#F6F6F6">
        <div class="container top">
		<h3 style="color: darkblue; font-weight:bold;">TEST MANAGEMENT</h3><br/>
        
		<div class="naviPanal">
            <div class="row" style="margin-left: 5px;">
              <h3 style="color: darkblue;">Test Groups</h3></div>
		</div><br/>
        @if(\Session::has('error'))
           <div class="alert alert-danger">
                {{ \Session::get('error') }}
           </div>
        @endif
        @if(\Session::has('success'))
           <div class="alert alert-success">
                {{ \Session::get('success') }}
           </div>
        @endif
        <div>
            <input type="text" class="form-control" id="searchInput" name="tg-search" placeholder="Search Test Group"/>
        </div><br/>
        <div class="row">
        <div class="col-md-9">
        </div>
        <div class="col-md-3">
             <input type="checkbox" id="activeCheckbox"/>
             <label style="font-size: 12px; color:green">Active List</label>&nbsp &nbsp &nbsp &nbsp
             <input type="checkbox" id="inactiveCheckbox"/>
             <label style="font-size: 12px; color:red">Inactive List</label>
        </div>
        </div><br/>
        <div class="pageTableScope" style="height: 250px">
            <table id="myTable" class="table table-bordered">
                  <thead class="viewTHead">
                    <tr style="color: white">
                        <th>TGID</th>
                        <th>Test Group Name</th>
                        <th>Price<br/>(LKR)</th>
                        <th>Cost<br/>(LKR)</th>
                        <th>Time<br/>(Hours)</th>
                        <th>Test Code</th>
                        <th>Status</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($test as $tes)
                    
                    <?php $class = ''; ?>

                    @foreach ($lab_has_tests as $lab_has_test)
                        @if ($lab_has_test->Testgroup_tgid == $tes->tgid)
                            <?php $class = 'highlighted'; ?>
                        @endif
                    @endforeach
                    <tr class="{{$class}}">
                        <td>{{$tes->tgid}}</td>
                        <td class="name">{{$tes->name}}</td>
                        <td>{{$tes->price}}</td>
                        <td>{{$tes->cost}}</td>
                        <td>{{$tes->testingtime}}</td>
                        <td>{{$tes->testCode}}</td>
                        <td>
                            @if($tes->isActive == 1)
                               <div class="status" style="color: green;">Active</div>
                            @else
                               <div class="status" style="color: red;">Inactive</div>
                            @endif
                        </td>
                        <td><a class="btn1 btn-primary arrow4 fetchButton" data-testid="{{$tes->tgid}}">Select</a>&nbsp &nbsp</td>
                        <td>@if($tes->isActive == 1)<a href="{{URL::route('teststatuschange',['id' => $tes->tgid])}}" onclick="return confirm('Are you sure you want to inactivate this test group?');" class="btn1 btn-warning">Inactivate</a>@else<a href="{{URL::route('teststatuschange',['id' => $tes->tgid])}}" onclick="return confirm('Are you sure you want to activate this test group?');" class="btn1 btn-success">Activate</a>@endif&nbsp &nbsp</td>
                        <td><a href="{{URL::route('testdelete',['id' => $tes->tgid])}}" onclick="return confirm('Are you sure you want to delete this test group?');" class="btn1 btn-danger">Delete</a></td>
                    </tr>
                    @endforeach
                  </tbody>
            </table>
            <div id="noResults">No Results Found</div>
        </div><br/>
        <div class="row">
             <div class="col-md-9" style="font-size: 14px">
                 <b>Count : &nbsp{{$test_count}}</b> 
             </div>
             <div class="col-md-3" style="font-size: 14px">
                <div class="row">
                <div class="col-md-2" style="background-color: black; width: 5px; height: 15px;">
                </div>
                <div class="col-md-10"><b> Test groups without parameters</b></div>
                </div>
             </div>
        </div><br/><br/>
        <!--Manage Test Group Section-->
        <div class="naviPanal">
            <div class="row">
            <div class="col-md-11">
            <h3 style="color: darkblue">Manage Test Group</h3></div>
            <div style="font-size:25px;" class="arrow1 col-md-1">🔽</div>
            </div>
        </div><br/><br/>
        <div class="mySection">
        <form method="POST" action="{{ url('addtestgroup') }}">
        <div class="row">
            <div class="col-md-7">
                    <div style="margin-bottom: 10px;">
                        <div class="row">
                        <div class="col-md-2">
                        <label><h5>Name</h5></label>
                        </div>
                        <div class="col-md-10">
                        <input type="text" name="tg_name" class="form-control" placeholder="Enter Test Group Name" required/>
                        </div>
                        </div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <div class="row">
                          <div class="col-md-2">
                           <label><h5>Test Code</h5></label>
                          </div>
                          <div class="col-md-10">
                           <input type="text" name="tcode" class="form-control" placeholder="Enter Test Code" required/>
                          </div>
                    </div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <div class="row">
                            <div class="col-md-2">
                        <label><h5>Price(LKR)</h5></label></div>
                        <div class="col-md-10">
                        <input type="text" name="tg_price" class="form-control" placeholder="Enter Price" required/></div></div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <div class="row">
                            <div class="col-md-2">
                        <label><h5>Cost(LKR)</h5></label></div>
                        <div class="col-md-10">
                        <input type="text" name="tg_cost" class="form-control" placeholder="Enter Cost" required/></div></div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <div class="row">
                            <div class="col-md-2">
                        <label><h5>Duration(Hours)</h5></label></div>
                        <div class="col-md-10">
                        <input type="text" name="tg_time" class="form-control" placeholder="Enter Duration" required/></div></div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <div class="row">
                            <div class="col-md-2">
                        <label><h5>Sam.Container</h5></label></div>
                        <div class="col-md-10">
                        <select name="smpcon" class="form-control">
                            @foreach ( $sample_containers as $sample_container)
                             <option value="{{$sample_container->scid}}">{{ $sample_container->name }}</option>
                            @endforeach
                        </select></div></div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <div class="row">
                            <div class="col-md-2">
                        <label><h5>Rep.Category</h5></label></div>
                        <div class="col-md-10">
                        <select name="rptcat" class="form-control">
                            @foreach ( $test_categories as $test_category)
                            <option value="{{$test_category->tcid}}">{{ $test_category->name }}</option>
                            @endforeach
                       </select></div></div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <div class="row">
                            <div class="col-md-2">
                        <label><h5>Specimen</h5></label></div>
                        <div class="col-md-10">
                        <select name="spc" class="form-control">
                            @foreach ( $testinputs as $testinput)
                            <option value="{{ $testinput->tiid }}">{{ $testinput->name }}</option>
                            @endforeach
                        </select></div></div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <div class="row">
                            <div class="col-md-2">
                        <label><h5>Analyzer</h5></label></div>
                        <div class="col-md-10">
                        <select name="ana" class="form-control">
                            <option value="">Empty</option>
                            @foreach ( $analyzers as $analyzer)
                            <option value="{{$analyzer->anid}}">{{$analyzer->name}}</option>
                            @endforeach
                        </select></div></div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <div class="row">
                            <div class="col-md-2">
                        <label><h5>Section</h5></label></div>
                        <div class="col-md-10">
                        <select name="sec" class="form-control">
                            @foreach ( $tg_sections as $tg_section)
                            <option value="{{$tg_section->id}}">{{$tg_section->name}}</option>
                            @endforeach
                        </select></div></div>
                    </div><br/>
                    <div style="margin-bottom: 10px;">
                        <label><h5>Parameter Wise Barcode</h5></label>&nbsp
                        <input type="checkbox" name="par_wise_bar"/>
                    </div><br/>
                    <div style="margin-bottom: 10px;">
                        <label><h5>View Analyzer</h5></label>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                        <input type="checkbox" name="view_ana"/>
                    </div>
             </div>
            <div class="col-md-5">
                <div class="viewTHead">
                    <h3 style="color: darkblue">Test Sub Headings</h3>
                </div><br/>
                <div class="row" style="margin-top: -10px;">
                <div class="col-md-6">
                    <h4><b>1.Test Name Column</b></h4><br/>
                    <div style="margin-top: -20px;">
                    <label><h5><b>Name</b></h5></label>&nbsp &nbsp
                    <input type="text" name="tname" value="TEST" class="form-control" required/>
                    </div>
                    <div>
                    <label><h5><b>Width</b></h5></label>
                    <input type="range" name="twidth" id="twidthRange" min="250" max="900" step="10" value="250" oninput="updateTableWidth1(this.value)"/>
                    <span id="rangeValue1">250</span> px <br/><br/>
                    <table id="myTable2" class="table-bordered">
                        <thead>
                          <tr>
                            <th class="text-center">&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Test Name Column &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td></td>
                          </tr>
                          <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                    </div>
                    <div>
                    <label><h5><b>Align</b></h5></label>&nbsp
                    <select name="talign" class="form-control">
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                    </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <h4><b>2.Value Name Column</b></h4><br/>
                    <div style="margin-top: -20px;">
                    <label><h5><b>Name</b></h5></label>&nbsp &nbsp
                    <input type="text" name="vname" value="VALUE" class="form-control" required/>
                    </div>
                    <div>
                    <label><h5><b>Width</b></h5></label>
                    <input type="range" name="vwidth" id="vwidthRange" min="250" max="900" step="10" value="250" oninput="updateTableWidth2(this.value)"/>
                    <span id="rangeValue2">250</span> px <br/><br/>
                    <table id="myTable3" class="table-bordered">
                        <thead>
                          <tr>
                            <th class="text-center">&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Value Name Column &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td></td>
                          </tr>
                          <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                    </div>
                    <div>
                    <label><h5><b>Align</b></h5></label>&nbsp
                    <select name="valign" class="form-control">
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                    </select>
                    </div>
                </div>
                </div>
                <hr/>
                <div class="row">
                <div class="col-md-4">
                    <h4><b>3.Unit Name Column</b></h4><br/>
                    <div style="margin-top: -20px;">
                    <label><h5><b>Name</b></h5></label>&nbsp &nbsp
                    <input type="text" name="uname" value="UNIT" class="form-control" required style="width: 80px;"/>
                    </div>
                    <div>
                    <label><h5><b>Width</b></h5></label>
                    <input type="range" name="uwidth" id="uwidthRange" min="50" max="500" step="10" value="100" oninput="updateTableWidth3(this.value)"/>
                    <span id="rangeValue3">100</span> px <br/><br/>
                    <table id="myTable4" class="table-bordered">
                        <thead>
                          <tr>
                            <th class="text-center">&nbspUnit--Name&nbsp</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td></td>
                          </tr>
                          <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                    </div>
                    <div>
                    <label><h5><b>Align</b></h5></label>&nbsp
                    <select name="ualign" class="form-control">
                        <option value="left">Left</option>
                        <option value="center" selected>Center</option>
                    </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <h4><b>4.Flag Name Column</b></h4><br/>
                    <div style="margin-top: -20px;">
                    <label><h5><b>Name</b></h5></label>&nbsp &nbsp
                    <input type="text" name="fname" value="FLAG" class="form-control" required style="width: 80px;"/>
                    </div>
                    <div>
                    <label><h5><b>Width</b></h5></label>
                    <input type="range" name="fwidth" id="fwidthRange" min="50" max="500" step="10" value="100" oninput="updateTableWidth4(this.value)"/>
                    <span id="rangeValue5">100</span> px <br/><br/>
                    <table id="myTable5" class="table-bordered">
                        <thead>
                          <tr>
                            <th class="text-center">&nbspFlag--Name&nbsp</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td></td>
                          </tr>
                          <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                    </div>
                    <div>
                    <label><h5><b>Align</b></h5></label>&nbsp
                    <select name="falign" class="form-control">
                        <option value="left">Left</option>
                        <option value="center" selected>Center</option>
                    </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <h4><b>5.Ref. Range Column</b></h4><br/>
                    <div style="margin-top: -20px;">
                    <label><h5><b>Name</b></h5></label>&nbsp &nbsp
                    <input type="text" name="rname" value="REFERENCE RANGE" class="form-control" required style="width: 160px;"/>
                    </div>
                    <div>
                    <label><h5><b>Width</b></h5></label>
                    <input type="range" name="rwidth" id="rwidthRange" min="50" max="500" step="10" value="100" oninput="updateTableWidth5(this.value)"/>
                    <span id="rangeValue6">100</span> px <br/><br/>
                    <table id="myTable6" class="table-bordered">
                        <thead>
                          <tr>
                            <th class="text-center">&nbspRef --Name&nbsp</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td></td>
                          </tr>
                          <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                    </div>
                    <div>
                    <label><h5><b>Align</b></h5></label>&nbsp
                    <select name="ralign" class="form-control">
                        <option value="left">Left</option>
                        <option value="center" selected>Center</option>
                    </select>
                    </div>
                </div>
                </div>
            </div>  
        </div>
        <div>
                <label><h5>Test Comment</h5></label>
                <textarea id="editor" class="form-control" name="tcomment" rows="5"></textarea>
        </div><br/>
        <div>
            <input type="submit" value="Create" class="btn1 btn-success"/>
                    {{-- <input type="submit" value="Update" class="btn btn-success"/> --}}
        </div>
        <hr/>
        </form>
        <div class="mb-3">
            <a class="btn1 btn-success addann">Add Analyzer</a>
            <a class="btn1 anaview btn-primary">View Analyzers</a><br/><br/>
            <div class="addana">
                    {{ Form::open(array('url' => 'addanalyzer', 'method' => 'post')) }}
                              {{Form::text('ananame',null,['required'])}}
                              {{Form::submit('Save',['class'=>'btn1 btn-success'])}}
                    {{ Form::close() }}
            </div><br/>
            <div class="analy">
            <table class="table table-bordered">
                 <thead class="viewTHead">
                    <tr>
                        <th>anid</th>
                        <th>Name</th>
                        <th></th>
                    </tr>
                 </thead>
                 <tbody>
                    @foreach($analyzers as $analyzer)
                    <tr>
                        <td>{{$analyzer->anid}}</td>
                        <td>{{$analyzer->name}}</td>
                        <td>
                            <a href="{{URL::route('analyzerdelete',['id' => $analyzer->anid])}}" onclick="return confirm('Are you sure you want to delete this analyzer?');" class="btn1 btn-danger">Delete</a>
                        </td>
                    </tr>
                    @endforeach
                 </tbody>
            </table>
            </div>
         </div>
        </div>
        <div class="mySection4">
            <form method="POST" action="{{URL::route('updatetestgroup',['id' => ''])}}" id="tg_iddd">
            <div class="row">
                <div class="col-md-7" style="margin-top: -20px;">
                        <a class="btn1 btn-primary arrow6" id="resetButton">New</a>
                        <div class="mb-3">
                            {{-- <label><h6>TGID</h6></label>&nbsp --}}
                            <input type="hidden" id="tg_idd" name="tg_id" class="form-control" placeholder="Enter Test Group ID" required/>&nbsp &nbsp
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Name</h5></label></div>
                            <div class="col-md-10">
                            <input type="text" id="tg_named" name="tg_name" class="form-control" placeholder="Enter Test Group Name" required/></div></div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Test Code</h5></label></div>
                            <div class="col-md-10">
                            <input type="text" id="tg_coded" name="tcode" class="form-control" placeholder="Enter Test Code" required/></div></div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Price(LKR)</h5></label></div>
                            <div class="col-md-10">
                            <input type="text" id="tg_priced" name="tg_price" class="form-control" placeholder="Enter Price" required/></div></div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Cost(LKR)</h5></label></div>
                            <div class="col-md-10">
                            <input type="text" id="tg_costd" name="tg_cost" class="form-control" placeholder="Enter Cost" required/></div></div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Duration(Hours)</h5></label></div>
                            <div class="col-md-10">
                            <input type="text" id="tg_timed" name="tg_time" class="form-control" placeholder="Enter Duration" required/></div></div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Sam.Container</h5></label></div>
                            <div class="col-md-10">
                            <select name="smpcon" class="form-control" id="sm_con">
                                @foreach ( $sample_containers as $sample_container)
                                 <option value="{{$sample_container->scid}}">{{ $sample_container->name }}</option>
                                @endforeach
                            </select></div></div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Rep.Category</h5></label></div>
                            <div class="col-md-10">
                            <select name="rptcat" class="form-control" id="rep_cat">
                                @foreach ( $test_categories as $test_category)
                                <option value="{{$test_category->tcid}}">{{ $test_category->name }}</option>
                                @endforeach
                           </select></div></div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Specimen</h5></label></div>
                            <div class="col-md-10">
                            <select name="spc" class="form-control" id="spec">
                                @foreach ( $testinputs as $testinput)
                                <option value="{{ $testinput->tiid }}">{{ $testinput->name }}</option>
                                @endforeach
                            </select></div></div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Analyzer</h5></label></div>
                            <div class="col-md-10">
                            <select name="ana" class="form-control" id="anal">
                                <option value="empty">Empty</option>
                                @foreach ( $analyzers as $analyzer)
                                <option value="{{$analyzer->anid}}">{{$analyzer->name}}</option>
                                @endforeach
                            </select></div></div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Section</h5></label></div>
                            <div class="col-md-10">
                            <select name="sec" class="form-control" id="sect">
                                @foreach ( $tg_sections as $tg_section)
                                <option value="{{$tg_section->id}}">{{$tg_section->name}}</option>
                                @endforeach
                            </select></div></div>
                        </div><br/>
                        <div style="margin-bottom: 10px;">
                            <label><h5>Parameter Wise Barcode</h5></label>&nbsp
                            <input type="checkbox" name="par_wise_bar" id="par_wise_bard"/>
                        </div><br/>
                        <div style="margin-bottom: 10px;">
                            <label><h5>View Analyzer</h5></label>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                            <input type="checkbox" name="view_ana" id="view_anad"/>
                        </div>
                 </div>
                <div class="col-md-5">
                    <div class="viewTHead">
                        <h3 style="color: darkblue">Test Sub Headings</h3>
                    </div><br/>
                    <div class="row" style="margin-top: -10px;">
                        <div class="col-md-6">
                        <h4><b>1.Test Name Column</b></h4><br/>
                        <div style="margin-top: -20px;">
                        <label><h5><b>Name</b></h5></label>&nbsp &nbsp
                        <input type="text" name="tname" id="tname_id" class="form-control" required/>
                        </div>
                        <div>
                        <label><h5><b>Width</b></h5></label>
                        <input type="range" name="twidth" min="250" max="900" step="10" oninput="updateTableWidth11(this.value)"/>
                        <span id="rangeValue7">250</span> px <br/><br/>
                        <table id="myTable7" class="table-bordered">
                            <thead>
                              <tr>
                                <th class="text-center">&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Test Name Column &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td></td>
                              </tr>
                              <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                        </div>
                        <div>
                        <label><h5><b>Align</b></h5></label>&nbsp
                        <select name="talign" id="talign_id" class="form-control">
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                        </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4><b>2.Value Name Column</b></h4><br/>
                        <div style="margin-top: -20px;">
                        <label><h5><b>Name</b></h5></label>&nbsp &nbsp
                        <input type="text" name="vname" id="vname_id" class="form-control" required/>
                        </div>
                        <div>
                        <label><h5><b>Width</b></h5></label>
                        <input type="range" name="vwidth" min="250" max="900" step="10" oninput="updateTableWidth12(this.value)"/>
                        <span id="rangeValue8">250</span> px <br/><br/>
                        <table id="myTable8" class="table-bordered">
                            <thead>
                              <tr>
                                <th class="text-center">&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Value Name Column &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td></td>
                              </tr>
                              <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                        </div>
                        <div>
                        <label><h5><b>Align</b></h5></label>&nbsp &nbsp
                        <select name="valign" id="valign_id" class="form-control">
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                        </select>
                        </div>
                    </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-4">
                        <h4><b>3.Unit Name Column</b></h4><br/>
                        <div style="margin-top: -20px;">
                        <label><h5><b>Name</b></h5></label>&nbsp &nbsp
                        <input type="text" name="uname" id="uname_id" class="form-control" required style="width: 80px;"/>
                        </div>
                        <div>
                        <label><h5><b>Width</b></h5></label>
                        <input type="range" name="uwidth" min="50" max="500" step="10" oninput="updateTableWidth13(this.value)"/>
                        <span id="rangeValue9">100</span> px <br/><br/>
                        <table id="myTable9" class="table-bordered">
                            <thead>
                              <tr>
                                <th class="text-center">&nbspUnit--Name&nbsp</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td></td>
                              </tr>
                              <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                        </div>
                        <div>
                        <label><h5><b>Align</b></h5></label>&nbsp
                        <select name="ualign" id="ualign_id" class="form-control">
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                        </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h4><b>4.Flag Name Column</b></h4><br/>
                        <div style="margin-top: -20px;">
                        <label><h5><b>Name</b></h5></label>&nbsp &nbsp
                        <input type="text" name="fname" id="fname_id" class="form-control" required style="width: 80px;"/>
                        </div>
                        <div>
                        <label><h5><b>Width</b></h5></label>
                        <input type="range" name="fwidth" min="50" max="500" step="10" oninput="updateTableWidth14(this.value)"/>
                        <span id="rangeValue10">100</span> px <br/><br/>
                        <table id="myTable10" class="table-bordered">
                            <thead>
                              <tr>
                                <th class="text-center">&nbspFlag--Name&nbsp</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td></td>
                              </tr>
                              <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                        </div>
                        <div>
                        <label><h5><b>Align</b></h5></label>&nbsp
                        <select name="falign" id="falign_id" class="form-control">
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                        </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h4><b>5.Ref. Range Column</b></h4><br/>
                        <div style="margin-top: -20px;">
                        <label><h5><b>Name</b></h5></label>&nbsp &nbsp
                        <input type="text" name="rname" id="rname_id" class="form-control" required style="width: 160px;"/>
                        </div>
                        <div>
                        <label><h5><b>Width</b></h5></label>
                        <input type="range" name="rwidth" min="50" max="500" step="10" oninput="updateTableWidth15(this.value)"/>
                        <span id="rangeValue11">100</span> px <br/><br/>
                        <table id="myTable11" class="table-bordered">
                            <thead>
                              <tr>
                                <th class="text-center">&nbspRef --Name&nbsp</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td></td>
                              </tr>
                              <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                        </div>
                        <div>
                        <label><h5><b>Align</b></h5></label>&nbsp
                        <select name="ralign" id="ralign_id" class="form-control">
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                        </select>
                        </div>
                    </div>
                </div>
                </div>
            </div>
                <div>
                    <label><h5>Test Comment</h5></label>
                    <textarea id="editor1" class="form-control editor2" name="tcomment" rows="5"></textarea>
                </div><br/>
            <div>
                        {{-- <input type="submit" value="Create" class="btn btn-primary"/> --}}
                <input type="submit" onclick="submitForm()" value="Update" class="btn1 btn-success"/>
            </div>
            </form>
            <hr/>
            <div>
                        <a class="btn1 btn-success addann">Add Analyzer</a>
                        <a class="btn1 anaview btn-primary">View Analyzers</a><br/><br/>
                        <div class="addana">
                                {{ Form::open(array('url' => 'addanalyzer', 'method' => 'post')) }}
                                          {{Form::text('ananame',null,['required' => 'required'])}}
                                          {{Form::submit('Save',['class'=>'btn1 btn-success'])}}
                                {{ Form::close() }}
                        </div><br/>
                        <div class="analy">
                        <table class="table table-bordered">
                             <thead class="viewTHead">
                                <tr>
                                    <th>anid</th>
                                    <th>Name</th>
                                    <th></th>
                                </tr>
                             </thead>
                             <tbody>
                                @foreach($analyzers as $analyzer)
                                <tr>
                                    <td>{{$analyzer->anid}}</td>
                                    <td>{{$analyzer->name}}</td>
                                    <td>
                                        <a href="{{URL::route('analyzerdelete',['id' => $analyzer->anid])}}" onclick="return confirm('Are you sure you want to delete this analyzer?');" class="btn1 btn-danger">Delete</a>
                                    </td>
                                </tr>
                                @endforeach
                             </tbody>
                        </table>
                        </div>
                </div>
            </div>
        <!--Test Parameters Section-->
        <div class="naviPanal">
        <div class="row">
            <div class="col-md-11">
            <h3 style="color: darkblue">Test Parameters</h3></div>
            <div style="font-size:25px;" class="arrow3 col-md-1">🔽</div>
        </div>
        </div><br/>
        <div class="mySection3">
        <div style="margin-bottom: 10px;">
            <div class="row">
                <div class="col-md-2">
           <label><h6>Selected Test Name : </h6></label></div>
           <div class="col-md-10">
           <input type="text" id="tg_namedd" class="form-control" readonly/></div></div>
        </div>
        <div style="margin-bottom: 10px;">
            <div class="row">
                <div class="col-md-2">
            <label><h6>TGID :</h6></label></div>
            <div class="col-md-10">
            <input type="text" id="tg_idddd" class="form-control" readonly/></div></div>
        </div>
        <div class="pageTableScope" style="height: 150px">
            <form id="myForm6" method="POST" action="">
            <input type="hidden" id="tiddooo"/>
            {{-- <input type="hidden" id="tidwww"/>
            <input type="hidden" id="tidwwww"/> --}}
            <table class="table table-bordered" id="yourTableId">
                  <thead class="viewTHead">
                    <tr style="color: white">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Report Name</th>
                        <th>Unit</th>
                        <th>Ref.Min</th>
                        <th>Ref.Max</th>
                        <th>Order Number</th>
                        <th>LIS Id</th>
                        <th></th>
                    </tr>
                  </thead>
                  <tbody id="templateRow">
                  </tbody>
            </table>
            <div>
                  <input type="submit" value="Update Table" class="btn1 btn-success" onclick="submitFormnewestooooo(event)"/>
            </div><br/>
        </form>
        </div>
        </div><br/>
        <!--Manage Test Parameters Section-->
        <div class="naviPanal">
            <div class="row">
            <div class="col-md-11">
            <h3 style="color: darkblue">Manage Test Parameters</h3></div>
            <div style="font-size:25px;" class="arrow2 col-md-1">🔽</div>
            </div>
        </div><br/>
        <div class="mySection2">
        <form id="myForm10" method="POST" action="">
            <div class="row">
                <div class="col-md-7">
                        <a class="btn1 btn-primary arrow7">New</a><br/><br/>
                        {{-- <div class="mb-3"> --}}
                            {{-- <label><h6>ID</h6></label>&nbsp --}}
                            <input type="hidden" id="tidf" class="form-control">
                            {{-- <input type="hidden" name="tp_id" class="form-control" placeholder="Enter Test Parameter ID"/> --}}
                        {{-- </div> --}}
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Test Name</h5></label></div>
                            <div class="col-md-10">
                            <input type="text" id="test_namee" name="test_name" style="width:530px;" placeholder="Enter Test Name"/></div></div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Rep Name</h5></label></div>
                            <div class="col-md-10">
                            <input type="text" id="rep_namee" name="rep_name" style="width:530px;" placeholder="Enter Report Name"/></div></div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label><h5>Unit</h5></label>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                                <input type="text" id="unitt" name="unit" placeholder="Enter Unit"/>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label><h5>Reference Range</h5></label>
                                <input type="text" id="min_vall" name="min_val" placeholder="Min val" style="width: 90px;"/> -
                                <input type="text" id="max_vall" name="max_val" placeholder="Max Val" style="width: 90px;"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label><h5>Test Value Type</h5></label>&nbsp &nbsp
                                <select name="test_type" id="test_typee" style="width: 180px;">
                                    <option value=""></option>
                                    <option value="Integer">Integer</option>
                                    <option value="Decimal">Decimal</option>
                                    <option value="String">String</option>
                                    <option value="Negative/Postive">Negative/Positive</option>
                                    <option value="Paragraph">Paragraph</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label><h5>Character Count</h5></label>
                                <input type="text" id="chara_countt" name="chara_count" placeholder="Enter Character Count" style="width: 195px;"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label><h5>Value Range</h5></label>&nbsp &nbsp &nbsp &nbsp
                                </h6><input type="text" id="val_minn" name="val_min" placeholder="Min" style="width: 85px;"/> -
                                </h6><input type="text" id="val_maxx" name="val_max" placeholder="Max" style="width: 85px;"/>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label><h5>Decimal Points</h5></label>&nbsp 
                                <input type="text" id="deci_pointt" name="deci_point" placeholder="Decimal Points" style="width: 195px;"/>
                            </div>
                        </div>
                        <div class="row">
                        <div class="mb-3 col-md-6">
                            <label><h5>Default Value</h5></label>&nbsp &nbsp &nbsp
                            <input type="text" id="def_vall" name="def_val" placeholder="Enter Default Value"/>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label><h5>Test ID (LIS)</h5></label>&nbsp &nbsp &nbsp
                            <input type="text" id="test_iddd" name="test_id" placeholder="Enter Test ID" style="width: 195px;"/>
                        </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label><h5>Vertical Align</h5></label>&nbsp &nbsp &nbsp
                                <select id="alignn" name="align" style="width: 185px;">
                                    <option value="top">top</option>
                                    <option value="center">center</option>
                                    <option value="bottom">bottom</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label><h5>Bold Values</h5></label>&nbsp &nbsp &nbsp &nbsp
                                <input type="text" id="bold_vall" name="bold_val" style="width: 195px;"/>
                            </div></div><p style="color: red; font-size:12px;">Please add comma separated values for ‘Bold Values’.
                                Eg:- Positive,Reactive,Present</p>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label><h5>Order Number</h5></label>&nbsp &nbsp 
                                    <input type="text" id="ord_numm" name="ord_num" placeholder="Enter Order No"/>
                                </div>
                            </div>
                        <div class="mb-3">
                            <label><h5>View Normal Values</h5></label>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                            <input type="checkbox" id="nor_vall" name="nor_val"/>
                        </div>
                        <div class="mb-3">
                            <label><h5>Age and Gender Wise Reference</h5></label>&nbsp &nbsp
                            <input type="checkbox" id="gen_wise_reff" name="gen_wise_ref"/>
                        </div>
                        <div class="mb-3">
                            <label><h5>Hide When Empty</h5></label>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                            <input type="checkbox" id="hide_w_empp" name="hide_w_emp"/>
                        </div>
                        <div class="mb-3">
                            <label><h5>Selectable Results</h5></label>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                            <input type="checkbox" id="sel_ress" name="sel_res"/>
                        </div>
                        <div>
                            {{-- <input type="submit" value="Create" class="btn1 btn-success"/> --}}
                            <input type="submit" onclick="submitFormnewestooooonewww(event)" value="Update" class="btn1 btn-success"/>
                        </div><br/>
                       </form>
                        <form id="myForm" method="POST" action="#">
                        <div class="row">
                            <div class="col-md-7 mb-3"><label><h5>Values Suggestions</h5></label>
                                <input type="hidden" id="lhtiii"/>
                                <input type="text" id="val_sugg" name="val_sugg" style="width: 200px;" required/></div><div class="col-md-5 mb-3"><input type="submit" value="Add" class="btn1 btn-success" onclick="submitFormnew(event)"/></div>
                        </div>
                        </form>
                        <br/>
                        <div>
                            <table class="table table-bordered" id="yourTableId1">
                                  <thead class="viewTHead">
                                    <tr style="color: white">
                                        <th>Values</th>
                                        <th></th>
                                    </tr>
                                  </thead>
                                  <tbody id="templateRow1">
                                  </tbody>
                            </table>
                        </div>
                 </div>
                <div class="col-md-5">
                    <div class="viewTHead">
                        <h3 style="color: white">Age and Gender Wise Reference Ranges</h3>
                    </div><br/>
                    <form id="myForm2" method="POST" action="">
                        <input type="hidden" id="lhtiii"/>
                        <div class="row">
                        <div class="col-md-5">
                          <label><h5>Age Type</h5></label>
                          <select name="age_type">
                            <option value="days">Days</option>
                            <option value="months">Months</option>
                            <option value="years">Years</option>
                          </select>
                        </div>
                        <div class="col-md-7">
                          <label><h5>Age Range</h5></label>
                          <input type="text" id="min_agee" name="min_age" placeholder="Min" style="width: 80px;" required/> -
                          <input type="text" id="max_agee" name="max_age" placeholder="Max" style="width: 80px;" required/>
                        </div>
                        </div>
                        <div class="row">
                        <div class="col-md-5">
                        <label><h5>Gender</h5></label>&nbsp &nbsp
                        <select name="gender">
                            @foreach ( $genders as $gender)
                                <option value="{{$gender->idgender}}">{{$gender->gender}}</option>
                            @endforeach
                        </select>
                        </div>
                        <div class="col-md-7">
                            <label><h5>Ref. Range</h5></label>
                            <input type="text" id="min_reff" name="min_ref" placeholder="Min" style="width: 80px;" required/> -
                            <input type="text" id="max_reff" name="max_ref" placeholder="Max" style="width: 80px;" required/>
                        </div>
                        </div>
                        <input type="submit" value="Add" class="btn1 btn-success" onclick="submitFormnewest(event)"/><br/><br/>
                    </form>
                        <div>
                            <table class="table table-bordered" id="yourTableId2">
                                  <thead class="viewTHead">
                                    <tr style="color: white">
                                        <th>Age Type</th>
                                        <th>Age From</th>
                                        <th>Age To</th>
                                        <th>Gender</th>
                                        <th>Min Value</th>
                                        <th>Max Value</th>
                                        <th></th>
                                    </tr>
                                  </thead>
                                  <tbody id="templateRow2">
                                  </tbody>
                            </table>
                        </div>
                    <div class="viewTHead">
                        <h3 style="color: white">Add Material Consumption</h3>
                    </div><br/>
                    <form id="myForm3" method="POST" action="">
                        <div style="margin-bottom: 10px;">
                            <input type="hidden" id="lhtiii"/>
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Material</h5></label></div>
                            <div class="col-md-10">
                            <select name="material" style="width: 375px;">
                               @foreach ($materials as $material)
                                  <option value="{{$material->mid}}">{{$material->name}}</option>
                               @endforeach
                            </select></div></div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Value</h5></label></div>
                            <div class="col-md-10">
                            <input type="text" id="vallu" name="val" required style="width: 375px;"/></div></div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Unit</h5></label></div>
                            <div class="col-md-10">
                            <select name="unit" style="width: 375px;">
                                @foreach ($measurements as $measurement)
                                   <option value="{{$measurement->msid}}">{{$measurement->name}}</option>
                                @endforeach
                            </select></div></div>
                        </div>
                        <input type="submit" value="Add" class="btn1 btn-success" onclick="submitFormnewestnew(event)"/>
                    </form><br/>
                    <div>
                        <table class="table table-bordered" id="yourTableId3">
                              <thead class="viewTHead">
                                <tr style="color: white">
                                    <th>Material Name</th>
                                    <th>Value</th>
                                    <th>Unit</th>
                                    <th></th>
                                </tr>
                              </thead>
                              <tbody id="templateRow3">
                              </tbody>
                        </table>
                    </div>
                </div>
                </div>
        </div>
        <div class="mySection5">
        <form id="myForm4" method="POST" action="">
                        {{-- <a class="btn1 btn-primary">New</a><br/><br/> --}}
                        {{-- <div class="mb-3"> --}}
                            {{-- <label><h6>ID</h6></label>&nbsp --}}
                            <input type="hidden" id="tg_idddd" class="form-control" readonly/>
                            {{-- <input type="hidden" name="tp_id" class="form-control" placeholder="Enter Test Parameter ID"/> --}}
                        {{-- </div> --}}
                        <div class="row">
                            <div class="col-md-7">
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Test Name</h5></label></div>
                            <div class="col-md-10">
                            <input type="text" id="test_nameee" name="test_name" style="width:530px;" placeholder="Enter Test Name" required/></div></div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-2">
                            <label><h5>Rep Name</h5></label></div>
                            <div class="col-md-10">
                            <input type="text" id="rep_nameee" name="rep_name" style="width:530px;" placeholder="Enter Report Name" required/></div></div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                             <label><h5>Unit</h5></label>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp 
                             <input type="text" id="uniiiiiiiii" name="unit" placeholder="Enter Unit"/>
                            </div>
                            <div class="mb-3 col-md-6">
                            <label><h5>Reference Range</h5></label>
                            <input type="text" id="minvvv" name="min_val" placeholder="Min val" style="width: 90px;"/> -
                            <input type="text" id="maxvvv" name="max_val" placeholder="Max Val" style="width: 90px;"/>
                        </div></div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label><h5>Test Value Type</h5></label>&nbsp &nbsp
                                <select name="test_type" id="testType" style="width: 180px;" onchange="toggleDecimalPoints()">
                                    <option value=""></option>
                                    <option value="Integer">Integer</option>
                                    <option value="Decimal">Decimal</option>
                                    <option value="String">String</option>
                                    <option value="Negative/Postive">Negative/Positive</option>
                                    <option value="Paragraph">Paragraph</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label><h5>Character Count</h5></label>
                                <input type="text" value={{255}} name="chara_count" placeholder="Enter Character Count" style="width: 195px;"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label><h5>Value Range</h5></label>&nbsp &nbsp &nbsp &nbsp
                                </h6><input type="text" id="val_min" name="val_min" placeholder="Min" style="width: 85px;"/> -
                                </h6><input type="text" id="val_max" name="val_max" placeholder="Max" style="width: 85px;"/>
                            </div>
                            <div class="mb-3 col-md-6" id="decimalPointsContainer" style="display: none;">
                                <label><h5>Decimal Points</h5></label>&nbsp 
                                <input type="text" id="deci_pointttt" name="deci_point" placeholder="Decimal Points" style="width: 195px;"/>
                            </div>
                        </div>
                        <div class="row">
                        <div class="mb-3 col-md-6">
                            <label><h5>Default Value</h5></label>&nbsp &nbsp &nbsp
                            <input type="text" id="def_vallll" name="def_val" placeholder="Enter Default Value"/>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label><h5>Test ID (LIS)</h5></label>&nbsp &nbsp &nbsp
                            <input type="text" id="test_idddddddd" name="test_id" placeholder="Enter Test ID" style="width: 195px;"/>
                        </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label><h5>Vertical Align</h5></label>&nbsp &nbsp &nbsp
                                <select id="alignn" name="align" style="width: 185px;">
                                    <option value="top">top</option>
                                    <option value="center">center</option>
                                    <option value="bottom">bottom</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label><h5>Bold Values</h5></label>&nbsp &nbsp &nbsp &nbsp
                                <input type="text" id="bold_valuuu" name="bold_val" style="width: 195px;"/>
                            </div></div><p style="color: red; font-size:12px;">Please add comma separated values for ‘Bold Values’.
                                Eg:- Positive,Reactive,Present</p>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label><h5>Order Number</h5></label>&nbsp &nbsp 
                                    <input type="text" id="ord_numee" name="ord_num" placeholder="Enter Order No"/>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label><h5>View Normal Values</h5></label>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                                <input type="checkbox" id="nor_vallll" name="nor_val"/>
                            </div>
                            <div class="mb-3">
                                <label><h5>Age and Gender Wise Reference</h5></label>&nbsp &nbsp
                                <input type="checkbox" id="gen_wise_reffff" name="gen_wise_ref"/>
                            </div>
                            <div class="mb-3">
                                <label><h5>Hide When Empty</h5></label>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                                <input type="checkbox" id="hide_w_empppp" name="hide_w_emp"/>
                            </div>
                            <div class="mb-3">
                                <label><h5>Selectable Results</h5></label>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp
                                <input type="checkbox" id="sel_ressss" name="sel_res"/>
                            </div>
                        </div>
                        </div>
                        <div>
                            <input type="submit" value="Create" onclick="submitFormnewestvvvvvvv(event)" class="btn1 btn-success"/>
                                    {{-- <input type="submit" value="Update" class="btn btn-success"/> --}}
                        </div><br/><br/>
                    </form>
                 </div>
                </div>
        </div>
        </div>
        <br/>
        <script src="{{ asset('JS/bootstrap.min.js') }}"></script>
        <script src="{{ asset('JS/jquery-3.1.0.js') }}"></script>
        <script src="https://cdn.tiny.cloud/1/bo9ym7qdgpx1uhqi4qis54jf08xuaorp76xcmdrdvbv6fhg0/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> 
        
        <!-- DataTables JS -->
        <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
        <script>

            $(document).ready(function(){
                   $(".arrow1").click(function(){
                      $(".mySection").toggle();
                      $(".mySection3").hide();
                      $(".mySection4").hide();
                      $(this).toggleClass("arrow-up");
                   });
            
                   $(".arrow2").click(function(){
                      $(".mySection5").toggle();
                      $(".mySection2").hide();
                      $(this).toggleClass("arrow-up");
                   });

                   $(".arrow3").click(function(){
                      $(".mySection3").toggle();
                      $(this).toggleClass("arrow-up");
                   });

                   $(".arrow4").click(function(){
                      $(".mySection4").toggle();
                      $(".mySection3").toggle();
                      $(".mySection").hide();
                    //   $(this).toggleClass("arrow-new-up");
                   });

                   $(document).on('click', '.arrow5', function() {
                      $(".mySection2").toggle();
                      $(".mySection5").hide();
                   });

                   $(".anaview").click(function(){
                      $(".analy").toggle();
                    //   $(this).toggleClass("arrow-up");
                   });

                   $(".addann").click(function(){
                      $(".addana").toggle();
                    //   $(this).toggleClass("arrow-up");
                   });

                   $(".arrow6").click(function(){
                      $(".mySection").toggle();
                      $(".mySection4").hide();
                      $(".mySection3").hide();
                    //   $(this).toggleClass("arrow-up");
                   });

                   $(".arrow7").click(function(){
                      $(".mySection5").toggle();
                      $(".mySection2").hide();
                    //   $(".mySection3").hide();
                    //   $(this).toggleClass("arrow-up");
                   });

                   $('#resetButton').on('click', function() {
                        $('input[name="twidth"]').val($('input[name="twidth"]').prop('defaultValue'));
                        $('input[name="vwidth"]').val($('input[name="vwidth"]').prop('defaultValue'));
                        $('input[name="uwidth"]').val($('input[name="uwidth"]').prop('defaultValue'));
                        $('input[name="fwidth"]').val($('input[name="fwidth"]').prop('defaultValue'));
                        $('input[name="rwidth"]').val($('input[name="rwidth"]').prop('defaultValue'));
                   });

            });

        </script>
        <script>
        $(document).ready(function () {
                
                $('input[type="checkbox"]').change(function () {
                    filterTable();
                });
    
                // Search input keyup event
                $('#searchInput').keyup(function () {
                    filterTable();
                });

      
        $('.fetchButton').click(function() {
            var tidValues = [];
            var rowId = $(this).data('testid');
             console.log(rowId);
            // Make an AJAX request to the server
            $.ajax({
                type: 'GET',
                url: '/testgroupdata/' + rowId,
                success: function(response) {
                    // Update the separate div based on the server response
                    console.log(response);
                    // var response = [];

                    var editor = tinymce.get('editor1');
                    $('#tg_idd').val(response.tgid);
                    $('#tg_iddd').val(response.tgid);
                    $('#tg_idddd').val(response.tgid);
                    // $('#tg_idddd').val(response[0].tgid);
                    $('#tg_named').val(response.name);
                    // $('#unit_lab').val(response[1][0].measurement);
                    // $('#rep_nam').val(response[1][0].reportname);
                    // $('#ord_nam').val(response[1][0].orderno);
                    $('#tg_namedd').val(response.name);
                    // $('#tg_namedd').val(response[0].name);
                    $('#tg_coded').val(response.testCode);
                    // $('#tg_coded').val(response[0].testCode);
                    $('#tg_priced').val(response.price);
                    // $('#tg_priced').val(response[0].price);
                    $('#tg_costd').val(response.cost);
                    // $('#tg_costd').val(response[0].cost);
                    $('#tg_timed').val(response.testingtime);
                    // $('#tg_timed').val(response[0].testingtime);
                    $('#sm_con').val(response.sample_containers_scid);
                    // $('#sm_con').val(response[0].sample_containers_scid);
                    $('#rep_cat').val(response.testingcategory_tcid);
                    // $('#rep_cat').val(response[0].testingcategory_tcid);
                    $('#spec').val(response.testinginput_tiid);
                    // $('#spec').val(response[0].testinginput_tiid);
                    $('#anal').val(response.analyzers_anid);
                    // $('#anal').val(response[0].analyzers_anid);
                    $('#sect').val(response.tgsection_id);
                    // $('#sect').val(response[0].tgsection_id);
                    $('#par_wise_bard').prop('checked', response.parameter_wise_barcode == 1);
                    // $('#par_wise_bard').prop('checked', response[0].parameter_wise_barcode == 1);
                    $('#view_anad').prop('checked', response.view_analyzer == 1);
                    // $('#view_anad').prop('checked', response[0].view_analyzer == 1);
                    // $('#editor1').val(response.comment);
                    if (editor) {
    // Set the content of the TinyMCE editor
                    editor.setContent(response.comment);
                    // editor.setContent(response[0].comment);
                    } else {
    // Fallback to setting the content of the original textarea
                    $('#editor1').val(response.comment);
                    // $('#editor1').val(response[0].comment);
                    }
                    $('#tname_id').val(response.name_col_head);
                    $('input[name="twidth"]').val(response.name_col_width);
                    $('#talign_id').val(response.name_col_align);

                    $('#vname_id').val(response.value_col_head);
                    $('input[name="vwidth"]').val(response.value_col_width);
                    $('#valign_id').val(response.result_col_align);

                    $('#uname_id').val(response.unit_col_head);
                    $('input[name="uwidth"]').val(response.unit_col_width);
                    $('#ualign_id').val(response.unit_col_align);

                    $('#fname_id').val(response.flag_col_head);
                    $('input[name="fwidth"]').val(response.flag_col_width);
                    $('#falign_id').val(response.flag_col_align);

                    $('#rname_id').val(response.ref_col_head);
                    $('input[name="rwidth"]').val(response.ref_col_width);
                    $('#ralign_id').val(response.ref_col_align);

                    $('#rangeValue7').text(response.name_col_width);
                    $('#rangeValue8').text(response.value_col_width);
                    $('#rangeValue9').text(response.unit_col_width);
                    $('#rangeValue10').text(response.flag_col_width);
                    $('#rangeValue11').text(response.ref_col_width);
                    
                    
                    // $('#updatetestgroupdiv').html(response);
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });

            // $("#yourTableId tbody").html("");
            $.ajax({
              type: 'GET',
              url: '/gettestparameters/' + rowId,
              success: function(response) {
              console.log(response);
        
            //   tidValues = [];
              for (var i = 0; i < response.length && i < 60; i++) {
                  tidValues.push(response[i].tid);
              }
              console.log(tidValues);
              var concatenatedValues = tidValues.join(',');

        // Set the concatenated values to the hidden input
              $('#tiddooo').val(concatenatedValues);
        // Clear existing rows

              $('#templateRow').html("");
            //  var templateRow = $('#templateRow').clone().removeAttr('style').removeAttr('id');
              for (var i = 0; i < response.length; i++) {
                  var rowData = response[i];
                  console.log(response);
                  var tr="<tr><td width='80px'><input type='text' id='tid' value='"+ rowData.tid +"' class='form-control' readonly/></td><td width='180px'><input type='text' id='tname' value='"+ rowData.name +"' class='form-control' readonly/></td><td width='170px'><input type='text' id='rep_nam' name='rep_namm[]' value='"+ rowData.reportname +"' class='form-control'/></td><td width='100px'><input type='text' id='unit_lab' name='uniit[]' value='"+ rowData.measurement +"' class='form-control'/></td><td width='80px'><input type='text' id='reff_min' name='refer_min[]' value='"+ rowData.refference_min +"' class='form-control'/></td><td width='80px'><input type='text' id='reff_max' name='refer_max[]' value='"+ rowData.refference_max +"' class='form-control'/></td><td width='90px'><input type='text' id='ord_nam' name='ord_nummm[]' value='"+ rowData.orderno +"' class='form-control'/></td><td width='90px'><input type='text' id='liss_id' name='lisss_id[]' value='"+ rowData.listestid +"' class='form-control'/></td><td><a class='btn1 btn-primary arrow5 fetchButtonnew yourElement' data-testid='" + rowData.lhtid + "'>Select</a>&nbsp &nbsp<a data-testid='" + rowData.lhtid + "' class='btn1 btn-danger yourElementttttt fetchButtonnewwwwwwwwwww'>Inactive</a></td></tr>";
            // Update table cells with data
                //   var newRow = templateRow.clone();
                //   newRow.find('#tidw').val(rowData.tid);
                //   newRow.find('#tid').val(rowData.tid);
                //   newRow.find('#tidww').val(rowData.tid);
                //   newRow.find('#tname').val(rowData.name);
                //   newRow.find('#rep_nam').val(rowData.reportname);
                //   newRow.find('#unit_lab').val(rowData.measurement);
                //   newRow.find('#reff_min').val(rowData.refference_min);
                //   newRow.find('#reff_max').val(rowData.refference_max);
                //   newRow.find('#ord_nam').val(rowData.orderno);
                //   newRow.find('#liss_id').val(rowData.listestid);
                //   newRow.find('#lhtidd').val(rowData.lhtid);

            // // Append the new row to the table body
            //     //   $('#yourTableId tbody').remove();
                  $('#templateRow').append(tr);

            // Update the data-testid attribute for each row
                //   var dataTestId = rowData.lhtid;
                //   console.log(dataTestId);
                //   newRow.find('.yourElement').attr('data-testid', dataTestId);
                //   newRow.find('.yourElementttttt').attr('data-testid', dataTestId);
                }

                },

                error: function(error) {
                   console.error('Error:', error);
                }
            });

        });
    
                function filterTable() {
                   
                    var activeCheckbox = $('#activeCheckbox');
                    var inactiveCheckbox = $('#inactiveCheckbox');
                    var searchInput = $('#searchInput').val().toLowerCase();
                    var noResults = true;
    
                    $('#myTable tbody tr').each(function () {
                        var name = $(this).find('.name').text().toLowerCase();
                        var status = $(this).find('.status').text().toLowerCase();
                        var displayRow = true;

                        console.log(status);
                        
                        if (activeCheckbox.is(':checked') && status !== 'active') {
                            displayRow = false;
                        } 
                        else if (inactiveCheckbox.is(':checked') && status !== 'inactive') {
                            displayRow = false;
                        }
    
                        if (searchInput && name.indexOf(searchInput) === -1) {
                            displayRow = false;
                        }
    
                        $(this).toggle(displayRow);

                        if (displayRow) {
                        noResults = false;
                        }
                    });
                
                    $('#noResults').toggle(noResults);
                }
            });
        
                function updateTableWidth1(value) {
    // Get the table element
                   var table = document.getElementById("myTable2");

                   var headerCells = table.rows[0].getElementsByTagName("th");

    // Update the width of each header cell based on the range value
                   for (var i = 0; i < headerCells.length; i++) {
                       headerCells[i].style.width = value + "px";
                   }

    // Get all the data cells (td elements) in the tbody
                   var dataCells = table.getElementsByTagName("td");

    // Update the width of each data cell based on the range value
                   for (var i = 0; i < dataCells.length; i++) {
                       dataCells[i].style.width = value + "px";
                   }

    // Update the displayed range value
                   document.getElementById("rangeValue1").innerText = value;
    // document.getElementById("rangeValue2").innerText = value;
                }
                function updateTableWidth2(value) {
    // Get the table element
                   var table = document.getElementById("myTable3");

                   var headerCells = table.rows[0].getElementsByTagName("th");

    // Update the width of each header cell based on the range value
                   for (var i = 0; i < headerCells.length; i++) {
                       headerCells[i].style.width = value + "px";
                   }

    // Get all the data cells (td elements) in the tbody
                   var dataCells = table.getElementsByTagName("td");

    // Update the width of each data cell based on the range value
                   for (var i = 0; i < dataCells.length; i++) {
                       dataCells[i].style.width = value + "px";
                   }

    // Update the displayed range value
                   document.getElementById("rangeValue2").innerText = value;
    // document.getElementById("rangeValue2").innerText = value;
                }

                function updateTableWidth3(value) {
    // Get the table element
                   var table = document.getElementById("myTable4");

                   var headerCells = table.rows[0].getElementsByTagName("th");

    // Update the width of each header cell based on the range value
                   for (var i = 0; i < headerCells.length; i++) {
                       headerCells[i].style.width = value + "px";
                   }

    // Get all the data cells (td elements) in the tbody
                   var dataCells = table.getElementsByTagName("td");

    // Update the width of each data cell based on the range value
                   for (var i = 0; i < dataCells.length; i++) {
                       dataCells[i].style.width = value + "px";
                   }

    // Update the displayed range value
                   document.getElementById("rangeValue3").innerText = value;
    // document.getElementById("rangeValue2").innerText = value;
                }

                function updateTableWidth4(value) {
    // Get the table element
                   var table = document.getElementById("myTable5");

                   var headerCells = table.rows[0].getElementsByTagName("th");

    // Update the width of each header cell based on the range value
                   for (var i = 0; i < headerCells.length; i++) {
                       headerCells[i].style.width = value + "px";
                   }

    // Get all the data cells (td elements) in the tbody
                   var dataCells = table.getElementsByTagName("td");

    // Update the width of each data cell based on the range value
                   for (var i = 0; i < dataCells.length; i++) {
                       dataCells[i].style.width = value + "px";
                   }

    // Update the displayed range value
                   document.getElementById("rangeValue5").innerText = value;
    // document.getElementById("rangeValue2").innerText = value;
                }

                function updateTableWidth5(value) {
    // Get the table element
                   var table = document.getElementById("myTable6");

                   var headerCells = table.rows[0].getElementsByTagName("th");

    // Update the width of each header cell based on the range value
                   for (var i = 0; i < headerCells.length; i++) {
                       headerCells[i].style.width = value + "px";
                   }

    // Get all the data cells (td elements) in the tbody
                   var dataCells = table.getElementsByTagName("td");

    // Update the width of each data cell based on the range value
                   for (var i = 0; i < dataCells.length; i++) {
                       dataCells[i].style.width = value + "px";
                   }

    // Update the displayed range value
                   document.getElementById("rangeValue6").innerText = value;
    // document.getElementById("rangeValue2").innerText = value;
                }

                function updateTableWidth11(value) {
    // Get the table element
                   var table = document.getElementById("myTable7");

                   var headerCells = table.rows[0].getElementsByTagName("th");

    // Update the width of each header cell based on the range value
                   for (var i = 0; i < headerCells.length; i++) {
                       headerCells[i].style.width = value + "px";
                   }

    // Get all the data cells (td elements) in the tbody
                   var dataCells = table.getElementsByTagName("td");

    // Update the width of each data cell based on the range value
                   for (var i = 0; i < dataCells.length; i++) {
                       dataCells[i].style.width = value + "px";
                   }

    // Update the displayed range value
                   document.getElementById("rangeValue7").innerText = value;
    // document.getElementById("rangeValue2").innerText = value;
                }

                function updateTableWidth12(value) {
    // Get the table element
                   var table = document.getElementById("myTable8");

                   var headerCells = table.rows[0].getElementsByTagName("th");

    // Update the width of each header cell based on the range value
                   for (var i = 0; i < headerCells.length; i++) {
                       headerCells[i].style.width = value + "px";
                   }

    // Get all the data cells (td elements) in the tbody
                   var dataCells = table.getElementsByTagName("td");

    // Update the width of each data cell based on the range value
                   for (var i = 0; i < dataCells.length; i++) {
                       dataCells[i].style.width = value + "px";
                   }

    // Update the displayed range value
                   document.getElementById("rangeValue8").innerText = value;
    // document.getElementById("rangeValue2").innerText = value;
                }

                function updateTableWidth13(value) {
    // Get the table element
                   var table = document.getElementById("myTable9");

                   var headerCells = table.rows[0].getElementsByTagName("th");

    // Update the width of each header cell based on the range value
                   for (var i = 0; i < headerCells.length; i++) {
                       headerCells[i].style.width = value + "px";
                   }

    // Get all the data cells (td elements) in the tbody
                   var dataCells = table.getElementsByTagName("td");

    // Update the width of each data cell based on the range value
                   for (var i = 0; i < dataCells.length; i++) {
                       dataCells[i].style.width = value + "px";
                   }

    // Update the displayed range value
                   document.getElementById("rangeValue9").innerText = value;
    // document.getElementById("rangeValue2").innerText = value;
                }

                function updateTableWidth14(value) {
    // Get the table element
                   var table = document.getElementById("myTable10");

                   var headerCells = table.rows[0].getElementsByTagName("th");

    // Update the width of each header cell based on the range value
                   for (var i = 0; i < headerCells.length; i++) {
                       headerCells[i].style.width = value + "px";
                   }

    // Get all the data cells (td elements) in the tbody
                   var dataCells = table.getElementsByTagName("td");

    // Update the width of each data cell based on the range value
                   for (var i = 0; i < dataCells.length; i++) {
                       dataCells[i].style.width = value + "px";
                   }

    // Update the displayed range value
                   document.getElementById("rangeValue10").innerText = value;
    // document.getElementById("rangeValue2").innerText = value;
                }

                function updateTableWidth15(value) {
    // Get the table element
                   var table = document.getElementById("myTable11");

                   var headerCells = table.rows[0].getElementsByTagName("th");

    // Update the width of each header cell based on the range value
                   for (var i = 0; i < headerCells.length; i++) {
                       headerCells[i].style.width = value + "px";
                   }

    // Get all the data cells (td elements) in the tbody
                   var dataCells = table.getElementsByTagName("td");

    // Update the width of each data cell based on the range value
                   for (var i = 0; i < dataCells.length; i++) {
                       dataCells[i].style.width = value + "px";
                   }

    // Update the displayed range value
                   document.getElementById("rangeValue11").innerText = value;
    // document.getElementById("rangeValue2").innerText = value;
                }

                function submitForm() {
        // Get the value from the input field with id 'tg_iddd'
                   var idValue = $('#tg_iddd').find('#tg_idd').val();

        // Set the value in the URL for the form action
                   $('#tg_iddd').attr('action', '{{ url("updatetestgroup") }}/' + idValue);

        // Submit the form
                   $('#tg_iddd').submit();

                }

        //         function submitForm2() {
        // // Get the value from the input field with id 'tg_iddd'
        //            var idValue1 = $('#tidd').find('#tidf').val();

        // // Set the value in the URL for the form action
        //            $('#tidd').attr('action', '{{ url("updatetestparametes") }}/' + idValue1);

        // // Submit the form
        //            $('#tidd').submit();

        //         }

                // function submitForm3() {

                //    var ids = $('#tidlddd').find('#tiddooo').val();

                //    var url = '{{ url("updateselectedtestparametes") }}/' + ids;

                //    $('#tidlddd').attr('action', url);

                //    $('#tidlddd').submit();

                // }

                tinymce.init({
                   selector: '#editor',
                   height: 420,  // Set the height as needed
                   plugins: 'table',
                   toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | table',
                });

                tinymce.init({
                   selector: '.editor2',
                   height: 380,  // Set the height as needed
                   plugins: 'table',
                   toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | table',
                });
  

        </script>
        <script>
        $(document).ready(function() {
            $('#yourTableId').on('click', '.fetchButtonnew.yourElement', function() {
            var rowIdd = $(this).data('testid');
            console.log(rowIdd);
            // Make an AJAX request to the server
            $.ajax({
                type: 'GET',
                url: '/edittestparameters/' + rowIdd,
                success: function(response) {
                    // Update the separate div based on the server response
                    console.log(response);
                    // var response = [];
                    $('#tidf').val(response.tid);
                    $('#test_namee').val(response.name);
                    $('#rep_namee').val(response.reportname);
                    $('#unitt').val(response.measurement);
                    $('#min_vall').val(response.refmin);
                    $('#max_vall').val(response.refmax);
                    $('#ord_numm').val(response.orderno);
                    $('#test_iddd').val(response.listestid);
                    $('#test_typee').val(response.decimal);
                    $('#chara_countt').val(response.charcount);
                    $('#val_minn').val(response.minval);
                    $('#val_maxx').val(response.maxval);
                    $('#deci_pointt').val(response.decipoint);
                    $('#nor_vall').prop('checked', response.normalvalue == 1);
                    $('#def_vall').val(response.defval);
                    $('#gen_wise_reff').prop('checked', response.advnceref == 1);
                    $('#hide_w_empp').prop('checked', response.hidewhenempty == 1);
                    $('#sel_ress').prop('checked', response.selctval == 1);
                    $('#alignn').val(response.align);
                    $('#bold_vall').val(response.boldvalues);
                    $('#lhtiii').val(response.lhtid);
                    // $('#updatetestgroupdiv').html(response);
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
            $.ajax({
                type: 'GET',
                url: '/getvaluesuggestions/' + rowIdd,
                success: function(response) {
                    // Update the separate div based on the server response
                    console.log(response);
                    // var response = [];
                    // $('#tid').val(response.lhttid);
                    // $('#tidd').val(response.lhttid);
                    // $('#tname').val(response.testname);
                    // $('#rep_nam').val(response.reportname);
                    // $('#unit_lab').val(response.measurement);
                    // $('#reff_min').val(response.refference_min);
                    // $('#reff_max').val(response.refference_max);
                    // $('#ord_nam').val(response.orderno);
                    // $('#liss_id').val(response.listestid);
            //         var templateRow = $('#templateRow1').clone().removeAttr('style').removeAttr('id');
            //         for (var i = 0; i < response.length; i++) {
            //         var rowData = response[i];
            //         var newRow = templateRow.clone();
            // // newRow.find('#tidw').val(rowData.tid);
            //         newRow.find('#val_name').val(rowData.value);
            //         newRow.find('#iddss').val(rowData.id);
            
            $('#templateRow1').html("");
            //  var templateRow = $('#templateRow').clone().removeAttr('style').removeAttr('id');
              for (var i = 0; i < response.length; i++) {
                  var rowData = response[i];
                  console.log(response);
                  var tr="<tr><td><input type='text' value='"+ rowData.value +"' class='form-control' style='width: 200px; border: none' readonly/></td><td><a class='btn1 btn-danger fetchButtonnew111 yourElement2' data-testid='" + rowData.id + "'>Delete</a></td></tr>";
            // Append the new row to the table body
                    // $('#yourTableId1 tbody').append(newRow);
                  $('#templateRow1').append(tr);
            // Update the data-testid attribute for each row
                    // var dataTestId = rowData.id;
                    // console.log(dataTestId);
                    // newRow.find('.yourElement2').attr('data-testid', dataTestId);
                 }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
            $.ajax({
                type: 'GET',
                url: '/getreferencevalues/' + rowIdd,
                success: function(response) {
                    // Update the separate div based on the server response
                    console.log(response);
                    // var response = [];
                    // $('#tid').val(response.lhttid);
                    // $('#tidd').val(response.lhttid);
                    // $('#tname').val(response.testname);
                    // $('#rep_nam').val(response.reportname);
                    // $('#unit_lab').val(response.measurement);
                    // $('#reff_min').val(response.refference_min);
                    // $('#reff_max').val(response.refference_max);
                    // $('#ord_nam').val(response.orderno);
                    // $('#liss_id').val(response.listestid);
            //         var templateRow = $('#templateRow2').clone().removeAttr('style').removeAttr('id');
            //         for (var i = 0; i < response.length; i++) {
            //         var rowData = response[i];
            //         var newRow = templateRow.clone();
            // // newRow.find('#tidw').val(rowData.tid);
            //         newRow.find('#agetype').val(rowData.ageType);
            //         newRow.find('#agefrom').val(rowData.ageMin);
            //         newRow.find('#ageto').val(rowData.ageMax);
            //         newRow.find('#gen').val(rowData.gender_idgender == 1 ? 'Male' : 'Female');
            //         newRow.find('#min_val').val(rowData.rangeMin);
            //         newRow.find('#max_val').val(rowData.rangeMax);
            //         newRow.find('#lhtiddd').val(rowData.Lab_has_test_lhtid);
            //         newRow.find('#idref').val(rowData.id);

            //         $('#yourTableId2 tbody').append(newRow);

            // // Update the data-testid attribute for each row
            //         var dataTestId = rowData.id;
            //         console.log(dataTestId);
            //         newRow.find('.yourElement3').attr('data-testid', dataTestId);
            
            //         }
            $('#templateRow2').html("");
            //  var templateRow = $('#templateRow').clone().removeAttr('style').removeAttr('id');
              for (var i = 0; i < response.length; i++) {
                  var rowData = response[i];
                  console.log(response);
                  console.log(rowData.ageType);
                  var tr="<tr><td style='width: 40px'><input type='text' value='"+ rowData.ageType +"' class='form-control' style='width: 60px; border: none' readonly/></td><td style='width: 40px'><input type='text' value='"+ rowData.ageMin +"' class='form-control' style='width: 40px; border: none' readonly/></td><td style='width: 50px'><input type='text' value='"+ rowData.ageMax +"' class='form-control' style='width: 50px; border: none' readonly/></td><td style='width: 70px'><input type='text' value='"+ (rowData.gender_idgender == 1 ? 'Male' : 'Female') +"' class='form-control' style='width: 70px; border: none' readonly/></td><td style='width: 40px'><input type='text' value='"+ rowData.rangeMin +"' class='form-control' style='width: 40px; border: none' readonly/></td><td style='width: 50px'><input type='text' value='"+ rowData.rangeMax +"' class='form-control' style='width: 50px; border: none' readonly/></td><td><a class='btn1 btn-danger fetchButtonnew111111 yourElement3' data-testid='" + rowData.id + "'>Delete</a></td></tr>";
            // Append the new row to the table body
                    // $('#yourTableId1 tbody').append(newRow);
                  $('#templateRow2').append(tr);
            // Update the data-testid attribute for each row
                    // var dataTestId = rowData.id;
                    // console.log(dataTestId);
                    // newRow.find('.yourElement2').attr('data-testid', dataTestId);
                 }
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
            $.ajax({
                type: 'GET',
                url: '/getmaterialconsumption/' + rowIdd,
                success: function(response) {
                    
                console.log(response);

                var dataArray = JSON.parse(response);

                $('#templateRow3').html("");
            //  var templateRow = $('#templateRow').clone().removeAttr('style').removeAttr('id');
              for (var i = 0; i < dataArray.length; i++) {
                  var rowData = dataArray[i];
                  console.log(response);
                  var tr="<tr><td><input type='text' value='"+ rowData.name +"' class='form-control' style='width: 120px; border: none' readonly/></td><td><input type='text' value='"+ rowData.value +"' class='form-control' style='width: 40px; border: none' readonly/></td><td><input type='text' value='"+ rowData.unit +"' class='form-control' style='width: 40px; border: none' readonly/></td><td><a class='btn1 btn-danger fetchButtonnew111111111 yourElement4' data-testid='" + rowData.id + "'>Delete</a></td></tr>";
            // Append the new row to the table body
                    // $('#yourTableId1 tbody').append(newRow);
                  $('#templateRow3').append(tr);
            // Update the data-testid attribute for each row
                    // var dataTestId = rowData.id;
                    // console.log(dataTestId);
                    // newRow.find('.yourElement2').attr('data-testid', dataTestId);
        
            //     var templateRow = $('#templateRow3').clone().removeAttr('style').removeAttr('id');

            //     for (var i = 0; i < dataArray.length; i++) {

            //     var rowData = dataArray[i];
            //     var newRow = templateRow.clone();
            //     newRow.find('#mat_name').val(rowData.name);
            //     newRow.find('#valuuu').val(rowData.value);
            //     newRow.find('#unitttt').val(rowData.unit);
            //     newRow.find('#idi').val(rowData.id);

            //     $('#yourTableId3 tbody').append(newRow);

            // // Update the data-testid attribute for each row
            //     var dataTestId = rowData.id;
            //     console.log(dataTestId);
            //     newRow.find('.yourElement4').attr('data-testid', dataTestId);
            
                }

                },

                error: function(error) {
                    console.error('Error:', error);
                }

            });

            });
        });
        </script>
        <script>
            function submitFormnew(event) {
                event.preventDefault();
                if ($('#val_sugg').val() === '') {
                   alert('Please fill in the required field.');
                   return false; // Prevent form submission
                }
                var formData = $('#myForm').serialize();
                var lhtiddValue = $('#lhtiii').val();
                console.log('lhtiddValue:', lhtiddValue);
                // var table = $('#yourTableId1').DataTable();
                $.ajax({
                    type: 'POST',
                    url: '/addvaluesuggestion/' + lhtiddValue, // Replace with the actual backend endpoint
                    data: formData,
                    success: function(response) {
                    var rowData = response;
                    var tr="<tr><td><input type='text' value='"+ rowData.value +"' class='form-control' style='width: 200px; border: none' readonly/></td><td><a class='btn1 btn-danger fetchButtonnew111 yourElement2' data-testid='" + rowData.id + "'>Delete</a></td></tr>";
            // Append the new row to the table body
                    // $('#yourTableId1 tbody').append(newRow);
                    $('#templateRow1').append(tr);

                    $('#val_sugg').val('');

                    },
                    error: function(error) {
                        console.error('Error submitting form:', error);
                    }
                });
            }
        </script>
        <script>
        $(document).ready(function() {
                $('#yourTableId1').on('click', '.fetchButtonnew111.yourElement2', function() {
                var rowId = $(this).data('testid');
                console.log(rowId);
                var rowToDelete = $(this).closest('tr');
                if (confirm('Are you sure you want to delete this test parameter value suggestion?')) {
                $.ajax({
                  type: 'GET',
                  url: '/deletevaluesuggestion/' + rowId,
                  success: function(response) {
                // Handle success response
                  console.log('Deleted successfully:', response);
                // Optionally, you can update your UI or perform other actions
                  rowToDelete.remove();
                },
                error: function(error) {
                // Handle error response
                console.error('Error deleting:', error);
                }
                });
                }
                });

        });
        </script>
        <script>
            function submitFormnewest(event) {
                event.preventDefault();
                if ($('#min_agee').val() === '' || $('#max_agee').val() === '' || $('#min_reff').val() === '' || $('#max_reff').val() === '') {
                   alert('Please fill in the required field.');
                   return false; // Prevent form submission
                }
                var formData = $('#myForm2').serialize();
                var lhtiddValue = $('#lhtiii').val();
                $.ajax({
                    type: 'POST',
                    url: '/addreferencerangevalues/' + lhtiddValue, // Replace with the actual backend endpoint
                    data: formData,
                    success: function(response) {
                        // Assuming the response contains the value for the new row
                        var rowData = response;
                        var tr="<tr><td style='width: 40px'><input type='text' value='"+ rowData.ageType +"' class='form-control' style='width: 60px; border: none' readonly/></td><td style='width: 40px'><input type='text' value='"+ rowData.ageMin +"' class='form-control' style='width: 40px; border: none' readonly/></td><td style='width: 50px'><input type='text' value='"+ rowData.ageMax +"' class='form-control' style='width: 50px; border: none' readonly/></td><td style='width: 70px'><input type='text' value='"+ (rowData.gender_idgender == 1 ? 'Male' : 'Female') +"' class='form-control' style='width: 70px; border: none' readonly/></td><td style='width: 40px'><input type='text' value='"+ rowData.rangeMin +"' class='form-control' style='width: 40px; border: none' readonly/></td><td style='width: 40px'><input type='text' value='"+ rowData.rangeMax +"' class='form-control' style='width: 40px; border: none' readonly/></td><td><a class='btn1 btn-danger fetchButtonnew111111 yourElement3' data-testid='" + rowData.id + "'>Delete</a></td></tr>";
            // Append the new row to the table body
                    // $('#yourTableId1 tbody').append(newRow);
                    $('#templateRow2').append(tr);
        
                        // Clear the form input
                        $('#min_agee').val('');
                        $('#max_agee').val('');
                        $('#min_reff').val('');
                        $('#max_reff').val('');
                    },
                    error: function(error) {
                        console.error('Error submitting form:', error);
                    }
                });
            }
        </script>
        <script>
            $(document).ready(function() {
                $('#yourTableId2').on('click', '.fetchButtonnew111111.yourElement3', function() {
                var rowId = $(this).data('testid');
                console.log(rowId);
                var rowToDelete = $(this).closest('tr');
                if (confirm('Are you sure you want to delete this test parameter age and gender wise reference range?')) {
                $.ajax({
                    type: 'GET',
                    url: '/deleteReferenceValue/' + rowId,
                    success: function(response) {
                        // Handle success response
                        console.log('Deleted successfully:', response);
                        // Optionally, you can update your UI or perform other actions
                        rowToDelete.remove();
                    },
                    error: function(error) {
                        // Handle error response
                        console.error('Error deleting:', error);
                    }
                });
                }
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $('#yourTableId3').on('click', '.fetchButtonnew111111111.yourElement4', function() {
                var rowId = $(this).data('testid');
                console.log(rowId);
                var rowToDelete = $(this).closest('tr');
                if (confirm('Are you sure you want to delete this test parameter material consumption?')) {
                        $.ajax({
                            type: 'GET',
                            url: '/deletematerialconsumption/' + rowId,
                            success: function(response) {
                                // Handle success response
                                console.log('Deleted successfully:', response);
                                // Optionally, you can update your UI or perform other actions
                                rowToDelete.remove();
                            },
                            error: function(error) {
                                // Handle error response
                                console.error('Error deleting:', error);
                            }
                        });
                    }
                });
            });
        </script>
        <script>
            function submitFormnewestnew(event) {
                event.preventDefault();
                if ($('#vallu').val() === '') {
                   alert('Please fill in the required field.');
                   return false; // Prevent form submission
                }
                var formData = $('#myForm3').serialize();
                var lhtiddValue = $('#lhtiii').val();
                $.ajax({
                    type: 'POST',
                    url: '/addmaterialconsumptionvalues/' + lhtiddValue, // Replace with the actual backend endpoint
                    data: formData,
                    success: function(response) {
                        console.log(response);
                        var unitDisplay;
                    if (response.unit == '8') {
                       unitDisplay = 'ml';
                    } else if (response.unit == '9') {
                       unitDisplay = 'l';
                    } else {
                       unitDisplay = ''; // Handle other cases or leave it empty if no match
                    }
                    var nameDisplay;
                    if (response.Lab_has_materials_lmid == '1') {
                    nameDisplay = 'ChemOne';
                    } else if (response.Lab_has_materials_lmid == '2') {
                    nameDisplay = 'ChemTwo';
                    } else {
                    nameDisplay = ''; // Handle other cases or leave it empty if no match
                    }
                        // Assuming the response contains the value for the new row
                        var rowData = response;
                        var tr="<tr><td><input type='text' value='"+ nameDisplay +"' class='form-control' style='width: 120px; border: none' readonly/></td><td><input type='text' value='"+ rowData.qty +"' class='form-control' style='width: 50px; border: none' readonly/></td><td><input type='text' value='"+ unitDisplay +"' class='form-control' style='width: 50px; border: none' readonly/></td><td><a class='btn1 btn-danger fetchButtonnew111111111 yourElement4' data-testid='" + rowData.id + "''>Delete</a></td></tr>";
            // Append the new row to the table body
                    // $('#yourTableId1 tbody').append(newRow);
                    $('#templateRow3').append(tr);
        
                        // Clear the form input
                    $('#vallu').val('');

                    },

                    error: function(error) {
                        console.error('Error submitting form:', error);
                    }

                });
            }
        </script>
        <script>
        $(document).ready(function() {
           $('#yourTableId').on('click', '.fetchButtonnewwwwwwwwwww.yourElementttttt', function() {
              var rowIdd = $(this).data('testid');
              console.log(rowIdd);
              var rowToDelete = $(this).closest('tr');
    // Make an AJAX request to the server
              if (confirm('Are you sure you want to inactive this test parameter?')) {
              $.ajax({
                type: 'POST',
                url: '/deactivetestparameter/' + rowIdd,
                success: function(response) {
            // Update the separate div based on the server response
                console.log(response);
                rowToDelete.remove();  
                },
                error: function(error) {
                  console.error('Error:', error);
                }
              });
              }
            });
        });
        </script>

        <script>
        function submitFormnewestvvvvvvv(event) {
          event.preventDefault();
          var selectedValue = $('#testType').val();
          if ($('#test_nameee').val() === '' || $('#rep_nameee').val() === '' || $('#val_min').val() === '' || $('#val_max').val() === '' || $('#ord_numee').val() === '') {
             alert('Please fill in the required field.');
             return false; // Prevent form submission
          }
          if (selectedValue === 'Decimal' && ($('#deci_pointttt').val() === '0' || $('#deci_pointttt').val() === '')) {
            alert('Decimal Points cannot be zero or null.');
             return false; // Prevent form submission
          }
          var formData = $('#myForm4').serialize();
          var lhtiddValue = $('#tg_idddd').val();
          $.ajax({
            type: 'POST',
            url: '/addtestparameter/' + lhtiddValue, // Replace with the actual backend endpoint
            data: formData,
            success: function(response) {
                if (response.success) {
                console.log(response);
                var rowData = response
                
                // Assuming the response contains the value for the new row
            //     var newRows = '<tr><td><input type="text" value="' + response.data.tid + '" class="form-control" readonly></td>' +
            //    '<td><input type="text" value="' + response.data.name + '" class="form-control" readonly></td>' +
            //    '<td><input type="text" value="' + response.data.labhastest.reportname + '" class="form-control"></td>' +
            //    '<td><input type="text" value="' + response.data.labhastest.measurement  + '" class="form-control"></td>' +
            //    '<td><input type="text" value="' + response.data.labtest.refference_min + '" class="form-control"></td>' +
            //    '<td><input type="text" value="' + response.data.labtest.refference_max + '" class="form-control"></td>' +
            //    '<td><input type="text" value="' + response.data.labhastest.orderno + '" class="form-control"></td>' +
            //    '<td><input type="text" value="' + response.data.labtest.listestid + '" class="form-control"></td>' +
            //    '<td><a class="btn1 btn-primary" data-testid="">Select</a>&nbsp &nbsp &nbsp<a class="btn1 btn-danger" data-testid="">Inactive</a></td></tr>';
                var tr="<tr><td width='80px'><input type='text' id='tid' value='"+ rowData.data.tid +"' class='form-control' readonly/></td><td width='170px'><input type='text' id='tname' value='"+ rowData.data.name +"' class='form-control' readonly/></td><td width='180px'><input type='text' id='rep_nam' name='rep_namm[]' value='"+ rowData.data.labhastest.reportname +"' class='form-control'/></td><td width='100px'><input type='text' id='unit_lab' name='uniit[]' value='"+ rowData.data.labhastest.measurement +"' class='form-control'/></td><td width='80px'><input type='text' id='reff_min' name='refer_min[]' value='"+ rowData.data.labtest.refference_min +"' class='form-control'/></td><td width='80px'><input type='text' id='reff_max' name='refer_max[]' value='"+ rowData.data.labtest.refference_max +"' class='form-control'/></td><td width='90px'><input type='text' id='ord_nam' name='ord_nummm[]' value='"+ rowData.data.labhastest.orderno +"' class='form-control'/></td><td width='90px'><input type='text' id='liss_id' name='lisss_id[]' value='"+ rowData.data.labtest.listestid +"' class='form-control'/></td><td><a class='btn1 btn-primary arrow5 fetchButtonnew yourElement' data-testid='" + rowData.data.labhastest.lhtid + "'>Select</a>&nbsp &nbsp<a data-testid='" + rowData.data.labhastest.lhtid + "' class='btn1 btn-danger yourElementttttt fetchButtonnewwwwwwwwwww'>Inactive</a></td></tr>";
                // Append the new row to the table body
                $('#templateRow').append(tr);
                
    //             // Clear the form input
                $('#test_nameee').val('');
                $('#rep_nameee').val('');
                $('#uniiiiiiiii').val('');
                $('#minvvv').val('');
                $('#nor_vallll').prop('checked', false);
                $('#gen_wise_reffff').prop('checked', false);;
                $('#hide_w_empppp').prop('checked', false);
                $('#sel_ressss').prop('checked', false);
                $('#maxvvv').val('');
                $('#def_vallll').val('');
                $('#test_idddddddd').val('');
                $('#val_min').val('');
                $('#val_max').val('');
                $('#deci_pointttt').val('');
                $('#ord_numee').val('');
                $('#bold_valuuu').val('');
                $('#alignn').val('top');
                }
                else {
            // Handle error
                 alert(response.error); // or display the error message in a modal, etc.
                }
            },
            error: function(error) {
                console.error('Error submitting form:', error);
            }
          });
        }
        </script>
        <script>
            function submitFormnewestooooo(event) {
                event.preventDefault();
                var formData = $('#myForm6').serialize();
                var lhtiddValue = $('#tiddooo').val();
                $.ajax({
                    type: 'POST',
                    url: '/updateselectedtestparametes/' + lhtiddValue, // Replace with the actual backend endpoint
                    data: formData,
                    success: function(response) {
                        alert('Test Parameter Table Updated Successfully!');
                    },
                    error: function(error) {
                        console.error('Error submitting form:', error);
                    }
                });
            }
        </script>
        <script>
            function submitFormnewestooooonewww(event) {
                event.preventDefault();
                var selectedValue = $('#test_typee').val();
                if ($('#test_namee').val() === '' || $('#rep_namee').val() === '' || $('#val_minn').val() === '' || $('#val_maxx').val() === '' || $('#ord_numm').val() === '') {
                   alert('Please fill in the required field.');
                   return false; // Prevent form submission
                }
                if (selectedValue === 'Decimal' && ($('#deci_pointt').val() === '0' || $('#deci_pointt').val() === '')) {
                   alert('Decimal Points cannot be zero or null.');
                return false; // Prevent form submission
                }
                var formData = $('#myForm10').serialize();
                var lhtiddValue = $('#tidf').val();
                $.ajax({
                    type: 'POST',
                    url: '/updatetestparametes/' + lhtiddValue, // Replace with the actual backend endpoint
                    data: formData,
                    success: function(response) {
                        console.log(response);
                        var rowData = response;

// Find the existing row using the unique identifier (e.g., tid)
                    var existingRow = $('#templateRow').find('tr:has(#tid[value="' + rowData.testlabhas.test_tid + '"])');

// Check if the row already exists
                    if (existingRow.length > 0) {
    // Update the HTML content of the existing row
                        if(rowData.test){
                            existingRow.find('#tname').val(rowData.test.name);
                        }
                        existingRow.find('#rep_nam').val(rowData.testlabhas.reportname);
                        existingRow.find('#unit_lab').val(rowData.testlabhas.measurement);
                        existingRow.find('#reff_min').val(rowData.testlabdetail.refference_min);
                        existingRow.find('#reff_max').val(rowData.testlabdetail.refference_max);
                        existingRow.find('#ord_nam').val(rowData.testlabhas.orderno);
                        existingRow.find('#liss_id').val(rowData.testlabdetail.listestid);
                    }
                        alert('Test Parameter Updated Successfully!');
                        $(".mySection2").hide();
                    },
                    error: function(error) {
                        console.error('Error submitting form:', error);
                    }
                });
            }
        </script>
        <script>
           function toggleDecimalPoints() {
        var testType = $('#testType').val();
        var decimalPointsContainer = $('#decimalPointsContainer');

        if (testType === 'Decimal') {
            decimalPointsContainer.show();
        } else {
            decimalPointsContainer.hide();
        }
    }
        </script>
	</body>
</html>
