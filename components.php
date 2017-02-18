<?php
if (!isset($_SESSION)) session_start();

ini_set('display_errors','on');
error_reporting(E_ALL);
include_once('conf/dbconfig.php');


class pageConstructor {
    private $db;
    private $mode;
    private $postDetails;
    private $loggedIn = false;
    private $userid;
    private $userAlias;
    private $page = 1;
    private $posts_per_page = 6;
    private $page_limit = 1;
    private $post_start = 0;

    function __construct($debug = false){
        debugOut('debug is ON',$debug);

        debugOut('Making connection to database',$debug);
        $this->db = $this->getConnection();

        if (!$this->db) {
            debugOut('Error: Unable to connect to MySQL.',true);
            debugOut('Debugging errno: ' . mysqli_connect_errno(),true);
            exit;
        }

        $this->loggedIn = isset($_SESSION['userid']);
        if($this->loggedIn){
            $this->userid = $_SESSION['userid'];
            $sql = 'SELECT * from users WHERE id = "' . $this->userid . '" LIMIT 1';
            $result = mysqli_query($this->db,$sql);
            $row = mysqli_fetch_array($result);
            $this->userAlias = $row['alias'];
        }

        if(isset($_GET['mode'])){
            $this->mode = $_GET['mode'];
        }else{
            $this->mode = 'home';
        }

        debugOut('Using mode: ' . $this->mode,$debug);

        if ($this->mode=='viewpost' && isset($_GET['id'])){
            $sql = 'SELECT * from posts p INNER JOIN users u ON p.userid = u.id INNER JOIN postdetails d ON p.id = d.postid WHERE p.id="' . intval($_GET['id']) . '" AND published = TRUE LIMIT 1';
            $result = mysqli_query($this->db,$sql);
            $this->postDetails = mysqli_fetch_array($result);
            debugOut('loaded post details',$debug);
        }

    }

    // method declaration
    public function isLoggedIn(){
        return $this->loggedIn;
    }
    public function getUserAlias(){
        return $this->userAlias;
    }
    public function getUserID(){
        return $this->userid;
    }

    private function setPageOffsets(){
        if(isset($_GET['page'])){
            $this->page = intval($_GET['page']);
            $this->post_start = (($this->page)-1) * $this->posts_per_page;
        }
        if($this->post_start < 0){
            $this->post_start = 0;
        }
    }

    public function getConnection(){
        $db_creds = new databaseCredentials();
        $conn = new mysqli($db_creds->address,$db_creds->user,$db_creds->pass,$db_creds->schema);
        return $conn;
    }
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
                        <?php if($this->loggedIn){ ?>
                            <li>
                                <a href="index.php?mode=admin">Administer</a>
                            </li>
                        <?php } ?>
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

