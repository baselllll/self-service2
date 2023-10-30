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
            <div>
                <h1 class="alert alert-primary" style="font-weight: bold">Add Feature</h1>
            </div>


            <div class="row">
                <div class="col-2">
                    <h5>Add Details Of New Features</h5>
                </div>
                <div class="col-6">
                    <div>
                        <div class="col-md-6 mx-auto">
                            <div class="mb-3">
                                <label for="title_feature" class="form-label">Title</label>
                                <input type="hidden" value="@if(isset($feature_new->feature_id)) {{$feature_new->feature_id}} @endif" id="feature_id" name="feature_id">
                                <input value="@if(isset($feature_new->title)) {{$feature_new->title}} @endif"  type="text" class="form-control" id="title_feature" aria-describedby="title_feature">
                                <br/>
                                <label for="desc_feature" class="form-label">Description</label>
                                <input  name="desc_feature" class="form-control" id="desc_feature" value="@if(isset($feature_new->description)) {{$feature_new->description}} @endif">
                                <br/>
                                <select class="form-control" name="status_feature" id="status_feature">
                                    <option class="form-control" value="Active">Active</option>
                                    <option class="form-control" value="Non-Active">Non-Active</option>
                                </select>
                                <span class="sr-only" id="message_updated">Loading...</span>
                            </div>
                            <button id="feature_btn" class="btn  btn-secondary">Apply</button>
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
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>\
<script>
    $('#feature_btn').click(function () {
        var title_feature = $('#title_feature').val();
        var desc_feature = $('#desc_feature').val();
        var status_feature = $('#status_feature').val();
        var feature_id = $('#feature_id').val();
        $.ajax({
            url: "{{ route('add_details_feature') }}",
            method: 'POST',
            data: {
                title_feature: title_feature,
                desc_feature: desc_feature,
                status_feature: status_feature,
                feature_id: feature_id,
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
