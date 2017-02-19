
    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4>Login</h4>
                </div>
                <div class="modal-body">
                    <form name="loginForm" id="loginForm" novalidate="">
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Email Address</label>
                                <input type="email" class="form-control" placeholder="Email Address" id="emailaddress" required="" data-validation-required-message="Please enter your email address." aria-invalid="false">
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <div class="row control-group">
                            <div class="form-group col-xs-12 floating-label-form-group controls">
                                <label>Password</label>
                                <input type="password" class="form-control" placeholder="Password" id="password" required="" data-validation-required-message="Please enter your password.">
                                <p class="help-block text-danger"></p>
                            </div>
                        </div>
                        <br>
                        <div id="success"></div>
                        <div class="row">
                            <div class="form-group col-xs-12">
                                <button type="submit" class="btn btn-default btn-block">Login</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
    <script>
        $(function() {

            $("#loginForm input,#loginForm textarea").jqBootstrapValidation({
                preventSubmit: true,
                submitError: function($form, event, errors) {
                    // additional error messages or events
                },
                submitSuccess: function($form, event) {
                    event.preventDefault(); // prevent default submit behaviour
                    // get values from FORM
                    var email = $("input#emailaddress").val();
                    var password = $("input#password").val();

                    $.ajax({
                        url: "processor.php",
                        type: "POST",
                        data: {
                            mode: "login",
                            email: email,
                            pass: password
                        },
                        dataType:"json",
                        cache: false,
                        success: function(obj) {
                            var classtype = (obj.validated ? 'success' : 'warning');
                            console.log(classtype);
                            $('#success').html("<div class='alert alert-" + classtype + "'>");
                            $('#success > .alert-' + classtype).html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                                .append("</button>");
                            $('#success > .alert-' + classtype).append(obj.message);
                            $('#success > .alert-' + classtype).append('</div>');

                            if(obj.validated){
                                //reload the page. You're logged in now.
                                location.reload();
                            }
                        },
                        error: function(ret) {
                            $('#success').html("<div class='alert alert-danger'>");
                            $('#success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                                .append("</button>");
                            $('#success > .alert-danger').append("<strong>An error has occurred. Please try again later!");
                            $('#success > .alert-danger').append('</div>');
                            console.log('fail',ret);
                        }
                    });
                },
                filter: function() {
                    return $(this).is(":visible");
                }
            });


        });

    </script>