    /**
     *
     */
    public function buildHeader(){
        switch($this->mode){

            case 'viewpost': ?>
            <!-- Post Page Header -->
            <header class="intro-header" style="background-image: url('<?php echo $this->postDetails['backgroundimage'];?>')">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                            <div class="post-heading">
                                <h1><?php echo $this->postDetails['headline'];?></h1>
                                <h2 class="subheading"><?php echo $this->postDetails['subtitle'];?></h2>
                                <span class="meta">Posted by <a href="?author=<?php echo $this->postDetails['alias'];?>"><?php echo $this->postDetails['alias'];?></a> on <?php echo date('l, jS F Y',strtotime($this->postDetails['date']));?></p></span>
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

            case 'admin': ?>
                <header style="background-image: url('img/contact-bg.jpg'); height:54px"></header>
            <?php
            break;

            default:
            $this->setPageOffsets(); //sets the start and end for the post query limits.
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
                        <div id="content" class="content"><?php echo $this->postDetails['bodycontent'];?></div>
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
                    <p>Father, husband, employee, technologist, cyclist, occasionally asleep, often lost, always around.</p>
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
                        <p>Want to get in touch with me? Fill out the form below to send me a message and I will try to get back to you!</p>
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

        case 'admin':?>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
                        <p>Add Post</p>
                          <form name="addPost" id="addPost" method="post" action="processor.php" enctype="multipart/form-data" novalidate>
                            <div class="row control-group">
                                <div class="form-group col-xs-12 floating-label-form-group controls">
                                    <label>Headline</label>
                                    <input type="text" class="form-control" placeholder="Headline" name="headline" id="headline" required data-validation-required-message="Please enter a headline for this post.">
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="row control-group">
                                <div class="form-group col-xs-12 floating-label-form-group controls">
                                    <label>Subtitle</label>
                                    <input type="text" class="form-control" placeholder="Subtitle" name="subtitle" id="subtitle" required data-validation-required-message="Please enter a subtitle for this post.">
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="row control-group">
                                <div class="form-group col-xs-12 floating-label-form-group controls">
                                    <label>Background Image</label>
                                    <input type="file" class="form-control" placeholder="Background Image" name="file" id="file" required data-validation-required-message="Please enter a background image.">
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                            <div class="row control-group">
                                <div class="form-group col-xs-12 floating-label-form-group controls">
                                    <label>Content</label>
                                    <textarea rows="5" class="form-control" placeholder="Content" name="content" id="content" required data-validation-required-message="Please enter some content for this post."></textarea>
                                    <p class="help-block text-danger"></p>
                                </div>
                            </div>
                              <div class="row control-group">
                                  <div class="form-group col-xs-12 floating-label-form-group controls">
                                      <label>Publish Date</label>
                                      <input type="date" class="form-control" placeholder="eg. 12 January 2016" name="date" id="date" required data-validation-required-message="Please enter a publish date.">
                                      <p class="help-block text-danger"></p>
                                  </div>
                              </div>
                              <br>
                            <div id="success"></div>
                            <div class="row">
                                <div class="form-group col-xs-12">
                                    <input type="hidden" name="mode" value="createpost"/>
                                    <button type="submit" class="btn btn-default">Add Post</button>
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
                            //get the page count
                            $sql = 'SELECT p.id, p.headline, p.subtitle, p.`date`, u.alias  from posts p INNER JOIN users u ON p.userid = u.id WHERE p.published=TRUE ORDER by p.`date` DESC ';
                            $result = mysqli_query($this->db,$sql);
                            $this->page_limit = mysqli_num_rows($result)/$this->posts_per_page;

                            //now get the summaries for the posts falling into this page's range.
                            $sql .= ' LIMIT ' . $this->post_start . ',' . $this->posts_per_page . '';
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
                        <p class="post-meta">Posted by <a href="?author=<?php echo $row['alias'];?>"><?php echo $row['alias'];?></a> on <?php echo date('l, jS F Y',strtotime($row['date']));?></p>
                    </div>
                                <hr>

                            <?php
                            }
                        ?>

                    <!-- Pager -->
                    <ul class="pager">
                        <?php if($this->page > 1){?>
                        <li class="prev">
                            <a href="index.php?page=<?php echo $this->page-1;?>">&larr; Newer Posts</a>
                        </li>
                        <?php }
                        if($this->page < $this->page_limit){ ?>
                        <li class="next">
                            <a href="index.php?page=<?php echo $this->page+1;?>">Older Posts &rarr;</a>
                        </li>
                        <?php }?>
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
                        <p class="copyright text-muted">Copyright &copy; Lotek 2016 | <?php
                        if($this->loggedIn){?>
                            <span class="trigger trigger-logout">Logout</span>
                        <?php }else{ ?>
                            <span class="trigger trigger-login">Login</span> <?php
                        }
                        ?></p>
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

        <!-- Additions -->
        <script src="js/custom.js"></script>

        <?php
        if($this->loggedIn){?>
            <!-- include codemirror for inline source view -->
            <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.css">
            <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/theme/monokai.css">
            <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.js"></script>
            <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/xml/xml.js"></script>
            <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/codemirror/2.36.0/formatting.js"></script>

            <!-- include summernote css/js-->
            <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.css" rel="stylesheet">
            <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.js"></script>

        <script>
            $(document).ready(function(){


                $('<button id="startEdit" class="follow-scroll" type="button"  onclick="startEdit();">Edit Post</button>').insertBefore('.content');
                $('<button id="endEdit" class="follow-scroll" style="display:none" type="button"  onclick="endEdit();">Save Changes</button>').insertBefore('.content');
                $('<button type="button" class="follow-scroll" onclick="deletePost();">Delete post</button>').insertBefore('.content');
                setupFollowScroll();

            });

            function setupFollowScroll(){
                var element = $('.follow-scroll'),
                    originalY = element.offset().top;

                var topMargin = 5;

                element.css({"position":"relative","z-index":"10000"});

                $(window).on('scroll', function() {
                    var scrollTop = $(window).scrollTop();

                    element.stop(false, false).animate({
                        top: scrollTop < originalY
                            ? 0
                            : scrollTop - originalY + topMargin
                    }, 300);
                });
            }

            function deletePost(){
                if(confirm('Delete this post?')){
                    request = jQuery.ajax({
                        url: "processor.php",
                        type: "POST",
                        data: {
                            mode : "deletepost",
                            id : "<?php echo $this->postDetails['postid'];?>"
                        },
                        dataType: "json",
                        success: function(r){
                            console.log(r);
                            window.location.href = 'index.php';
                        }
                    });

                }
            }

            function sendFile(file) {
                data = new FormData();
                data.append("file", file);
                data.append("mode",'imageupload');
                $.ajax({
                    data: data,
                    type: "POST",
                    url: "processor.php",
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function(obj) {
                        $('.content').summernote('pasteHTML', '<img src="' + obj.filepath + '" class="img-responsive"/>');
                    }

                });
            }

            function startEdit(){
                $('.content').summernote({
                    airMode: true,
                    popover: {
                        air: [

                            ['font', ['style','bold', 'italic', 'clear']],
                            ['para', ['ul', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link', 'picture']]
                        ]
                    },
                    callbacks: {
                        onImageUpload: function(files) {
                            sendFile(files[0]);
                        }
                    }

                });
                $('#endEdit').toggle(true);
                $('#startEdit').toggle(false);
            }

            function endEdit(){
                $('#endEdit').toggle(false);
                $('#startEdit').toggle(true);

                var markup = $('.content').summernote('code');
                $('.content').summernote('destroy');
                console.log(markup);

                if(confirm('Do you want to update this post?')){

                    request = jQuery.ajax({
                        url: "processor.php",
                        type: "POST",
                        data: {
                            mode : "editinline",
                            column : "bodycontent",
                            content : $('.content').html(),
                            id : "<?php echo $this->postDetails['postid'];?>"
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

    public function validateUser($email,$pass){
        $sql = 'SELECT * from users WHERE emailaddress = "' . $email . '" AND pass = "' . MD5($pass) . '" LIMIT 1';

        $result = mysqli_query($this->db,$sql);
        while($row = mysqli_fetch_array($result)){
            $_SESSION['userid']  = $row['id'];
            $_SESSION['alias'] = $row['alias'];
            return true;
        }
        return false;
    }
    public function logoutUser(){
        unset($_SESSION['userid']);
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

function debugOut($msg,$debug){
    if($debug){
        echo PHP_EOL . $msg;
    }
}

