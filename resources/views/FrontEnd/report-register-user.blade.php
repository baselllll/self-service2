<!DOCTYPE html>
<html>
<head>
    <title>Register Report</title>
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
<div class="container">
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <h1>Tracking Requests for Employee</h1>
            <div class="table-responsive">
                <table id="example3" class="display" style="width:100%">
                    <thead class="style_tabled">
                    <tr>
                        <th>EmpNum</th>
                        <th>EmpName</th>
                        <th>PhoneNo</th>
                        <th>Email</th>
                        <th>Service</th>
                        <th>Department</th>
                        <th>Manager</th>
                        <th>AdminMr</th>
                        <th>TopMr</th>

                        <th>PointStopStatus</th>
                        <th>FinalStatus</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tracking_users as $item)

                        <tr>
                            <td>{{$item->empno}}</td>
                            <td>{{explode(" ",$item->requestor)[0]}} {{explode(" ",$item->requestor)[1]}}</td>
                            <td>{{$item->phone}}</td>
                            <td>{{$item->email_address}}</td>
                            <td>{{$item->absence_type}}</td>

                            <td>{{$item->cost_center_name}}</td>
                            <td>({{$item->admin_emp_number->first_name}} {{$item->admin_emp_number->last_name}}- {{$item->admin_emp_number->employee_number}})</td>
                            <td>({{$item->mgr_emp_number->first_name}} {{$item->admin_emp_number->last_name}}- {{$item->mgr_emp_number->employee_number}})</td>
                            <td>({{$item->top_emp_number->first_name}} {{$item->top_emp_number->last_name}}- {{$item->top_emp_number->employee_number}})</td>

                            <td>({{$item->mgr_approval_status}} - {{$item->admin_mgr_approval_status}} - {{$item->top_management_approval_status}})</td>
                            <td>{{$item->approval_status}}</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-1"></div>
    </div>
    <br/> <br/> <br/>
    <hr/>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
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
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-1"></div>
    </div>
    <br/> <br/> <br/>
    <hr/>

    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
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
        <div class="col-1"></div>
    </div>
    <br/> <br/> <br/>

    <hr/>
    <br/> <br/> <br/>
    <div class="row">
        <div class="col-2">
            <h5>Reset Users from Different Devices</h5>
        </div>
        <div class="col-6">
            <div>
                <div class="col-md-6 mx-auto">
                        <div class="mb-3">
                            <label for="emp_updated" class="form-label">Employee Number</label>
                            <input type="number" class="form-control" id="emp_updated" aria-describedby="emp_updated">
                            <div id="emp_updated" class="form-text"></div>
                            <span class="sr-only" id="message_updated">Loading...</span>
                        </div>
                    <button id="emp_updated_btn" class="btn  btn-secondary">Reset</button>
                    <br/>


                </div>

                </div>
            </div>
        <div class="col-4">

            <div>
                <h5>Registration Users Charts</h5>
                <div class="col-md-10 mx-auto">
                    <canvas id="myPieChart"></canvas>
                </div>
            </div>
        </div>
        </div>
    </div>
<hr/>
<div class="row">
    <div class="col-1"></div>
    <div class="col-10">
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
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
    <div class="col-1"></div>
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
            order: [[4, 'desc']] // Assuming 'creation_date' is the second column (index 1)
        });
    });

    $(document).ready(function () {
        $('#example2').DataTable({
            dom: 'Bfrtip',
            buttons: ['excel']
        });
    });
    $(document).ready(function () {
        $('#example3').DataTable({
            dom: 'Bfrtip',
            buttons: ['excel']
        });
    });
    $(document).ready(function () {
        $('#example4').DataTable({
            dom: 'Bfrtip',
            buttons: ['excel']
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
</body>
</html>
