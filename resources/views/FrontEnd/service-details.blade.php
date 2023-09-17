<!DOCTYPE html>
<html lang="en">

@include('includes._header')

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
    <!-- Spinner End -->


    @include('includes._navbar',['requested_notification' => $requested_notification])



    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">

                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->


   <!-- Contact Start -->
   <div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">@lang('messages.Services')   @lang('messages.' . $name) </h6>
        </div>
        <br>
        @if(isset($service_type))
            <div class="row g-4">

                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">

                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">


                    <form action="{{route('add-service-detail')}}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <input type="hidden"  name="absence_attendance_type_id" value="{{$absence_attendance_type_id}}">
                                <input type="hidden" name="person_id" value="{{$employee->person_id}}">
                                <input type="hidden" name="occurrence" value="{{$occurrence}}">
                                <input type="hidden" name="absence_type" value="{{$name}}">
                                <input type="hidden" name="attribute_category" value="@if(isset($attribute_category)) {{$attribute_category}} @else null @endif">
                                <input type="hidden" name="date_notification" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                @if($absence_attendance_type_id==62)
                                    <div class="form-floating">
                                        <span id="get_Accruals_valid" style="font-weight: bold;font-size: 14px;color: #0c4128"><span style="color: red"></span>@lang("messages.Eligible_Vacation_Days")</span></span>
                                        <br/>
                                        <input type="hidden" id="get_Accruals_data" name="get_Accruals_data" value="">
                                        <span style="font-size: 20px;font-weight: bold; color:  #8f4e04">&nbsp;  @lang("messages.Accrued Days")</span> : <span id="get_Accruals" style="font:bold 30px Aldhabi;color:  green"></span>
                                    </div>
                                @endif

                                <div class="form-floating">
                                    @php
                                        $formattedDate = isset($start_date_unauthorized) ? date('Y-m-d', strtotime($start_date_unauthorized)) : '';
                                    @endphp
                                    <input required type="{{ trim($absence_attendance_type_id) !== '2064' && trim($absence_attendance_type_id) !== '2063' ? 'date' : 'datetime-local' }}" value="{{ $formattedDate }}" class="form-control" name="start_date" id="start_date" placeholder="Subject">
                                    <label for="start_date">@lang('messages.Start Date')</label>
                                </div>


                                <br/>
                                <div class="form-floating">
                                    <input required type="{{ trim($absence_attendance_type_id) !== '2064' && trim($absence_attendance_type_id) !== '2063' ? 'date' : 'datetime-local' }}" class="form-control" name="end_date" id="end_date" placeholder="Subject">
                                    <label for="start_date">@lang('messages.End Date')</label>
                                </div>
                                <br/>

                                @if($absence_attendance_type_id=="62")
                                    <div class="form-floating">
                                        <span  style="font-weight: bold;font-size: 14px;color: #0c4128"><span>@lang('messages.Annual Days') : </span><span id="get_difference_date" style="font:bold 30px Aldhabi;color:  green"></span></span>
                                    </div>
                                    <br/>
                                    <div id="start_date_unathorized_input" style="display: none" class="form-floating">
                                        <input type="date" id="start_date_unathorized_input_date" class="form-control" name="start_date_unathorized" placeholder="Subject">
                                        <label for="start_date_unathorized">@lang('messages.Start Date UnAuthorized')</label>
                                    </div>

                                    <br/>
                                @endif


                                <div id="end_date_unathorized_input" style="display: none" class="form-floating">
                                    <input  id="end_date_unathorized_input_date" type="date" class="form-control" name="end_date_unathorized"  placeholder="Subject">
                                    <label for="end_date_unathorized">@lang('messages.End Date UnAuthorized')</label>
                                </div>

                                <br/>
                                <div id="get_difference_date_unauthorized_div" class="form-floating" style="display: none">
                                    <span  style="font-weight: bold;font-size: 14px;color: #0c4128"><span>@lang("messages.UnAuthorized Days") : </span><span id="get_difference_date_unauthorized_span" style="font:bold 30px Aldhabi;color:  green"></span></span>
                                    <br/>
                                    <span  style="font-weight: bold;font-size: 14px;color: #0c4128"><span>@lang("messages.Total Days") : </span><span id="total_annual_unauthorized" style="font:bold 30px Aldhabi;color:  green"></span></span>
                                </div>

                                <div class="form-floating">
                                    <select @if($absence_attendance_type_id=="62" or $absence_attendance_type_id=="1061") required @else @endif name="replacement_employee_number" class="form-control">
                                        <option class="form-control" value=""></option>
                                        @foreach($emp_department as $emp)
                                            <option class="form-control" value="{{$emp->employee_number}}">{{$emp->employee_number}} - {{$emp->full_name}}</option>
                                        @endforeach
                                    </select>
                               <label for="start_date">@lang('messages.Replacement Employee Number')</label>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-4"></div>
                                <div class="col-4">
                                    <button class="btn btn-primary w-100 h-100" type="submit">@lang('messages.Send')</button>
                                </div>
                                <div class="col-4"> </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 col-md-12 wow fadeInUp" data-wow-delay="0.5s"></div>
            </div>
        @endif

    </div>
