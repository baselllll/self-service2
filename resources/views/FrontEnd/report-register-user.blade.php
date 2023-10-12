<!DOCTYPE html>
<html>
<head>
    <title>SSHR Dashboard</title>
    <link rel="icon" href="{{asset("img/ajmi.png")}}" type="image/png">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <style>
        .style_tabled {
            background: slategrey;
            color: white;
        }
    </style>
</head>

<body>
    <div class="row">
        <div class="col-12">
            <h1>Tracking Requests for Employee</h1>
            <div class="table-responsive">
                <table id="example3" class="display" style="width:100%">
                    <thead class="style_tabled">
                    <tr>
                        <th>Id</th>
                        <th>EmpNo</th>
                        <th>EmpName</th>
                        <th>Service</th>
                        <th>Depr</th>
                        <th>Crea.D</th>
                        <th>Hours</th>
                        <th>Mgr</th>
                        <th>Admin.Mng</th>
                        <th>Top.Mng</th>
                        <th>Po.Status</th>
                        <th>Fin.Status</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tracking_users as $item)
                        @php
                            $start_date = new DateTime($item->absence_start_date);
                            $end_date = new DateTime($item->absence_end_date);
                            $interval = $start_date->diff($end_date);
                            $hours = $interval->format('%h');
                            $minutes = $interval->format('%i');
                            $total_hours = $hours + ($minutes / 60);
                        @endphp
                        <tr>
                            <td>{{$item->transaction_id}}</td>
                            <td>{{$item->empno}}</td>
                            <td>{{explode(" ",$item->requestor)[0]}} {{explode(" ",$item->requestor)[1]}}</td>
                            <td>{{$item->absence_type}}</td>
                            <td>{{$item->cost_center_name}}</td>
                            <td>{{$item->creation_date}}</td>
                            @if($item->absence_type=="Permission - Personal Work" or $item->absence_type=="Permission - Official Work")
                                <td>@if($total_hours==0) 2 @else {{$total_hours}} @endif</td>
                            @else
                                <td></td>
                            @endif

                            <td>({{$item->mgr_emp_number->first_name}} {{$item->mgr_emp_number->last_name}}- {{$item->mgr_emp_number->employee_number}})</td>
                            <td>({{$item->admin_emp_number->first_name}} {{$item->admin_emp_number->last_name}}- {{$item->admin_emp_number->employee_number}})</td>

                        @if($item->no_of_approvals=="3")
                                <td>({{$item->top_emp_number->first_name}} {{$item->top_emp_number->last_name}}- {{$item->top_emp_number->employee_number}})</td>
                            @else
                               <td></td>
                            @endif

                            @if($item->no_of_approvals=="3")
                                <td>({{$item->mgr_approval_status}} - {{$item->admin_mgr_approval_status}} - {{$item->top_management_approval_status}})</td>
                            @else
                                <td>({{$item->mgr_approval_status}} - {{$item->admin_mgr_approval_status}})</td>

                            @endif
                           <td>{{$item->approval_status}}</td>
                            <td>{{$item->phone}}</td>
                            <td>{{$item->email_address}}</td>
                            @if($item->no_of_approvals=="3")
                                @if($item->added_absence_check=="N" and  !str_contains($item->approval_status,'Rejected') and !str_contains($item->top_management_approval_status,'Pending'))
                                    <td><button id="insert_absence" type="button" data-trax="{{$item->transaction_id}}" class="btn btn-primary">Insert</button></td>
                                @else
                                    <td></td>
                                @endif
                            @endif

                            @if($item->no_of_approvals=="2")
                                @if($item->added_absence_check=="N" and  !str_contains($item->approval_status,'Rejected') and !str_contains($item->admin_mgr_approval_status,'Pending'))
                                    <td><button  type="button" data-trax="{{$item->transaction_id}}" class="btn btn-primary insert_absence">Insert</button></td>
                                @else
                                    <td></td>
                                @endif
                            @endif


                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br/> <br/> <br/>
    <hr/>
    <div class="row">
        <div class="col-12">
            <h1>Registered User</h1>
            <div class="table-responsive">
                <table id="example" class="display" style="width:100%">
                    <thead class="style_tabled">
                    <tr>
                        <th>EmpNum</th>
                        <th>EmpName</th>
                        <th>Nationality</th>
                        <th>RegStatus</th>
                        <th>CreationDate</th>
                        <th>Email</th>
                        <th>Phone</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reg_users as $item)
                        <tr>
                            <td>{{$item->employee_number}}</td>
                            <td>{{explode(" ",$item->full_name)[0]}} {{explode(" ",$item->full_name)[1]}}</td>
                            <td>{{$item->nationality}}</td>
                            <td>{{$item->registration_status}}</td>
                            <td>{{$item->creation_date}}</td>
                            <td>{{$item->email_address}}</td>
                            <td>{{$item->mobile_no}}</td>
                        </tr>
                    @endforeach


                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br/> <br/> <br/>
    <hr/>

    <div class="row">
        <div class="col-12">
            <h1>Not Registered User</h1>
            <div class="table-responsive">
                <table id="example2" class="display" style="width:100%">
                    <thead class="style_tabled">
                    <tr>
                        <th>EmpNum</th>
                        <th>EmpName</th>
                        <th>Nationality</th>
                        <th>RegStatus</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($non_reg_users as $item)
                        <tr>
                            <td>{{$item->employee_number}}</td>
                            <td>{{explode(" ",$item->full_name)[0]}} {{explode(" ",$item->full_name)[1]}}</td>
                            <td>{{$item->nationality}}</td>
                            <td>{{$item->registration_status}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br/> <br/> <br/>

    <hr/>
    <br/> <br/> <br/>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-4">
            <h5>Registration Users Charts</h5>
        </div>
        <div class="col-2"></div>
        <div class="col-2">

            <div>
                <div class="col-md-10 mx-auto">
                    <canvas id="myPieChart"></canvas>
                </div>
            </div>
        </div>
        </div>

<hr/>
<div class="row">
    <div class="col-12">
        <h1>Active Session</h1>
        <div class="table-responsive">
            <table id="example4" class="display" style="width:100%">
                <thead class="style_tabled">
                <tr>
                    <th>EmpNum</th>
                    <th>EmpName</th>
                    <th>SessionTime(Min)</th>
                    <th>IpAddress</th>
                    <th>Otp</th>
                    <th>ExpirationDate</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($activeSession as $item)

                    <tr>
                        <td>{{$item->employee_number}}</td>
                        <td>{{explode(" ",$item->full_name)[0]}} {{explode(" ",$item->full_name)[1]}}</td>
                        <th>{{env('SESSION_LIFETIME')}}</th>
                        <td>{{$item->attribute4}}</td>
                        <td>{{$item->attribute2}}</td>
                        <td>{{$item->attribute11}}</td>
                        <td><button class="kill-session-btn btn btn-success" type="button" data-empp="{{$item->employee_number}}" >End</button></td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function () {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: ['excel'],
            order: [[4, 'desc']],
            "pageLength": 5
        });
    });

    $(document).ready(function () {
        $('#example2').DataTable({
            dom: 'Bfrtip',
            buttons: ['excel'],
            "pageLength": 5
        });
    });
    $(document).ready(function () {
        $('#example3').DataTable({
            dom: 'Bfrtip',
            buttons: ['excel'],
            "pageLength": 5
        });
    });
    $(document).ready(function () {
        $('#example4').DataTable({
            dom: 'Bfrtip',
            buttons: ['excel'],
            "pageLength": 5 // Set the number of records per page to 5
        });
    });


