<?php
class result{
    public $status = 'pending';
    public $message = 'initialising';
}
$result = new result();

// Check for empty fields
if(empty($_POST['name'])      ||
   empty($_POST['email'])     ||
   empty($_POST['message'])   ||
   !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
     $result->status = false;
     $result->message =  'Incomplete details provided! Cannot send message.';
}else {
    $name = strip_tags(htmlspecialchars($_POST['name']));
    $email_address = strip_tags(htmlspecialchars($_POST['email']));
    $phone = strip_tags(htmlspecialchars($_POST['phone']));
    $message = strip_tags(htmlspecialchars($_POST['message']));

    // Create the email and send the message
    $to = 'arallsopp@gmail.com'; //
    $email_subject = "Blog Contact Form:  $name";
    $email_body = "You have received a new message from your website contact form.\n\n" . "Here are the details:\n\nName: $name\n\nEmail: $email_address\n\nPhone: $phone\n\nMessage:\n$message";
    $headers = "From: noreply@yourdomain.com\n"; // This is the email address the generated message will be from. We recommend using something like noreply@yourdomain.com.
    $headers .= "Reply-To: $email_address";
    mail($to, $email_subject, $email_body, $headers);

    $result->status = true;
    $result->message = 'Your message has been sent';
}
echo json_encode($result);