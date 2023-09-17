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
            <h6 class="section-title bg-white text-center text-primary px-3">@lang('messages.UpdateDelegation') </h6>
        </div>
        <br>
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
          </div>
        <br>
            <div class="row g-4">

                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">

                </div>
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <form action="{{route("update-delegate")}}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <input type="hidden" class="form-control" name="delegate_from_emp" id="delegate_from_emp" value="{{$delegate_record->delegate_from_emp}}">
                                <input type="hidden" class="form-control" name="delegate_id" id="delegate_id" value="{{$delegate_record->delegate_id}}">


                                <div class="form-floating">
                                    <select name="delegate_to_emp" class="form-control">
                                        <option class="form-control" value=""></option>
                                        @foreach($emp_department as $emp)
                                            <option class="form-control" value="{{$emp->employee_number}}">{{$emp->full_name}} - {{$emp->employee_number}}</option>
                                        @endforeach
                                    </select>
                                    <label for="delegate_to_emp">@lang('messages.delegate_to_emp')</label>
                                </div>
                                <br/>
                                <div class="form-floating">
                                    <input type="text" value="{{ $delegate_record->delegate_from_date }}" class="form-control" name="delegate_from_date" id="delegate_from_date" placeholder="Subject">
                                    <label for="delegate_from_date">@lang('messages.delegate_from_date')</label>
                                </div>
                                <br/>
                                <div class="form-floating">
                                    <input type="text" value="{{ $delegate_record->delegate_to_date }}" class="form-control" name="delegate_to_date" id="delegate_to_date" placeholder="Subject">
                                    <label for="delegate_to_date">@lang('messages.delegate_to_date')</label>
                                </div>
                                <br/>
                                <div class="form-floating">
                                    <select name="delegation_status" class="form-control">
                                        <option class="form-control" value=""></option>
                                        <option class="form-control" value="Active">Active</option>
                                        <option class="form-control" value="Inactive">InActive</option>
                                    </select>
                                    <label for="delegation_status">@lang('messages.delegation_status')</label>
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


    </div>
</div>
<!-- Contact End -->





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
    var today = new Date();
    document.getElementById('out-date').innerText  = today;
</script>
</html>
