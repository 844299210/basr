<?php
header("Content-type: text/html; charset=utf-8");
class smtp{
  /* Public Variables */
  var $smtp_port;
  var $time_out;
  var $host_name;
  var $log_file;
  var $relay_host;
  var $debug;
  var $auth;
  var $user;
  var $pass;
  /* Private Variables */
  var $sock;
  /* Constractor */
  function smtp($relay_host = "", $smtp_port = 25,$auth = false,$user,$pass){
    $this->debug = true;
    $this->smtp_port = $smtp_port;
    $this->relay_host = $relay_host;
    $this->time_out = 30; //is used in fsockopen()
    $this->auth = $auth;//auth
    $this->user = $user;
    $this->pass = $pass;
    $this->host_name = "localhost"; //is used in HELO command
    $this->log_file = "";
    $this->sock = FALSE;
  }
  /* Main Function */
  function sendmail($to, $from, $subject = "", $body = "", $mailtype, $cc = "", $bcc = "", $additional_headers = ""){
    $mail_from = $this->get_address($this->strip_comment($from));
    $body = ereg_replace("(^|(\r\n))(\.)", "\1.\3", $body);
    $header = "MIME-Version:1.0\r\n";
    if($mailtype=="HTML"){
      $header .= "Content-Type:text/html\r\n";
    }
    $header .= "To: ".$to."\r\n";
    if ($cc != "") {
      $header .= "Cc: ".$cc."\r\n";
    }
    $header .= "From: $from<".$from.">\r\n";
    $header .= "Subject: ".$subject."\r\n";
    $header .= $additional_headers;
    $header .= "Date: ".date("r")."\r\n";
    $header .= "X-Mailer:By Redhat (PHP/".phpversion().")\r\n";
    list($msec, $sec) = explode(" ", microtime());
    $header .= "Message-ID: <".date("YmdHis", $sec).".".($msec*1000000).".".$mail_from.">\r\n";
    $TO = explode(",", $this->strip_comment($to));
    if ($cc != "") {
      $TO = array_merge($TO, explode(",", $this->strip_comment($cc)));
    }
    if ($bcc != "") {
      $TO = array_merge($TO, explode(",", $this->strip_comment($bcc)));
    }
    $sent = TRUE;
    foreach ($TO as $rcpt_to) {
      $rcpt_to = $this->get_address($rcpt_to);
      if (!$this->smtp_sockopen($rcpt_to)) {
        $this->log_write("Error: Cannot send email to ".$rcpt_to."\n");
        $sent = FALSE;
        continue;
      }
      if ($this->smtp_send($this->host_name, $mail_from, $rcpt_to, $header, $body)) {
        $this->log_write("E-mail has been sent to <".$rcpt_to.">\n");
      } else {
        $this->log_write("Error: Cannot send email to <".$rcpt_to.">\n");
        $sent = FALSE;
      }
      fclose($this->sock);
      $this->log_write("Disconnected from remote host\n");
    }
    return $sent;
  }
  /* Private Functions */
  function smtp_send($helo, $from, $to, $header, $body = "")
  {
    if (!$this->smtp_putcmd("HELO", $helo)) {
      return $this->smtp_error("sending HELO command");
    }
    #auth
    if($this->auth){
    if (!$this->smtp_putcmd("AUTH LOGIN", base64_encode($this->user))) {
    return $this->smtp_error("sending HELO command");
    }
    if (!$this->smtp_putcmd("", base64_encode($this->pass))) {
    return $this->smtp_error("sending HELO command");
}
}
if (!$this->smtp_putcmd("MAIL", "FROM:<".$from.">")) {
return $this->smtp_error("sending MAIL FROM command");
}
if (!$this->smtp_putcmd("RCPT", "TO:<".$to.">")) {
return $this->smtp_error("sending RCPT TO command");
}
if (!$this->smtp_putcmd("DATA")) {
return $this->smtp_error("sending DATA command");
}
if (!$this->smtp_message($header, $body)) {
return $this->smtp_error("sending message");
}
if (!$this->smtp_eom()) {
return $this->smtp_error("sending <CR><LF>.<CR><LF> [EOM]");
}
if (!$this->smtp_putcmd("QUIT")) {
return $this->smtp_error("sending QUIT command");
}
return TRUE;
}
function smtp_sockopen($address)
{
if ($this->relay_host == "") {
return $this->smtp_sockopen_mx($address);
    } else {
    return $this->smtp_sockopen_relay();
}
}
function smtp_sockopen_relay()
{
$this->log_write("Trying to ".$this->relay_host.":".$this->smtp_port."\n");
$this->sock = @fsockopen($this->relay_host, $this->smtp_port, $errno, $errstr, $this->time_out);
if (!($this->sock && $this->smtp_ok())) {
$this->log_write("Error: Cannot connenct to relay host ".$this->relay_host."\n");
$this->log_write("Error: ".$errstr." (".$errno.")\n");
    return FALSE;
}
$this->log_write("Connected to relay host ".$this->relay_host."\n");
return TRUE;;
}
function smtp_sockopen_mx($address)
{
$domain = ereg_replace("^.+@([^@]+)$", "\1", $address);
if (!@getmxrr($domain, $MXHOSTS)) {
$this->log_write("Error: Cannot resolve MX \"".$domain."\"\n");
return FALSE;
    }
    foreach ($MXHOSTS as $host) {
        $this->log_write("Trying to ".$host.":".$this->smtp_port."\n");
        $this->sock = @fsockopen($host, $this->smtp_port, $errno, $errstr, $this->time_out);
        if (!($this->sock && $this->smtp_ok())) {
$this->log_write("Warning: Cannot connect to mx host ".$host."\n");
$this->log_write("Error: ".$errstr." (".$errno.")\n");
continue;
    }
    $this->log_write("Connected to mx host ".$host."\n");
return TRUE;
}
$this->log_write("Error: Cannot connect to any mx hosts (".implode(", ", $MXHOSTS).")\n");
return FALSE;
    }
    function smtp_message($header, $body)
    {
fputs($this->sock, $header."\r\n".$body);
$this->smtp_debug("> ".str_replace("\r\n", "\n"."> ", $header."\n> ".$body."\n> "));
return TRUE;
    }
    function smtp_eom(){
    fputs($this->sock, "\r\n.\r\n");
    $this->smtp_debug(". [EOM]\n");
return $this->smtp_ok();
    }
    function smtp_ok(){
    $response = str_replace("\r\n", "", fgets($this->sock, 512));
$this->smtp_debug($response."\n");
if (!ereg("^[23]", $response)) {
fputs($this->sock, "QUIT\r\n");
fgets($this->sock, 512);
$this->log_write("Error: Remote host returned \"".$response."\"\n");
return FALSE;
}
return TRUE;
}
function smtp_putcmd($cmd, $arg = ""){
if ($arg != "") {
if($cmd=="") $cmd = $arg;
else $cmd = $cmd." ".$arg;
}
fputs($this->sock, $cmd."\r\n");
$this->smtp_debug("> ".$cmd."\n");
return $this->smtp_ok();
}
function smtp_error($string){
$this->log_write("Error: Error occurred while ".$string.".\n");
return FALSE;
}
function log_write($message){
$this->smtp_debug($message);
if ($this->log_file == "") {
    return TRUE;
}
$message = date("M d H:i:s ").get_current_user()."[".getmypid()."]: ".$message;
if (!@file_exists($this->log_file) || !($fp = @fopen($this->log_file, "a"))) {
$this->smtp_debug("Warning: Cannot open log file \"".$this->log_file."\"\n");
return FALSE;
}
flock($fp, LOCK_EX);
fputs($fp, $message);
fclose($fp);
return TRUE;
}
function strip_comment($address){
$comment = "\([^()]*\)";
while (ereg($comment, $address)) {
$address = ereg_replace($comment, "", $address);
}
return $address;
}
function get_address($address){
$address = ereg_replace("([ \t\r\n])+", "", $address);
$address = ereg_replace("^.*<(.+)>.*$", "\1", $address);
return $address;
}
function smtp_debug($message){
if ($this->debug) {
echo $message;
}
}
}

