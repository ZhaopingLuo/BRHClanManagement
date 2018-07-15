<?php

require_once(FCPATH.'vendor/autoload.php');

use Mailgun\Mailgun;


function send_email($data)
{
	try{
	$email_message = $data['email_message'];
	$email_to = $data['email_to'];
	$email_subject = $data['email_subject'];

	// settings of mailgun
	$mgClient = new Mailgun('key-3306573d6a8d19e6fdc43228e2655137');
	$domain = "mg.luozp.cn";

	//$mgClient = new Mailgun('key-PleaseCopyTheKeyMailGunWebsite');
    //$domain = "yourdomain.com";


	$result = $mgClient->sendMessage("$domain",
		array(  'from'    => 'Ace Project Auto-Mail<system@'. $domain.'>',
			'to'      => 'Dear User <' . $email_to . '>',
			'subject' => $email_subject,
			'html'    =>  $email_message));

	}
	catch(Exception $e)
	{
		throw new Exception('Failed to send the email (Is the email address correct?)');
	}
}

/*
function send_email($data)
{
    try{
        $email_message = $data['email_message'];
        $email_to = $data['email_to'];
        $email_subject = $data['email_subject'];

        $ci =& get_instance();

        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'youraccount@gmail.com',
            'smtp_pass' => 'password',
            'mailtype'  => 'html',
            'charset'   => 'iso-8859-1'
        );

        $ci->load->library('email', $config);
        $ci->email->from('youraccount@gmail.com', 'ace project space' );
        $ci->email->to($email_to);

        $ci->email->subject($email_subject);
        $ci->email->message($email_message);
        $ci->email->set_newline("\r\n");

        $result = $ci->email->send();

        $ci->email->clear();
    }
    catch(Exception $e)
    {
        throw new Exception('Failed to send the email (Is the email address correct?)');
    }
}
*/
