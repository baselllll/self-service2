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
                            @if($user_type=="employee")
                            @else
                                <a  href="{{route("create-delegate")}}"> <button style="margin-top: -4px;"  class="btn btn-primary" type="button"><i class="fas fa-user">Grant</i></button></a>
                            @endif
                        </div>
                        <div style=" overflow: auto;">
                            <table id="absense_table" class="table table-striped bg-dark" style="width:100%; color: white">
                                <thead style="background: #9b6118">
                                <tr>
                                    <th>@lang('messages.delegate_from_emp')</th>
                                    <th>@lang('messages.delegate_to_emp')</th>
                                    <th>@lang('messages.delegate_from_date')</th>
                                    <th>@lang('messages.delegate_to_date')</th>
                                    <th>@lang('messages.Services')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($get_delegetion as $item)
                                  <tr>
                                      <td style="color: white; font-size: 15px;">{{$item->delegate_from_emp}}</td>
                                      <td style="color: white; font-size: 15px;">{{$item->delegate_to_emp}}</td>
                                      <td style="color: white; font-size: 15px;">{{\Carbon\Carbon::parse($item->delegate_from_date)->format('Y-m-d')}}</td>
                                      <td style="color: white; font-size: 15px;">{{\Carbon\Carbon::parse($item->delegate_to_date)->format('Y-m-d')}}</td>
                                      <td style="color: white; font-size: 15px;">
                                          @foreach($item->absence_type_ar as $absence_type)
                                              <ul>
                                                  <li>{{$absence_type->name}}</li>
                                              </ul>
                                          @endforeach
                                      </td>
                                      <td>
                                      @if($user_type=="employee")
                                          @else
                                              <a  href="{{route("delete-delegate",['delegate_id'=>$item->delegate_id])}}"><button  type="button" class="btn btn-danger addedNoteClick" data-toggle="modal" data-target="#addedNote"><i class="fas fa-trash"></i></button></a>
{{--                                              <a  href="{{route("update-view-delegate",['delegate_id'=>$item->delegate_id])}}"> <button type="button" class="btn btn-success confirmationClick" data-toggle="modal" data-target="#confirmation"><i class="fas fa-edit"></i></button></a>--}}
                                          @endif
                                      </td>
                                  </tr>
                                @endforeach
                                </tbody>
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