/****************************公共头部***********************************/
define('EMAIL_HEAHER_RIGHT', 'To Be the World-class Supplier in <br> Optical Communications');
define('EMAIL_MENU_HOME','Home');
define('EMAIL_HOME_URL','http://www.fs.com/?utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_content=top&utm_term=Home');
define('EMAIL_MENU_SUPPORT','Support');
define('EMAIL_SUPPORT_URL','http://www.fs.com/support.html?utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_content=top&utm_term=Support');
define('EMAIL_MENU_TUTORIAL','Tutorial');
define('EMAIL_TUTORIAL_URL','http://www.fs.com/tutorial.html?utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_content=top&utm_term=Tutorial');
define('EMAIL_MENU_ABOUT_US','About Us');
define('EMAIL_ABOUT_US_URL','http://www.fs.com/about_us.html?utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_content=top&utm_term=About%20US');
define('EMAIL_MENU_SERVICE','Service');
define('EMAIL_SERVICE_URL','http://www.fs.com/service.html?utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_content=top&utm_term=Service');
define('EMAIL_MENU_CONTACT_US','Contact Us');
define('EMAIL_CONTACT_US_URL','http://www.fs.com/contact_us.html?utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_content=top&utm_term=Contact%20US');

/****************************公共底部****************************************/
define('EMAIL_MENU_PURCHASE_HELP','Purchase Help');
define('EMAIL_PURCHASE_HELP_URL','http://www.fs.com/how_to_buy.html?utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_term=Purchase%20Help');
define('EMAIL_FOOTER_PROMPT','This mailbox is unattended, so please do not reply to this message.<br>  For other inquiries, contact us via Help Center or Email to sales@fs.com.');
define('EMAIL_FOOTER_FS_COPYRIGHT','Copyright &copy; 2002-2017 fs.com  All Rights Reserved.');

/**************************************content common text**************************************/
define('EMAIL_BODY_COMMON_DEAR','Dear');
define('EMAIL_BODY_COMMON_THANKS','Thanks');
define('EMAIL_BODY_COMMON_PHONE','Phone : ');
define('EMAIL_BODY_COMMON_PARTNER','Partner');
define('EMAIL_BODY_COMMON_URL_BASE','http://www.fs.com');

/****************************公共头部法语***********************************/
define('EMAIL_HEAHER_RIGHT_FR', 'Devenir Fournisseur de Classe Mondiale <br> dans les Communications Optiques');
define('EMAIL_MENU_HOME_FR','Accueil');
define('EMAIL_HOME_URL_FR','http://www.fs.com/fr');
define('EMAIL_MENU_SUPPORT_FR','Support');
define('EMAIL_SUPPORT_URL_FR','http://www.fs.com/fr/support.html');
define('EMAIL_MENU_TUTORIAL_FR','Tutoriel');
define('EMAIL_TUTORIAL_URL_FR','http://www.fs.com/tutorial.html');
define('EMAIL_MENU_ABOUT_US_FR','A propos de Nous');
define('EMAIL_ABOUT_US_URL_FR','http://www.fs.com/fr/about_us.html');
define('EMAIL_MENU_SERVICE_FR','Service');
define('EMAIL_SERVICE_URL_FR','http://www.fs.com/fr/service.html');
define('EMAIL_MENU_CONTACT_US_FR','Contactez-Nous');
define('EMAIL_CONTACT_US_URL_FR','http://www.fs.com/fr/contact_us.html');

