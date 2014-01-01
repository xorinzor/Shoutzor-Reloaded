<?php

//
// include the phpMailer class
//
include(LIB_PATH . "phpmailer/class.phpmailer.php");

class mailer
{
        private $validSubjects;

        public function __CONSTRUCT()
        {
                $this->validSubjects = array(
                                    "resetpassword" => "Reset Password",
                                    "registration" => "Account Registration",
                                    "removeaccount" => "Account removal"
                                );

		        $this->phpMailer = new PHPMailer();
        }

        public function sendMail($subject, $to, $params = array()) {
            global $_LANG, $_configuration;
                if(array_key_exists($subject, $_LANG['mail'])):
                	//
                	// Check if SMTP is enabled
                	//
					if($_configuration['settings']['smtp'] === true):
						try {
							$this->phpMailer->IsSMTP();
							$this->phpMailer->SMTPSecure = 'ssl';
							$this->phpMailer->Host       = $_configuration['smtp']['default']['host'];
							$this->phpMailer->SMTPAuth   = $_configuration['smtp']['default']['auth'];
							$this->phpMailer->Port       = $_configuration['smtp']['default']['port'];
							$this->phpMailer->Username   = $_configuration['smtp']['default']['user'];
							$this->phpMailer->Password   = $_configuration['smtp']['default']['pass'];

							$this->phpMailer->SetFrom('noreply@mysocialsync.com', 'MySocialSync');
							$this->phpMailer->AddAddress($to);

							$this->phpMailer->Subject = $_LANG['mail'][$subject]['subject']." - MySocialSync";
							$this->phpMailer->AltBody = 'The email you are trying to view is an HTML email, unfortunatly your email client doesn\'t support this'; // optional - MsgHTML will create an alternate automatically

                            $params['sprintf'] = array_merge(array(
                                                                0  => $_LANG['mail'][$subject]['body']
                                                            ), $params['sprintf']);
                            
                            cl::g("tpl")->assign("TITLE", $_LANG['mail'][$subject]['subject']);
                            cl::g("tpl")->assign("CONTENT", call_user_func_array('sprintf', $params['sprintf']));
                            
                            $this->phpMailer->MsgHTML(cl::g("tpl")->fetch(VIEW_PATH . 'themes/mail/1/template.tpl'));

							$result = $this->phpMailer->send();
							if(!$result):
								debug::addLine("ERROR", "An error occured while trying to send an email: ".$this->phpmailer->ErrorInfo, __FILE__, __LINE__);
								$succes = false;
							else:
								$succes = true;
							endif;
						} catch (phpmailerException $e) {
							debug::addLine("ERROR", $e->errorMessage(), __FILE__, __LINE__);
							$succes = false;
						} catch (Exception $e) {
							debug::addLine("ERROR", $e->getMessage(), __FILE__, __LINE__);
							$succes = false;
						}

						return (boolean) $succes;
					else:
						//
						// Set-up the mail basics
						//
						$mail_from = "noreply@mysocialsync.com";
						$name_from = "MySocialSync";
						$mail_subject = $this->validSubjects[$subject]." - MySocialSync";

						$headers  = 'MIME-Version: 1.0' . "\n";
						$headers .= 'Content-Type: text/html; charset=UTF-8' . "\n";
						$headers .= 'From: '.$name_from.' <'.$mail_from.'>';

						//
						// Send the actual mail and return TRUE or FALSE
						//
						$mail = mail($to, $mail_subject, $mailContent, $headers);      						

						return (boolean) $mail;
					endif;
				else:
					return false;
                endif;
        }
}