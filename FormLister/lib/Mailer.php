<?php namespace Helpers;

class Mailer
{
    protected $mail = null;
    protected $modx = null;
    public $config = array();
    protected $debug = false;


    public function __construct($modx, $cfg, $debug = false) {
        $this->modx = $modx;
        $modx->loadExtension('MODxMailer');
        $this->mail = $modx->mail;
        $this->config = $cfg;
        $this->debug = $debug;
    }

    public function addAddressToMailer($type, $addr)
    {
        if (empty($addr)) {
            return;
        }
        $a = array_filter(array_map('trim', explode(',', $addr)));
        foreach ($a as $address) {
            switch ($type) {
                case 'to':
                    $this->mail->AddAddress($address);
                    break;
                case 'cc':
                    $this->mail->AddCC($address);
                    break;
                case 'bcc':
                    $this->mail->AddBCC($address);
                    break;
                case 'replyTo':
                    $this->mail->AddReplyTo($address);
            }
        }
    }

    public function AttachFilesToMailer($attachFiles) {
        if(count($attachFiles)>0){
            foreach($attachFiles as $attachFile){
                if(!file_exists($attachFile)) continue;
                $FileName = $attachFile;
                $contentType = "application/octetstream";
                if (is_uploaded_file($attachFile)){
                    foreach($_FILES as $n => $v){
                        if($_FILES[$n]['tmp_name']==$attachFile) {
                            $FileName = $_FILES[$n]['name'];
                            $contentType = $_FILES[$n]['type'];
                        }
                    }
                }
                $patharray = explode(((strpos($FileName,"/")===false)? "\\":"/"), $FileName);
                $FileName = $patharray[count($patharray)-1];
                $this->mail->AddAttachment($attachFile,$FileName,"base64",$contentType);
            }
        }
    }

    public function send($report, $attachments = array())
    {
        //если отправлять некуда или незачем, то делаем вид, что отправили
        if (!$this->getCFGDef('to') || $this->getCFGDef('noemail')) {
            return true;
        } elseif(empty($report)) {
            return false;
        }

        $this->mail->IsHTML($this->getCFGDef('isHtml'));
        $this->mail->From = $this->getCFGDef('from');
        $this->mail->FromName = $this->getCFGDef('fromName');
        $this->mail->Subject = $this->getCFGDef('subject');
        $this->mail->Body = $report;
        $this->addAddressToMailer("replyTo", $this->getCFGDef('replyTo'));
        $this->addAddressToMailer("to", $this->getCFGDef('to'));
        $this->addAddressToMailer("cc", $this->getCFGDef('cc'));
        $this->addAddressToMailer("bcc", $this->getCFGDef('bcc'));

        //AttachFilesToMailer($modx->mail,$attachments);

        $result = $this->mail->send();
        if ($result) {
            $this->mail->ClearAllRecipients();
            $this->mail->ClearAttachments();
        }
        return $result;
    }

    public function getCFGDef($param) {
        $out = '';
        if (isset($this->config[$param]) && !empty($this->config[$param])) $out = $this->config[$param];
        return $out;
    }
}