</script>
<script>
    // Updated data for the pie chart
    var data = {
        labels: ['Registered', 'Not Registered'],
        datasets: [{
            data: [{{$count_register}}, {{$count_not_register}}], // Example percentages (adjust these values)
            backgroundColor: ['#36A2EB', '#FFCE56']
        }]
    };

    var options = {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            position: 'bottom'
        }
    };

    var ctx = document.getElementById('myPieChart').getContext('2d');

    var myPieChart = new Chart(ctx, {
        type: 'pie',
        data: data,
        options: options
    });

</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

<script>
        $('#emp_updated_btn').click(function () {
            var emp_updated = $('#emp_updated').val();
            console.log(emp_updated);
            $.ajax({
                url: "{{ route('close-different-login') }}",
                method: 'POST',
                data: {
                    emp_number: emp_updated,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response.results) {
                        $('#message_updated').text(response.results);

                    }
                }
            });
        });
</script>
<script>
        $('.insert_absence').click(function () {
            var check = confirm("Are to need to insert absence ?");
            var transaction_id = $(this).data('trax');
            if (check){
                $.ajax({
                    url: "{{ route('continue_process_absence') }}",
                    data: {
                        transaction_id: transaction_id
                    },
                    success: function (response) {
                        if(response.results){
                            alert(response.results)
                        }
                    }
                });
            }


        });
        $('.kill-session-btn').click(function () {
            var check = confirm("Are to need to kill session for that user ?");
            var emp_no = $(this).data('empp');
            if (check){
                $.ajax({
                    url: "{{ route('close-different-login') }}",
                    method: 'POST',
                    data: {
                        emp_number: emp_no,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if(response.results){
                            alert(response.results)
                        }
                    }
                });
            }


        });
</script>

</body>
</html>
