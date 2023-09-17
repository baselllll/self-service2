<!DOCTYPE html>
<html>
<head>
    <title>PDF Report</title>
    <!-- Add Bootstrap CSS link -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Add custom CSS styles here (if needed) */
        @page {
            size: A4;
            margin: 0;
            border: 4px solid #000;
        }
        body {
            margin: 1.6cm; /* Adjust the margin for your needs */
        }
        .container {
            background-color: white;
        }
        .row {
            margin-bottom: 10px;
        }
        .orange-block {
            background-color: wheat;
            border-radius: 5px;
            color: black;
            font-size: 20px;
        }


        @media print {
            html, body {
                width: 300mm;
                height: 200mm;
            }
            .container {
                padding-top: 7cm;
                margin-top: -7cm;
                font-weight: bold;
            }
            .content {
                transform: scale(4);
                transform-origin: center;

            }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="row" style="margin-left: 17px;
    font-size: 19px;
    font-weight: bold;">
        <div class="col-lg-4">
            <div>
                <p>ABDUL ALI AL-AJMI COMPANY</p>
                <p>Human Resource Department</p>
                <p>Self-Service Report</p>
            </div>
        </div>
        <div class="col-lg-4">
            <img width="200px" height="181px" src="{{asset('img/ajmi_logo_report.jpg')}}" style="margin-left: 30px;
    margin-top: -27px;" alt="Company Logo">
        </div>
        <div class="col-lg-4">
            <div>
                <p>شركـة عبد العــالـي الـعجـمـي</p>
                <p>قسم الموارد البشرية</p>
                <p>تقرير عن الخدمة الذاتية</p>
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <h1 style="text-align: center;text-decoration: underline">Self-Service Report</h1>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>

    <div class="row">
        <div class="col-lg-4">
            <div class="orange-block">
                <p>Trxn ID     : {{ $data->transaction_id }}</p>
                <p>Emp No      : {{ $data->empno }}</p>
                <p>Emp Name    : {{ $data->emp_name }}</p>
                <p>Mgr Approval: {{ $data->mgr_approval_status }}</p>
                <p>Absence Type: {{ $data->absence_type }}</p>
                <p>Start Date  : {{ $data->absence_start_date }}</p>
                <p>End Date    : {{ $data->absence_end_date }}</p>
            </div>
        </div>
        <div class="col-lg-4" >
            <div class="orange-block" style="height: 409px">
                <p>Top Mgmt Approval    : {{ $data->top_management_approval_status }}</p>
                <p>No. of Approvals     : {{ $data->no_of_approvals }}</p>
                <p>Replacement No       : {{ $data->replacement_no }}</p>
                <p>Replacement Name     : {{ $data->replacement_name }}</p>
                {{--            <p>Mgr Note             : {{ $data->mgr_approve_note }}</p>--}}
                {{--            <p>Admin Mgr Note       : {{ $data->admin_mgr_approval_note }}</p>--}}
                <p>Update Date          : {{ $data->update_date }}</p>
                <p>Mgr Action Date      : {{ \Carbon\Carbon::parse($data->mgr_action_date )->format('Y-m-d')}}</p>
                {{--            <p>Admin Mgr Action Date: {{  \Carbon\Carbon::parse($data->admin_mgr_action_date)->format('Y-m-d') }}</p>--}}
            </div>
        </div>
        <div class="col-lg-4">
            <div class="orange-block">
                <p>Admin Mgr Approval : {{ $data->admin_mgr_approval_status }}</p>
                <p>Creation Date      : {{ \Carbon\Carbon::parse($data->creation_date )->format('Y-m-d')}}</p>
                <p>Mgr Name           : {{ $data->mgr_name }}</p>
                <p>Time Start         : {{ $data->time_start }}</p>
                <p>Time End           : {{ $data->time_end }}</p>
                <p>Absence Hours      : {{ $data->absence_hours }}</p>
            </div>
        </div>

    </div>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <div class="row" style="margin-left: 117px;
    font-size: 24px;
    font-weight: bold;">
        <div class="col-lg-4">
            <div>
                <p>مدير الموظف</p>
                <img width="140px" height="170px" src="{{asset('img/manger_sign.png')}}" alt="Company Logo">
            </div>
        </div>
        <div class="col-lg-4">
            <p>المدير الاداري</p>
            <img width="140px" height="170px" src="{{asset('img/manger_sign.png')}}" alt="Company Logo">
        </div>
        <div class="col-lg-4">
            <div>
                <p>المدير العام</p>
                <img width="140px" height="170px" src="{{asset('img/manger_sign.png')}}" alt="Company Logo">
            </div>
        </div>
    </div>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>


    <hr/>



    <div class="row" style="
    font-weight: bold;">
        <div class="col-lg-8">
            <p>
                <span id="out-date"></span>
            </p>
        </div>

        <div class="col-lg-4">
            <p>
                <span id="out-date">{{session()->get('employee')->employee_name}}</span>
            </p>
        </div>
    </div>
</div>

<script>
    var get_local = "{{App::getLocale()}}";
    if (get_local == "ar") {
        var today = new Date();
        var options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric'
        };
        var arabicDate = today.toLocaleString('ar-EG', options);
        document.getElementById('out-date').innerText = arabicDate;
    }else {
        var today = new Date();
        document.getElementById('out-date').innerText = today.toString();

    }
    window.print();



</script>
</body>
</html>
