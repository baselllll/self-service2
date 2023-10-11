<!DOCTYPE html>
<html>
<head>
    <title>PDF Report</title>
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
    <div class="col-12">
        <div class="row">
            <div>
                <h1 class="alert alert-primary" style="font-weight: bold">Tracking Request</h1>
            </div>

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
                                <th>Service</th>
                                <th>Department</th>
                                <th>Manager</th>
                                <th>AdminMr</th>
                                <th>TopMr</th>
                                <th>PointStopStatus</th>
                                <th>FinalStatus</th>
                                <th>Phone</th>
                                <th>Email</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tracking_users as $item)

                                <tr>
                                    <td>{{$item->empno}}</td>
                                    <td>{{explode(" ",$item->requestor)[0]}} {{explode(" ",$item->requestor)[1]}}</td>
                                    <td>{{$item->absence_type}}</td>

                                    <td>{{$item->cost_center_name}}</td>
                                    <td>({{$item->admin_emp_number->first_name}} {{$item->admin_emp_number->last_name}}- {{$item->admin_emp_number->employee_number}})</td>
                                    <td>({{$item->mgr_emp_number->first_name}} {{$item->admin_emp_number->last_name}}- {{$item->mgr_emp_number->employee_number}})</td>
                                    <td>({{$item->top_emp_number->first_name}} {{$item->top_emp_number->last_name}}- {{$item->top_emp_number->employee_number}})</td>

                                    <td>({{$item->mgr_approval_status}} - {{$item->admin_mgr_approval_status}} - {{$item->top_management_approval_status}})</td>
                                    <td>{{$item->approval_status}}</td>
                                    <td>{{$item->phone}}</td>
                                    <td>{{$item->email_address}}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-1"></div>
            </div>

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
        $('#example3').DataTable({
            dom: 'Bfrtip',
            buttons: ['excel']
        });
    });

</script>
</body>
</html>