/****************************公共底部****************************************/
define('EMAIL_MENU_PURCHASE_HELP_FR','Aides à l’Achat');
define('EMAIL_PURCHASE_HELP_URL_FR','http://www.fs.com/fr/support.html');
define('EMAIL_FOOTER_PROMPT_FR','This mailbox is unattended, so please do not reply to this message.<br>  For other inquiries, contact us via Help Center or Email to sales@fs.com.');
define('EMAIL_FOOTER_FS_COPYRIGHT_FR','Copyright &copy; 2002-2017 fs.com  Tous Droits Réservés.');

/**************************************content common text**************************************/
define('EMAIL_BODY_COMMON_DEAR_FR','Bonjour');
define('EMAIL_BODY_COMMON_THANKS_FR','Merci');
define('EMAIL_BODY_COMMON_PHONE_FR','Téléphone : ');
define('EMAIL_BODY_COMMON_PARTNER_FR','Partenaire');
define('EMAIL_BODY_COMMON_URL_BASE_FR','http://www.fs.com/fr');


/****************************公共头部德语***********************************/
define('EMAIL_HEAHER_RIGHT_DE', 'Weltklasse-Lieferrant in der <br> optischen Kommunikation zu werden');
define('EMAIL_MENU_HOME_DE','Startseite');
define('EMAIL_HOME_URL_DE','http://www.fs.com');
define('EMAIL_MENU_SUPPORT_DE','Unterstützung');
define('EMAIL_SUPPORT_URL_DE','http://www.fs.com/de/support.html');
define('EMAIL_MENU_TUTORIAL_DE','Anleitung');
define('EMAIL_TUTORIAL_URL_DE','http://www.fs.com/de/tutorial.html');
define('EMAIL_MENU_ABOUT_US_DE','Über uns');
define('EMAIL_ABOUT_US_URL_DE','http://www.fs.com/de/about_us.html');
define('EMAIL_MENU_SERVICE_DE','Service');
define('EMAIL_SERVICE_URL_DE','http://www.fs.com/de/service.html');
define('EMAIL_MENU_CONTACT_US_DE','Kontakt');
define('EMAIL_CONTACT_US_URL_DE','http://www.fs.com/de/contact_us.html');

/****************************公共底部****************************************/
define('EMAIL_MENU_PURCHASE_HELP_DE','Einkaufshilfe');
define('EMAIL_PURCHASE_HELP_URL_DE','http://www.fs.com/de/support.html');
define('EMAIL_FOOTER_PROMPT_DE','Diese Mailbox ist unbeaufsichtigt, so antworten Sie bitte auf diese Nachricht nicht.<br>  Für andere Anfragen kontaktieren Sie uns über Hilfe oder E-Mail an sales@fs.com.');
define('EMAIL_FOOTER_FS_COPYRIGHT_DE','Copyright &copy; 2002-2017 fs.com  Alle Rechte vorbehalten.');

/**************************************content common text**************************************/
define('EMAIL_BODY_COMMON_DEAR_DE','Sehr geehrte(r)');
define('EMAIL_BODY_COMMON_THANKS_DE','Danke');
define('EMAIL_BODY_COMMON_PHONE_DE','Telefon : ');
define('EMAIL_BODY_COMMON_PARTNER_DE','Partner');
define('EMAIL_BODY_COMMON_URL_BASE_DE','http://www.fs.com/de');

/****************************公共头部俄语***********************************/
define('EMAIL_HEAHER_RIGHT_RU', 'Стремится Стать Поставщиком Мирового Класса <br> в Интустрии Оптической Связи');
define('EMAIL_MENU_HOME_RU','Главная');
define('EMAIL_HOME_URL_RU','http://www.fs.com/ru/');
define('EMAIL_MENU_SUPPORT_RU','Продукты');
define('EMAIL_SUPPORT_URL_RU','http://www.fs.com/ru/support.html');
define('EMAIL_MENU_TUTORIAL_RU','Руководство');
define('EMAIL_TUTORIAL_URL_RU','http://www.fs.com/ru/tutorial.html');
define('EMAIL_MENU_ABOUT_US_RU','О Нас');
define('EMAIL_ABOUT_US_URL_RU','http://www.fs.com/ru/about_us.html');
define('EMAIL_MENU_SERVICE_RU','Сервис');
define('EMAIL_SERVICE_URL_RU','http://www.fs.com/ru/service.html');
define('EMAIL_MENU_CONTACT_US_RU','Контакт');
define('EMAIL_CONTACT_US_URL_RU','http://www.fs.com/ru/contact_us.html');

/****************************公共底部****************************************/
define('EMAIL_MENU_PURCHASE_HELP_RU','Помощь при Покупке');
define('EMAIL_PURCHASE_HELP_URL_RU','http://www.fs.com/ru/how_to_buy.html');
define('EMAIL_FOOTER_PROMPT_RU','This mailbox is unattended, so please do not reply to this message.<br>  For other inquiries, contact us via Help Center or Email to sales@fs.com.');
define('EMAIL_FOOTER_FS_COPYRIGHT_RU','© 2002-2017 FS.COM  Все Права Защищены.');

