<?php
if (!isset($_SESSION)) session_start();

ini_set('display_errors','on');
error_reporting(E_ALL);


class pageConstructor {
    private $db;
    private $mode;
    private $postDetails;
    private $allowEdit = false;

    function __construct(){
        $this->db = mysqli_connect("localhost", "root", "root", "lotek");

        if (!$this->db) {
            echo "Error: Unable to connect to MySQL." . PHP_EOL;
            echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
            echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
            exit;
        }

        $this->allowEdit = (isset($_GET['pass']) && $_GET['pass'] == 'alphab3ta');

        if(isset($_GET['mode'])){
            $this->mode = $_GET['mode'];
        }else{
            $this->mode = 'home';
        }

        if ($this->mode=='viewpost' && isset($_GET['id'])){
            $sql = 'SELECT * from posts p INNER JOIN postdetails d ON p.id = d.postid WHERE p.id="' . intval($_GET['id']) . '" AND published = TRUE LIMIT 1';
            $result = mysqli_query($this->db,$sql);
            $this->postDetails = mysqli_fetch_array($result);
        }

    }

    // method declaration

    public function buildHead(){?>
        <head>

            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="description" content="">
            <meta name="author" content="">

            <title>Lotek and Ugly</title>

            <!-- Bootstrap Core CSS -->
            <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

            <!-- Theme CSS -->
            <link href="css/clean-blog.min.css" rel="stylesheet">

            <!-- Custom Fonts -->
            <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
            <link href='https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
            <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>

            <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
            <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
            <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
            <![endif]-->

        </head>
    <?php }
    public  function buildNav(){?>
        <nav class="navbar navbar-default navbar-custom navbar-fixed-top">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header page-scroll">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        Menu <i class="fa fa-bars"></i>
                    </button>
                    <a class="navbar-brand" href="index.php">Lotek & Ugly</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="index.php">Home</a>
                        </li>
                        <li>
                            <a href="index.php?mode=about">About</a>
                        </li>
                        <li>
                            <a href="index.php?mode=contact">Contact</a>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container -->
        </nav>
    <?php }

    public function buildHeader(){
        switch($this->mode){

            case 'viewpost':?>
            <!-- Post Page Header -->
            <header class="intro-header" style="background-image: url('img/post-bg.jpg')">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                            <div class="post-heading">
                                <h1><?php echo $this->postDetails['headline'];?></h1>
                                <h2 class="subheading"><?php echo $this->postDetails['subtitle'];?></h2>
                                <span class="meta">Posted by <a href="?author=<?php echo $this->postDetails['author'];?>"><?php echo $this->postDetails['author'];?></a> on <?php echo $this->postDetails['date'];?></p></span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <?php
            break;

            case 'about':?>
            <header class="intro-header" style="background-image: url('img/about-bg.jpg')">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                            <div class="page-heading">
                                <h1>About Me</h1>
                                <hr class="small">
                                <span class="subheading">This is what I do.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <?php
            break;

            case 'contact':?>
                <!-- Page Header -->
                <!-- Set your background image for this header on the line below. -->
                <header class="intro-header" style="background-image: url('img/contact-bg.jpg')">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                                <div class="page-heading">
                                    <h1>Contact Me</h1>
                                    <hr class="small">
                                    <span class="subheading">Have questions? I have answers (maybe).</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
            <?php
            break;

            default:
            ?>
            <!-- Blog Page Header -->
            <header class="intro-header" style="background-image: url('img/home-bg.jpg')">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                            <div class="site-heading">
                                <h1>Kitchen Tales</h1>
                                <hr class="small">
                                <span class="subheading">Finding fascination in things that are neither lo-tech, nor ugly.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
        <?php }
    }

