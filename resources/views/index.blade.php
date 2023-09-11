<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit-no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BryanGlennardy-CRUD</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body>
    {{-- Form --}}
    <div class="container mt-5">

        <div class="row justify-content-center">
            <div class="card shadow col-sm-10">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="name" id="name"
                                placeholder="Enter Name">
                        </div>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="email" id="email"
                                placeholder="Enter Email">
                        </div>
                        <div class="col-sm-2" name="save">
                            <button type="button" class="btn btn-primary btn-block"
                                onclick="saveFunction()">Save</button>
                        </div>
                        <div class="col-sm-2 d-none" name="update">
                            <button type="button" class="btn btn-success btn-block updateProcessButton">Update</button>
                        </div>
                        <div class="col-sm-2 d-none" name="cancel">
                            <button type="button" class="btn btn-danger btn-block"
                                onClick="cancelFunction()">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Data Table --}}
        <div class="card mt-5 shadow">
            <div class="card-header bg-primary text-white">
                List of Data
            </div>
            <div class="card-body">

                <table class="table table-bordered w-100" id="myTable">
                    <thead>
                        <tr>
                            <th class="col-md-3">Name</th>
                            <th class="col-md-3">Email</th>
                            <th class="col-md-4">Action</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                serverside: true,

                // Access Data
                ajax: "{{ url('userAjax') }}",

                // Used Columns
                columns: [{
                    data: 'name',
                    name: 'Name',

                }, {
                    data: 'email',
                    name: 'Email',
                }, {
                    data: 'action',
                    name: 'Action',
                    orderable: false,
                    searchable: false

                }]
            });
        });

        // Global Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Function Save and Update
        const saveFunction = (id = "") => {

            if (id == "") {
                var ajax_url = 'userAjax';
                var ajax_type = 'POST';
            } else {
                var ajax_url = 'userAjax/' + id;
                var ajax_type = 'PUT';
            }

            const name = $('#name').val();
            const email = $('#email').val();
            const emailValid = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;

            if (name.length < 1 || email.length < 1) {
                swal.fire("Warning", "Form can't be empty", "info");
                return false;
            } else if (!email.match(emailValid)) {
                swal.fire("Warning", "Invalid Address", "info");
                return false;
            }

            if (id == "") {
                $.ajax({
                    url: ajax_url,
                    type: ajax_type,
                    data: {
                        name,
                        email,
                    },
                    success: function(res) {
                        $('#name').val("");
                        $('#email').val("");

                        swal.fire("Success", "Add data successfully", "success");

                        $('#myTable').DataTable().ajax.reload();
                    }
                });
            } else {

                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger mr-2'
                    },
                    buttonsStyling: false
                })
                swalWithBootstrapButtons.fire({
                    title: 'Are you sure want to update data?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update it!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: ajax_url,
                            type: ajax_type,
                            data: {
                                name,
                                email,
                            },
                            success: function(res) {
                                $('#name').val("");
                                $('#email').val("");

                                swal.fire("Success", "Update data successfully", "success");

                                $('#myTable').DataTable().ajax.reload();
                            }
                        });
                    }
                })
            }
        }

        // Function Edit
        const updateButton = (id) => {
            console.log('tes')
            $('[name="save"]').addClass('d-none');
            $('[name="name"]').attr('disabled', true);
            $('[name="update"]').removeClass('d-none');
            $('[name="cancel"]').removeClass('d-none');

            $.ajax({
                url: 'userAjax/' + id + '/edit',
                type: 'GET',
                success: function(res) {
                    const name = $('#name').val(res.result.name);
                    const email = $('#email').val(res.result.email);
                    $('.updateProcessButton').click(function() {
                        saveFunction(id);
                    })
                }
            })
        }

        // Function Cancel
        const cancelFunction = () => {
            $('[name="save"]').removeClass('d-none');
            $('[name="name"]').removeAttr('disabled');
            $('[name="update"]').addClass('d-none');
            $('[name="cancel"]').addClass('d-none');

            $('#name').val("");
            $('#email').val("");
        }

        // Function Delete
        const deleteFunction = (id) => {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger mr-2'
                },
                buttonsStyling: false
            })
            swalWithBootstrapButtons.fire({
                title: 'Are you sure want to delete data?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'userAjax/' + id,
                        type: 'DELETE',
                    });
                    swal.fire("Success", "Delete data successfully", "success");

                    $('#myTable').DataTable().ajax.reload();
                }
            })
        }
    </script>

</body>

</html>