/**************************************content common text**************************************/
define('EMAIL_BODY_COMMON_DEAR_RU','Уважаемый/-ая');
define('EMAIL_BODY_COMMON_THANKS_RU','Большое спасибо');
define('EMAIL_BODY_COMMON_PHONE_RU','Тел: ');
define('EMAIL_BODY_COMMON_PARTNER_RU','Партнер');
define('EMAIL_BODY_COMMON_URL_BASE_RU','http://www.fs.com/ru/');

/****************************公共头部西语***********************************/
define('EMAIL_HEAHER_RIGHT_ES', 'Para ser un proveedor de clase mundial en <br> comunicaciones ópticas');
define('EMAIL_MENU_HOME_ES','Inicio');
define('EMAIL_HOME_URL_ES','http://www.fs.com/es');
define('EMAIL_MENU_SUPPORT_ES','Soporte');
define('EMAIL_SUPPORT_URL_ES','http://www.fs.com/es/support.html');
define('EMAIL_MENU_TUTORIAL_ES','Tutorial');
define('EMAIL_TUTORIAL_URL_ES','http://www.fs.com/es/tutorial.html');
define('EMAIL_MENU_ABOUT_US_ES','Sobre nosotros');
define('EMAIL_ABOUT_US_URL_ES','http://www.fs.com/es/about_us.html');
define('EMAIL_MENU_SERVICE_ES','Servicios');
define('EMAIL_SERVICE_URL_ES','http://www.fs.com/es/service.html');
define('EMAIL_MENU_CONTACT_US_ES','Contáctenos');
define('EMAIL_CONTACT_US_URL_ES','http://www.fs.com/es/contact_us.html');

/****************************公共底部****************************************/
define('EMAIL_MENU_PURCHASE_HELP_ES','Ayuda de compras');
define('EMAIL_PURCHASE_HELP_URL_ES','http://www.fs.com/es/support.html');
define('EMAIL_FOOTER_PROMPT_ES','Este buzón es desatendido, por eso por favor no responda a este mensaje.<br>  Para otras consultas, pónte en contacto con nosotros por Centro de ayuda o envíanos un correo a sales@fs.com.');
define('EMAIL_FOOTER_FS_COPYRIGHT_ES','Derecho de autor &copy; 2002-'.date('Y').' fs.com  Todos los derechos reservados.');

/**************************************content common text**************************************/
define('EMAIL_BODY_COMMON_DEAR_ES','Estimado');
define('EMAIL_BODY_COMMON_THANKS_ES','Gracias');
define('EMAIL_BODY_COMMON_PHONE_ES','Teléfono: ');
define('EMAIL_BODY_COMMON_PARTNER_ES','Socios');
define('EMAIL_BODY_COMMON_URL_BASE_ES','http://www.fs.com/es');