    public function buildContent(){
    switch($this->mode){
        case 'viewpost':?>
        <!-- Post Content -->
        <article>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                        <div class="content"><?php echo $this->postDetails['bodycontent'];?></div>
                    </div>
                </div>
            </div>
        </article>
    <?php
        break;

        case 'about':?>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                    <p>Father, husband, employee, technologist, cyclist, occasionally asleep.</p>
                </div>
            </div>
        </div>
        <hr>
        <?php
        break;

        case 'contact':?>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                        <p>Want to get in touch with me? Fill out the form below to send me a message and I will try to get back to you within 24 hours!</p>
                        <!-- Contact Form - Enter your email address on line 19 of the mail/contact_me.php file to make this form work. -->
                        <!-- WARNING: Some web hosts do not allow emails to be sent through forms to common mail hosts like Gmail or Yahoo. It's recommended that you use a private domain email address! -->
                        <!-- NOTE: To use the contact form, your site must be on a live web host with PHP! The form will not work locally! -->
                        <form name="sentMessage" id="contactForm" novalidate>
                            <div class="row control-group">
                                <div class="form-group col-xs-12 floating-label-form-group controls">
                                    <label>Name</label>
                                    <input type="text" class="form-control" placeholder="Name" id="name" required data-validation-required-message="Please enter your name.">
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="row control-group">
                                <div class="form-group col-xs-12 floating-label-form-group controls">
                                    <label>Email Address</label>
                                    <input type="email" class="form-control" placeholder="Email Address" id="email" required data-validation-required-message="Please enter your email address.">
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="row control-group">
                                <div class="form-group col-xs-12 floating-label-form-group controls">
                                    <label>Phone Number</label>
                                    <input type="tel" class="form-control" placeholder="Phone Number" id="phone" required data-validation-required-message="Please enter your phone number.">
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="row control-group">
                                <div class="form-group col-xs-12 floating-label-form-group controls">
                                    <label>Message</label>
                                    <textarea rows="5" class="form-control" placeholder="Message" id="message" required data-validation-required-message="Please enter a message."></textarea>
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <br>
                            <div id="success"></div>
                            <div class="row">
                                <div class="form-group col-xs-12">
                                    <button type="submit" class="btn btn-default">Send</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <hr><?php
        break;

        default:
    ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                        <?php
                            $sql = 'SELECT * from posts WHERE published=TRUE ORDER by "date"';
                            $result = mysqli_query($this->db,$sql);
                            while($row = mysqli_fetch_array($result)){
                            ?>

                    <div class="post-preview">
                        <a href="index.php?mode=viewpost&id=<?php echo $row['id'];?>">
                            <h2 class="post-title">
                                <?php echo $row['headline'];?>
                            </h2>
                            <h3 class="post-subtitle">
                                <?php echo $row['subtitle'];?>
                            </h3>
                        </a>
                        <p class="post-meta">Posted by <a href="?author=<?php echo $row['author'];?>"><?php echo $row['author'];?></a> on <?php echo $row['date'];?></p>
                    </div>
                                <hr>

                            <?php
                            }
                        ?>

                    <!-- Pager -->
                    <ul class="pager">
                        <li class="next">
                            <a href="#">Older Posts &rarr;</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <hr>
    <?php }
    }

    public function buildFooter(){?>
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                        <ul class="list-inline text-center">
                            <li>
                                <a target="_blank"
                                   href="http://twitter.com/intent/tweet?status=I+just+read+<?php

                                   $thisURL = curPageURL(); //get the current URL
                                   $thisURL = preg_replace('/(&|\?)pass=alphab3ta/', '', $thisURL); //remove the password if present
                                   $thisURL = urlencode($thisURL);

                                    echo urlencode($this->postDetails['headline']) . ' at ' . $thisURL;?>">

                                <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-2x"></i>
                                    <i class="fa fa-twitter fa-stack-1x fa-inverse"></i>
                                </span>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.facebook.com/arallsopp">
                                <span class="fa-stack fa-lg">
                                    <i class="fa fa-circle fa-stack-2x"></i>
                                    <i class="fa fa-facebook fa-stack-1x fa-inverse"></i>
                                </span>
                                </a>
                            </li>

                        </ul>
                        <p class="copyright text-muted">Copyright &copy; Lotek 2016</p>
                    </div>
                </div>
            </div>
        </footer>
    <?php }
    public function linkScripts(){?>
        <!-- jQuery -->
        <script src="vendor/jquery/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

        <!-- Contact Form JavaScript -->
        <script src="js/jqBootstrapValidation.js"></script>
        <script src="js/contact_me.js"></script>

        <!-- Theme JavaScript -->
        <script src="js/clean-blog.min.js"></script>

        <?php
        if($this->allowEdit){?>
        <script>
            $(document).ready(function(){

                $('<button type="button"  onclick="beginEdit();">Edit</button>').insertBefore('.content');
                $('<button type="button"  onclick="endEdit();">Save Changes</button>').insertBefore('.content');

            });
            function beginEdit(){
                $('.content').attr('contenteditable', true)
            }

            function endEdit(){
                if(confirm('update page')){
                    request = jQuery.ajax({
                        url: "inlineupdate.php",
                        type: "POST",
                        data: {
                            id : <?php echo $_GET['id'];?>,
                            pass: "<?php echo $_GET['pass'];?>",
                            column : "bodycontent",
                            content : $('.content').html()
                            },
                        dataType: "html",
                        success: function(r){
                            console.log(r);
                        }
                    });

                }
            }
        </script>
        <?php }
    }
}

function curPageURL() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER['HTTTS'] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}
