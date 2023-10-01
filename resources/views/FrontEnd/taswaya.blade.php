<!DOCTYPE html>
<html lang="en">
@php
    $accrued_balance= new \App\Http\Repository\MainOracleQueryRepo();
    @endphp
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
            <h6 class="section-title bg-white text-center text-primary px-3">@lang('messages.taswaya') </h6>
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
                        <div style=" overflow: auto;">
                            <table id="absense_table" class="table table-striped bg-dark" style="width:100%; color: white">
                                <thead style="background: #9b6118">
                                <tr>
                                    <th>@lang('messages.SerialNumber')</th>
                                    <th scope="col">@lang('messages.mr_ServiceName') </th>
                                    <th scope="col">@lang('messages.mr_EmployeeNumber') </th>
                                    <th scope="col">@lang('messages.mr_EmployeeName') </th>
                                    <th scope="col">@lang('messages.mr_StartDate') </th>
                                    <th scope="col">@lang('messages.mr_EndDate') </th>
                                    <th scope="col">@lang('messages.mr_Status') </th>
                                    <th scope="col"> </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($absence_requests as $item)
                                  <tr>
                                      <td style="color: white; font-size: 15px;">{{$loop->iteration }}</td>
                                      <td style="color: white">{{$item->absence_type}}</td>
                                      <td style="color: white">{{$item->empno}}</td>
                                      <td style="color: white">{{$item->emp_name}}</td>
                                      <td style="color: white">{{$item->absence_start_date}}</td>
                                      <td style="color: white">{{$item->absence_end_date}}</td>
                                      <td style="color: white">{{$item->approval_status}}</td>
                                      @if($item->taswiath_status!=null)
                                      @else
                                          <td>
                                              <div style="display: flex;justify-content: space-between;">
                                                  <a href="#">
                                                      <button
                                                          data-transaction_id="{{$item->transaction_id}}"
                                                          type="button"
                                                          class="btn btn-success confirmationClick"
                                                          data-toggle="modal" data-target="#confirmation"><i
                                                              class="fas fa-check"></i>
                                                      </button>
                                                  </a>
                                                  <a href="#">
                                                      <button
                                                          data-transaction_id="{{$item->transaction_id}}"
                                                          type="button"
                                                          class="btn btn-danger addedNoteClick"
                                                          data-toggle="modal" data-target="#addedNote"><i
                                                              class="fas fa-sharp fa-solid fa-x fa-bounce fa-lg"
                                                              style="color: #c92233;"></i>X
                                                      </button>
                                                  </a>
                                                  @php
                                                      $date = \Carbon\Carbon::create($item->absence_start_date);
                                                      $formattedDate = $date->format('d-M-Y');
                                                       $number_accural = $accrued_balance->accrued_balance($item->empno,$formattedDate);
                                                  @endphp
                                                  <a href="#">
                                                      <button
                                                          data-start_date="{{$item->absence_start_date}}"
                                                          data-end_date="{{$item->absence_end_date}}"
                                                          data-accurals_no="{{$number_accural}}"
                                                          data-taswiath_status="{{ $item->taswiath_status == null ? '' : $item->taswiath_status }}"
                                                          data-taswiath_note="{{$item->taswiath_note}}"
                                                          type="button"
                                                          class="btn btn-warning showAccutalsDetailsClick"
                                                          data-toggle="modal"
                                                          data-target="#showAccutalsDetails">

                                                          <i class="fa fa-bars" aria-hidden="true"></i>
                                                      </button>
                                                  </a>
                                              </div>

                                          </td>
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

    </div>

   @include('includes._footer')

    <div class="modal fade" id="addedNote" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div>
                        <form action="{{route('reject-request-taswaya')}}" method="post">
                            @csrf
                            <input type="hidden" name="transaction_id" id="transaction_id" value=""/>
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1"
                                       style="float: left;">@lang('messages.Added_Notes')</label>
                                <textarea name="note" class="form-control" id="note" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" style="margin: 17px 166px;width: 163px"
                                        class="btn btn-primary align-content-center">@lang('messages.Yes')</button>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('messages.Close')</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="confirmation" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div style="text-align: center">
                        <div>
                            <form action="{{route('approve-request-taswaya')}}" method="post">
                                @csrf

                                <input type="hidden" name="transaction_id" id="transaction_id" value=""/>
                                <div class="form-group">
                                    <label for="exampleFormControlTextarea1"
                                           style="float: left;">@lang('messages.Added_Notes')</label>
                                    <textarea name="note" class="form-control" id="note" rows="3"></textarea>
                                </div>
                                <div class="form-group">

                                    <button type="submit" style="margin: 17px 166px;width: 163px"
                                            class="btn btn-primary">@lang('messages.Yes')</button>
                                </div>

                            </form>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                        data-dismiss="modal">@lang('messages.Close')</button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="showAccutalsDetails" tabindex="-1" role="dialog" aria-labelledby="showAccutalsDetails" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div style="text-align: center">
                        <div>
                            <p><strong>@lang("messages.StartDate"):</strong> <span id="modal-start-date"></span></p>
                            <p><strong>@lang("messages.EndDate"):</strong> <span id="modal-end-date"></span></p>
                            <p><strong>@lang("messages.Accurals"):</strong> <span id="modal-accurals-no"></span></p>
                            <p><strong>@lang("messages.Differnce_Date"):</strong> <span id="modal-differnceDate"></span></p>
                            <p><strong>@lang("messages.taswiath_status"):</strong> <span id="modal-taswiath_status"></span></p>
                            <p><strong>@lang("messages.taswiath_note"):</strong><span id="modal-taswiath_note"></span></p>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('messages.Close')</button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>