define('EMAIL_HEADER_INFO', '
     <!-- 头部-->
    <div style="width:100%; height:100%; border-top:2px solid #CC1229; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:22px; color:#333;">
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td><table width="650" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td><img src="http://www.fiberstore.com/images/logo_fs_01.gif" width="190" height="64" alt="logo" /></td>
              <td align="right"><font color="#666666"></font></td>
            </tr>
          </table></td>
      </tr>
      <tr>
        <td align="center" style="border-top:1px solid #ddd; border-bottom:1px solid #ddd;"><table width="650" height="46" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="center"><a style="color:#333; font-size:13px; text-decoration:none;" href="$EMAIL_HOME_URL">$EMAIL_MENU_HOME</a></td>
              <td align="center"><a style="color:#333; font-size:13px; text-decoration:none;" href="$EMAIL_SUPPORT_URL">$EMAIL_MENU_SUPPORT</a></td>
              <td align="center"><a style="color:#333; font-size:13px; text-decoration:none;" href="$EMAIL_TUTORIAL_URL">$EMAIL_MENU_TUTORIAL</a></td>
              <td align="center"><a style="color:#333; font-size:13px; text-decoration:none;" href="$EMAIL_ABOUT_US_URL">$EMAIL_MENU_ABOUT_US</a></td>
              <td align="center"><a style="color:#333; font-size:13px; text-decoration:none;" href="$EMAIL_SERVICE_URL">$EMAIL_MENU_SERVICE</a></td>
              <td align="center"><a style="color:#333; font-size:13px; text-decoration:none;" href="$EMAIL_CONTACT_US_URL">$EMAIL_MENU_CONTACT_US</a></td>
            </tr>
          </table></td>
      </tr>
    </table>
    <!--头部结束-->

    <!--中间-->
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f9f9f9">
      <tr>
        <td>
        <table width="650" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td><br/></td>
            </tr>
            <tr>
              <td bgcolor="#ffffff">
  		');




define('EMAIL_FOOTER_INFO', '
              </td></tr></table></td></tr></table>
        <!--中间结束-->
        <!--底部-->
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"  bgcolor="#f9f9f9">
          <tr><td><table width="650" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr><td align="center" style="font-size:12px;"><br/><a style="color:#333; text-decoration:none;" href="$BT_EMAIL_CONTACT_US_URL">$EMAIL_MENU_CONTACT_US </a>
    | <a style="color:#333; text-decoration:none;" href="$BT_EMAIL_ABOUT_US_URL">$EMAIL_MENU_ABOUT_US</a> | 
    <a style="color:#333; text-decoration:none;" href="$BT_EMAIL_SUPPORT_URL">$EMAIL_MENU_SUPPORT </a>|<a style="color:#333; text-decoration:none;" href="$EMAIL_PURCHASE_HELP_URL"> $EMAIL_MENU_PURCHASE_HELP</a> <br />
                      <span>$EMAIL_FOOTER_PROMPT</span><br/>
                      <font color="#999999">$EMAIL_FOOTER_FS_COPYRIGHT</font><br/><br/></td>
                </tr>
              </table></td>
          </tr>
        </table>
        </div>
  		');
if(substr(PHP_SAPI,0,3)!=='cli'){
  die('请在命令行下运行');
}
/********  连接数据库  **********/
//$con=mysqli_connect("localhost","fiberstoredb","yUxuan3507","fiberstore_spain"); 
$con=mysqli_connect("192.168.0.138","fs_beta","feisu.com17","fiberstore_spain");
$data=array();
if (mysqli_connect_errno($con)) 
{ 
    echo "连接 MySQL 失败: " . mysqli_connect_error(); die();
}
$sql = 'SELECT order_number,orders_id,customers_emails,products_instock_id,orders_num FROM `products_instock_shipping` 
    WHERE `shipping_date` regexp "'.date('Y-m-d',strtotime('-7 day')).'" and `shipping_number` !="" and change_order=0 and `is_split` = 0 and is_seattle=0
       and orders_id>0';
$sql = 'SELECT order_number,orders_id,customers_emails,products_instock_id,orders_num FROM `products_instock_shipping`
    WHERE `shipping_date` regexp "2017-02-15" and `shipping_number` !="" and change_order=0 and `is_split` = 0 and is_seattle=0
       and orders_id>0';
$result = mysqli_query($con,$sql);
//print_r(mysqli_fetch_assoc($result));die;
if($result){
  while ($row = mysqli_fetch_assoc($result)){
    $data[] = array('orders_id'=>$row['orders_id'],
        'order_number'=>$row['order_number'],
        'customers_email'=>$row['customers_emails'],
        'orders_num'=>$row['orders_num'],
        'products_instock_id'=>$row['products_instock_id']);
  }
  mysqli_free_result($result);
}
print_r($data);die;

//$smtpserver = "smtp.googlemail.com";//SMTP服务器
//$smtpserverport = 465;//SMTP服务器端口
$smtpserver = "smtp.exmail.qq.com";//SMTP服务器
$smtpserverport = 25;//SMTP服务器端口
$smtpusermail = "service@fiberstore.net";//SMTP服务器的用户邮箱
$smtpuser = "service@fiberstore.net";//SMTP服务器的用户帐号
$smtppass = "YUxuan_3507";//SMTP服务器的用户密码
//$smtpusermail = 'support@fs.com';
//$smtpuser = 'support@fs.com';
//$smtppass = 'SZfs2015_0201';
$mailtitle = 'Confirm Receipt & Give Feedback Reminder';//邮件主题
$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
//************************ 配置信息 ****************************
$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
$smtp->debug = false;//是否显示发送的调试信息
if(sizeof($data)){
  foreach ($data as $info){
    if(!$info['orders_id']){
      continue;
    }
    if(strlen($info['orders_num'])>13){
      continue;
    }
    $order_query = $order_info = $email_common_text = $html_header = $html_footer=$customers_email=$html_msg=$html_content='';
    $shipping_info=$shipping_query='';
    $order_query = mysqli_query($con,'select customers_email_address,language_id,customers_name,customers_id,orders_number from orders where orders_id='.(int)$info['orders_id']);
    $order_info = mysqli_fetch_assoc($order_query);
    mysqli_free_result($order_query);
    if($order_info['language_id']>5){
      continue;
    }
    $html_header=EMAIL_HEADER_INFO;
    $html_footer=EMAIL_FOOTER_INFO;
    if($order_info){
      if($order_info['language_id']==6){$order_info['language_id']=1;}
      if($order_info['language_id']==1||1==1){
        $email_common_text['EMAIL_HEAHER_RIGHT']=EMAIL_HEAHER_RIGHT;
        $email_common_text['EMAIL_MENU_HOME']=EMAIL_MENU_HOME;
        $email_common_text['EMAIL_HOME_URL']=EMAIL_HOME_URL;
        $email_common_text['EMAIL_MENU_SUPPORT']=EMAIL_MENU_SUPPORT;
        $email_common_text['EMAIL_SUPPORT_URL']=EMAIL_SUPPORT_URL;
        $email_common_text['EMAIL_MENU_TUTORIAL']=EMAIL_MENU_TUTORIAL;
        $email_common_text['EMAIL_TUTORIAL_URL']=EMAIL_TUTORIAL_URL;
        $email_common_text['EMAIL_MENU_ABOUT_US']=EMAIL_MENU_ABOUT_US;
        $email_common_text['EMAIL_ABOUT_US_URL']=EMAIL_ABOUT_US_URL;
        $email_common_text['EMAIL_MENU_SERVICE']=EMAIL_MENU_SERVICE;
        $email_common_text['EMAIL_SERVICE_URL']=EMAIL_SERVICE_URL;
        $email_common_text['EMAIL_MENU_CONTACT_US']=EMAIL_MENU_CONTACT_US;
        $email_common_text['EMAIL_CONTACT_US_URL']=EMAIL_CONTACT_US_URL;
        $email_common_text['EMAIL_MENU_PURCHASE_HELP']=EMAIL_MENU_PURCHASE_HELP;
        $email_common_text['EMAIL_PURCHASE_HELP_URL']=EMAIL_PURCHASE_HELP_URL;
        $email_common_text['EMAIL_FOOTER_FS_COPYRIGHT']=EMAIL_FOOTER_FS_COPYRIGHT;
        $email_common_text['EMAIL_FOOTER_PROMPT'] = EMAIL_FOOTER_PROMPT;
        $email_common_text['BT_EMAIL_CONTACT_US_URL'] = 'http://www.fs.com/contact_us.html?utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_content=bottom&utm_term=Contact%20US';
        $email_common_text['BT_EMAIL_ABOUT_US_URL'] = 'http://www.fs.com/about_us.html?utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_content=bottom&utm_term=About%20US';
        $email_common_text['BT_EMAIL_SUPPORT_URL'] = 'http://www.fs.com/support.html?utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_content=bottom&utm_term=Support';
        
        $content['EMAIL_BODY_COMMON_DEAR'] =EMAIL_BODY_COMMON_DEAR;
      }else if($order_info['language_id']==2){
        $email_common_text['EMAIL_HEAHER_RIGHT']=EMAIL_HEAHER_RIGHT_ES;
        $email_common_text['EMAIL_MENU_HOME']=EMAIL_MENU_HOME_ES;
        $email_common_text['EMAIL_HOME_URL']=EMAIL_HOME_URL_ES;
        $email_common_text['EMAIL_MENU_SUPPORT']=EMAIL_MENU_SUPPORT_ES;
        $email_common_text['EMAIL_SUPPORT_URL']=EMAIL_SUPPORT_URL_ES;
        $email_common_text['EMAIL_MENU_TUTORIAL']=EMAIL_MENU_TUTORIAL_ES;
        $email_common_text['EMAIL_TUTORIAL_URL']=EMAIL_TUTORIAL_URL_ES;
        $email_common_text['EMAIL_MENU_ABOUT_US']=EMAIL_MENU_ABOUT_US_ES;
        $email_common_text['EMAIL_ABOUT_US_URL']=EMAIL_ABOUT_US_URL_ES;
        $email_common_text['EMAIL_MENU_SERVICE']=EMAIL_MENU_SERVICE_ES;
        $email_common_text['EMAIL_SERVICE_URL']=EMAIL_SERVICE_URL_ES;
        $email_common_text['EMAIL_MENU_CONTACT_US']=EMAIL_MENU_CONTACT_US_ES;
        $email_common_text['EMAIL_CONTACT_US_URL']=EMAIL_CONTACT_US_URL_ES;
        $email_common_text['EMAIL_MENU_PURCHASE_HELP']=EMAIL_MENU_PURCHASE_HELP_ES;
        $email_common_text['EMAIL_PURCHASE_HELP_URL']=EMAIL_PURCHASE_HELP_URL_ES;
        $email_common_text['EMAIL_FOOTER_FS_COPYRIGHT']=EMAIL_FOOTER_FS_COPYRIGHT_ES;
        $email_common_text['EMAIL_FOOTER_PROMPT'] = EMAIL_FOOTER_PROMPT_ES;
        
        $content['EMAIL_BODY_COMMON_DEAR'] =EMAIL_BODY_COMMON_DEAR_ES;
      }else if($order_info['language_id']==3){
        $email_common_text['EMAIL_HEAHER_RIGHT']=EMAIL_HEAHER_RIGHT_FR;
        $email_common_text['EMAIL_MENU_HOME']=EMAIL_MENU_HOME_FR;
        $email_common_text['EMAIL_HOME_URL']=EMAIL_HOME_URL_FR;
        $email_common_text['EMAIL_MENU_SUPPORT']=EMAIL_MENU_SUPPORT_FR;
        $email_common_text['EMAIL_SUPPORT_URL']=EMAIL_SUPPORT_URL_FR;
        $email_common_text['EMAIL_MENU_TUTORIAL']=EMAIL_MENU_TUTORIAL_FR;
        $email_common_text['EMAIL_TUTORIAL_URL']=EMAIL_TUTORIAL_URL_FR;
        $email_common_text['EMAIL_MENU_ABOUT_US']=EMAIL_MENU_ABOUT_US_FR;
        $email_common_text['EMAIL_ABOUT_US_URL']=EMAIL_ABOUT_US_URL_FR;
        $email_common_text['EMAIL_MENU_SERVICE']=EMAIL_MENU_SERVICE_FR;
        $email_common_text['EMAIL_SERVICE_URL']=EMAIL_SERVICE_URL_FR;
        $email_common_text['EMAIL_MENU_CONTACT_US']=EMAIL_MENU_CONTACT_US_FR;
        $email_common_text['EMAIL_CONTACT_US_URL']=EMAIL_CONTACT_US_URL_FR;
        $email_common_text['EMAIL_MENU_PURCHASE_HELP']=EMAIL_MENU_PURCHASE_HELP_FR;
        $email_common_text['EMAIL_PURCHASE_HELP_URL']=EMAIL_PURCHASE_HELP_URL_FR;
        $email_common_text['EMAIL_FOOTER_FS_COPYRIGHT']=EMAIL_FOOTER_FS_COPYRIGHT_FR;
        $email_common_text['EMAIL_FOOTER_PROMPT'] = EMAIL_FOOTER_PROMPT_FR;
        
        $content['EMAIL_BODY_COMMON_DEAR'] =EMAIL_BODY_COMMON_DEAR_FR;
      }else if($order_info['language_id']==4){
        $email_common_text['EMAIL_HEAHER_RIGHT']=EMAIL_HEAHER_RIGHT_RU;
        $email_common_text['EMAIL_MENU_HOME']=EMAIL_MENU_HOME_RU;
        $email_common_text['EMAIL_HOME_URL']=EMAIL_HOME_URL_RU;
        $email_common_text['EMAIL_MENU_SUPPORT']=EMAIL_MENU_SUPPORT_RU;
        $email_common_text['EMAIL_SUPPORT_URL']=EMAIL_SUPPORT_URL_RU;
        $email_common_text['EMAIL_MENU_TUTORIAL']=EMAIL_MENU_TUTORIAL_RU;
        $email_common_text['EMAIL_TUTORIAL_URL']=EMAIL_TUTORIAL_URL_RU;
        $email_common_text['EMAIL_MENU_ABOUT_US']=EMAIL_MENU_ABOUT_US_RU;
        $email_common_text['EMAIL_ABOUT_US_URL']=EMAIL_ABOUT_US_URL_RU;
        $email_common_text['EMAIL_MENU_SERVICE']=EMAIL_MENU_SERVICE_RU;
        $email_common_text['EMAIL_SERVICE_URL']=EMAIL_SERVICE_URL_RU;
        $email_common_text['EMAIL_MENU_CONTACT_US']=EMAIL_MENU_CONTACT_US_RU;
        $email_common_text['EMAIL_CONTACT_US_URL']=EMAIL_CONTACT_US_URL_RU;
        $email_common_text['EMAIL_MENU_PURCHASE_HELP']=EMAIL_MENU_PURCHASE_HELP_RU;
        $email_common_text['EMAIL_PURCHASE_HELP_URL']=EMAIL_PURCHASE_HELP_URL_RU;
        $email_common_text['EMAIL_FOOTER_FS_COPYRIGHT']=EMAIL_FOOTER_FS_COPYRIGHT_RU;
        $email_common_text['EMAIL_FOOTER_PROMPT'] = EMAIL_FOOTER_PROMPT_RU;
        
        $content['EMAIL_BODY_COMMON_DEAR'] =EMAIL_BODY_COMMON_DEAR_RU;
      }else if($order_info['language_id']==5){
        $email_common_text['EMAIL_HEAHER_RIGHT']=EMAIL_HEAHER_RIGHT_DE;
        $email_common_text['EMAIL_MENU_HOME']=EMAIL_MENU_HOME_DE;
        $email_common_text['EMAIL_HOME_URL']=EMAIL_HOME_URL_DE;
        $email_common_text['EMAIL_MENU_SUPPORT']=EMAIL_MENU_SUPPORT_DE;
        $email_common_text['EMAIL_SUPPORT_URL']=EMAIL_SUPPORT_URL_DE;
        $email_common_text['EMAIL_MENU_TUTORIAL']=EMAIL_MENU_TUTORIAL_DE;
        $email_common_text['EMAIL_TUTORIAL_URL']=EMAIL_TUTORIAL_URL_DE;
        $email_common_text['EMAIL_MENU_ABOUT_US']=EMAIL_MENU_ABOUT_US_DE;
        $email_common_text['EMAIL_ABOUT_US_URL']=EMAIL_ABOUT_US_URL_DE;
        $email_common_text['EMAIL_MENU_SERVICE']=EMAIL_MENU_SERVICE_DE;
        $email_common_text['EMAIL_SERVICE_URL']=EMAIL_SERVICE_URL_DE;
        $email_common_text['EMAIL_MENU_CONTACT_US']=EMAIL_MENU_CONTACT_US_DE;
        $email_common_text['EMAIL_CONTACT_US_URL']=EMAIL_CONTACT_US_URL_DE;
        $email_common_text['EMAIL_MENU_PURCHASE_HELP']=EMAIL_MENU_PURCHASE_HELP_DE;
        $email_common_text['EMAIL_PURCHASE_HELP_URL']=EMAIL_PURCHASE_HELP_URL_DE;
        $email_common_text['EMAIL_FOOTER_FS_COPYRIGHT']=EMAIL_FOOTER_FS_COPYRIGHT_DE;
        $email_common_text['EMAIL_FOOTER_PROMPT'] = EMAIL_FOOTER_PROMPT_DE;
        
        $content['EMAIL_BODY_COMMON_DEAR'] =EMAIL_BODY_COMMON_DEAR_DE;
      }
    
      foreach ($email_common_text as $k=>$v){
        $html_header = str_replace('$'.$k,$v,$html_header);
        $html_footer = str_replace('$'.$k,$v,$html_footer);
      }
      
      if(empty(trim($info['customers_email']))){
        $customers_email = $order_info['customers_email_address'];
      }else{
        $customers_email = $info['customers_email'];
      }
      
      /*$shipping_query = mysqli_query($con,'select products_id from products_instock_shipping_info where is_change!=3 and products_instock_id='.$info['products_instock_id']);
      if($shipping_query){
        while ($row = mysqli_fetch_assoc($shipping_query)){
          $shipping_info[]=$row['products_id'];
        }
        mysqli_free_result($shipping_query);
      }*/
      $shipping_query = mysqli_query($con,'select products_id from orders_products where orders_id='.$info['orders_id']);
      if($shipping_query){
        while ($row = mysqli_fetch_assoc($shipping_query)){
          $shipping_info[]=$row['products_id'];
        }
        mysqli_free_result($shipping_query);
      }
      $products_html = '';
      if(sizeof($shipping_info)){
        foreach($shipping_info as $products_id){
          $products_name=$products_model=$query=$res=$products_images='';
         // $query = mysqli_query($con,'select products_name from products_description where products_id='.(int)$products_id.' and language_id='.(int)$order_info['language_id']);
          $query = mysqli_query($con,'select products_name from products_description where products_id='.(int)$products_id.' and language_id=1');
          $res = mysqli_fetch_assoc($query);
          $products_name = $res['products_name'];
          mysqli_free_result($query);
          $query = mysqli_query($con,'select products_image,products_model from products where products_id='.(int)$products_id);
          $res = mysqli_fetch_assoc($query);
          $products_model = $res['products_model'];
          $products_images = $res['products_image'];
          $products_html .= '<tr style="font-size:11px;">
                    <td  style="font-size:11px; text-align:center; padding:5px 0;"><div style=" border:1px solid #dedede;"><img src="http://www.fs.com/images/'.$products_images.'" alt="'.$products_name.'" title="'.$products_name.'" width="60" height="60"></div></td>
                    <td width="0"></td>
                    <td style="font-size:11px;">'.$products_name.'
                     <br />
                     '.$products_model.'
                    </td>
                  </tr>';
        }
      }
      //邮件内容部分
      
      $html_content = '<table width="650" cellspacing="0" cellpadding="0" border="0" align="center" style=" font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#333333; line-height:18px; border:0;">
        <tbody>
          <tr>
            <td bgcolor="#ffffff" colspan="2" style=" padding:10px 30px 0 30px; font-size:11px;">
              <b style=" display:block; padding-top:10px;">$EMAIL_BODY_COMMON_DEAR $CUSTOMER_NAME,</b>
              </td>
          </tr>
          <tr>
            <td  colspan="2" bgcolor="#ffffff" style=" padding:0 30px; font-size:11px;">
              <br>
              Thank you for your recent purchase from <a href="http://www.fs.com/?utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_term=fs.com">FS.COM</a>.
             </td>
          </tr>
          <tr>
            <td  colspan="2" bgcolor="#ffffff" style=" padding:0 30px; font-size:11px;">
              <br>
             According to the tracking information online, we noticed that you have received the item we shipped out (Order Number: <a href="$ORDERS_INFO_URL" style=" color:#2971ba;">$ORDERS_NUMBER</a>). We sincerely hope you are happy with your purchase and if you are, please spare some time to submit a review or even share an image for the product you purchased. </td>
          </tr>
          <tr>
            <td  colspan="2" bgcolor="#ffffff" style=" padding:0 30px; font-size:11px;">
              <br>
              Your comments and feedback stimulate us to improve products and services constantly for our customers, which are essential to the development of our business. It would be highly appreciated if you could leave us a positive feedback. Any questions or suggestions are also welcome and will be replied ASAP.
             </td>
          </tr>
          <tr>
            <td bgcolor="#ffffff" style=" padding:0 30px; font-size:11px;"><br>It’s easy to submit a review–just click the <a href="$WRITE_REVIEW_URL" style=" color:#2971ba;">Write a Review</a> next to the product.</td>
          </tr>
          <tr>
            <td bgcolor="#ffffff" style=" padding:0 30px; font-size:11px;"><br /><br /><b>Your Order Summary:</b></td>
          </tr>
          <tr>
            <td bgcolor="#ffffff" colspan="2" style=" padding:0 30px;"><p style="padding-bottom:15px; height:0;margin:0;"> </p>
              <table width="100%" border="0" align="center" cellspacing="1" cellpadding="5" style=" font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#333333; line-height:18px;">
                <tbody>
                  $PRODUCTS_HTML
                  <tr style="font-size:11px;">
                    <td colspan="3" style="font-size:11px; padding:10px 0 10px 0;"><a href="$WRITE_REVIEW_URL" style="height:30px; line-height:30px; border:1px solid #2971ba; padding:0 15px; display:inline-block; color:#2971ba; text-decoration:none; ">Write a Review</a></td>
                  </tr>
                </tbody>
              </table></td>
          </tr>
          <tr>
              <td colspan="2" style=" padding:0 30px 20px 30px; font-size:11px;">
            <br>
            By the way, remember to change your order status by clicking <a href="$HERE_URL" style=" color:#2971ba;">here</a> in order to apply for after-sales service in case you would have any problem. </td>
      
          </tr>
      
          </tbody></table>';
      
      $content['CUSTOMER_NAME'] = $order_info['customers_name'];
      $content['PRODUCTS_HTML'] = $products_html;
      $content['ORDERS_NUMBER'] = $order_info['orders_number'];
      $content['HERE_URL'] = 'https://www.fs.com/index.php?main_page=account_history_info&orders_id='.$info['orders_id'].'&utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_term=here';
      $content['WRITE_REVIEW_URL'] = 'https://www.fs.com/index.php?main_page=submit_orders_review&orders_id='.$info['orders_id'].'&utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_term=Write%20a%20Review';
      $content['ORDERS_INFO_URL'] = 'https://www.fs.com/index.php?main_page=account_history_info&orders_id='.$info['orders_id'].'&utm_source=newsletter&utm_campaign=review_feedback&utm_medium=email&utm_term='.$order_info['orders_number'];
      
      $html_msg = $html_header;
      
      foreach ($content as $key=>$val){
        $html_content = str_replace('$'.$key,$val,$html_content);
      }
      $html_msg .= $html_content;
      
      $html_msg .= $html_footer;
      
      /*$cw[]=array('orders_num'=>$info['orders_num'],'fs'=>$order_info['orders_number'],'email'=>$customers_email);
      $state = $smtp->sendmail('amberxyzstevens@gmail.com', $smtpusermail, $mailtitle, $html_msg, $mailtype);
      $state = $smtp->sendmail('hkecopto@gmail.com', $smtpusermail, $mailtitle, $html_msg, $mailtype);
      $state = $smtp->sendmail('kern@szyuxuan.com', $smtpusermail, $mailtitle, $html_msg, $mailtype);die();*/
     if($customers_email){
       $smtp->sendmail('buck.pan@szyuxuan.com',$smtpusermail,$mailtitle,$html_msg,$mailtype);die;
      // $state = $smtp->sendmail($customers_email, $smtpusermail, $mailtitle, $html_msg, $mailtype);
     }
    }
  }
}
mysqli_close($con);
