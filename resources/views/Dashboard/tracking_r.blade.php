<!DOCTYPE html>
<html>
<head>
    <title>Al-Ajmi SSHR Dashboard</title>
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
            <div class="col-12">
                <h1>Tracking Requests for Employee</h1>
                <div class="table-responsive">
                    <table id="example3" class="display" style="width:213%">
                        <thead class="style_tabled">
                        <tr>
                            <th>Id</th>
                            <th>Emp#</th>
                            <th>EmpName</th>
                            <th>Serv</th>
                            <th>Depr</th>
                            <th>Crea.D</th>
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
                                 $cost_center_name = $item->cost_center_name;
                                 if (mb_strlen($cost_center_name, 'UTF-8') > 40) {
                                     $cost_center_name = mb_substr($cost_center_name, 0, 40, 'UTF-8') . "...";
                                 }
                            @endphp
                            <tr>
                                <td>{{$item->transaction_id}}</td>
                                <td>{{$item->empno}}</td>
                                <td>{{explode(" ",$item->requestor)[0]}} {{explode(" ",$item->requestor)[1]}}</td>
                                <td>{{$item->absence_type}}</td>
                                <td>{{$cost_center_name}}</td>
                                <td>{{$item->creation_date}}</td>


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
                                        <td><button type="button" data-trax="{{$item->transaction_id}}" class="btn btn-primary insert_absence">Insert</button></td>
                                    @else
                                        <td></td>
                                    @endif
                                @endif
                                @if($item->added_absence_check=="N")
                                    <td><button  type="button" data-trax="{{$item->transaction_id}}" class="btn btn-primary insert_absence">Insert</button></td>
                                @else
                                    <td></td>
                                @endif



                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
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
            order: [[6, 'desc']],
            buttons: ['excel']
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
