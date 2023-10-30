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
                                    <th>@lang('messages.ClearId')</th>
                                    <th>@lang('messages.eos_clearance_status')</th>

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
                                        <td style="color: white; font-size: 15px;">{{$absence->clearance_id}}</td>
                                        <td style="color: white; font-size: 15px;">{{$absence->eos_clearance_status}}</td>

                                        <td>
                                            <div class="d-inline-flex justify-content-around">
                                                <a href="{{route("get-details",['transaction_id'=>$absence->transaction_id])}}">
                                                    <button class="btn btn-warning" type="button"><i class="fas fa-bars" aria-hidden="true"></i></button>
                                                </a>
                                                @if($absence->eos_clearance_status=="Fully Approved" and $absence->eos_taswiah_status=="0")
                                                    <button class="btn btn-success initial-clearance-button" data-transx="{{$absence->transaction_id}}" data-toggle="modal" data-target="#initialClearanceModal" type="button">
                                                        <i class="fas fa-plus" aria-hidden="true"></i>
                                                    </button>

                                                @endif
                                            </div>

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


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listener for button click
            var initialClearanceButtons = document.getElementsByClassName('initial-clearance-button');

            Array.from(initialClearanceButtons).forEach(function(button) {
                button.addEventListener('click', function() {
                    // Get the transaction_id from the data attribute
                    var transactionId = this.getAttribute('data-transx');


                    // Set the value in the modal input field
                    document.getElementById('transx_id').value = transactionId;
                });
            });
        });
    </script>

    <!-- Modal -->
    <div class="modal fade" id="initialClearanceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('messages.clea_initia')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div style="text-align: left">
                        <input type="hidden" class="form-control" id="transxId" value="">
                        <div style="font-weight: bold;color: red">
                            <ul>
                                <li>@lang('messages.note_clea_1')</li>
                                <li>@lang('messages.note_clea_2')</li>
                            </ul>
                            <p>@lang('messages.note_clea_3')</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('messages.clear_no_btn')</button>
                    <button type="button" class="btn btn-primary"  id="continue_btn"  data-toggle="modal" data-target="#ContinueClearanceModal">@lang('messages.clear_contin_btn')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ContinueClearanceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('messages.clear_fields')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="terminationForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="transx_id">
                        <div class="form-group">
                            <label for="actualTerminationDate">@lang('messages.clear_actual')</label>
                            <input type="date" class="form-control" id="actualTerminationDate" name="actualTerminationDate" required>
                        </div>
                        <div class="form-group">
                            <label for="deptRecovery">@lang('messages.deptRecovery')</label>
                            <select class="form-control" id="deptRecovery" name="deptRecovery" required>
                                <option value="Y">@lang('messages.clear_ye')</option>
                                <option value="N">@lang('messages.clear_no')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="noticePeriodExemption">@lang('messages.clear_notice')</label>
                            <select class="form-control" id="noticePeriodExemption" name="noticePeriodExemption" required>
                                <option value="Y">@lang('messages.clear_ye')</option>
                                <option value="NE">@lang('messages.clear_no_E')</option>
                                <option value="NC">@lang('messages.clear_no_C')</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="close_btn"  data-dismiss="modal">@lang('messages.clear_no_btn')</button>
                        <button type="submit" class="btn btn-primary" id="save_changes_btn" >@lang('messages.clear_save_btn')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>





    <!-- JavaScript to handle form submission -->
    <script>
        document.getElementById('terminationForm').addEventListener('submit', function(event) {
            event.preventDefault();
            // Access form data
            var actualTerminationDate = document.getElementById('actualTerminationDate').value;
            var deptRecovery = document.getElementById('deptRecovery').value;
            var transx_id = document.getElementById('transx_id').value;
            var noticePeriodExemption = document.getElementById('noticePeriodExemption').value;

            $('#initialClearanceModal').css('display', 'none');
            $('#save_changes_btn').css('display', 'none');
            $('#close_btn').css('display', 'none');

            $.ajax({
                url: "{{ route('insert_eos') }}",
                method: 'POST',
                data: {
                    transaction_id: transx_id,
                    actualTerminationDate: actualTerminationDate,
                    deptRecovery: deptRecovery,
                    noticePeriodExemption: noticePeriodExemption,
                    _token: "{{ csrf_token() }}"
                },

                success: function (response) {
                    if (response) {
                        $('#ContinueClearanceModal .modal-body').html('<div id="loadingIcon" class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i></div>');
                        window.location.href = '{{env('APP_URL')}}/clearance';
                    }
                }

            });


        });

    </script>
</body>

</html>
