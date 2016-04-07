<?php
        session_start();
        require_once('config.php');
        require 'vendor/autoload.php';
        require_once('helpers.php');
        $logger = new Katzgrau\KLogger\Logger("$logdir");
        
        ### Set the apache note ###
        apache_note('username', $_SESSION['cn']);

        if (!$_SESSION['user_authenticated']) {
            $_SESSION['previous_url'] = $_SERVER['REQUEST_URI'];             
            header("Location: /");            
        }
                
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="TimeLine Application" content="">
        <meta name="Uday Dhokale/Jose Garcia" content="">
        <!-- End of META tags -->
        <!-- Favicon and Title -->
        <link rel="icon" href="images/favicon.ico">
        <title>TimeLine</title>
        <!-- All CSS Stylesheets -->
        <link href="css/bootstrap_dark.css" rel="stylesheet" id="theme">
        <link href="css/sticky-footer.css" rel="stylesheet">
        <link href="css/formValidation.min.css" rel="stylesheet">
        <link href="css/bootstrap.icon-large.min.css" rel="stylesheet">
        <link href="css/custom_timeline.css" rel="stylesheet">
        <!-- <link href="css/sc_table/css/style.css" rel="stylesheet"> -->
        <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
        <meta name="theme-color" content="#ffffff">                

    </head>
