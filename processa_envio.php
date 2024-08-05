<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "./bibliotecas/php_mailer/Exception.php";
require_once "./bibliotecas/php_mailer/PHPMailer.php";
require_once "./bibliotecas/php_mailer/SMTP.php";

class Mensagem
{
    private $destinatario = '';
    private $assunto = '';
    private $mensagem = '';
    public $status = array('codigo_status' => null, 'descricao_status' => '');

    public function __get($attr)
    {
        return $this->$attr;
    }

    public function __set($attr, $value)
    {
        $this->$attr = $value;
    }

    public function __mensagemValida()
    {
        if (empty($this->destinatario) || empty($this->assunto) || empty($this->mensagem)) {
            return false;
        }
        return true;
    }
}

$mensagem = new Mensagem();
$mensagem->__set('destinatario', $_POST['destinatario']);
$mensagem->__set('assunto', $_POST['assunto']);
$mensagem->__set('mensagem', $_POST['mensagem']);

if (!$mensagem->__mensagemValida()) {
    header("Location: index.php");
}

//envio email PHPMailer
$mail = new PHPMailer(true);
try {
    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->SMTPDebug = false;
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'user@example.com';                     //SMTP username
    $mail->Password   = 'secret';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('exemplo@gmail.com', 'João Matias');
    //$mail->addAddress('ellen@example.com', 'Ellen Destinatário');     //Add a recipient
    $mail->addAddress($mensagem->__get('destinatario'));               //Name is optional
    // $mail->addReplyTo('info@example.com', 'Information'); //recebedor de feedback default
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Adicao de arquivos
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Adicao de arquivos

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $mensagem->__get('assunto');
    $mail->Body    = $mensagem->__get('mensagem');
    $mail->AltBody = 'É necessário ter um client que suporte HTML para ter acesso a todo o conteúdo!';

    $mail->send();
    $mensagem->status['codigo_status'] = 1;
    $mensagem->status['descricao_status'] = 'E-mail enviado com sucesso!';
} catch (Exception $e) {
    $mensagem->status['codigo_status'] = 0;
    $mensagem->status['descricao_status'] = "Falha ao enviar e-mail. Error: {$mail->ErrorInfo}";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>App Send Mail</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>
<body>
    <div class="py-3 text-center">
        <img class="d-block mx-auto mb-2" src="logo.png" alt="" width="72" height="72">
        <h2>Send Mail</h2>
        <p class="lead">Seu app de envio de e-mails particular!</p>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="container mb-4 pb-4">
                <?php if($mensagem->status['codigo_status']) : ?>
                    <h1 class="dislay-4 text-success text-center">Sucesso!</h1>
                    <p class="text-center"><?= $mensagem->status['descricao_status'] ?></p>
                    <a href="index.php" class="btn btn-success btn-lg mt-3 btn-block">Inicio</a>
                <?php else : ?>
                    <h1 class="dislay-4 text-danger text-center">Ops!</h1>
                    <p class="text-center"><?= $mensagem->status['descricao_status'] ?></p>
                    <a href="index.php" class="btn btn-danger btn-lg mt-3 btn-block">Inicio</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>