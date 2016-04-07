<?php
  ### Include the login script to set the session and authenticate the user ###
  require_once('login.php');
  require_once('config.php');
  require 'vendor/autoload.php';
  $logger = new Katzgrau\KLogger\Logger("$logdir");

  ### Set the apache note ###
  apache_note('username', $_SESSION['cn']);

  if($_SESSION['user_authenticated']) {  
    $url = '';
    if(isset($_SESSION['previous_url'])) {                                
        $url = $_SESSION['previous_url'];
        $_SESSION['previous_url'] = '';
        if ($url)
        {
            header("Location:".$url);    
        }
        
    } 
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
        <link href="css/custom_index.css" rel="stylesheet">
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
        <div class="container clearfix">
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
                                        <b>Devops Email :</b>  <a href=<?php echo '"mailto:' . $config_data['contact_mail'] . '"'; ?>><?=$config_data['contact_mail']?></a><br><br>

                                    </div>
                                    <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div> <!-- end of model content -->
                            </div> <!-- end of modal dialog --> 
                        </div>
                        <!-- end of modal -->
                    <?php if($_SESSION['user_authenticated']) {  ?>
                            <li>
                                <a href="integrations"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span>&nbsp Integration</a>
                            </li>
                            <li>
                                <a href="dashboard"><span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span>&nbsp Dashboard</a>
                            </li>
                    <?php } ?>
                </ul>
                <!-- End of Left Navbar -->
                <!-- Start of Right Navbar -->
                <ul class="nav navbar-nav navbar-right">                    
                    <?php if($_SESSION['user_authenticated']) {  ?>                    
                    <?php      error_log('HTTP_REFERER: ' . $_SERVER['HTTP_REFERER']); ?>
                    <?php      error_log('CURRENT URI: ' ."$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>                    
                            <li>
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Settings &nbsp<span class="glyphicon glyphicon-cog" aria-hidden="true"></span><span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a href="account"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp &nbsp Account</a>
                                        </li>
                                        <li>
                                            <a href="logout"><span class="glyphicon glyphicon-off" aria-hidden="true"></span>&nbsp &nbsp Logout</a>
                                        </li>
                                    </ul>
                            </li>
                    <?php  } else { ?> 
                    <?php      error_log('HTTP_REFERER: ' . $_SERVER['HTTP_REFERER']); ?>
                    <?php      error_log('CURRENT URI: ' ."$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>                    
                    <!-- Login Form -->
                            <li class="dropdown" id="menuLogin">
                                <a class="dropdown-toggle" href="#" data-toggle="dropdown" id="navLogin">Login</a>
                                    <div class="dropdown-menu">
                                        <form id="defaultForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                            <div class="form-group">
                                                <label for="username">Username</label>
                                                <input type="username" class="form-control" id="username" name="username" placeholder="Username" >
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password</label>
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                            </div>
                                            <button class="form-group btn btn-md-lg btn-primary btn-block submit-btn" type="submit">Sign in</button>
                                        </form> 
                                    </div>
                            </li>
                    <?php  } ?>
                </ul> <!-- End of Right Navbar -->
            </div><!--/.nav-collapse -->
        </div> <!-- End of container -->
    </nav>
    <!-- End of Navigation Code -->

    <!-- Begin page content -->
    <div class="container" >
        <?php 
        if(!$_SESSION['user_authenticated'] && $_SERVER['REQUEST_METHOD'] == 'POST') {
          ?>
        <div class="col-md-6 col-md-offset-3 alert alert-danger " role="alert">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
              <div align='center'><h5>You entered a wrong password. Please enter your ldap password !!!</h5></div>
        </div> 
        <?php } ?>
        <div class="timeline-logo">  
            
        </div> 
    </div>
    <footer class="footer">
      <div class="container" style="margin:10px">
        <p class="text-muted">Please report bugs to <a href=<?php echo '"mailto:' . $config_data['bug_report'] . '"'; ?>><?=$config_data['bug_report']?></a></p>
      </div>
    </footer>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/formValidation.min.js"></script>
    <script src="js/formvalidate_bootstrap.min.js"></script>
    <!-- Form Validation JS -->
    <script type="text/javascript">
      $(".alert").delay(3000).addClass("in").fadeOut(1000);
      var referer_url = "<?php isset($referer) ? $referer : ''; ?>";
      $(document).ready(function() {        
          $('#defaultForm').formValidation({
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
                  username: {
                     validators: {
                        notEmpty: {
                            message: 'The username is required and can\'t be empty'
                        },
                        remote: {
                            type: 'POST',
                            url: 'valid_ldap_user.php',
                            delay: 1000
                        }
                     }
                  },
                password: {
                     validators: {
                        notEmpty: {
                            message: 'Password is required and can\'t be empty'
                        }
                     }
                  }
              }
          });
      });
</script>
</body>
</html>
