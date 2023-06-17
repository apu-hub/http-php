<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private string $receiverMail = "";
    private string $receiverName = "";
    private string $subject = "";
    private $body = null;
    private string $template = "";
    private array $attachments = [];
    private string $mailer_id = "";
    private string $mailer_name = "";

    private bool $is_html = false;


    function __construct(string $mailer_id)
    {
        $this->mailer_id = $mailer_id;
    }

    public function setReceiverMail(string $value)
    {
        $this->receiverMail = $value;
    }

    public function setReceiverName(string $value)
    {
        $this->receiverName = $value;
    }

    public function setSubject(string $value)
    {
        $this->subject = $value;
    }

    public function setBody($value)
    {
        if (is_array($value))
            $this->is_html = true;

        $this->body = $value;
    }

    public function setTemplate(string $value)
    {
        $this->template = $value;
    }
    public function setAttachments(string $value)
    {
        $this->attachments = $value;
    }

    public function send(): bool
    {
        // get detais from env
        $Secure = $_ENV[$this->mailer_id . '_PROTOCAL'] ?? "";
        $Port = $_ENV[$this->mailer_id . '_PORT'] ?? "";
        $Auth = $_ENV[$this->mailer_id . '_AUTH'] ?? "";
        $Host = $_ENV[$this->mailer_id . '_HOST'] ?? "";
        $UserName = $_ENV[$this->mailer_id . '_USER'] ?? "";
        $Password = $_ENV[$this->mailer_id . '_Password'] ?? "";

        $this->mailer_name = $_ENV['APP_NAME'] ?? "";


        $mail = new PHPMailer();
        $mail->isSMTP();

        // set-up mailer details
        $mail->Host = $Host;
        $mail->SMTPAuth = $Auth;
        $mail->Username = $UserName;
        $mail->Password = $Password;
        $mail->Port = $Port;
        $mail->SMTPSecure = $Secure;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => true
            )
        );

        $mail->addReplyTo($UserName, $this->mailer_name);
        $mail->setFrom($UserName, $this->mailer_name);
        $mail->addAddress($this->receiverMail, $this->receiverName);
        $mail->Subject = $this->subject;

        // add attachments
        foreach ($this->attachments as $value) {
            $path = Utils::public_path("../".$value);
            $mail->addAttachment($path);
        }

        $mail->Body = $this->body;

        if ($this->is_html) {
            $mail->isHTML(true);
            $mail->Body  = $this->generateMailFromTemplate($this->template, $this->body);
            $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
        }
        if (!$mail->send()) {
            throw new Exception("$mail->ErrorInfo");
            return false;
        } else {
            return true;
        }
    }

    function generateMailFromTemplate($template, $body)
    {

        $Host_Name = $_ENV['APP_URL'] ?? "";
        // $Host_Name = "https://" . $_SERVER['HTTP_HOST'];
        $image_path =  Utils::url('/images/mail/' . $template . '/');
        $data = array(
            "server_url" => $Host_Name,
            "image_path" => $image_path,
            "server_url" => $Host_Name,
        );

        $template_file_path = Utils::public_path("mail/" . $template . "/index.html");

        if (!file_exists($template_file_path)) {
            throw new Exception("Cant Find Template");
        };

        $template = file_get_contents($template_file_path);

        if (gettype($body) != "array") return $template;

        $body = array_merge($body, $data);

        foreach ($body as $key => $value) {
            $template = str_replace('{{ ' . $key . ' }}', $value, $template);
        }
        return $template;
    }
}
