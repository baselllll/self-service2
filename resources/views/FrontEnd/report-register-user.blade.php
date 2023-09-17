<!DOCTYPE html>
<html>
<head>
    <title>Register Report</title>
    <link rel="icon" href="{{asset("img/ajmi.png")}}" type="image/png">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.0/css/buttons.dataTables.min.css"/>

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
        <div class="col-2"></div>
        <div class="col-8">
            <h1>Registered User</h1>
            <div class="table-responsive">
                <table id="example" class="display" style="width:100%">
                    <thead class="style_tabled">
                    <tr>
                        <th>EmpNum</th>
                        <th>EmpName</th>
                        <th>Nationality</th>
                        <th>RegStatus</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($reg_users as $item)
                        <tr>
                            <td>{{$item->employee_number}}</td>
                            <td>{{$item->full_name}}</td>
                            <td>{{$item->nationality}}</td>
                            <td>{{$item->registration_status}}</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-2"></div>
    </div>
    <br/> <br/> <br/>
    <hr/>
    <br/>
    <br/>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8">
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
                            <td>{{$item->full_name}}</td>
                            <td>{{$item->nationality}}</td>
                            <td>{{$item->registration_status}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-2"></div>
    </div>
    <br/> <br/> <br/>
    <hr/>
    <br/> <br/> <br/>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8">
            <h1>Pi Charts Of Users</h1>
            <div>
                <div class="col-md-6 mx-auto">
                    <canvas id="myPieChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-2"></div>
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
        $('#example,#example2').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'excel'
            ]
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
</body>
</html>
