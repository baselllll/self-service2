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
                <h1 class="alert alert-primary" style="font-weight: bold">Absence Manual </h1>
            </div>


            <div class="row">
                <div class="col-4">
                    <h5>Absence Manual Service for Continue Process Of Approval </h5>
                </div>
                <div class="col-8">
                    <div>
                        <div class="col-md-6 mx-auto">
                            <div class="mb-3">
                                <label for="emp_updated" class="form-label">Employee Number</label>
                                <input type="number" class="form-control" id="emp_updated" aria-describedby="emp_updated">
                                <div id="emp_updated" class="form-text"></div>
                                <span class="sr-only" id="message_updated">Loading...</span>
                            </div>
                            <button id="emp_updated_btn" class="btn  btn-secondary">Continue</button>
                            <br/>


                        </div>

                    </div>
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
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: ['excel']
        });
    });

</script>
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