</div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">@lang("messages.Submit")</h5>
                </div>
                <div class="modal-body">
                    <p>@lang("messages.accured_part4") <span style="font-weight: bold;color: green" id="daysRemaining"></span>  @lang("messages.accured_part1")  <br/> @lang("messages.accured_part2")  <span style="font-weight: bold;color: green"> <span id="start_date_unauthrized" style="font-weight: bold;color: green"></span> </span>  </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelButton" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmButton">Confirm</button>


                </div>
            </div>
        </div>
    </div>


    @include('includes._footer')
    <script>
        $(document).ready(function() {
            $('#cancelButton').click(function() {
                $('#confirmationModal').modal('hide');
            });
             $('#confirmButton').click(function() {
                 $('#end_date_unathorized_input').show();
                 $('#start_date_unathorized_input').show();
                     var end_date_unathorized_input_date = new Date($('#end_date_unathorized_input_date').val());
                     var start_date_unathorized_input_date = new Date($('#start_date_unathorized_input_date').val());

                 $('#end_date_unathorized_input input').val()
                 var difference = end_date_unathorized_input_date.getTime() - start_date_unathorized_input_date.getTime();
                 var differenceInDays = difference / (1000 * 3600 * 24) + 1;
                 console.log("differenceInDays"+differenceInDays)
                 $('#get_difference_date_unauthorized_span').text(differenceInDays + "  {{__('messages.days')}}");
                 var total_unauthorized = $('#get_difference_date_unauthorized_span').text();
                 var total_annual = $('#get_difference_date').text();
                 var total = parseInt(total_unauthorized) + parseInt(total_annual);

                 $('#total_annual_unauthorized').text(total + "  {{__('messages.days')}}");
                 $('#confirmationModal').modal('hide');
             });

            @if($absence_attendance_type_id=="62")
            $('#start_date').change(async function () {
                var start_date = new Date($('#start_date').val());

                try {
                    const response = await fetch("{{ route('get-accruals') }}", {
                        method: 'POST',
                        body: JSON.stringify({
                            start_date: start_date,
                            _token: "{{ csrf_token() }}"
                        }),
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data.results) {
                            console.log(data.results);
                            $('#get_Accruals').text(data.results + ' {{__('messages.days')}}');
                            $('#get_Accruals_data').val(data.results);
                        }
                    } else {
                        console.error("Failed to fetch data");
                    }
                } catch (error) {
                    console.error("An error occurred:", error);
                }
            });

            {{--$('#end_date_unathorized_input_date').change(function () {--}}
            {{--    var end_date_unathorized_input_date = new Date($('#end_date_unathorized_input_date').val());--}}
            {{--    var start_date_unathorized_input_date = new Date($('#start_date_unathorized_input_date').val());--}}

            {{--    if (end_date_unathorized_input_date <= start_date_unathorized_input_date) {--}}
            {{--        // End date is not greater than start date--}}
            {{--        alert('End date must be greater than start date.');--}}
            {{--        // $('#end_date_unathorized_input input').val(start_date_unathorized_input_date.toISOString().substring(0, 10))--}}
            {{--        $('#end_date_unathorized_input input').val(end_date.toISOString().substring(0, 10))--}}
            {{--    }else{--}}
            {{--        var difference = end_date_unathorized_input_date.getTime() - start_date_unathorized_input_date.getTime();--}}
            {{--        var differenceInDays = difference / (1000 * 3600 * 24) + 1;--}}
            {{--        $('#end_date_unathorized_input input').val(end_date.toISOString().substring(0, 10))--}}
            {{--        $('#get_difference_date_unauthorized_span').text(differenceInDays + "  {{__('messages.days')}}");--}}

            {{--        var total_unauthorized = $('#get_difference_date_unauthorized_span').text();--}}
            {{--        var total_annual = $('#get_difference_date').text();--}}
            {{--        var total = parseInt(total_unauthorized) + parseInt(total_annual);--}}

            {{--        $('#total_annual_unauthorized').text(total + "  {{__('messages.days')}}");--}}
            {{--    }--}}


            {{--});--}}

            $('#start_date , #end_date').change(async function() {
                var start_date = new Date($('#start_date').val());
                var end_date = new Date($('#end_date').val());
                try {
                    $('#end_date_unathorized_input input').val(end_date.toISOString().substring(0, 10));
                } catch (e) {
                }
                console.log(start_date, end_date);

                if (start_date == "Invalid Date" || end_date == "Invalid Date") {
                    $('#get_Accruals').html("");
                    $('#get_difference_date').text("");
                } else {
                    var daysToAdd = null;
                    try {
                        const response = await fetch("{{ route('get-accruals') }}", {
                            method: 'POST',
                            body: JSON.stringify({
                                start_date: start_date,
                                _token: "{{ csrf_token() }}"
                            }),
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        });

                        if (response.ok) {
                            const responseData = await response.json();
                            if (responseData.results) {
                                daysToAdd = parseInt(responseData.results); // Extract numerical value
                                calculateDates(daysToAdd);
                            }
                        }
                    } catch (error) {
                        console.error("Error fetching accruals:", error);
                    }

                    function calculateDates(daysToAdd) {
                        var start_date = new Date($('#start_date').val());
                        var endDate = new Date($('#end_date').val());
                        var differenceInMilliseconds = endDate - start_date;
                        var differenceInDays = differenceInMilliseconds / (1000 * 3600 * 24);
                        var accured = daysToAdd;
                        $('#get_difference_date').text(parseInt(differenceInDays) + " {{__('messages.days')}}");

                        if (differenceInDays > accured) {
                            fetch("{{ route('get-accruals') }}", {
                                method: 'POST',
                                body: JSON.stringify({
                                    start_date: start_date,
                                    _token: "{{ csrf_token() }}"
                                }),
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            }).then(response => response.json())
                                .then(responseData => {
                                    if (responseData.results) {
                                        $('#get_Accruals').text(responseData.results + ' {{__('messages.days')}}');
                                        $('#get_Accruals_data').val(responseData.results);
                                        var start_date = new Date($('#start_date').val());
                                        var endDate = new Date(start_date.getTime());
                                        var daysToAdd = parseInt(responseData.results); // Extract numerical value
                                        endDate.setDate(start_date.getDate() + daysToAdd);
                                        var end_date_anuual = new Date(endDate);
                                        var difference = end_date_anuual.getTime() - start_date.getTime();
                                        var differenceInDays = difference / (1000 * 3600 * 24);
                                        $('#get_difference_date').text(parseInt(differenceInDays) + " {{__('messages.days')}}");
                                        if (differenceInDays >= parseInt(responseData.results)) {
                                            $('#get_difference_date').text(parseInt(differenceInDays) + " {{__('messages.days')}}");
                                            var daysRemaining = responseData.results;
                                            $('#daysRemaining').text(daysRemaining);
                                            $('#confirmationModal').modal('show');
                                            $('#get_difference_date_unauthorized_div').show(); // Make the element appear
                                            $('#get_difference_date_unauthorized_span').text(""); // Update the value of the span element
                                            $('#end_date').val(end_date_anuual.toISOString().substring(0, 10));
                                            var start_date_unaut_addOne = new Date(end_date_anuual);
                                            start_date_unaut_addOne.setDate(start_date_unaut_addOne.getDate() + 1);
                                            $('#start_date_unathorized_input input').val(start_date_unaut_addOne.toISOString().substring(0, 10));
                                        }
                                    }
                                }).catch(error => {
                                console.error("Error fetching accruals:", error);
                            });
                        }
                    }
                }
            });

            @endif


        });
    </script>
</body>

<script>
    var today = new Date();
    document.getElementById('out-date').innerText  = today;
</script>
@if(isset($absence_attendance_type_id))

    @if( $absence_attendance_type_id == 70)
    @else
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var inputs = document.querySelectorAll('input[type="date"]');
                var inputs2 = document.querySelectorAll('input[type="datetime-local"]');
                var today = new Date().toISOString().split('T')[0];

                for (var i = 0; i < inputs.length; i++) {
                    inputs[i].setAttribute('min', today);
                }

                for (var j = 0; j < inputs2.length; j++) {
                    // Set the minimum value to the current date in Riyadh without time
                    var riyadhDate = new Date(new Date().toLocaleString("en-US", { timeZone: "Asia/Riyadh" }));
                    var riyadhDateISO = riyadhDate.toISOString().split('T')[0];
                    inputs2[j].setAttribute('min', riyadhDateISO);
                }
            });

        </script>
    @endif
@endif
@if(session()->get('employee_not_assign') == "true")
    <script>
        setTimeout(function() {
            window.location.href = "/public/index.php/home";
        }, 3000);
    </script>
@endif

</html>
