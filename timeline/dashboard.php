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
        <?php if ($debug_mode) { ?>
        <!-- All CSS Stylesheets -->
        <link href="css/vis.css" rel="stylesheet">
        <link href="css/bootstrap_dark.css" rel="stylesheet" id="theme">
        <link href="css/sticky-footer.css" rel="stylesheet">
        <link href="css/bootstrap.icon-large.min.css" rel="stylesheet">
        <link href="css/custom_dashboard.css" rel="stylesheet">                
        <link href="js/jquery.qtip.custom/jquery.qtip.css" rel="stylesheet">
        <link href="js/json_human/json.human.css" rel="stylesheet">
        <!--<link href="css/emoji.css" rel="stylesheet" type="text/css" /> -->
        <link href="css/emojify.css" rel="stylesheet" >
        <?php } else {  ?>
        <link href="css/dashboard_all-minified.css" rel="stylesheet">
        <?php } ?>

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
    <nav id="menunav" class="navbar navbar-default navbar-fixed-top">      
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
                                        <b>Devops Email :</b>  <a href=<?php echo '"mailto:' . $config_data['contact_mail'] . '"'; ?> ><?=$config_data['contact_mail']?></a><br><br>
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
                                <!-- <li>
                                    <a href="account"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp &nbsp Account</a>
                                </li> -->
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
    <div class="container col-md-12 col-md-offset-0" >
            <!-- Begin page content -->
            <div id="visualization">                
                <div class="menu">
                        <button type="button" class="btn btn-sm btn-default" id="share" data-toggle="modal" data-target="#share_modal">Share</button>
                        <button type="button" class="btn btn-sm btn-default" id="zoomIn"><span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></button>
                        <button type="button" class="btn btn-sm btn-default" id="zoomOut"><span class="glyphicon glyphicon-zoom-out" aria-hidden="true"></button>
                        <button type="button" class="btn btn-sm btn-default" id="moveLeft"><span class="glyphicon glyphicon-menu-left" aria-hidden="true"></button>
                        <button type="button" class="btn btn-sm btn-default" id="moveRight"><span class="glyphicon glyphicon-menu-right" aria-hidden="true"></button>
            		    <button type="button" class="btn btn-sm btn-default" id="button1">Now</button>                        
                </div>
            </div> <!-- Timeline -->

    </div>
    <footer class="footer">
      <div class="container" style="margin:10px">
        <p class="text-muted">Please report bugs to <a href="<?=$config_data['bug_report']?>"><?=$config_data['bug_report']?></a></p>
      </div>
    </footer>

    <?php if ($debug_mode) { ?>

    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

    <script type="text/javascript" src="js/vis.js"></script>
    <script type="text/javascript" src="js/moment.min.js"></script>    
    <script type="text/javascript" src="js/light_spinner/ajax-loading.js"></script>    
    <script type="text/javascript" src="js/jquery.qtip.custom/jquery.qtip.js"></script>    
    <!--<script type="text/javascript" src="js/json_human/json.human.js"></script> -->
    <script type="text/javascript" src="js/clipboard.js"></script>
    <script type="text/javascript" src="js/lightMarkdown.js"></script>
    <!--<script type="text/javascript" src="js/emoji.js" ></script> -->
    <script type="text/javascript" src="js/emojify.js"></script>
    <script type="text/javascript" src="js/custom_dashboard.js"></script>

    <?php } else {?>
    <script type="text/javascript" src="js/dashboard_all-minified.js"></script>

    <?php } ?>

    <script>

    </script>
    <!-- Form Validation JS -->
    <script type="text/javascript">
        
    
</script>
<!-- Modal -->
        <div class="modal" id="share_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"  aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="customModal">Share Link</h4>
                    </div>
                    <div class="modal-body centered">
                        <textarea id="linktext" name="textarea" rows="5"  readonly></textarea>
                    </div>
                    <div class="modal-footer">
                        <button id="copyToClipboard" data-clipboard-target="#linktext" type="button" class="btn btn-primary">Copy to Clipboard</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>                                        
                    </div>
                </div>
            </div>
        </div>
</body>
</html>
