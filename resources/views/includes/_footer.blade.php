<!-- Footer Start -->
<div id="footer_ajmi" class="container-fluid bg-dark text-light footer pt-5  wow fadeIn"  data-wow-delay="0.1s">
    <div class="container">
        <div class="">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    @lang("messages.All_Right_Reserved_By") <a class="border-bottom" href="https://alajmicompany.com/">@lang("messages.Alajmi_Department_IT")</a>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="row g-3">

                        <div class="col-12"><span id="out-date"></span></div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Footer End -->

<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('lib/wow/wow.min.js')}}"></script>
<script src="{{asset('lib/easing/easing.min.js')}}"></script>
<script src="{{asset('lib/waypoints/waypoints.min.js')}}"></script>
<script src="{{asset('lib/owlcarousel/owl.carousel.min.js')}}"></script>
<!-- Template Javascript -->
<script src="{{asset('js/main.js')}}"></script>
<script>
    $(document).ready(function () {
        var dataTable = $('#absense_table').DataTable({
            lengthChange: false,
            "order": [
                [0, "desc"]
            ]
        });
        $('#approval_status_select').on('change', function() {
            var selectedValue = $(this).val();

            console.log(selectedValue)
            if (selectedValue === '') {
                dataTable.search('').draw(); // Reset search and redraw DataTable
            } else {
                dataTable.column(7).search(selectedValue).draw(); // Perform search on column 0 (adjust if needed)
            }
        });

        var dataTable2 = $('#manger_employee_table').DataTable({
            lengthChange: false,
            "order": [
                [0, "desc"]
            ]

        });
       $('#manger_profile_employee_table').DataTable({
            lengthChange: false,
           "order": [
               [0, "desc"]
           ]
        });
    })

</script>
<script>
    $(document).ready(function () {
        var $collapseButton = $(".toggle-button-annal");
        var authorizedUnpaidVisible = false;
        var rowsToShow = $(".toggleable-row[data-absence_type='Authorized Unpaid Leave']");
        var annualLeavePending = $(".toggleable-row[data-absence_type='Annual Leave']").length > 1;
        console.log(annualLeavePending)
        if(annualLeavePending==false){
            rowsToShow.show()
        }else{
            rowsToShow.hide()
        }

        $collapseButton.click(function () {
            authorizedUnpaidVisible = !authorizedUnpaidVisible;

            var rowsToShow = $(".toggleable-row[data-absence_type='Authorized Unpaid Leave']");

            if (authorizedUnpaidVisible) {
                rowsToShow.show();
            } else {
                rowsToShow.hide();

            }
        });
    });





    $(".addedNoteClick").click(function () {
            var notify = $(this).data('notify_confirm');
            var note = $(this).data('note');
            var transaction_id = $(this).data('transaction_id');
            var type_person = $(this).data('type_person');
            var user_type = $(this).data('user_type');
            $(".modal-body #notify_confirm").val(notify);
            $(".modal-body #transaction_id").val(transaction_id);
            $(".modal-body #type_person").val(type_person);
            $(".modal-body #note").val(note);
            $(".modal-body #user_type").val(user_type);
        });

        $(".confirmationClick").click(function () {
            var notify = $(this).data('notify_confirm');
            var note = $(this).data('note');
            var transaction_id = $(this).data('transaction_id');
            var type_person = $(this).data('type_person');
            var user_type = $(this).data('user_type');
            console.log(notify,type_person)
            $(".modal-body #notify_confirm").val(notify);
            $(".modal-body #transaction_id").val(transaction_id);
            $(".modal-body #type_person").val(type_person);
            $(".modal-body #note").val(note);
            $(".modal-body #user_type").val(user_type);
        });

        $(".showAccutalsDetailsClick").click(function () {
            var start_date = $(this).data('start_date');
            var end_date = $(this).data('end_date');
            var accurals_no = $(this).data('accurals_no');
            var taswiath_status = $(this).data('taswiath_status');
            var taswiath_note = $(this).data('taswiath_note');
            $('#modal-start-date').text(start_date);
            $('#modal-end-date').text(end_date);
            $('#modal-accurals-no').text( accurals_no);

            if (taswiath_status === "1") {
                $('#modal-taswiath_status').text("@lang('messages.taswiath_message_approve')");
            } else if (taswiath_status == "0") {
                $('#modal-taswiath_status').text("@lang('messages.taswiath_message_reject')");
            }


            $('#modal-taswiath_note').text(taswiath_note);
            var startDateString = start_date;
            var endDateString = end_date;
            var startDate = new Date(startDateString);
            var endDate = new Date(endDateString);
            var timeDifference = endDate - startDate;
            var daysDifference = timeDifference / (1000 * 3600 * 24);
            $('#modal-differnceDate').text(daysDifference);

        });
</script>
</body>
<script>

   var get_local = "{{App::getLocale()}}";
    if(get_local=="ar"){
        var today = new Date();
        var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric', timeZoneName: 'long' };
        var arabicDate = today.toLocaleString('ar-EG', options);
        document.getElementById('out-date').innerText = arabicDate;
    }else{
        var today = new Date();
        document.getElementById('out-date').innerText  = today;
    }
</script>

<script>
    // Check if the firstLogin variable is set
    var isFirstLogin = "{{ isset($_GET['isFirstLogin']) ? $_GET['isFirstLogin'] : 'false' }}";
    if (isFirstLogin == true) {
        $(document).ready(function() {
            $('#welcomeModal').modal('show');
        });
    }
    $(document).ready(function() {
        $('#email_confirm').modal('show');
    });
</script>

<script>
    var empoloyee = {!! json_encode(session()->get('employee')) !!}

    if(empoloyee) {
        console.log(empoloyee)
            setTimeout(function() {
                $.ajax({
                    url: "{{route('close-different-login')}}",
                    method: 'POST',
                    data: {
                        emp_number: empoloyee.employee_number,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {

                    },
                });
            }, 1500000); // 25 minutes in milliseconds
    }

</script>



