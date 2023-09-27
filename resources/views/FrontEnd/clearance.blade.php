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
        <br>
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h6 class="section-title bg-white text-center text-primary px-3">@lang('messages.Delegation') </h6>
        </div>
        <br>
        <!-- Service Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row">
                    <div class="text-center wow fadeInUp" data-wow-delay="0.1s">

                    </div>
                    <div class="col-1">

                    </div>
                    <div class="col-12">
                        <div style="width: 300px ;position: absolute;top: 200px;">

                        </div>
                        <div style=" overflow: auto;">
                            <table id="absense_table" class="table table-striped bg-dark" style="width:100%; color: white">
                                <thead style="background: #9b6118">
                                <tr>
                                    <th>@lang('messages.SerialNumber')</th>
                                    <th>@lang('messages.EmpNumber')</th>
                                    <th>@lang('messages.EmpName')</th>
                                    <th>@lang('messages.AbsenceType')</th>
                                    <th>@lang('messages.CreationDate')</th>
                                    <th>@lang('messages.ApprovalStatus')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($clearance_initialed as $absence)
                                    <tr>
                                        <td style="color: white; font-size: 15px;">{{$loop->iteration }}</td>
                                        <td  style="color: white; font-size: 15px;">{{$absence->empno}}</td>
                                        <td style="color: white; font-size: 15px;">{{$absence->emp_name}}</td>
                                        <td style="color: white; font-size: 15px;">{{$absence->absence_type}}</td>
                                        <td style="color: white; font-size: 15px;">{{\Carbon\Carbon::parse($absence->creation_date)->format('Y-m-d')}}</td>
                                        <td style="color: white; font-size: 15px;">@if(str_contains($absence->approval_status,"Rejected")) Rejected @else {{$absence->approval_status}} @endif</td>
                                        <td>
                                            <a href="http://127.0.0.1:8000/home" target="_blank">
                                                <button type="button"><i class="fas fa-bullhorn" aria-hidden="true"></i></button>
                                            </a>
                                            <a href="{{route("get-details",['transaction_id'=>$absence->transaction_id])}}">
                                                <button type="button"><i class="fa fa-bars" aria-hidden="true"></i></button>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

   @include('includes._footer')

</body>

</html>