<body>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">      
        <div class="container">
            <div class="navbar-header">
            <!-- Brand and toggle get grouped for better mobile display -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <!-- Upwork Navbrand -->
                <a class="navbar-brand" href="/"><strong><img src="<?=$config_data['company_logo']?>" alt="timeline" height="25" width="90"></strong></a>
            </div> 
            <!-- End of navbar-header. This will collapse in mobile view -->
            <div id="navbar" class="navbar-collapse collapse">
                <!-- Navbar left start -->
                <ul class="nav navbar-nav">
                    <li><a href="#" data-toggle="modal" data-target="#bannerformmodal">About <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a></li>
                        <div class="modal fade bannerformmodal" tabindex="-1" role="dialog" aria-labelledby="bannerformmodal" aria-hidden="true" id="bannerformmodal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">Author: Uday Dhokale/Jose Garcia</h4>
                                    </div>
                                    <!-- Modal Body -->
                                    <div class="modal-body">
                                        <p>Timeline is an application that helps you view events from multiple alerting sources on a common timeline. This project is desgined to debug issues faster and reduce the MTTR(Mean Time To Resolve) of the issues. </p>
                                    </div>
                                    <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div> <!-- end of model content -->
                            </div> <!-- end of modal dialog --> 
                        </div>
                        <!-- end of modal -->
                    <li>
                        <a href="#" data-toggle="modal" data-target="#supportmodal">Support <span class="glyphicon glyphicon-earphone" aria-hidden="true"></span></a>
                    </li>
                        <div class="modal fade supportmodal" tabindex="-1" role="dialog" aria-labelledby="supportmodal" aria-hidden="true" id="supportmodal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="supportmodal"><b>Contact Information </b></h4>
                                    </div>
                                    <!-- Modal Body -->
                                    <div class="modal-body">
                                        <b>Devops Email :</b>  <a href="mailto:<?=$config_data['contact_mail']?>"><?=$config_data['contact_mail']?></a><br><br>
                                    </div>
                                    <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div> <!-- end of model content -->
                            </div> <!-- end of modal dialog --> 
                        </div>
                        <!-- end of modal -->
                            <li>
                                <a href="integrations"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span>&nbsp Integration</a>
                            </li>
                            <li>
                                <a href="dashboard"><span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span>&nbsp Dashboard</a>
                            </li>
                </ul>
                <!-- End of Left Navbar -->
                <!-- Start of Right Navbar -->
                <ul class="nav navbar-nav navbar-right">                
                    <li>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Settings &nbsp<span class="glyphicon glyphicon-cog" aria-hidden="true"></span><span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">                                
                                <li>
                                    <a href="logout"><span class="glyphicon glyphicon-off" aria-hidden="true"></span>&nbsp &nbsp Logout</a>
                                </li>
                            </ul>
                    </li>
                </ul> <!-- End of Right Navbar -->
            </div><!--/.nav-collapse -->
        </div> <!-- End of container -->
    </nav>
    <!-- End of Navigation Code -->


    <!-- Begin page content -->
    <div class="container col-md-10 col-md-offset-1 top-separator" > 

        <div class="row">
            <div class="col-md-2 pull-left">
                <button type="button" class="btn btn-lg" data-toggle="modal" data-target="#newIntegration">
                    + Add Integration
                </button>                
            </div>                        
            <div class="col-md-2 pull-right">            
                <button type="button" id="SendToDashboard" class="btn btn-lg" data-toggle="modal" data-target="">
                    Send to Dashboard
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 pull-left">
                <input type="text" id="livesearch" class="form-control" placeholder="Search">                
            </div>
            <div class="col-md-2 pull-left">
                <select id="livesearchkey" class="form-control">
                    <option value="1">Integration Name</option>
                    <option value="2">Owner</option>
                    <option value="3">Type</option>  
                </select>                
            </div>
        </div>

                
        

        <table id='mytable' class="table table-striped responsive-table responsive-table-input-matrix">
          <thead>
            <tr>
              <th>#</th>
              <th>Integration Name</th>
              <th>API Key</th>
              <th>Owner</th>
              <th>Created On</th>
              <th>Type</th>
              <th>Action</th>              
            </tr>
          </thead>
          <tbody>
    <?php
         
        $index = 1;
        $integrations = get_all_integrations(); 
        $scheme = $_SERVER['REQUEST_SCHEME'] . '://';
        $domainName = $_SERVER['SERVER_NAME'].'/';       	
        $url = 'https://' . $domainName;

        //Generate the table data	
        foreach($integrations as $integration) {            
            $api_url = $url . 'api/webhooks/' . $integration->integration_name . '/' . $integration->apiKey;            
            $api_link = '<a href="' . $api_url . '">' . $integration->apiKey . '</a>';
            echo "<tr>
                    <td>{$index}</td>
                    <td>{$integration->integration_name}</td>
                    <td>{$api_link}</td><td>{$integration->user}</td>
                    <td>{$integration->createdOn}</td>
                    <td>{$integration->type}</td><td>"; 
          ?>
            <button class="btn btn-danger btn-xs" data-title="Delete" data-toggle="modal"  data-target="#delete" <?php echo ' data-id="' . $integration->integration_name . '"'; ?>  ><span class="glyphicon glyphicon-trash"></span></button></td></tr>
        <?php            
          $index++;          
        }
        
    ?>
         </tbody>
       </table>    
   </div>
    <footer class="footer">
      <div class="container" style="margin:10px">
        <p class="text-muted">Please report bugs to <a href="mailto:<?=$config_data['bug_report']?>"><?=$config_data['bug_report']?></a></p>
      </div>
    </footer>


    <div class="modal" id="delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
                    <div class="modal-content">
                            <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                                    <h4 class="modal-title custom_align" id="Heading">Delete Integration</h4>
                            </div>
                            <div class="modal-body">
                                    <div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span> Are you sure you want to delete this Integration?</div>
                            </div>
                            <div class="modal-footer ">
                                    <button id="delete-button" type="button" class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok-sign"></span> Yes</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> No</button>
                            </div>
                    </div>
        <!-- /.modal-content --> 
            </div>
      <!-- /.modal-dialog -->             
    </div>

    <!-- Modal -->
        <div class="modal fade" id="newIntegration" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add New Integration</h4>
                </div>
                <div class="modal-body">
                        <form class="form-horizontal" name="commentform" method="post" action="">
                            <div class="form-group">
                                <label class="control-label col-md-4" for="integration_name">Integration Name</label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="integration_name" name="integration_name" placeholder="uniqueName"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label col-md-4" for="type">Type</label>
                                <div class="col-md-6">
                                    <select id="type" name="type" class="form-control">
                                        <option>pagerduty</option>
                                        <option>custom</option>
                                        <option>newrelic</option>                                    
                                    </select>
                                </div>
                            </div>                            
                        </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="saveIntegration" type="button" class="btn btn-primary">Save</button>
                </div>
                </div>
            </div>
        </div>



        <div class="modal fade" id="genericModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
                      <div class="modal-content">
                              <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                                      <h4 class="modal-title custom_align" id="Heading">Information</h4>
                              </div>
                              <div class="modal-body">
                                Please, select a few Integrations before send to dashboard.
                              </div>
                              <div class="modal-footer ">
                                      <button id="okInfo" type="button" class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok-sign"></span></button>                                      
                              </div>
                      </div>
          <!-- /.modal-content --> 
              </div>
        <!-- /.modal-dialog -->             
      </div>


      <div class="modal" id="warningIntegrations" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
                    <div class="modal-content">
                            <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
                                    <h4 class="modal-title custom_align" id="Heading">Warning</h4>
                            </div>
                            <div class="modal-body">
                                    <div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span> 
                                        Sending too many integrations to dashboard could make your visualization not so comfortable, depending on the events number and your screen size.
                                        Maybe you want to try with 3, and see how that works.
                                    </div>
                            </div>
                            <div class="modal-footer ">
                                    <button type="button" class="btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok-sign"></span> Ok, I understand.</button>                                    
                            </div>
                    </div>
        <!-- /.modal-content --> 
            </div>
      <!-- /.modal-dialog -->             
    </div>
    






    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/formValidation.min.js"></script>
    <script src="js/formvalidate_bootstrap.min.js"></script>
    <!-- Form Validation JS -->
    <script type="text/javascript">
      $(document).ready(function() {

		user_session = <?php echo "'" . $_SESSION['cn'] . "'"; ?>;

        default_admins = ['josegarcia', 'udhokale'];

        //on delete we get the data-id of the integration
        $('#delete').on('show.bs.modal', function(e) {        
            $(this).find('.btn-success').attr('data-id', $(e.relatedTarget).attr('data-id'));
            $(this).find('.btn-success').children().attr('data-id', $(e.relatedTarget).attr('data-id'));
            console.log('btn-success: ' + $(this).find('.btn-success')[0].outerHTML);

        });

        /**
        * Generate api key link from JSON data
        *     
        */
        function get_api_link(data)
        {            
            var host_name = window.location.protocol + "//" + window.location.hostname;
            var api_url = host_name + "/api/webhooks/" + data['integration_name'] + '/' + data['apiKey'];
            var api_link = '<a href="' + api_url + '">' + data['apiKey'] + '</a>';
            return api_link;    
        }
        

        //when user click on delete button
        $('#delete-button').click(function(e) {
            data_to_delete = $(e.target).attr('data-id');        
            console.log('target: ' + $(e.target)[0].outerHTML );

            // we allow to delete to admins only
            if (data_to_delete && ((user_session === default_admins[0]) ||  (user_session === default_admins[1])))
            {
                // we query the delete API with the integration data-id
                $.ajax({
                    type: "POST",
                    url: window.location.protocol + "//" + window.location.hostname + "/api/integration/remove",
                    contentType: "application/json",
                    data: JSON.stringify({"integration_name": data_to_delete }),            
                    success: function(data) {              
                        tr_deleted = $('#mytable').find("[data-id='" + data_to_delete + "']").closest('tr');
                        //after de API delete we remove the row with an effect
                        $(tr_deleted)
                            .children('td')                    
                            .animate({ padding: 0 })
                            .wrapInner('<div />')
                            .addClass('flash_delete')
                            .children()
                            .slideUp(750, function() {                         
                                $(this).remove();
                                //we reload all the table to make sure we are updated 
                                get_all_integrations();
                            });                 
                    },
                    error: function(xhr, status, error) {                
                        alert(xhr.responseText);
                    },
                    dataType: 'json'
                });    
            }
            else
            {                        
                console.log('Something went wrong!')
            }


        });


        // on save the integration event
        $("#saveIntegration").click(function() {            
            var new_integration = $('#integration_name').val();        
            $.ajax({
                type: "POST",
                url: window.location.protocol + "//" + window.location.hostname + "/api/integration/add",
                contentType: "application/json",
                data: JSON.stringify({
                  "integration_name": new_integration,
                  "created_by": user_session,
                  "type":  $('#type').val()
                }),            
                success: function(data) {
                    //hide the modal and generate the new row td with the data               
                    $('#newIntegration').modal('hide');                    
                    var new_row = '';
                    new_row += "<td>1</td>";
                    new_row += "<td>" + data['integration_name'] + "</td>";
                    api_link= get_api_link(data);
                    new_row += "<td>"+api_link+"</td>";
                    new_row += "<td>"+data['user']+"</td>";
                    new_row += "<td>"+data['createdOn']+"</td>";
                    new_row += "<td>"+data['type']+"</td>";
                    new_row += "<td></td>";
                    new_row = "<tr>"+new_row+"</tr>";

                    //we add the new row and show a flash effect for the user to notice
                    $('#mytable').prepend($(new_row));
                    $('#mytable td:nth-child(2)').filter(function (index) {
                        if ($(this).text() == data['integration_name']) 
                        {                    
                            $(this).closest('tr').addClass('flash');                        
                        }
                      
                    });

                    //after that we reload all the integrations
                    setTimeout(function(){ get_all_integrations(); }, 2200);                                        
                                                    
                },
                error: function(xhr, status, error) {                    
                    alert(xhr.responseText);
                },
                dataType: 'json'
          });
        });

      

        /**
        * This function load all the integrations from the API and generates the tables
        * with all the data
        *     
        */
        function get_all_integrations()
        {

            $.getJSON(window.location.protocol + "//" + window.location.hostname + "/api/integrations/getAll", function(data) {
                var tbl_body = "";
                var odd_even = false;
                counter = 1;
                $.each(data, function() {
                    var tbl_row = "";            
                    tbl_row = "<td>"+counter.toString()+"</td>";
                    tbl_row += "<td>"+this['integration_name']+"</td>";
                    api_link = get_api_link(this);
                    tbl_row += "<td>"+api_link+"</td>";
                    tbl_row += "<td>"+this['user']+"</td>";
                    tbl_row += "<td>"+this['createdOn']+"</td>";
                    tbl_row += "<td>"+this['type']+"</td>";
                    delete_button = $("#mytable td button").first().attr('data-id', this['integration_name'])[0].outerHTML;
                    tbl_row += "<td>" + delete_button + "</td>";           
                    tbl_body += "<tr class=\""+( odd_even ? "odd" : "even")+"\">"+tbl_row+"</tr>";
                    counter++;
                    odd_even = !odd_even;            
                });          

                $("#mytable tbody").html(tbl_body);
                
                checkSearch();          

             }).fail(function(xhr, status, error) {
                alert(xhr.responseText);           
            });
        }


        /**
        * we make the search by the selected field on the search
        *     
        */
        function checkSearch()
        {

            // Retrieve the input field text and reset the count to zero
            var filter = $('#livesearch').val();
            var search_key = $("#livesearchkey option:selected").text();

            if (search_key == "Integration Name")
            {
                var field_selector = '#mytable td:nth-child(2)';
            } else if (search_key == "Owner") {
                var field_selector = '#mytable td:nth-child(4)';
            } else if (search_key == "Type") {
                var field_selector = '#mytable td:nth-child(6)';
            } else {
                var field_selector = '#mytable td:nth-child(2)';
            }

            // Loop through the field that is selected
            $(field_selector).each(function(){     
                // If the list item does not contain the text phrase fade it out
                if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                    $(this).closest('tr').fadeOut();                         
                // Show the list item if the phrase matches 
                } else {
                    $(this).closest('tr').show();                                        
                }
            });
                 
        }


        $("#livesearch").keyup(function(){
            checkSearch();            
        });




        /**
        * Function to validate the integration create form
        *     
        */
        FormValidation.Validator.validIntegration = {
            validate: function(validator, $field, options) {

                var value = $field.val();

                if (value === "") {
                    return {
                        valid: false,
                        message: 'Integration name not valid!'
                    };
                }

                get_all_integrations();            

                var already_exists = false;

                //we check on the table in integration name exists
                $("#mytable tr td:nth-child(2)").each(function() {
                    if ( value === $(this).text() ) {
                        already_exists = true
                        return    
                    }                              
                });


                if (already_exists === true) 
                {
                    return {
                      valid: false,
                      message: 'Integration name already Exists!'
                    };  
                }

                //we check for no valid names
                if ( (value.indexOf('$') > -1) || 
                    (value.indexOf('system.') == 0) || 
                    (value.indexOf('\0')) > -1 )
                {
                    return {
                        valid: false,
                        message: 'Not valid chars!'
                    };
                }

                validation = 1;                            

                return true;              
            }
        };


        
        
        $('#newIntegration').formValidation({
            framework: 'bootstrap',
            err: {
                container: 'tooltip'
            },
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                integration_name: {
                    validators: {
                        notEmpty: {
                            message: 'Integration Name is required and can\'t be empty'
                        },                        
                        validIntegration: {
                            message: 'Integration Name is not valid'
                        }
                    }
                },
            }
        });


        
        /**
        * This functions hightlight the selected integrations and
        * and disable the hightlight if it's already selected
        */
        $(document).on('click', '#mytable tbody tr', function () {

            if ($(this).hasClass('highlight'))
            {
                $(this).removeClass('highlight');
                $(this).find('td').removeClass('highlight');
            }
            else
            {
                $(this).addClass('highlight');
                $(this).find('td').addClass('highlight');
                var selected_trs = $('#mytable tbody tr.highlight');
                if (selected_trs.length == 4)
                {
                    $('#warningIntegrations').modal('toggle');                
                }
            }
          
        });

        $(document).on('click', '#SendToDashboard', function () {

            //we get all selected integrations
            selected = $('#mytable tbody tr').find('.highlight').parent();

            //if there is no element selected we show a message          
            if (selected.length === 0)
            {
                $('#genericModal').modal('toggle');
                return false;
            }

            var dashboard_preferences = {};
            dashboard_preferences.integrations = [];                                    

            //we generate the data preferences
            $(selected).each(function(index, tr) {            
                selected_integration = $(tr).find('td:nth-child(2)').text();
                api_key = $(tr).find('td:nth-child(3)').text();
                owner = $(tr).find('td:nth-child(4)').text();
                createdOn = $(tr).find('td:nth-child(5)').text();
                dashboard_preferences.integrations.push({
                    Integration_Name: selected_integration,
                    API_key: api_key,
                    Owner: owner,
                    Created_On: createdOn
                });
            
            });

            localStorage.setItem('dashboard_preferences', JSON.stringify(dashboard_preferences) );
            new_url = window.location.protocol + "//" + window.location.hostname + '/dashboard' ;
            window.location = new_url;


          
        });  
        







      });


      
      

      

      


</script>
</body>
</html>
