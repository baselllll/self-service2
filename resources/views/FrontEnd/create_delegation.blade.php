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


    @include('includes._navbar')

        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">@lang('messages.CreateDelegation') </h6>
            </div>
            <br>
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s"></div>
            <br>
            <div class="row">
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s"></div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <form action="{{route("store-delegate")}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <input type="hidden" class="form-control" name="delegate_from_emp" id="delegate_from_emp" value="{{session()->get("employee")->employee_number}}">
                                <div class="form-floating">
                                    <select class="form-control" id="department_select">
                                        <option value=""></option>
                                        <option value="my_department">@lang('messages.My Department')</option>
                                        <option value="other_department">@lang('messages.Other Department')</option>
                                    </select>
                                    <label for="department_select">@lang('messages.Select Department')t</label>
                                </div>
                                <br/>
                                <div class="form-floating" id="my_department_select" style="display: none;">
                                    <select name="delegate_to_emp" class="form-control">
                                        <option value="">@lang('messages.Select Person')</option>
                                        @foreach($emp_department as $emp)
                                            <option class="form-control" value="{{$emp->employee_number}}">{{$emp->full_name}} - {{$emp->employee_number}}</option>
                                        @endforeach
                                    </select>
                                    <label for="delegate_to_emp">@lang('messages.delegate_to_emp')</label>
                                </div>
                                <div class="form-floating" id="other_department_select" style="display: none;">
                                    <select class="form-control" name="delegate_to_emp_other_department" id="other_department">
                                        <option value="">@lang('messages.Select Person')</option>
                                        @foreach($GetOtherMangerDepartment as $emp)
                                            <option class="form-control" value="{{$emp->employee_number}}">{{$emp->full_name}} - {{$emp->employee_number}}</option>
                                        @endforeach
                                    </select>
                                    <label for="other_department">@lang('messages.delegate_to_emp')</label>
                                </div>
                                <br/>
                                <div class="form-floating">
                                    <input type="date" class="form-control" name="delegate_from_date" id="delegate_from_date" placeholder="Subject">
                                    <label for="delegate_from_date">@lang('messages.delegate_from_date')</label>
                                </div>
                                <br/>
                                <div class="form-floating">
                                    <input type="date" class="form-control" name="delegate_to_date" id="delegate_to_date" placeholder="Subject">
                                    <label for="delegate_to_date">@lang('messages.delegate_to_date')</label>
                                </div>
                                <br/>
                                <div class="form-floating" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($all_services as $service)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="selectedOptions[]" value="{{ $service->absence_attendance_type_id }}" id="service{{ $service->absence_attendance_type_id }}" {{ in_array($service->absence_attendance_type_id, $all_services) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="service{{ $service->absence_attendance_type_id }}">
                                                {{ $service->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <br/>
                                <div class="form-floating">
                                    <textarea name="comments" class="form-control" id="comments" rows="3"></textarea>
                                    <label for="comments">@lang('messages.Comments')</label>
                                </div>
                                <br/>
                                <div class="row g-2">
                                    <div class="col-4"></div>
                                    <div class="col-4">
                                        <button class="btn btn-primary w-100 h-100" type="submit">@lang('messages.Send')</button>
                                    </div>
                                    <div class="col-4"></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 col-md-12 wow fadeInUp" data-wow-delay="0.5s"></div>
            </div>
        </div>


  @include('includes._footer')
    <script>
        $(document).ready(function() {
            $('#start_date , #end_date').change(function() {
                var start_date = new Date($('#start_date').val());
                var end_date = new Date($('#end_date').val());
                var difference = end_date.getTime() - start_date.getTime();
                var differenceInDays = difference / (1000 * 3600 * 24) + 1;
                console.log(start_date,end_date)
                if (start_date == "Invalid Date" || end_date == "Invalid Date") {
                    $('#get_Accruals').html("");
                    $('#get_difference_date').text("");
                } else {
                    $('#get_difference_date').text(differenceInDays + " Days");
                    $.ajax({
                        url: "{{ route('get-accruals') }}",
                        method: 'POST',
                        data: {
                            start_date: start_date,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if(response.results) {
                                console.log(response.results)
                                $('#get_Accruals').text(response.results);
                                $('#get_Accruals_data').val(response.results);

                            }
                        }
                    });
                }
            });
        });
    </script>
</body>


    <script>
        $(document).ready(function() {
        $('#department_select').change(function() {
            var selectedOption = $(this).val();
            if (selectedOption === 'my_department') {
                $('#my_department_select').show();
                $('#other_department_select').hide();
            } else if (selectedOption === 'other_department') {
                $('#my_department_select').hide();
                $('#other_department_select').show();
            } else {
                $('#my_department_select').hide();
                $('#other_department_select').hide();
            }
        });
    });

</script>
<script>
    var today = new Date();
    document.getElementById('out-date').innerText  = today;
</script>
</html>
