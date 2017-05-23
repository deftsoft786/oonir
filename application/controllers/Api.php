<?php
	/**
		Created by  : N/S 23/November/2015
				    : 10-Dec-2015	
					
					
					LIVE
	*/
	
	// date_default_timezone_set("UTC");
	date_default_timezone_set("ASIA/KOLKATA");
	ini_set('memory_limit' , '100M');	
	// ini_set('post_max_size', '100M');
	ini_set('max_execution_time', 600);
	class Api extends CI_Controller
	{
		function __construct()
		{
			parent::__construct();
			$this->load->helper('url');
			$this->load->database();
			$this->load->model('Tran_model');
			$this->load->library('email');	
			// $this->load->library('email', $config);
			
		}
		public function sendMail(){
		$config = Array(
					 'protocol' => 'smtp', //sendmail
					 'smtp_host' => 'localhost',									
					 'smtp_port' => 465,// 465 587
					 'smtp_user' => 'support@oonir.com', // change it to yours
					 'smtp_pass' => '4R!Ut-nDFgHI', // change it to yours
					 'mailtype' => 'html',
					 'charset' => 'iso-8859-1',
					 'wordwrap' => TRUE
						); 	
						// $url = "http://parkuhr.com/parkweb/home/activate_email?user_id=$result_id&status=1";
						$url = "http://oonir.com/oonir/activateEmail?user_id=123&status=1";
						$message  = "<body style='background-color: #F1E4E4;padding:15px;border-radius:3px'>";
						$message .= "<p>Hello Tester,</p>";
						$message .="<p>To activate your account please click on Activate button</p></br></br>";
						$message .="<table cellspacing='0' cellpadding='0'>";						
						$message .=" <tr><td align='center' width='300' height='40' bgcolor='#000091' style='-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;color: #ffffff; display: block;'>";
						$message .= "<a href='".$url."' style='color: #ffffff; font-size:16px; font-weight: bold; font-family: Helvetica, Arial, sans-serif; text-decoration: none;
						line-height:40px; width:100%; display:inline-block'>Click to Activate</a>";
						$message .= "</td> </tr> </table>";
						$message .= "</br>";
						$message .="<p>Thank you,<br></p>";
						$message .="<p>Oonir Support Team</p>";					
						$message .="<p>Address :</p>";
						$message .="<p>26/3/2555, Sapthagiri colony,<br/> BV Nagar, Mini bypass road,<br/> opp GVRR College Nellore,<br/> Pincode 524001, Contact Number : +91-8612313177<br/><br/></p>";
						$message .="<p>Disclaimer : This is system auto generated email. Please do not reply on this email. If you have any concern then please contact us on helpdesk@oonir.com
						</p>";	
						$message .= "</body>";
						
						$this->load->library('email', $config);
						$this->email->set_mailtype("html");
						$this->email->set_newline("\r\n");
						$this->email->from('support@oonir.com', "Oonir Support Team");
						// $this->email->to($email);					
						// $this->email->to("riya.sen485@gmail.com");	
						$this->email->to("tt35093@gmail.com");	
						$this->email->subject("Activate Your Account");
						$this->email->message("<p>To activate your account please click on Activate buttton</p>");
						$this->email->message($message);
						   
						if($this->email->send())
						{  
							$msg['return'] = 1;
							$msg['message'] = "Mail sent successfully";   
						}else
						{
							$data['message'] = "Sorry Unable to send email"; 
							$msg['return'] = 0;
							$msg['error'] = show_error($this->email->print_debugger());
						} 	
						echo json_encode($msg);exit;
		}
		function Signup($username = '',$email='',$mobile_number='',$location='',$password='',$image='',$device_token='',$device_type='',$user_type='',$company_name='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($username) && !empty($email) && !empty($mobile_number) && !empty($password) && !empty($device_type)  && !empty($device_token)&& !empty($user_type))
				{
					$promo_code = $this->Tran_model->random_string();	
					
					$postData['username'] 		= $username;
					$postData['email'] 			= $email;
					$postData['password'] 		= $password;
					$postData['mobile_number'] 	= $mobile_number;
					$postData['location'] 		= $location;
					$postData['device_token'] 	= $device_token;
					$postData['device_type'] 	= $device_type;
					$postData['user_type'] 		= $user_type;
					$postData['otp_code'] 		= $promo_code;
					$postData['company_name'] 	= $company_name;										
					$postData['user_status'] 	= 1;
					$postData['mobile_verification'] = 0;
					$postData['email_verification'] = 0;
					$postData['created_date'] 	= date('Y-m-d h:i:s');
					
				
					$condition = array('email'=>$postData['email']);	
					$field = array('email');			
					$data = $this->Tran_model->get_selected_data($field,'user',$condition);
					if(is_array($data) && !empty($data))
					{
						
						$msg['return'] = 0;
						$msg['result'] = 'Email  already exists';					
						echo json_encode($msg);exit;
					}
					
					$condition = array('mobile_number'=>$postData['mobile_number']);	
					$field = array('mobile_number','otp_code');			
					$result_data = $this->Tran_model->get_selected_data($field,'user',$condition);
					if(is_array($result_data) && !empty($result_data))
					{	
						$msg['return'] = 0;
						$msg['result'] = 'Mobile number already exists';					
						echo json_encode($msg);exit;								
					}
					 
					$result_id = $this->Tran_model->insert_data('user',$postData);
					if($result_id)
					{
						/************** send otp *********************/
					
						$PATH = "application/twilio/Services/Twilio.php";
						$this->load->file($PATH, true);					
						$account_sid = 'ACc5b9e58882afd33353efae0bd82342ad'; 
						$auth_token  = 'b3dde870e90dc41907c668bfb69804f8'; 
						
						$client = new Services_Twilio($account_sid, $auth_token); 
						try 
						{	
							if($mobile_number=="5124977435")
							{
								$message = $client->account->messages->create(array( 
												'To' => "+91".$mobile_number,  // 917837234329
												// 'From' => "+19375230160", 
												'From' => "+1 512-456-3141",
												'Body' => "Your One Time Password : ".$promo_code,   
										));	
										
							}else{
								 $message = $client->account->messages->create(array( 
											'To' => "+91".$mobile_number,  // 917837234329
											// 'From' => "+19375230160", 
											'From' => "+1 512-456-3141",
											'Body' => "Your One Time Password for oonir application registration is : ".$promo_code,   
									));	
							}
						} catch (Services_Twilio_RestException $e) {
							// echo $e->getMessage();
							$msg['return'] 	= 0;
							// $msg['result'] 	= $e->getMessage();
							$msg['result'] 	= "Your number is not verified for sending OTP";							
							$msg['message'] = 'failed';
									
						}
						/************** send otp *********************/
								
						$config = Array(
									 'protocol' => 'smtp', //sendmail
									 'smtp_host' => 'localhost',									
									 'smtp_port' => 465,// 465 587
									 'smtp_user' => 'support@oonir.com', // change it to yours
									 'smtp_pass' => '4R!Ut-nDFgHI', // change it to yours
									 'mailtype' => 'html',
									 'charset' => 'iso-8859-1',
									 'wordwrap' => TRUE
								); 	
					
						$url = "http://www.oonir.com/ws/api/activate_email?user_id=$result_id&status=1";
						$message  = "<body style='background-color: #F1E4E4;padding:15px;border-radius:3px'>";
						$message .= "<p>Hello $username,</p>";
						$message .="<p>To activate your account please click on Activate button</p></br></br>";
						$message .="<table cellspacing='0' cellpadding='0'>";						
						$message .=" <tr><td align='center' width='300' height='40' bgcolor='#000091' style='-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;color: #ffffff; display: block;'>";
						$message .= "<a href='".$url."' style='color: #ffffff; font-size:16px; font-weight: bold; font-family: Helvetica, Arial, sans-serif; text-decoration: none;
						line-height:40px; width:100%; display:inline-block'>Click to Activate</a>";
						$message .= "</td> </tr> </table>";
						$message .= "</br>";
						$message .="<p>Thank you,<br></p>";
						$message .="<p>Oonir Support Team</p>";					
						$message .="<p>Address :</p>";
						$message .="<p>26/3/2555, Sapthagiri colony,<br/> BV Nagar, Mini bypass road,<br/> opp GVRR College Nellore,<br/> Pincode 524001, Contact Number : +91-8612313177<br/><br/></p>";
						$message .="<p>Disclaimer : This is system auto generated email. Please do not reply on this email. If you have any concern then please contact us on helpdesk@oonir.com
						</p>";	
						$message .= "</body>";
						
						$this->load->library('email', $config);
						$this->email->set_mailtype("html");
						$this->email->set_newline("\r\n");
						$this->email->from('support@oonir.com', "Oonir Support Team");
						$this->email->to($email);					
						// $this->email->to("riya.sen485@gmail.com");	
						$this->email->subject("Activate Your Account");
						$this->email->message("<p>To activate your account please click on Activate buttton</p>");
						$this->email->message($message);
						   
						if($this->email->send())
						{  
							$msg['return'] = 1;
							$msg['message'] = "Mail sent successfully";   
						}
						else
						{
							$data['message'] = "Sorry Unable to send email"; 
							$msg['return'] = 0;
							$msg['error'] = show_error($this->email->print_debugger());
						}
						
						$condition = array('user_id'=> $result_id);		
						// $result_data = $this->Tran_model->select_single_row_data('user',$condition,0,0);
						$result_data = $this->Tran_model->select_single_row('user',$condition,0,0);
						// echo "<pre>";
						// print_r($result_data2);
						if(!empty($result_data))
						{						
							if($user_type==1)
							{
								$msg['return'] = 1;
								$msg['result'] = 'Transporter registered successfully';								
								$msg['data'] = $result_data;								
							}
							else if($user_type==2)
							{
								$msg['return'] = 1;
								$msg['result'] = 'Customer registered successfully';								
								$msg['data'] = $result_data;							
							}							
													
						}else{
							$msg['return'] = 0;
							$msg['result'] = 'failed';	
						}
			
					}else{
						$msg['return'] = 0;
						$msg['result'] = 'Insertion failed';					
					}
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : username,email,password,image,device_type,device_token,location,user_type,mobile_number,otp,company_name';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] =  "please provide parameter: username,email,password,image,device_token,device_type,location,user_type,mobile_number,otp,company_name";
			}
			echo json_encode($msg);
		}
		/**********************************  ********************************************/
		
		/************************************** OTP verify  *********************************/
		function otp_verify($email_id='',$otp_code='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($otp_code) && !empty($email_id))
				{		
					$query = $this->db->query('select user_id from user where email="'.$email_id.'" and BINARY otp_code="'.$otp_code.'"');
					if($query->num_rows()>0)
					{
						$this->db->query('update user set mobile_verification=1 where email="'.$email_id.'" ');
						$msg['return'] = 1;
						$msg['result'] = 'Verified successfully'	;									
					}else{
						$msg['return'] = 0;
						$msg['result'] = 'You have entered wrong otp code';
					}
					
				}else{
					$msg['return'] = 0;
					$msg['result'] =  "please provide parameter: email_id,otp_code";
				}
				echo json_encode($msg);	
			}
		}	
		
		
		/************************************** OTP send  *********************************/
		function otp_send($mobile_number='',$email='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($mobile_number) && !empty($email))
				{		
					$promo_code = $this->Tran_model->random_string();	
					$PATH = "application/twilio/Services/Twilio.php";
					$this->load->file($PATH, true);
					// require('/path/to/twilio-php/Services/Twilio.php'); 
						/*  $http = new Services_Twilio_TinyHttp(
						'https://api.twilio.com',
						array('curlopts' => array(
							CURLOPT_SSL_VERIFYPEER => true,
							CURLOPT_SSL_VERIFYHOST => 2,
						))
					); */
					// $account_sid = 'ACfb607a99c371ba07ce1018fdc99fa983'; 
					// $auth_token = '30f5c4eaa1b273ed2954154b0e157ba9'; 
					
					$account_sid = 'ACc5b9e58882afd33353efae0bd82342ad'; 
					$auth_token  = 'b3dde870e90dc41907c668bfb69804f8'; 
					
					$client = new Services_Twilio($account_sid, $auth_token); 
					// $client = new Services_Twilio($account_sid, $auth_token, "2010-04-01", $http);
						
						
						/* ########### mobile check ######## */
						$condition = array('mobile_number'=> $mobile_number);		
						$field = array('mobile_verification');	
						$result_data = $this->Tran_model->get_selected_data($field,'user',$condition);
						if(is_array($result_data) && !empty($result_data))
						{	
							// echo "<pre>";
							// print_r($result_data);
							$query = $this->db->query('select * from user where email="'.$email.'"');
							if($query->num_rows()>0)
							{
								$result_data = $query->result_array();
								$email_verify = $result_data[0]['email_verification'];
								if($email_verify==1)
								{	
									$email_verify = 'verified';	
								}else{
									$email_verify = 'not verified';	
								}
								$email_exist = 'Email already exists';	
							}else{
								$email_exist = '';	
								$email_verify = '';	
							}
							// exit;
							$mobile_verify = $result_data[0]['mobile_verification'];
							if($mobile_verify==1)
							{
								$msg['return'] 	=  0;
								$msg['mobile_result'] 	=  "Mobile number already exists";								
								$msg['mobile_status'] 	=  "verified";
								$msg['email_result'] 	=  $email_exist;	
								$msg['email_status'] 	=  $email_verify ;
							}else{								
							
								$msg['return'] 	=  0;
								$msg['mobile_result'] 	=  "Mobile number already exists";
								$msg['mobile_status'] 	=  "not-verified";
								$msg['email_result'] 	=  $email_exist;	
								$msg['email_status'] 	=  $email_verify ;								
							}	
										
							echo json_encode($msg);exit;
						}else{
							
							$query = $this->db->query('select * from user where email="'.$email.'"');
							if($query->num_rows()>0)
							{
								$result_data = $query->result_array();
								$email_verify = $result_data[0]['email_verification'];
								if($email_verify==1)
								{	
									$email_verify = 'verified';	
								}else{
									$email_verify = 'not verified';	
								}
								$email_exist = 'Email already exists';	
								$msg['return'] 	=  0;
								$msg['mobile_result'] 	=  "";
								$msg['mobile_status'] 	=  "";
								$msg['email_result'] 	=  $email_exist;	
								$msg['email_status'] 	=  $email_verify ;
								// $msg['data'] 			=  $promo_code;			
							}else{									
									try 
									{	
										if($mobile_number=="5124977435")
										{
											$message = $client->account->messages->create(array( 
															'To' => "+91".$mobile_number,  // 917837234329
															// 'From' => "+19375230160", 
															'From' => "+1 512-456-3141",
															'Body' => "Your One Time Password : ".$promo_code,   
													));	
													
										}else{
											 $message = $client->account->messages->create(array( 
														'To' => "+91".$mobile_number,  // 917837234329
														// 'From' => "+19375230160", 
														'From' => "+1 512-456-3141",
														'Body' => "Your One Time Password : ".$promo_code,   
												));	
										}
									} catch (Services_Twilio_RestException $e) {
										// echo $e->getMessage();
										$msg['return'] 	= 0;
										// $msg['result'] 	= $e->getMessage();
										$msg['result'] 	= "Your number is not verified for sending OTP";
										
										$msg['message'] = 'failed';
												
									}
								
								
								$email_exist = '';	
								$email_verify = '';	
								$msg['return'] 	=  1;
								$msg['mobile_result'] 	=  "";
								$msg['mobile_status'] 	=  "";
								$msg['email_result'] 	=  $email_exist;	
								$msg['email_status'] 	=  $email_verify ;
								$msg['data'] 			=  $promo_code;				
							}	
							echo json_encode($msg);exit;							
						}
						
						
						/* ############ mobile check ########  */
						
				}else{
					$msg['return'] = 0;
					$msg['result'] =  "Please enter valid mobile number";
				}
			}	
			else{
				$msg['return'] = 0;
				$msg['result'] =  "Please enter valid mobile number";
			}
			echo json_encode($msg);
		}	
		/**************************************  OTP Send *********************************/
		
		
		/************************************** OTP resend  *********************************/
		function otp_resend($mobile_number='',$user_id='',$type='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($mobile_number))
				{		
					$promo_code = $this->Tran_model->random_string();	
					$PATH = "application/twilio/Services/Twilio.php";
					$this->load->file($PATH, true);					
					// $account_sid = 'ACfb607a99c371ba07ce1018fdc99fa983'; 
					// $auth_token  = '30f5c4eaa1b273ed2954154b0e157ba9'; 
					
					$account_sid = 'ACc5b9e58882afd33353efae0bd82342ad'; 
					$auth_token  = 'b3dde870e90dc41907c668bfb69804f8'; 
					$client = new Services_Twilio($account_sid, $auth_token); 	
					try
					{
						
						// if(isset($user_id))
						// {
							
						// }
						if($type==1) // edit profile 
						{
							$query = $this->db->query('select mobile_number from user where mobile_number="'.$mobile_number.'"');							
							if($query->num_rows()>0)
							{
								$msg['return'] 	= 0;							
								$msg['result'] 	= "Mobile number already registered";
								// $msg['data'] 	= $promo_code;	
							}else{
								
								
								if($mobile_number=="5124977435")
						{
							  $message = $client->account->messages->create(array( 
										'To' => "+1".$mobile_number,  // 917837234329
										// 'From' => "+19375230160", // +1 512-456-3141
										'From' => "+1 512-456-3141", // +1 512-456-3141
										'Body' => "Your One Time Password : ".$promo_code,   
								));	
						}else{
							 $message = $client->account->messages->create(array( 
										'To' => "+91".$mobile_number,  // 917837234329
										// 'From' => "+19375230160", 
										'From' => "+1 512-456-3141",
										'Body' => "Your One Time Password for oonir application registration is  : ".$promo_code,   
								));	
						}
								$this->db->query('update user set otp_code="'.$promo_code.'" where mobile_number="'.$mobile_number.'" ');
								$msg['return'] 	= 1;							
								$msg['result'] 	= "OTP has been sent to your number successfully";
								$msg['data'] 	= $promo_code;	
							}	
						}else{
							
							if($mobile_number=="5124977435")
						{
							  $message = $client->account->messages->create(array( 
										'To' => "+1".$mobile_number,  // 917837234329
										// 'From' => "+19375230160", // +1 512-456-3141
										'From' => "+1 512-456-3141", // +1 512-456-3141
										'Body' => "Your One Time Password : ".$promo_code,   
								));	
						}else{
							 $message = $client->account->messages->create(array( 
										'To' => "+91".$mobile_number,  // 917837234329
										// 'From' => "+19375230160", 
										'From' => "+1 512-456-3141",
										'Body' => "Your One Time Password for oonir application registration is  : ".$promo_code,   
								));	
						}
							$this->db->query('update user set otp_code="'.$promo_code.'" where mobile_number="'.$mobile_number.'" ');
							$msg['return'] 	= 1;							
							$msg['result'] 	= "OTP has been sent to your number successfully";
							$msg['data'] 	= $promo_code;	
							
						}
						
					} 
					catch (Services_Twilio_RestException $e) 
					{							
						$msg['return'] 	= 0;							
						$msg['result'] 	= "Your number is not verified for sending OTP";								
						$msg['message'] = 'failed';										
					}

				}else{
					$msg['return'] = 0;
					$msg['result'] =  "Please enter valid mobile number";
				}
			}	
			else{
				$msg['return'] = 0;
				$msg['result'] =  "Please enter valid mobile number";
			}
			echo json_encode($msg);
		}	
		/**************************************  OTP Send *********************************/
		
		
		/**************************************  Email verification *********************************/
		public function activate_email($user_id='',$status='')
		{	
			extract($_REQUEST);		
			$query = $this->db->query('select * from user where user_id="'.$user_id.'" and email_verification = 1');
			if($query->num_rows()>0)
			{
				echo "<div style=' background: #ebf4fb none repeat scroll 0 0;
						border: 2px dotted #ebf4fb;
						color: red;
						font-size: 16px;
						font-weight: bold;
						height: 80px;
						margin: 100px auto;
						padding-top: 30px;
						text-align: center;
						width: 900px;'>You already activated your email account";
			}
			else
			{
				$query = $this->db->query('update user set email_verification ="'.$status.'" where user_id = "'.$user_id.'" ');
				if($query)
				{
					echo "<div style=' background: #ebf4fb none repeat scroll 0 0;
						border: 2px dotted #ebf4fb;
						color: green;
						font-size: 16px;
						font-weight: bold;
						height: 80px;
						margin: 100px auto;
						padding-top: 30px;
						text-align: center;
						width: 900px;'>Congratulation!! Your Account has been activated</div>";
				}else{
					echo "Failed to activate your email account";
					
				}
			}
		}
		/**************************************  Email verification *********************************/
		
		/************************************** Login  *********************************/
		function login($username = '',$password='',$device_token='',$device_type='',$user_type='')
		{		
			if(!empty($_REQUEST) && isset($_REQUEST))
			{				
				extract($_REQUEST);
				$postData =array();
				if(!empty($username) && !empty($device_type)  && !empty($device_token) && !empty($user_type))
				{					
					$postData['username'] 		= $username;
					$postData['password'] 		= $password;
					$postData['user_type'] 		= $user_type;
					$postData['device_token'] 	= $device_token;
					$postData['device_type']	= $device_type;
					
					if($user_type == 1) // Transporter
					{
						$condition = array('email'=> $username,'password'=>$password,'user_type'=>$user_type);		
						// $result = $this->Tran_model->select_single_row_data('user',$condition,0,0);
						
						$query = $this->db->query('select user_status,mobile_verification,email,mobile_number from user where (email="'.$username.'" and BINARY password="'.$password.'" and user_type=1 ) or (mobile_number="'.$username.'" and BINARY password="'.$password.'" and user_type=1 ) ');						
						if($query->num_rows()>0)
						{							
							$result = $query->row_array();							
							if($result['mobile_verification']==0)
							{
								$mobile_number = $result['mobile_number'];
								$email = $result['email'];
								$msg['return'] = 2;
								$msg['result'] = "Your mobile number $mobile_number is not verified. Verify now?";
								$msg['mobile_number']   = $mobile_number;
								$msg['email'] = $email;
								echo json_encode($msg);exit;									
							}
						
							$query = $this->db->query('select email from user where email="'.$username.'" and BINARY password="'.$password.'" and email_verification=0');
							if($query->num_rows()>0)
							{
								$msg['return'] = 0;
								$msg['result'] = 'Please check your email to verify your account';								
								echo json_encode($msg);exit;	
							}
							
							$query = $this->db->query('select email from user where mobile_number="'.$username.'" and BINARY password="'.$password.'" and mobile_verification=0');
							if($query->num_rows()>0)
							{
								$msg['return'] = 2;
								$msg['result'] = 'Your mobile number $username is not verified. verify now?';
								$msg['mobile_number']   = $username;
								$msg['email'] = $result['email'];
								echo json_encode($msg);exit;	
							}
							
														
							$array_data = array(
												'device_token'=>$postData['device_token'],
												'device_type'=>$postData['device_type']
											); 
							$condition1 = array('email' => $username);
							$condition2 = array('mobile_number' => $username);
							$this->Tran_model->update_selected_data('user',$array_data,$condition1);
							$this->Tran_model->update_selected_data('user',$array_data,$condition2);
							// $condition = array('email'=> $username,'password'=>$password,'user_type'=>$user_type);	
							// $result_data = $this->Tran_model->select_single_row_data('user',$condition,0,0);
							$query = $this->db->query('select * from user where (email="'.$username.'" and BINARY password="'.$password.'" and user_type=1 ) or (mobile_number="'.$username.'" and BINARY password="'.$password.'" and user_type=1 ) ');
						
							if($query->num_rows()>0)
							{								
								$result_data = $query->row();								
								$msg['return'] = 1;
								$msg['result'] = 'Login successfully';	
								// $msg['result'] = 'Some error occurred. Please contact admin';	
								$msg['data']   = $result_data;								
								echo json_encode($msg);exit;								
							}else{
								$msg['return'] = 0;
								$msg['result'] = 'Login failed';	
								echo json_encode($msg);exit;	
							}
							
							
						}else{
							// echo "failed";
							$msg['return'] = 0;
							$msg['result'] = 'Email or Password is incorrect';					
							echo json_encode($msg);exit;
						}
						
					}
					else if($user_type == 2) // customer
					{						
						$query = $this->db->query('select * from user where (email="'.$username.'" and BINARY password="'.$password.'" and user_type=2 ) or (mobile_number="'.$username.'" and BINARY password="'.$password.'" and user_type=2 ) ');
						// echo 'select * from user where (email="'.$username.'" and password="'.$password.'" and user_type=1 ) or (mobile_number="'.$username.'" and password="'.$password.'" and user_type=1 ) ';					
						if($query->num_rows()>0)
						{	
							$result = $query->row_array();				
							if($result['mobile_verification']==0)
							{
								$mobile_number = $result['mobile_number'];
								$email = $result['email'];
								$msg['return'] = 2;
								$msg['result'] = "Your mobile number $mobile_number is not verified. Verify now?";
								$msg['mobile_number']   = $mobile_number;
								$msg['email'] = $email;
								echo json_encode($msg);exit;									
							}
							
							$query = $this->db->query('select email from user where email="'.$username.'" and BINARY password="'.$password.'" and email_verification=0');
							if($query->num_rows()>0)
							{
								$msg['return'] = 0;
								$msg['result'] = 'Please check your email to verify your account';								
								echo json_encode($msg);exit;	
							}
							
							$query = $this->db->query('select email from user where mobile_number="'.$username.'" and BINARY password="'.$password.'" and mobile_verification=0');
							if($query->num_rows()>0)
							{
								$msg['return'] = 2;
								$msg['data']   = $username;
								$msg['result'] = 'Your mobile number $username is not verified. verify now?';
								echo json_encode($msg);exit;	
							}
							
							$array_data = array(
												'device_token'=>$postData['device_token'],
												'device_type'=>$postData['device_type']
											); 
							$condition1 = array('email' => $username);
							$condition2 = array('mobile_number' => $username);
							$this->Tran_model->update_selected_data('user',$array_data,$condition1);
							$this->Tran_model->update_selected_data('user',$array_data,$condition2);
							
							$query = $this->db->query('select * from user where (email="'.$username.'" and BINARY password="'.$password.'" and user_type=2 ) or (mobile_number="'.$username.'" and BINARY password="'.$password.'" and user_type=2 ) ');						
							if($query->num_rows()>0)
							{
								$result_data = $query->row_array();								
								// if($result_data['user_status']==0)
								// {
									// $msg['return'] = 0;
									// $msg['result'] = 'You are blocked user';
								// }else{
									// $msg['return'] = 1;
									// $msg['result'] = 'Login successfully';	
									// $msg['data']   = $result_data;	
									// echo json_encode($msg);exit;		
								// }	
								$msg['return'] = 1;
								$msg['result'] = 'Login successfully';	
								$msg['data']   = $result_data;	
								echo json_encode($msg);exit;
							}else{
								$msg['return'] = 0;
								$msg['result'] = 'Login failed';	
								echo json_encode($msg);exit;	
							}
							
						}else{
							// echo "failed";
							$msg['return'] = 0;
							$msg['result'] = 'Email or Password is incorrect';					
							echo json_encode($msg);exit;
						}	
						
					}
					
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : username, password, device_token, device_type,  user_type';					
				}
				
			}else{
				$msg['return'] = 0;
				$msg['result'] =  "please provide parameter: username, password, device_token, device_type,  user_type";
			}	
			echo json_encode($msg);
		}
		
		/***************************** update device token  ****************************************/
		function update_device_token($user_id='',$device_token = '')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				if(!empty($user_id) && !empty($device_token))
				{
					$query = $this->db->query('update user set device_token="'.$device_token.'"  where user_id="'.$user_id.'"');					
					if($query)
					{									
						$msg['return'] = 1;
						$msg['result'] = 'Device token updated successfully';		
					
					}else{
						$msg['return'] = 0;
						$msg['result'] = 'updation failed';							
					}			
				}
				else{
					$msg['return'] = 0;
					$msg['result'] =  "please provide parameter: user_id,device_token";
				}			
			}
			echo json_encode($msg);
		}
		/******************************* update device token  **********************************/
		
		/************************************** Login Driver ***********************************/
		function login_driver($truck_code = '',$driver_code = '', $password = '')
		{		
			if(!empty($_REQUEST) && isset($_REQUEST))
			{				
				extract($_REQUEST);
				$postData =array();
				if(!empty($truck_code) && !empty($driver_code) && !empty($password))
				{	
					$query1 = $this->db->query('select * from truck_driver where driver_code ="'.$driver_code.'" and password = "'.$password.'"');						
						if($query1->num_rows()>0)
						{
							$result_data1 = $query1->result_array();
							if($result_data1[0]['admin_verify'] == 1){
						$query = $this->db->query('select tl.* ,u.username as transporter_name,u.mobile_number from truck_list tl INNER JOIN user u on tl.user_id=u.user_id where truck_code="'.$truck_code.'" ');						
						if($query->num_rows()>0)
						{						
							$result_data = $query->result_array();
							$result_data[0]['driver_name'] = $result_data1[0]['driver_name'];
							$result_data[0]['contact_number'] = $result_data1[0]['contact_number'];
							$result_data[0]['driver_code'] = $result_data1[0]['driver_code'];
							$result_data[0]['driver_id'] = $result_data1[0]['id'];
							// echo "<pre>";
							// print_r($result_data);
							
							$msg['return'] 	= 1;
							$msg['result'] 	= 'Login successfully';
							$msg['data'] 	= $result_data;							
							
						}else{
							$msg['return'] = 0;
							$msg['result'] = 'Please enter valid truck id';				
						}
							}else{
								$msg['return'] = 0;
								$msg['result'] = 'You are not verified by user';			
							}
					}else{
						$msg['return'] = 0;
						$msg['result'] = 'Please enter valid driver code,Password';				
					}					
					
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : truck_code,driver_code,password';					
				}
				
			}else{
				$msg['return'] = 0;
				$msg['result'] =  "please provide parameter: truck_code";
			}	
			echo json_encode($msg);
		}
		
		
		function login_driver_without_password($truck_code = '',$driver_code = '')
		{		
			if(!empty($_REQUEST) && isset($_REQUEST))
			{				
				extract($_REQUEST);
				$postData =array();
				if(!empty($truck_code) && !empty($driver_code))
				{	
					$query1 = $this->db->query('select * from truck_driver where driver_code ="'.$driver_code.'"');						
						if($query1->num_rows()>0)
						{
							$result_data1 = $query1->result_array();
							if($result_data1[0]['admin_verify'] == 1){
						$query = $this->db->query('select tl.* ,u.username as transporter_name,u.mobile_number from truck_list tl INNER JOIN user u on tl.user_id=u.user_id where truck_code="'.$truck_code.'" ');						
						if($query->num_rows()>0)
						{						
							$result_data = $query->result_array();
							$result_data[0]['driver_name'] = $result_data1[0]['driver_name'];
							$result_data[0]['contact_number'] = $result_data1[0]['contact_number'];
							$result_data[0]['driver_code'] = $result_data1[0]['driver_code'];
							$result_data[0]['driver_id'] = $result_data1[0]['id'];
							// echo "<pre>";
							// print_r($result_data);
							
							$msg['return'] 	= 1;
							$msg['result'] 	= 'Login successfully';
							$msg['data'] 	= $result_data;							
							
						}else{
							$msg['return'] = 0;
							$msg['result'] = 'Please enter valid truck id';				
						}
							}else{
								$msg['return'] = 0;
								$msg['result'] = 'You are not verified by user';			
							}
					}else{
						$msg['return'] = 0;
						$msg['result'] = 'Please enter valid driver code';				
					}					
					
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : truck_code,driver_code';					
				}
				
			}else{
				$msg['return'] = 0;
				$msg['result'] =  "please provide parameter: truck_code";
			}	
			echo json_encode($msg);
		}
		
		
		
		/***************************** update image  ****************************************/
		function update_image($truck_id = '',$image='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				if(!empty($truck_id))
				{
					if(!empty($_FILES['image']['name']))
					{	
						$filename = $_FILES['image']['name'];
						$temp = explode(".", $_FILES['image']['name']);
						$extension = end($temp);
						$UNEEQUE = uniqid();
						$finalimage = $UNEEQUE.'_img.'.$extension;
						$new_dir = dirname(dirname(__FILE__))."/upload/truck_image/".$finalimage;
						move_uploaded_file($_FILES["image"]["tmp_name"], $new_dir);
						$postData['image'] = $finalimage;
					}
					else{
						$postData['image'] = 'dummy11.jpeg';
					}
					// $postData['user_id'] = $user_id;
					$condition = array('truck_id'=> $truck_id);		
					$result = $this->Tran_model->select_single_row_data('truck_list',$condition,0,0);
			
					if(is_array($result) && !empty($result))
					{									
						// $msg['return'] = 1;
						// $msg['result'] = 'Image update successfully';
							
							
						$image_name = $result[0]['image'];
						$path =  dirname(dirname(__FILE__))."/upload/truck_image/".$image_name ;
						if(!empty($path))
						{
							@unlink($path);
						}
						$array_data = array(
											'image'=>$postData['image']
										); 
						$condition = array('truck_id'=> $truck_id);		
						$result_data = $this->Tran_model->update_selected_data('truck_list',$array_data,$condition);
						// $image_name = $postData['image'];
						if($result_data)
						{
							$msg['return'] = 1;
							$msg['result'] = 'Image update successfully';
							$msg['image']   =$postData['image'];							
						}else{
							$msg['return'] = 0;
							$msg['result'] = 'Image update failed';	
						}
					 
					}else{
							$msg['return'] = 0;
							$msg['result'] = 'Image update failed';
							// $msg['data'] = $result;							
					}	
		
				}
				else{
					$msg['return'] = 0;
					$msg['result'] =  "please provide parameter: truck_id,image";
				}
			
			}
			echo json_encode($msg);
		}
		

		/***************************** update image  ****************************************/
		function update_base_image($truck_id = '',$image='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				if(!empty($truck_id))
				{						
					$data = str_replace('data:image/png;base64,', '', $image);
					$data = str_replace(' ', '+', $data);
					$data = base64_decode($data);
					// $file = 'images/'. uniqid() . '.png';
					$new_dir = dirname(dirname(__FILE__))."/upload/truck_image/".uniqid().'png';
					$file = $new_dir;
					$success = file_put_contents($file, $data);
					
					$condition = array('truck_id'=> $truck_id);		
					$result = $this->Tran_model->select_single_row_data('truck_list',$condition,0,0);
			
					if(is_array($result) && !empty($result))
					{									
						// $msg['return'] = 1;
						// $msg['result'] = 'Image update successfully';
							
							
						$image_name = $result[0]['image'];
						$path =  dirname(dirname(__FILE__))."/upload/truck_image/".$image_name ;
						if(!empty($path))
						{
							@unlink($path);
						}
						$array_data = array(
											'image'=>$postData['image']
										); 
						$condition = array('truck_id'=> $truck_id);		
						$result_data = $this->Tran_model->update_selected_data('truck_list',$array_data,$condition);
						// $image_name = $postData['image'];
						if($result_data)
						{
							$msg['return'] = 1;
							$msg['result'] = 'Image update successfully';
							// $msg['isfsdf']   = $image_name;							
						}else{
							$msg['return'] = 0;
							$msg['result'] = 'Image update failed';	
						}
					 
					}else{
							$msg['return'] = 0;
							$msg['result'] = 'Image update failed';
							// $msg['data'] = $result;							
					}	
		
				}
				else{
					$msg['return'] = 0;
					$msg['result'] =  "please provide parameter: user_id,image";
				}
			
			}
			echo json_encode($msg);
		}
		
		/******************************* update profile **********************************/
		function update_profile($user_id = '',$username='',$mobile_number='',$email='',$otp_code='',$company_name='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id))
				{				
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($user_id);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					foreach($_REQUEST as $key=>$value)
					{
						if(!empty($value))
						{
							$postData[$key] = $value;
						}
					}
					
					if(empty($postData[$key]))
					{
						$postData[$key] = '';
					}
				
					$query = $this->db->query('select user_id,email from user where user_id='.$user_id);
					if($query->num_rows()==0)
					{
						$msg['return'] = 0;
						$msg['result'] = 'user id does not exists';
					}else{						
						
						$rs = $query->row_array();
						// echo $rs['email'];
						// exit;
						$query = $this->db->query('select user_id,email from user where user_id not in("'.$user_id.'") and email="'.$postData['email'].'"');
						if($query->num_rows()>0)
						{
							$msg['return'] = 0;
							$msg['result'] = 'email id already exists';
							echo json_encode($msg);exit;							
						}
						
						if($rs['email']==$postData['email'])
						{
						
						
						}else{							 
								
								$this->db->query('update user set email_verification=0 where user_id='.$user_id);
								$config = Array(
										 'protocol' => 'smtp', //sendmail
										 'smtp_host' => 'localhost',									
										 'smtp_port' => 465,// 465 587
										 'smtp_user' => 'support@oonir.com', // change it to yours
										 'smtp_pass' => '4R!Ut-nDFgHI', // change it to yours
										 'mailtype' => 'html',
										 'charset' => 'iso-8859-1',
										 'wordwrap' => TRUE
									); 	
									
								$url = "http://www.oonir.com/ws/api/activate_email?user_id=$user_id&status=1";
								
								$message  = "<body style='background-color: #F1E4E4;padding:15px;border-radius:3px'>";
								$message .= "<p>Hello $username,</p>";
								$message .="<p>To activate your account please click on Activate button</p></br></br>";
								$message .="<table cellspacing='0' cellpadding='0'>";						
								$message .=" <tr><td align='center' width='300' height='40' bgcolor='#000091' style='-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;color: #ffffff; display: block;'>";
								$message .= "<a href='".$url."' style='color: #ffffff; font-size:16px; font-weight: bold; font-family: Helvetica, Arial, sans-serif; text-decoration: none;
								line-height:40px; width:100%; display:inline-block'>Click to Activate</a>";
								$message .= "</td> </tr> </table>";
								$message .= "</br>";
								$message .="<p>Thank you,<br></p>";
								$message .="<p>Oonir Support Team</p>";					
								$message .="<p>Address :</p>";
								$message .="<p>26/3/2555, Sapthagiri colony,<br/> BV Nagar, Mini bypass road,<br/> opp GVRR College Nellore,<br/> Pincode 524001, Contact Number : +91-8612313177<br/><br/></p>";
								$message .="<p>Disclaimer : This is system auto generated email. Please do not reply on this email. If you have any concern then please contact us on helpdesk@oonir.com
								</p>";	
								
								$this->load->library('email', $config);
								$this->email->set_mailtype("html");
								$this->email->set_newline("\r\n");
								$this->email->from('support@oonir.com', "Oonir Support Team");
								$this->email->to($postData['email']);					
								// $this->email->to("riya.sen485@gmail.com");	
								$this->email->subject("Activate Your Account");
								$this->email->message("<p>To activate your account please click on Activate buttton</p>");
								$this->email->message($message);
								   
								if($this->email->send())
								{  
									$msg['return'] = 1;
									$msg['message'] = "Mail sent successfully";   
								}
								else
								{
									$data['message'] = "Sorry Unable to send email"; 
									$msg['return'] = 0;
									$msg['error'] = show_error($this->email->print_debugger());
								}
			
						
						}
						
						$condition = array('user_id'=>$user_id);			
						$result = $this->Tran_model->update_selected_data('user',$postData,$condition);	
						if($result)
						{								
							$result_data = $this->Tran_model->select_single_row_data('user',$condition,0,0);	
							if(is_array($result_data) && !empty($result_data))
							{					
								$msg['return'] 	= 1;
								$msg['result'] 	= 'Updated successfully';				
								$msg['data'] 	= $result_data;						
							}
							
						}else{	
							$msg['return'] = 0;
							$msg['result'] = 'updation failed';
						} 		
					}	
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : user_id,username,mobile_number,email,otp_code';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] = 'parameter missing : user_id,username,mobile_number,email,otp_code';					
			}
			echo json_encode($msg);
		}
		/******************************* update profile **********************************/
		
		/******************************* update password  **********************************/
		function update_password($user_id = '',$password='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id)&&!empty($password))
				{				
					$query = $this->db->query('select user_id from user where user_id='.$user_id);
					if($query->num_rows()==0)
					{
						$msg['return'] = 0;
						$msg['result'] = 'user_id does not exists';
					}else{
						
						$condition = array('user_id'=>$user_id);	
						$postData['password'] = $password;
						$result = $this->Tran_model->update_selected_data('user',$postData,$condition);	
						if($result)
						{					
							$result_data = $this->Tran_model->select_single_row_data('user',$condition,0,0);	
							if(is_array($result_data) && !empty($result_data))
							{					
								$msg['return'] 	= 1;
								$msg['result'] 	= 'Password updated successfully';						
								$msg['data'] 	= $result_data;						
							}
													
						}else{	
							$msg['return'] = 0;
							$msg['result'] = 'updation failed';
						} 		
					}	
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : user_id,password';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] = 'parameter missing : user_id,password';					
			}
			echo json_encode($msg);
		}
		/******************************* update passowrd **********************************/
	
		/************************************** Add Truck *********************************/
		function add_truck($user_id = '',$truck_type='',$capacity='',$model='',$pickup_location='',$dropup_location='',$driver_name='',$contact_number='',$registration_no='',$inbetween_location='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id)&&!empty($capacity)&&!empty($model)&&!empty($truck_type)&&!empty($pickup_location)&&!empty($dropup_location)&&!empty($driver_name)&&!empty($contact_number)&&!empty($registration_no))
				{
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($user_id);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					$postData['user_id'] 			= $user_id;
					$postData['truck_type']			= $truck_type;
					$postData['capacity']			= $capacity;
					$postData['model']				= $model;
					$postData['pickup_location'] 	= $pickup_location;
					$postData['dropup_location'] 	= $dropup_location;
					$postData['driver_name']		= $driver_name;
					$postData['contact_number']		= $contact_number;
					$postData['registration_no'] 	= $registration_no;
					$postData['inbetween_location']	= $inbetween_location;		
					$postData['truck_code']			= 'TK'.$this->Tran_model->random_string();		
					$postData['status']				= '1';					
					$postData['created_date'] 		= date('Y-m-d h:i:s');
					
					// $query = $this->db->query('select * from truck_list where user_id="'.$user_id.'" and registration_no="'.$this->db->escape($registration_no).'"');
					
					// $sql = "select * from truck_list where user_id = ? AND registration_no = ?"; 
					// $query = $this->db->query($sql,array($user_id,$registration_no));
					$condition = array('registration_no'=>$registration_no);
					$query = $this->db->get_where('truck_list',$condition);
					if($query->num_rows()>0)
					{
						$msg['return'] = 0;
						$msg['result'] = 'Truck number already registered';
						echo json_encode($msg);exit;
					}
					
					$result_id = $this->Tran_model->insert_data('truck_list',$postData);
					if($result_id)
					{
						if(!empty($inbetween_location))
						{
							$inbetween_arr = explode('@',$inbetween_location);							
							foreach($inbetween_arr as $value)
							{
								$postData1['truck_id'] 			= $result_id;
								$postData1['between_location'] 	= $value;
								$postData1['status']			= '1';	
								$postData1['created_date'] 		= date('Y-m-d h:i:s');																
								$result_id1 = $this->Tran_model->insert_data('truck_location',$postData1);
							}							
						}	
						$msg['return'] = 1;
						$msg['result'] = 'Truck added successfully';					
					}else{
						$msg['return'] = 0;
						$msg['result'] = 'Insertion failed';					
					}
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : user_id,truck_type,capacity,model,pickup_location,dropup_location,driver_name,contact_number,registration_no,inbetween_location';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] = 'parameter missing : user_id,truck_type,capacity,model,pickup_location,dropup_location,driver_name,contact_number,registration_no,inbetween_location';					
			}
			echo json_encode($msg);
		}
		
		/******************************* Truck list **********************************/
		function truck_list($user_id = '')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id))
				{	
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($user_id);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					// $condition = array('user_id'=> $user_id);		
					// $result_data = $this->Tran_model->select_single_row_data('truck_list',$condition,0,0);	
					$query = $this->db->query('select tl.*,u.user_id as transporter_id,u.username as transporter_name,u.rating from truck_list tl inner join user u on tl.user_id=u.user_id where tl.user_id='.$user_id);
					if($query->num_rows()>0)				
					{					
						$result_data = $query->result_array();
						for($i=0;$i<count($result_data);$i++)
						{
							$truck_id = $result_data[$i]['truck_id'];
							$result_data[$i]['truck_status'] =  $this->Tran_model->get_truck_booking_status($truck_id);							 
						}
						// echo "<pre>";
						// print_r($result_data);exit;
						
						$msg['return'] 	= 1;
						$msg['result'] 	= 'success';
						$msg['data'] 	= $result_data;						
					}else{						
						$msg['return'] = 0;
						$msg['result'] = 'No truck found';						
					} 		
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : user_id';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] =  'please provide parameter: user_id';
			}
			echo json_encode($msg);
		}
		/******************************* Truck list end **********************************/
		
		
		/******************************* update truck **********************************/
		function truck_update($user_id = '',$truck_id='',$truck_type='',$pickup_location='',$dropup_location='',$driver_name='',$contact_number='',$registration_no='',$inbetween_location='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id) && !empty($truck_id))
				{
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($user_id);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					unset($_REQUEST['api/truck_update'] );
					foreach($_REQUEST as $key=>$value)
					{
						$postData[$key] = $value;
						// if(!empty($value))
						// {
							// $postData[$key] = $value;
						// }
						
						
					}
					
					// echo "<pre>";
					// print_r($postData);
					// echo "#################################";
						if(empty($postData[$key]))
						{
							$postData[$key] = '';
						}
				
					$query = $this->db->query('select user_id,truck_id from truck_list where user_id='.$user_id.' and truck_id='.$truck_id.'');
					if($query->num_rows()==0)
					{
						$msg['return'] = 0;
						$msg['result'] = 'user_id or truck_id does not exists';
					}else{
												
						$query = $this->db->query('select registration_no from truck_list where truck_id="'.$truck_id.'" and registration_no="'.$postData['registration_no'].'"');
						if($query->num_rows()>0)
						{
							// $msg['return'] = 0;
							// $msg['result'] = 'Truck number already registered';
							// echo json_encode($msg);exit;
						}else{
							$query1 = $this->db->query('select registration_no from truck_list where registration_no="'.$postData['registration_no'].'"');
							if($query1->num_rows()>0)
							{
								$msg['return'] = 0;
								$msg['result'] = 'Truck number already registered';
								echo json_encode($msg);exit;
							}							
						}
						
						// echo "<pre>";
						// print_r($postData);
						$condition = array('user_id'=>$user_id,'truck_id'=>$truck_id);			
						$result = $this->Tran_model->update_selected_data('truck_list',$postData,$condition);	
						if($result)
						{								
							$result_data = $this->Tran_model->select_single_row_data('truck_list',$condition,0,0);	
							if(is_array($result_data) && !empty($result_data))
							{					
								$msg['return'] 	= 1;
								$msg['result'] 	= 'Truck info updated successfully';				
								$msg['data'] 	= $result_data;						
							}
							
						}else{	
							$msg['return'] = 0;
							$msg['result'] = 'updation failed';
						} 		
					}	
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : user_id,truck_id,truck_type,pickup_location,dropup_location,driver_name,contact_number,registration_no,inbetween_location';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] = 'parameter missing : user_id,truck_id,truck_type,pickup_location,dropup_location,driver_name,contact_number,registration_no,inbetween_location';					
			}
			echo json_encode($msg);
		}
		
		
		
		/******************************* delete car **********************************/
		function delete_truck($truck_id = '')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($truck_id))
				{
				
					/************* check blocked status *************/
					// $block_status = $this->Tran_model->check_block_status($user_id);	
					// if($block_status==0)
					// {						
						// $msg['return'] = 0;
						// $msg['result'] = 'You are blocked user.please contact administrator to login';		
						// echo json_encode($msg);exit;
					// }	
					/************* check blocked status *************/
					
					$query = $this->db->query('select truck_id from truck_list where truck_id='.$truck_id);
					if($query->num_rows()==0)
					{
						$msg['return'] = 0;
						$msg['result'] = 'truck id  does not exist';
					}else{
					
						$condition_array = array('truck_id'=>$truck_id);
						$result_data = $this->Tran_model->delete_data('truck_list',$condition_array);	
						$result_data = $this->Tran_model->delete_data('truck_location',$condition_array);	
						if($result_data)
						{
							$msg['return'] = 1;
							$msg['result'] = 'success';
							//$msg['data'] = $result_data;	
						}else{	
							$msg['return'] = 0;
							$msg['result'] = 'failed to delete truck';
						} 		
					}	
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : truck_id';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] =  "please provide parameter: truck_id";
			}
			echo json_encode($msg);
		}
		
		
		/******************************* Get load type **********************************/
		function get_load_type()
		{			
			$query = $this->db->query('select load_type from load_type');
			if($query->num_rows()>0)
			{
				$result_data = $query->result_array();
				$msg['return'] = 1;
				$msg['result'] = 'success';
				$msg['data'] = $result_data;
			}else{
				$msg['return'] = 0;
				$msg['result'] = 'No data found';
						
			}				
			
			echo json_encode($msg);
		}
		/******************************* Get load type **********************************/
		
		
		/******************************* Get truck type **********************************/
		function get_truck_type()
		{			
			$query = $this->db->query('select truck_type from truck_type order by truck_type');
			$query1 = $this->db->query('select load_type from load_type order by load_type');
			if($query->num_rows()>0)
			{
				$truck_type = $query->result_array();
				$load_type = $query1->result_array();
				
				$msg['return'] = 1;
				$msg['result'] = 'success';
				$msg['truck_type'] = $truck_type;
				$msg['load_type']  = $load_type;
			}else{
				$msg['return'] = 0;
				$msg['result'] = 'No data found';
						
			}				
			
			echo json_encode($msg);
		}
		/******************************* Get load type **********************************/
	
		/******************************* Get customer notification **********************************/
		function get_customer_notification($user_id='')
		{			
			
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id))
				{
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($user_id);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					$this->db->query('update user set badge=0 where user_id='.$user_id);
					// $query = $this->db->query('select message,DATE_FORMAT(created_date,"%d-%m-%Y %h:%i:%s %p") as created_date from truck_notification where user_id="'.$user_id.'" and type="customer" order by created_date desc');
					
					$query = $this->db->query('select * from truck_notification where user_id="'.$user_id.'" and type="customer" order by created_date desc');
					
					
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();
						for($i=0;$i<count($result_data);$i++)
						{
							$create_date = $result_data[$i]['created_date'];
							$result_data[$i]['created_date'] = date("d-m-Y h:i:s A",strtotime($create_date));
							
						}
						$msg['return'] = 1;
						$msg['result'] = 'success';
						$msg['data'] = $result_data;
					}else{
						$msg['return'] = 0;
						$msg['result'] = 'No data found';
								
					}				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id";
							
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id";
						
			}	
			echo json_encode($msg);
		}
		/******************************* Get customer notification **********************************/
			
		 
		/******************************* Get transporter notification **********************************/
		function get_transporter_notification($user_id='')
		{			
			
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id))
				{
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($user_id);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					$this->db->query('update user set badge=0 where user_id='.$user_id);
					$query = $this->db->query('select * from truck_notification where user_id="'.$user_id.'" and type="transporter" order by created_date desc');
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();
						for($i=0;$i<count($result_data);$i++)
						{
							$create_date = $result_data[$i]['created_date'];
							$result_data[$i]['created_date'] = date("d-m-Y h:i:s A",strtotime($create_date));
							
						}
						$msg['return'] = 1;
						$msg['result'] = 'success';
						$msg['data'] = $result_data;
					}else{
						$msg['return'] = 0;
						$msg['result'] = 'No data found';
								
					}				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id";
							
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id";
						
			}	
			echo json_encode($msg);
		}
		/******************************* Get transporter notification **********************************/
			
		function search_truck($user_id = '',$pickup_location='',$dropup_location='',$pickup_date='',$capacity='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($pickup_location)&& !empty($dropup_location)&& !empty($pickup_date)&& !empty($capacity))
				{					
					
					
					
					$query = $this->db->query('select truck_id from truck_list  where capacity >="'.$capacity.'"  and status=1 and  
					((pickup_location="'.$pickup_location.'"  and dropup_location="'.$dropup_location.'") OR 
					(pickup_location="'.$pickup_location.'" and inbetween_location like "%'.$dropup_location.'%" ) OR 
					(inbetween_location like "%'.$pickup_location.'%" and dropup_location="'.$dropup_location.'" ) OR 
					(inbetween_location like "%'.$pickup_location.'%" and inbetween_location like "%'.$dropup_location.'%" )
					OR (pickup_location="'.$dropup_location.'"  and dropup_location="'.$pickup_location.'"))
						');
						
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();
						// echo "<pre>";
						// print_r($result_data);
						for($i=0;$i<count($result_data);$i++)
						{							
							$truck_array1[]  = $result_data[$i]['truck_id'];							
							$truck_id  = $result_data[$i]['truck_id'];							
							$query1 = $this->db->query('select truck_id from truck_shipment where pickup_location="'.$pickup_location.'"  and dropup_location="'.$dropup_location.'" and truck_id="'.$truck_id.'" and sharing=2 and pickup_date="'.$pickup_date.'"');
							if($query1->num_rows()>0)
							{
								$result_data2   = $query1->result_array();
								for($j=0;$j<count($result_data2);$j++)
								{
									$truck_array2[] = $result_data2[$j]['truck_id'];
								}
							}							
						}
						
						// echo "----------Shared disable------------";
						// echo "<pre>";
						// print_r($truck_array2);
						
						// echo "----------Total------------";						
						// echo "<pre>";
						// print_r($truck_array1);
						
						// echo "----------Final------------";	
						
						if(!empty($truck_array1) && !empty($truck_array2))
						{
							$truck_array3 = array_diff($truck_array1,$truck_array2);		
						}	
						if(!empty($truck_array1) && empty($truck_array2))
						{
							$truck_array3 = $truck_array1;
						}
						if(empty($truck_array1) && !empty($truck_array2))
						{
							$truck_array3 = $truck_array2;		
						}
						// echo "<pre>";
						// print_r($truck_array3);
						foreach($truck_array3 as $truck_id)
						{
							
							$query = $this->db->query('select tl.* ,u.username as transporter_name ,u.user_id as transporter_id, if(u.rating=0,"-1",u.rating) as rating from truck_list tl INNER JOIN user u on tl.user_id=u.user_id where tl.truck_id ="'.$truck_id.'" ');
							if($query->num_rows()>0)
							{
								$resultant[] = $query->row_array();
								 
							}
						}
						// echo "Final------------ Result -----";
						// echo "<pre>";
						// print_r($resultant);
						
						
						if(isset($resultant))
						{
							$msg['return'] = 1;
							$msg['result'] = "success";
							$msg['data']   = $resultant;
						}else{
							// $msg['return'] = 1;
							// $msg['result'] = "success";
							// $msg['data']   = '';
							$msg['return'] = 0;
						$msg['result'] = "We currently do not have any trucks available for this route. Shipment will be posted on market place and you will receive notifications when transporter(s) accepts offer. For any enquiries please contact customer service <phone number>";
						}	
						
					}else{
						$msg['return'] = 0;
						$msg['result'] = "We currently do not have any trucks available for this route. Shipment will be posted on market place and you will receive notifications when transporter(s) accepts offer. For any enquiries please contact customer service <+91 8612313177>"; 
					}
				}
				else
				{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id,pickup_location,dropup_location,pickup_date,capacity";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id,pickup_location,dropup_location,pickup_date,capacity";
			}
			echo json_encode($msg);
		}	
		
		/*************************************************  search truck new screen *******************************************/	
		function search_truck_new($pickup_location='',$dropup_location='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($pickup_location)&& !empty($dropup_location))
				{					
					$query = $this->db->query('select truck_id,truck_type,capacity,model,pickup_location,dropup_location from truck_list  where   
							((pickup_location="'.$pickup_location.'"  and dropup_location="'.$dropup_location.'") OR 
							(pickup_location="'.$pickup_location.'" and inbetween_location like "%'.$dropup_location.'%" ) OR 
							(inbetween_location like "%'.$pickup_location.'%" and dropup_location="'.$dropup_location.'" ) OR 
							(inbetween_location like "%'.$pickup_location.'%" and inbetween_location like "%'.$dropup_location.'%" ) OR (pickup_location="'.$dropup_location.'"  and dropup_location="'.$pickup_location.'"))
							');
						
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();						
						$msg['return'] = 1;
						$msg['result'] = "success";
						$msg['data']   = $result_data;
						
					}else{
						$msg['return'] = 0;
						$msg['result'] = "No truck found";
					}
				}
				
				if(!empty($pickup_location) && empty($dropup_location))
				{					
					$query = $this->db->query('select truck_id,truck_type,capacity,model,pickup_location,dropup_location from truck_list  where  pickup_location="'.$pickup_location.'" ');
						
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();						
						$msg['return'] = 1;
						$msg['result'] = "success";
						$msg['data']   = $result_data;
						
					}else{
						$msg['return'] = 0;
						$msg['result'] = "No truck found";
					}
				}
				
				if(!empty($dropup_location) && empty($pickup_location))
				{					
					$query = $this->db->query('select truck_id,truck_type,capacity,model,pickup_location,dropup_location from truck_list  where  dropup_location="'.$dropup_location.'" ');
						
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();						
						$msg['return'] = 1;
						$msg['result'] = "success";
						$msg['data']   = $result_data;
						
					}else{
						$msg['return'] = 0;
						$msg['result'] = "No truck found";
					}
				}
				
				if(empty($dropup_location) && empty($pickup_location))
				{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: pickup_location,dropup_location";
				}
				
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: pickup_location,dropup_location";
			}
			echo json_encode($msg);
		}	
		/********************  search truck new screen ********************************/	
		
		
		
		/*************************************************  search transporter side *******************************************/	
		function search_truck_transporter($user_id = '',$pickup_location='',$dropup_location='',$pickup_date='',$capacity='',$type='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id) && !empty($pickup_location)&& !empty($dropup_location)&& !empty($pickup_date)&& !empty($capacity)&& !empty($type))
				{					
					if($type==1) // all trucks list
					{
						$query = $this->db->query('select truck_id from truck_list  where user_id='.$user_id);						
					}
					
					if($type==2) // match trucks lsit
					{
						$query = $this->db->query('select truck_id from truck_list  where capacity >="'.$capacity.'" and user_id="'.$user_id.'" and  
						((pickup_location="'.$pickup_location.'"  and dropup_location="'.$dropup_location.'") OR 
						(pickup_location="'.$pickup_location.'" and inbetween_location like "%'.$dropup_location.'%" ) OR 
						(inbetween_location like "%'.$pickup_location.'%" and dropup_location="'.$dropup_location.'" ) OR 
						(inbetween_location like "%'.$pickup_location.'%" and inbetween_location like "%'.$dropup_location.'%" ))
							');
					}
					
						
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();
						// echo "<pre>";
						// print_r($result_data);
						for($i=0;$i<count($result_data);$i++)
						{						
							$truck_array1[]  = $result_data[$i]['truck_id'];
							
							$truck_id  = $result_data[$i]['truck_id'];							
							$query1 = $this->db->query('select truck_id from truck_shipment where pickup_location="'.$pickup_location.'"  and dropup_location="'.$dropup_location.'" and truck_id="'.$truck_id.'" and sharing=2 and pickup_date="'.$pickup_date.'"');
							if($query1->num_rows()>0)
							{
								$result_data2   = $query1->result_array();
								for($j=0;$j<count($result_data2);$j++)
								{
									$truck_array2[] = $result_data2[$j]['truck_id'];
								}
							}							
						}
						
						// echo "----------Shared disable------------";
						// echo "<pre>";
						// print_r($truck_array2);
						
						// echo "----------Total------------";						
						// echo "<pre>";
						// print_r($truck_array1);
						
						// echo "----------Final------------";	
						
						if(!empty($truck_array1) && !empty($truck_array2))
						{
							$truck_array3 = array_diff($truck_array1,$truck_array2);		
						}	
						if(!empty($truck_array1) && empty($truck_array2))
						{
							$truck_array3 = $truck_array1;
						}
						if(empty($truck_array1) && !empty($truck_array2))
						{
							$truck_array3 = $truck_array2;		
						}
						// echo "<pre>";
						// print_r($truck_array3);
						foreach($truck_array3 as $truck_id)
						{
							
							$query = $this->db->query('select tl.* ,u.username as transporter_name ,u.user_id as transporter_id, u.rating from truck_list tl INNER JOIN user u on tl.user_id=u.user_id where tl.truck_id ="'.$truck_id.'" ');
							if($query->num_rows()>0)
							{
								$resultant[] = $query->row_array();
								 
							}
						}
						// echo "Final------------ Result -----";
						// echo "<pre>";
						// print_r($resultant);
						
						
						
						$msg['return'] = 1;
						$msg['result'] = "success";
						$msg['data']   = $resultant;
						
					}else{
						$msg['return'] = 0;
						$msg['result'] = "No truck found";
					}
				}
				else
				{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id,pickup_location,dropup_location,pickup_date,capacity";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id,pickup_location,dropup_location,pickup_date,capacity";
			}
			echo json_encode($msg);
		}
		
		
		/******************************* Post Offer shipment **********************
			
			1=>bid,2=>offer,3=>readytodispatch,4=>dispatched,5=>cancel,6=>delivered
			
		**************************************************************************/
		
		function post_shipment_offer($user_id = '',$truck_id='',$transporter_id='',$pickup_location='',$pickup_city='',$dropup_location='',$dropup_city='',$pickup_date='',$capacity='',$load_type='',$material_type='',$tracking='',$sharing='',$distance='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id) && !empty($pickup_location)&& !empty($dropup_location)&& !empty($pickup_date)&& !empty($capacity))
				{	
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($user_id);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					$location = $this->Tran_model->get_lat_long($pickup_location);										
					$status     = $location['status'];
					$platitude  = $location['lat'];					
					$plongitude = $location['long'];
					if($status=='-1')
					{
						$msg['return'] 	=  0;
						$msg['result'] 	=  "Shipment location not found";
						echo json_encode($msg);exit;
					}
					
					$dlocation   = $this->Tran_model->get_lat_long($dropup_location);										
					$dstatus     = $dlocation['status'];
					$dlatitude   = $dlocation['lat'];					
					$dlongitude  = $dlocation['long'];
					if($dstatus=='-1')
					{
						$msg['return'] 	=  0;
						$msg['result'] 	=  "Shipment location not found";
						echo json_encode($msg);exit;
					}
					
					
					
					/****** get source&desti city lattitude nad longitude ************/
					
					$pkuplocation 	= $this->Tran_model->get_lat_long($pickup_city);										
					$pkupstatus     = $pkuplocation['status'];
					$pkuplatitude  	= $pkuplocation['lat'];					
					$pkuplongitude  = $pkuplocation['long'];
					if($status=='-1')
					{							
						$pkuplatitude  	= 'NA';					
						$pkuplongitude  = 'NA';
					}
					
					$drpuplocation  = $this->Tran_model->get_lat_long($dropup_city);										
					$drupstatus     = $drpuplocation['status'];
					$drpuplatitude	= $drpuplocation['lat'];					
					$druplongitude  = $drpuplocation['long'];
					if($dstatus=='-1')
					{
						$drpuplatitude 	= 'NA';					
						$druplongitude  = 'NA';
					}
					
					/*******get source&desti lattitude and longitude ************/
					
					
					$postData['user_id']			= $user_id;		
					$postData['pickup_location']	= $pickup_location;		
					$postData['dropup_location']	= $dropup_location;	
					$postData['pickup_city']		= $pickup_city;		
					$postData['dropup_city']		= $dropup_city;	
					$postData['pickup_lat_long']    = $pkuplatitude.','.$pkuplongitude;						
					$postData['dropup_lat_long']    = $drpuplatitude.','.$druplongitude;
					$postData['platitude']			= $platitude;	
					$postData['plongitude']			= $plongitude;
					$postData['dlatitude']			= $dlatitude;	
					$postData['dlongitude']			= $dlongitude;						
					$postData['pickup_date']		= $pickup_date;	
					$postData['capacity']			= $capacity;	
					$postData['load_type']			= $load_type;	
					$postData['material_type']		= $material_type;
					$postData['tracking']			= $tracking;		
					$postData['sharing']			= $sharing;	
					$postData['offered_price']		= $offered_price;		
					$postData['distance']			= $distance;						
					$postData['shipment_type']		= 2;						
					$postData['dispatch_status']	= 2;
					$postData['status']				= 1;
					$postData['created_date'] 		= date('Y-m-d h:i:s');
					
					if($select_truck=="1") // ********* means truck selected**********
					{
						// $query = $this->db->query('select * from truck_shipment where pickup_location="'.$pickup_location.'" and dropup_location="'.$dropup_location.'" and pickup_date="'.$pickup_date.'" and capacity="'.$capacity.'" and shipment_type=2 and load_type="'.$load_type.'" ');
						// if($query->num_rows()>0)
						// {
							// $msg['return'] 	=  0;
							// $msg['result'] 	=  "You have already posted a shipment with same details";
							// echo json_encode($msg);exit;
						// }
						// else
						// {
							$postData['offer_requested'] = 1;
							$result_id = $this->Tran_model->insert_data('truck_shipment',$postData);
							if($result_id)
							{
								if(!empty($truck_id)&&!empty($transporter_id))
								{
									$truck_id 		= explode(',',$truck_id);
									$transporter_id = explode(',',$transporter_id);
									$i=0;
									foreach($truck_id as $truckid)
									{
										$transporterid 					= $transporter_id[$i];								
										$postData1['shipment_id']		= $result_id;		
										$postData1['customer_id']		= $user_id;					
										$postData1['transporter_id']	= $transporterid;
										$postData1['truck_id']			= $truckid;	
										$postData1['shipment_type']		= 2;
										$postData1['request_status']	= 0;//0							
										$postData1['status']			= 1;
										$postData1['created_date'] 		= date('Y-m-d h:i:s');
										
										$result_id1 = $this->Tran_model->insert_data('truck_request',$postData1);
										
										/************* Send notificaion to transporter ************/
										$condition1 = array('user_id'=>$transporterid);
										$device_token1 = $this->Tran_model->get_device_token('device_token','user',$condition1);
										$device_token1 = $device_token1[0]['device_token']; 
										
										$condition 	= array('user_id'=>$user_id);
										$customer_name 	= $this->Tran_model->get_username('username','user',$condition);
										$customer_name  = $customer_name[0]['username']; 
										
										$truck_reg 	= $this->Tran_model->get_truck_num($truckid);
										
										
										$msgsender_id1['user_id'] 	 = $transporterid;	
										//$message1 = 'New shipment offer posted by customer';	
										// $message1 = "$customer_name has send a request of shipment (SHP-$result_id)  for a location $pickup_location to $dropup_location";
										
										$message1 = "$customer_name has selected your truck ($truck_reg) for a location $pickup_location to $dropup_location of shipment (SHP-$result_id)";										
										
										$data1 = array(
													'sound'		=>1,
													'message'	=>$message1,
													'notifykey'	=>'shipment_posted_offer',
													'shipment_id'=>$result_id,	
													'data'		=>$msgsender_id1		
												);
										$this->Tran_model->update_badge($transporterid);		
										if(!empty($device_token1))
										{
											$this->Tran_model->send_android_notification($device_token1,$data1);		
										}
										$insertdata1['user_id'] 	 = $transporterid;
										$insertdata1['message'] 	 = $message1;							
										$insertdata1['type']    	 = 'transporter';
										$insertdata1['shipment_id']  =  $result_id;
										$insertdata1['truck_id']     =  $truck_reg;
										$insertdata1['status'] 		 =  2 ;
										$insertdata1['created_date'] = date('Y-m-d H:i:s');			
										$this->Tran_model->insert_data('truck_notification',$insertdata1);
										// $this->db->query('update truck_notification set status=2 where shipment_id='.$result_id);									
									
										/************* Send notificaion to transporter ************/
										
										$i++;
									}
									$msg['return'] 	=  1;
									$msg['result'] 	=  "Thank you for sending request to selected truck(s). You will receive notifications when transporter(s) accept or reject offer.";									
								}else{
									$msg['return'] 	=  1;
									$msg['result'] 	=  "Thank you for sending request to selected truck(s). You will receive notifications when transporter(s) accept or reject offer.";	
								}	
							}else{							
									$msg['return'] 	=  0;
									$msg['result'] 	=  "Shipment booking failed";
							}
						// }	
					}
					
					else if($select_truck=="2") // ********Truck not selected**********
					{						
						// $query = $this->db->query('select * from truck_shipment where pickup_location="'.$pickup_location.'" and dropup_location="'.$dropup_location.'" and pickup_date="'.$pickup_date.'" and capacity="'.$capacity.'" and shipment_type=2 and load_type="'.$load_type.'"');
						// if($query->num_rows()>0)
						// {
							// $msg['return'] 	=  0;						
							// $msg['result'] 	=  "You have already posted a shipment with same details";
							// echo json_encode($msg);exit;
						// }else{
							$result_id = $this->Tran_model->insert_data('truck_shipment',$postData);						
							if($result_id)
							{
								$msg['return'] 	=  1;
								$msg['result'] 	=  "Thank you for posting shipment in our market place. You will receive notifications when transporter(s) accepts offer";
							}else{
								$msg['return'] 	=  0;
								$msg['result'] 	=  "Shipment booking failed";	
							}
						// }	
					}					
				
				}else{
					
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id,pickup_location,dropup_location,pickup_date,capacity,tracking,load_type,select_truck,transporter_id,truck_id,offered_price,sharing,pickup_city,dropup_city";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id,pickup_location,dropup_location,pickup_date,capacity,tracking,load_type,select_truck,transporter_id,truck_id,offered_price,sharing,pickup_city,dropup_city";
			}
			echo json_encode($msg);
		}
		/******************************* Post offer shipment **********************************/
		
		
		/******************************* Post Bid **********************************/
		function post_shipment_bid($user_id = '',$pickup_location='',$dropup_location='',$pickup_city='',$dropup_city='',$pickup_date='',$capacity='',$load_type='',$material_type='',$tracking='',$sharing='',$shipment_type='',$distance='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id) && !empty($pickup_location)&& !empty($dropup_location)&& !empty($pickup_date)&& !empty($capacity))
				{					
					
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($user_id);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					
					$location = $this->Tran_model->get_lat_long($pickup_location);										
					$status     = $location['status'];
					$platitude  = $location['lat'];					
					$plongitude = $location['long'];
					if($status=='-1')
					{							
						$msg['return'] 	=  0;
						$msg['result'] 	=  "Shipment location not found";
						echo json_encode($msg);exit;
					}
					
					$dlocation   = $this->Tran_model->get_lat_long($dropup_location);										
					$dstatus     = $dlocation['status'];
					$dlatitude   = $dlocation['lat'];					
					$dlongitude  = $dlocation['long'];
					if($dstatus=='-1')
					{
						$msg['return'] 	=  0;
						$msg['result'] 	=  "Shipment location not found";
						echo json_encode($msg);exit;
					}
					
					/****** get source&desti city lattitude nad longitude ************/
					
					$pkuplocation 	= $this->Tran_model->get_lat_long($pickup_city);										
					$pkupstatus     = $pkuplocation['status'];
					$pkuplatitude  	= $pkuplocation['lat'];					
					$pkuplongitude  = $pkuplocation['long'];
					if($status=='-1')
					{							
						$pkuplatitude  	= 'NA';					
						$pkuplongitude  = 'NA';
					}
					
					$drpuplocation  = $this->Tran_model->get_lat_long($dropup_city);										
					$drupstatus     = $drpuplocation['status'];
					$drpuplatitude	= $drpuplocation['lat'];					
					$druplongitude  = $drpuplocation['long'];
					if($dstatus=='-1')
					{
						$drpuplatitude 	= 'NA';					
						$druplongitude  = 'NA';
					}
					
					/*******get source&desti lattitude and longitude ************/
					
					
					$postData['user_id']			= $user_id;		
					$postData['pickup_location']	= $pickup_location;		
					$postData['dropup_location']	= $dropup_location;	
					$postData['pickup_city']		= $pickup_city;		
					$postData['dropup_city']		= $dropup_city;	
					$postData['platitude']			= $platitude;	
					$postData['plongitude']			= $plongitude;
					$postData['pickup_lat_long']    = $pkuplatitude.','.$pkuplongitude;						
					$postData['dropup_lat_long']    = $drpuplatitude.','.$druplongitude;						
					$postData['dlatitude']			= $dlatitude;	
					$postData['dlongitude']			= $dlongitude;	
					$postData['pickup_date']		= $pickup_date;	
					$postData['capacity']			= $capacity;	
					$postData['load_type']			= $load_type;	
					$postData['material_type']		= $material_type;
					$postData['tracking']			= $tracking;		
					$postData['sharing']			= $sharing;					
					$postData['distance']			= $distance;					
					$postData['shipment_type']		= 1;
					$postData['dispatch_status']	= 1;
					$postData['status']				= 1;
					$postData['created_date'] 		= date('Y-m-d h:i:s');
					
					// $query = $this->db->query('select * from truck_shipment where pickup_location="'.$pickup_location.'" and dropup_location="'.$dropup_location.'" and pickup_date="'.$pickup_date.'" and capacity="'.$capacity.'" and shipment_type=1 and load_type="'.$load_type.'"');
					// if($query->num_rows()>0)
					// {
						// $msg['return'] 	=  0;						
						// $msg['result'] 	=  "You have already posted a shipment with same details";
						// echo json_encode($msg);exit;
					// }
					$result_id = $this->Tran_model->insert_data('truck_shipment',$postData);
					
					if($result_id)
					{
						$msg['return'] 	=  1;
						$msg['result'] 	=  "Thank you for posting shipment in our market place. You will receive notifications when transporter(s) submit bids";
					}else{
						$msg['return'] 	=  0;
						$msg['result'] 	=  "Shipment booking failed";	
					}
						
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id,pickup_location,dropup_location,pickup_date,capacity,load_type,tracking,sharing,pickup_city,dropup_city";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id,pickup_location,dropup_location,pickup_date,capacity,load_type,tracking,sharing,pickup_city,dropup_city";
			}
			echo json_encode($msg);
		}
		/******************************* Post Bid **********************************/
		
		
		/******************************* My Shipment(Customer) **********************************/
		function shipment_customer($user_id = '')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id))
				{					
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($user_id);	
					if($block_status==0)
					{	
						// echo $block_status;
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					$query = $this->db->query('select COALESCE(tr.tkreq_id,"") as tkreq_id,COALESCE(tr.request_status,"") as request_status, COALESCE(tr.startup_status,"") as startup_status  ,COALESCE(tr.truck_id,"") as truck_id,COALESCE(tr.bid_value,"") as bid_value,ts.dispatch_status,ts.shipment_id,ts.user_id,ts.offer_requested,ts.pickup_location,ts.dropup_location,ts.pickup_city,ts.dropup_city,ts.pickup_date as pd,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.load_type,ts.material_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance,ts.created_date,COALESCE(u.username,"") as transporter_name,COALESCE(u.rating,"") as rating ,COALESCE(u.mobile_number,"") as mobile_number,COALESCE(u.user_id,"") as transporter_id,COALESCE(u.company_name,"") as transporter_company,COALESCE(tl.driver_name,"") as driver_name,COALESCE(tl.contact_number,"") as contact_number,COALESCE(tl.registration_no,"") as registration_no,ts.platitude,ts.plongitude,ts.dlatitude,ts.dlongitude,ts.pickup_lat_long,ts.dropup_lat_long from truck_shipment ts LEFT JOIN truck_request tr on  ts.shipment_id=tr.shipment_id  and ts.user_id=tr.customer_id and tr.request_status!=5 and tr.request_status!=6 LEFT JOIN  user u  on u.user_id=tr.transporter_id LEFT JOIN truck_list tl on tr.truck_id=tl.truck_id  where (ts.user_id="'.$user_id.'" and ts.dispatch_status!=5) AND (ts.user_id="'.$user_id.'" and ts.dispatch_status!=6) group by ts.shipment_id order by ts.pickup_date');
					
					if($query->num_rows()>0)
					{
						$result_data 	= $query->result_array();
						for($i=0;$i<count($result_data);$i++)
						{
							$shipment_id = $result_data[$i]['shipment_id'];
							$shipment_id = $result_data[$i]['shipment_id'];
							
							$count = $this->Tran_model->get_applied_transporter_count($user_id,$shipment_id);
							$time_status = $this->Tran_model->get_booking_cancel_before_time($shipment_id);
							
							$user_id2 = $this->Tran_model->get_user_detail($user_id);						
							// $result_data[$i]['mobile_number'] =  $user_id2[0]['mobile_number'];
							// $result_data[$i]['username'] =  $user_id2[0]['customer_name'];	
							$result_data[$i]['customer_company'] =  isset($user_id2[0]['company_name']) ? $user_id2[0]['company_name'] : "";	
							
							$result_data[$i]['count'] = $count  ;
							$result_data[$i]['cancel_status'] = $time_status  ;
						}
						
						// echo "<pre>";
						// print_r($result_data);
						
						
						foreach($result_data as $subKey => $subArray)
						{
							  if(($subArray['dispatch_status'] == 1 && strtotime($subArray['pd']) < strtotime(date('Y-m-d')) ) ||  ( $subArray['dispatch_status'] == 2 && strtotime($subArray['pd']) < strtotime(date('Y-m-d'))  ))
							  
							  // if(($subArray['dispatch_status'] == 1  ||   $subArray['dispatch_status'] == 2))
							  {
							    unset($result_data[$subKey]);
								$newarray = array_values($result_data);
							  }else{
								$newarray = array_values($result_data);
							  }
						}
						// echo "********************************************";
						// echo "<pre>";
						// print_r($newarray);
						// exit;
						$msg['return'] 	=  1;
						$msg['result'] 	=  "success";
						$msg['data'] 	=  $newarray;										
					}					
					else{
						$msg['return'] = 0;
						$msg['result'] = "No shipment available";
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id";
			}
			echo json_encode($msg);
		}
		/*******************************  My Shipment(Customer) **********************************/
		
		/******************************* Detail Shipment(Customer) **********************************/
		function shipment_detail($user_id = '',$shipment_id='',$shipment_type='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id)&&!empty($shipment_id)&&!empty($shipment_type))
				{					
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($user_id);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					
					if($shipment_type==1) // Bid
					{
						// echo 'select tr.tkreq_id,tr.request_status ,tr.truck_id,tr.bid_value,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.pickup_date,ts.load_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,tl.driver_name,tl.contact_number,tl.registration_no ,u.username as transporter_name from truck_request tr inner join truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id and u.user_id = tr.transporter_id where tr.customer_id="'.$user_id.'" and tr.shipment_id="'.$shipment_id.'" and tr.request_status="2" and tr.shipment_type="2"  order by tr.bid_value asc';
						$query = $this->db->query('select tr.tkreq_id,tr.request_status ,tr.truck_id,tr.bid_value,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.dropup_city,ts.pickup_city,ts.platitude,ts.plongitude,ts.dlatitude,ts.dlongitude,DATE_FORMAT(ts.pickup_date,"%d-%m-%Y") as pickup_date,ts.load_type,ts.material_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance,u.user_id as transporter_id,u.username as transporter_name,if(u.rating=0,"-1",u.rating) as rating,u.mobile_number from truck_request tr inner join truck_shipment ts INNER JOIN user u on ts.shipment_id=tr.shipment_id and u.user_id = tr.transporter_id where tr.customer_id="'.$user_id.'" and tr.shipment_id="'.$shipment_id.'" and tr.request_status="1" and tr.shipment_type="1"  order by tr.bid_value asc');						
						if($query->num_rows()>0)
						{
							$result_data = $query->result_array();					
							$msg['return'] 	=  1;
							$msg['result'] 	=  "success";
							$msg['data'] 	=  $result_data;					
						}					
						else{
							$msg['return'] = 0;
							$msg['result'] = "No transporter applied for this Bid";
						}
						// $query->free_result();
					}
					if($shipment_type==2) //offer
					{
						$query = $this->db->query('select tr.tkreq_id,tr.request_status,tr.truck_id,tr.bid_value,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.dropup_city,ts.pickup_city,ts.platitude,ts.plongitude,ts.dlatitude,ts.dlongitude,DATE_FORMAT(ts.pickup_date,"%d-%m-%Y") as pickup_date,ts.load_type,ts.material_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance,tl.driver_name,tl.contact_number,tl.registration_no ,u.username as transporter_name,u.user_id as transporter_id,if(u.rating=0,"-1",u.rating) as rating,u.mobile_number  from truck_request tr inner join truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id and u.user_id = tr.transporter_id where (tr.customer_id="'.$user_id.'" and tr.shipment_id="'.$shipment_id.'" and tr.shipment_type=2 and tr.request_status=1) 
							     OR  (tr.customer_id="'.$user_id.'" and tr.shipment_id="'.$shipment_id.'" and tr.shipment_type=2 and tr.request_status=0) ');
						if($query->num_rows()>0)
						{
							$result_data = $query->result_array();					
							$msg['return'] 	=  1;
							$msg['result'] 	=  "success";
							$msg['data'] 	=  $result_data;					
						}					
						else{
							$msg['return'] = 0;
							$msg['result'] = "No transporter accepted this Offer";
						}
						// $query->free_result();
					}
					
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id,shipment_id,shipment_type";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id,shipment_id,shipment_type";
			}
			echo json_encode($msg);
		}
		/*******************************  Detail Shipment(Customer) **********************************/
		
		
		
		/******************************* Detail Shipment(Transporter Side) **********************************/
		function shipment_detail_transporter($shipment_id='',$shipment_type='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($shipment_id)&&!empty($shipment_type))
				{					
					// $query = $this->db->query('select tr.tkreq_id,tr.request_status ,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.pickup_date,ts.load_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price from truck_request tr inner join truck_shipment ts INNER JOIN truck_list tl on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id where tr.customer_id="'.$user_id.'" ');
					if($shipment_type==1) // Bid
					{
						// echo 'select tr.tkreq_id,tr.request_status ,tr.truck_id,tr.bid_value,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.pickup_date,ts.load_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,tl.driver_name,tl.contact_number,tl.registration_no ,u.username as transporter_name from truck_request tr inner join truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id and u.user_id = tr.transporter_id where tr.customer_id="'.$user_id.'" and tr.shipment_id="'.$shipment_id.'" and tr.request_status="2" and tr.shipment_type="2"  order by tr.bid_value asc';
						$query = $this->db->query('select tr.tkreq_id,tr.request_status ,tr.shipment_type,tr.truck_id,tr.bid_value,ts.shipment_id,ts.offered_price,u.user_id as transporter_id,u.username as transporter_name,if(u.rating=0,"-1",u.rating) as rating,u.mobile_number from truck_request tr inner join truck_shipment ts INNER JOIN user u on ts.shipment_id=tr.shipment_id and u.user_id = tr.transporter_id where  tr.shipment_id="'.$shipment_id.'" and tr.request_status="1" and tr.shipment_type="1"  order by tr.bid_value asc');						
						if($query->num_rows()>0)
						{
							$result_data = $query->result_array();					
							$msg['return'] 	=  1;
							$msg['result'] 	=  "success";
							$msg['data'] 	=  $result_data;					
						}					
						else{
							$msg['return'] = 0;
							$msg['result'] = "No transporter applied for this Bid";
						}
						// $query->free_result();
					}
					if($shipment_type==2) //offer
					{
						// $query = $this->db->query('select tr.tkreq_id,tr.request_status,tr.truck_id,tr.bid_value,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.dropup_city,ts.pickup_city,ts.platitude,ts.plongitude,ts.dlatitude,ts.dlongitude,ts.pickup_date,ts.load_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,tl.driver_name,tl.contact_number,tl.registration_no ,u.username as transporter_name,u.user_id as transporter_id,u.rating,u.mobile_number  from truck_request tr inner join truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id and u.user_id = tr.transporter_id where (tr.customer_id="'.$user_id.'" and tr.shipment_id="'.$shipment_id.'" and tr.shipment_type=2 and tr.request_status=1) 
							     // OR  (tr.customer_id="'.$user_id.'" and tr.shipment_id="'.$shipment_id.'" and tr.shipment_type=2 and tr.request_status=0) ');
						$query = $this->db->query('select tr.tkreq_id,tr.request_status ,tr.shipment_type,tr.truck_id,tr.bid_value,ts.shipment_id,ts.offered_price,u.user_id as transporter_id,u.username as transporter_name,u.rating,u.mobile_number from truck_request tr inner join truck_shipment ts INNER JOIN user u on ts.shipment_id=tr.shipment_id and u.user_id = tr.transporter_id where  tr.shipment_id="'.$shipment_id.'" and tr.request_status="1" and tr.shipment_type="2"  order by tr.bid_value asc');						
						
						if($query->num_rows()>0)
						{
							$result_data = $query->result_array();					
							$msg['return'] 	=  1;
							$msg['result'] 	=  "success";
							$msg['data'] 	=  $result_data;					
						}					
						else{
							$msg['return'] = 0;
							$msg['result'] = "No transporter accepted this Offer";
						}
						// $query->free_result();
					}
					
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id,shipment_id,shipment_type";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id,shipment_id,shipment_type";
			}
			echo json_encode($msg);
		}
		/*******************************  Detail Shipment(Transporter) **********************************/
		
		
		/******************************* Shipment Delete ********************************************/
		function shipment_delete($customer_id = '',$shipment_id='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($customer_id) &&!empty($shipment_id))
				{				
					
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($customer_id);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					
					$query = $this->db->query('delete from truck_shipment where user_id="'.$customer_id.'" and shipment_id="'.$shipment_id.'" ');
					if($query)				
					{
						$query1 = $this->db->query('delete from truck_request where customer_id="'.$customer_id.'" and shipment_id="'.$shipment_id.'"');
						
						$this->db->query('update truck_notification  set status="deleted" where shipment_id='.$shipment_id);
						
						$msg['return'] 	= 1;
						$msg['result'] 	= 'success';						
					}else{						
						$msg['return'] = 0;
						$msg['result'] = 'Shipment deletion failed';						
					} 		
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : customer_id,shipment_id';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] =  'please provide parameter: customer_id,shipment_id';
			}
			echo json_encode($msg);
		}
		/******************************* Shipment delete **********************************/
		
		
		/******************************* Home Screen Shipment(Transporter) **********************************/
		function shipment_transporter($user_id = '',$search_type = '',$source='',$destination='',$distance='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id)&&!empty($search_type))
				{
					/*   temporary  condition for stop */
					
					$msg['return'] = 0;
						$msg['result'] = "No shipment available";
						$msg['truck_count'] = 0;
					echo json_encode($msg); exit;
					
					
					/*   temporary  condition for stop */
					if($search_type==1) // search enable
					{
						$block_status = $this->Tran_model->check_block_status($user_id);	
						if($block_status==0)
						{						
							$msg['return'] = 0;
							$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
							echo json_encode($msg);exit;
						}	
						
						$distance = $distance / 1.6093442;								
						if(!empty($source) && empty($destination))
						{
							// echo "source";
							$location = $this->Tran_model->get_lat_long($source);										
							$status     = $location['status'];
							$platitude  = $location['lat'];					
							$plongitude = $location['long'];
							if($status=='-1')
							{
								$msg['return'] 	=  0;
								$msg['result'] 	=  "Shipment location not found";
								echo json_encode($msg);exit;
							}
							
							// $query = $this->db->query('select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d-%m-%Y") as pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance,DATE_FORMAT(ts.created_date,"%d-%m-%Y") as created_date
													   // from  truck_shipment ts where  ts.pickup_location="'.$source.'" and  (ts.dispatch_status=1 OR ts.dispatch_status=2) order by ts.created_date desc');					
						
						
							$query = $this->db->query('select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.material_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance,DATE_FORMAT(ts.created_date,"%d %b %Y") as created_date
													   ,SQRT(POW(69.1*(ts.platitude- '.$platitude.'),2)+ POW(69.1*('.$plongitude.'-ts.plongitude)* COS(ts.platitude/57.3),2)) AS distances from  truck_shipment ts where (ts.dispatch_status=1 OR ts.dispatch_status=2) and date_format(ts.pickup_date,"%Y-%m-%d") >="'.date('Y-m-d').'" HAVING distances < '.$distance.' order by ts.created_date desc');
							
								
						}						
						
						if(!empty($destination) && empty($source))
						{
							// echo "destination".$destination."<br><br>source = ".$source;
							$location = $this->Tran_model->get_lat_long($destination);										
							$status     = $location['status'];
							$dlatitude  = $location['lat'];					
							$dlongitude = $location['long'];
							if($status=='-1')
							{
								$msg['return'] 	=  0;
								$msg['result'] 	=  "Shipment location not found";
								echo json_encode($msg);exit;
							}
							
							// $query = $this->db->query('select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d-%m-%Y") as pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance ,DATE_FORMAT(ts.created_date,"%d-%m-%Y") as created_date
									 // from  truck_shipment ts where  ts.dropup_location="'.$destination.'" and (ts.dispatch_status=1 OR ts.dispatch_status=2)  order by ts.created_date desc');
							
							$query = $this->db->query('select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.material_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance ,DATE_FORMAT(ts.created_date,"%d %b %Y") as created_date
									 ,SQRT(POW(69.1*(ts.dlatitude- '.$dlatitude.'),2)+ POW(69.1*('.$dlongitude.'-ts.dlongitude)* COS(ts.dlatitude/57.3),2)) AS distances  from  truck_shipment ts where  (ts.dispatch_status=1 OR ts.dispatch_status=2) and date_format(ts.pickup_date,"%Y-%m-%d") >="'.date('Y-m-d').'"  HAVING distances < '.$distance.'   order by ts.created_date desc');
																
						}
						
						if((!empty($source) && !empty($destination)))
						{	
							// echo "secnod lst".$destination;
							$query = $this->db->query('select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.capacity,ts.material_type,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance ,DATE_FORMAT(ts.created_date,"%d %b %Y") as created_date
									from  truck_shipment ts where  ts.pickup_location="'.$source.'" and  ts.dropup_location="'.$destination.'" and (ts.dispatch_status=1 OR ts.dispatch_status=2) and date_format(ts.pickup_date,"%Y-%m-%d") >="'.date('Y-m-d').'" order by ts.created_date desc');
							// echo 'select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.capacity,ts.material_type,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance ,DATE_FORMAT(ts.created_date,"%d %b %Y") as created_date
									// from  truck_shipment ts where  ts.pickup_location="'.$source.'" and  ts.dropup_location="'.$destination.'" and (ts.dispatch_status=1 OR ts.dispatch_status=2) order by ts.created_date desc';
									
															 
						}
						if((empty($source) && empty($destination))){	
							// echo "last";
							$query = $this->db->query('select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.capacity,ts.material_type,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance,DATE_FORMAT(ts.created_date,"%d %b %Y") as created_date
									from  truck_shipment ts where (ts.dispatch_status=1 OR ts.dispatch_status=2) and date_format(ts.pickup_date,"%Y-%m-%d") >="'.date('Y-m-d').'" order by ts.created_date desc');					
							
							// echo 'select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.capacity,ts.material_type,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance,DATE_FORMAT(ts.created_date,"%d %b %Y") as created_date
									// from  truck_shipment ts where (ts.dispatch_status=1 OR ts.dispatch_status=2) and ts.pickup_date >='.DATE_FORMAT(date('Y-m-d'),"%d %b %Y").' order by ts.created_date desc';	
								
						}	
						
					}
					
					if($search_type==2)	// search disable				
					{						
						$block_status = $this->Tran_model->check_block_status($user_id);	
						if($block_status==0)
						{						
							$msg['return'] = 0;
							$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
							echo json_encode($msg);exit;
						}	
						
						$query = $this->db->query('select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.capacity,ts.material_type,ts.tracking,ts.sharing,ts.shipment_type,
						ts.offered_price,ts.distance,ts.dispatch_status,DATE_FORMAT(ts.created_date,"%d %b %Y") as created_date,tl.truck_id,tl.driver_name,tl.contact_number,tl.registration_no ,u.username as transporter_name 
						from  truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on  u.user_id = tl.user_id where 
						(tl.user_id="'.$user_id.'" and (ts.dispatch_status=1 OR ts.dispatch_status=2) and tl.pickup_location=ts.pickup_location and tl.dropup_location=ts.dropup_location and ts.capacity<=tl.capacity and date_format(ts.pickup_date,"%Y-%m-%d") >="'.date('Y-m-d').'" ) 
						OR (tl.user_id="'.$user_id.'" and (ts.dispatch_status=1 OR ts.dispatch_status=2)  and tl.pickup_location=ts.dropup_location and tl.dropup_location=ts.pickup_location and ts.capacity<=tl.capacity and date_format(ts.pickup_date,"%Y-%m-%d") >="'.date('Y-m-d').'" ) 
						OR (tl.user_id="'.$user_id.'" and (ts.dispatch_status=1 OR ts.dispatch_status=2)  and tl.pickup_location=ts.pickup_location and tl.inbetween_location LIKE CONCAT("%",ts.dropup_location,"%") and ts.capacity<=tl.capacity and date_format(ts.pickup_date,"%Y-%m-%d") >="'.date('Y-m-d').'" ) 
						OR (tl.user_id="'.$user_id.'" and(ts.dispatch_status=1 OR ts.dispatch_status=2) and tl.inbetween_location LIKE CONCAT("%",ts.pickup_location,"%") and tl.dropup_location=ts.dropup_location and ts.capacity<=tl.capacity and date_format(ts.pickup_date,"%Y-%m-%d") >="'.date('Y-m-d').'" )
						OR (tl.user_id="'.$user_id.'" and (ts.dispatch_status=1 OR ts.dispatch_status=2)  and tl.inbetween_location LIKE CONCAT("%",ts.pickup_location,"%") and tl.inbetween_location LIKE CONCAT("%",ts.dropup_location,"%") and ts.capacity<=tl.capacity and date_format(ts.pickup_date,"%Y-%m-%d") >="'.date('Y-m-d').'" )						
						group by shipment_id order by ts.created_date desc');
						
						
						
						
						// $query = $this->db->query('select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,
						// ts.offered_price,ts.distance,ts.dispatch_status,DATE_FORMAT(ts.created_date,"%Y-%m-%d") as created_date,tl.truck_id,tl.driver_name,tl.contact_number,tl.registration_no ,u.username as transporter_name 
						// from  truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on  u.user_id = tl.user_id where 
						// (	tl.user_id="'.$user_id.'" and ts.dispatch_status!=6  and tl.pickup_location=ts.pickup_location and tl.dropup_location=ts.dropup_location) 
						// OR (tl.user_id="'.$user_id.'" and ts.dispatch_status!=6  and tl.pickup_location=ts.dropup_location and tl.dropup_location=ts.pickup_location) 
						// OR (tl.user_id="'.$user_id.'" and ts.dispatch_status!=6  and tl.pickup_location=ts.pickup_location and tl.inbetween_location LIKE CONCAT("%",ts.dropup_location,"%")) 
						// OR (tl.user_id="'.$user_id.'" and ts.dispatch_status!=6  and tl.inbetween_location LIKE CONCAT("%",ts.pickup_location,"%") and tl.dropup_location=ts.dropup_location)
						// OR (tl.user_id="'.$user_id.'" and ts.dispatch_status!=6  and tl.inbetween_location LIKE CONCAT("%",ts.pickup_location,"%") and tl.inbetween_location LIKE CONCAT("%",ts.dropup_location,"%"))						
						// group by shipment_id order by ts.created_date desc');
						
					}
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();
						// echo "<pre>";
						// print_r($result_data);	
						for($i=0;$i<count($result_data);$i++)
						{							
							$customer_id = $result_data[$i]['customer_id'];
							$shipment_id = $result_data[$i]['shipment_id'];
							
							$user_id2 = $this->Tran_model->get_user_detail($customer_id);						
							$result_data[$i]['mobile_number'] =  $user_id2[0]['mobile_number'];
							$result_data[$i]['username'] =  $user_id2[0]['customer_name'];	
							$result_data[$i]['customer_company'] =  $user_id2[0]['company_name'];	
							
							$tag = $this->Tran_model->get_request_status($customer_id,$shipment_id,$user_id);
							$result_data[$i]['tag'] = $tag;
							
						}
						// echo "<pre>";
						// print_r($result_data);	
						// exit;
						foreach($result_data as $subKey => $subArray)
						{
							  if($subArray['tag'] == 1){
							    unset($result_data[$subKey]);
								$newarray = array_values($result_data);
							  }else{
								$newarray = array_values($result_data);
							  }
						}
										
						// echo "<pre>";
						// print_r($newarray);
						$msg['total_record'] = count($newarray);
						if(!empty($newarray))
						{
							if(!empty($page_num)){
								$num_rec_per_page=10; 	
								// echo "page num = ".$page_num."<br>";	
								$start_from = ($page_num-1) * $num_rec_per_page; 
								$end        = $start_from +$num_rec_per_page; 
								$count      = 1;
								for($i=$start_from;$i<$end;$i++)
								{									
									if(!empty($newarray[$i])){
											// echo "i = ".$i."<br>";
											$newarray1[] = $newarray[$i];	
										}else{
											// $user3 ='';	
											$count++;
										}
								}
								if($count>=11)
								{
									$newarray1='';
								}
								
								if(!empty($newarray1)){
									// echo "count is " .count($newarray1);
									// echo "<pre>";
									// print_r($newarray1);exit;
									$newarray1;
								}else{
									$newarray1 ='';
								}
							}else{
								$newarray1 = $newarray;
							}	
							$msg['return'] 	=  1;
							$msg['result'] 	=  "success";
							$msg['truck_count'] = $this->Tran_model->get_truck_count($user_id);
							
							
							$msg['data'] 	=  $newarray1;	
						}else{
							$msg['return'] = 0;
							$msg['result'] = "No shipment available";
							$msg['truck_count'] = $this->Tran_model->get_truck_count($user_id);
						}						
					}					
					else{
						$msg['return'] = 0;
						$msg['result'] = "No shipment available";
						$msg['truck_count'] = $this->Tran_model->get_truck_count($user_id);
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id,search_type,source,destination,distance";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id,search_type,source,destination,distance";
			}
			echo json_encode($msg);
		}
		/******************************* Home Screen Shipment(Transporter) **********************************/
		
		/******************************* Apply Bid by transporter **********************************/
		function apply_bid($bid_value='',$tkreq_id='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($tkreq_id)&& !empty($bid_value))
				{					
					$query = $this->db->query('update truck_request set bid_value="'.$bid_value.'"  where tkreq_id="'.$tkreq_id.'"');
					if($query)
					{					
						$msg['return'] 	=  1;
						$msg['result'] 	=  "Bid successfully";					
					}					
					else{
						$msg['return'] = 0;
						$msg['result'] = "Updation failed";
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: tkreq_id,bid_value";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: tkreq_id,bid_value";
			}
			echo json_encode($msg);
		}
		
		/*******************************  Apply Bid by transporter **********************************/
		

		/******************************* accept offer by transporter **********************************/
		function accept_offer_by_transporter($shipment_id='',$tkreq_id='',$customer_id='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				
				$postData =array();
				if(!empty($tkreq_id)&& !empty($shipment_id)&&!empty($customer_id))
				{	

					/************* check blocked status *************/
								$block_status = $this->Tran_model->check_block_status($customer_id);	
								if($block_status==0)
								{						
									$msg['return'] = 0;
									$msg['result'] = 'Customer have been blocked by admin.';		
									echo json_encode($msg);exit;
								}	
								/************* check blocked status *************/
					$query = $this->db->query('select transporter_id,customer_id from truck_request where tkreq_id="'.$tkreq_id.'"');
					if($query->num_rows()>0)
					{	
						$result_data 	= $query->result_array();
						$transporter_id = $result_data[0]['transporter_id'];
						
						$this->db->query('update truck_request set request_status="3" where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'"');
						$this->db->query('update truck_shipment set dispatch_status="3" where shipment_id="'.$shipment_id.'"');				
							
						/*************** send notificaion to other reject transprter ********************/
						
						$qs = $this->db->query('select tr.tkreq_id,u.device_token,tr.transporter_id from truck_request tr inner join user u on u.user_id=tr.transporter_id where tr.shipment_id="'.$shipment_id.'"  and request_status=0 group by transporter_id');
						if($qs->num_rows()>0)
						{
							$rs = $qs->result_array();
							
							$condition1 	= array('user_id'=>$customer_id);
							$customer_name 	= $this->Tran_model->get_username('username','user',$condition1);
							$customer_name  = $customer_name[0]['username']; 
							
							$condition2 = array('shipment_id'=>$shipment_id);
							$fields = array('pickup_location','dropup_location');
							$shipment_rs = $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
							$pickup_location = $shipment_rs[0]['pickup_location']; 
							$dropup_location = $shipment_rs[0]['dropup_location']; 	
							
							for($i=0;$i<count($rs);$i++)
							{
								$device_token   = $rs[$i]['device_token'];										
								$transporter_id = $rs[$i]['transporter_id'];
							
								$msgsender_id['user_id'] 	 = $transporter_id;	
								
								$message = "$customer_name has cancelled request of shipment (SHP-$shipment_id) for a location $pickup_location to $dropup_location";
								$data = array(
											'sound'		=>1,
											'message'	=>$message,
											'notifykey'	=>'transporter_request_cancel',
											'shipment_id'=>$shipment_id,
											'request_status'=>3,
											'data'		=>$msgsender_id		
										);
								//$this->Tran_model->update_badge($transporter_id);			
								if(!empty($device_token))
								{							
									$this->Tran_model->send_android_notification($device_token,$data);										
								}
						
							}
							
						}
						
						/*************** send notificaion to other reject transprter ********************/
					   
						$this->db->query('delete from truck_request where shipment_id="'.$shipment_id.'" and request_status=0');					
						$this->db->query('update truck_shipment set offer_requested="0" where shipment_id="'.$shipment_id.'" and offer_requested=1');
					
						$condition = array('user_id'=>$customer_id);
						$device_token = $this->Tran_model->get_device_token('device_token','user',$condition);						
						$condition1 		= array('user_id'=>$transporter_id);
						$transporter_name 	= $this->Tran_model->get_username('username','user',$condition1);
						$transporter_name   = $transporter_name[0]['username']; 
						
						$condition2 	= array('shipment_id'=>$shipment_id);
						$fields 		= array('pickup_location','dropup_location');
						$shipment_rs 	= $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
						$pickup_location= $shipment_rs[0]['pickup_location']; 
						$dropup_location= $shipment_rs[0]['dropup_location']; 
								
						$device_token 			= $device_token[0]['device_token']; 							
						$msgsender_id['user_id']= $customer_id;	
						
						$message = "$transporter_name has accepted a request of shipment (SHP-$shipment_id) for a location $pickup_location to $dropup_location";							
						$data = array(
									'sound'		=>1,
									'message'	=>$message,
									'notifykey'	=>'offer_accepted',
									'shipment_id'=>$shipment_id,
									'data'		=>$msgsender_id		
						);
						$this->Tran_model->update_badge($customer_id);	
						if(!empty($device_token))
						{							
							$result1 =  $this->Tran_model->send_android_notification($device_token,$data);																	
						}
						$insertdata['user_id'] 			=  $customer_id ;
						$insertdata['message'] 			=  $message ;							
						$insertdata['type']    			= 'customer';
						$insertdata['shipment_id'] 		=  $shipment_id ;
						$insertdata['status'] 			=  3 ;
						$insertdata['created_date'] 	= date('Y-m-d H:i:s');
						$this->Tran_model->insert_data('truck_notification',$insertdata);				
						
						$msg['return'] 	=  1;
						$msg['result'] 	=  "Offer accpted successfully";					
					}					
					else{
						$msg['return'] = 0;
						$msg['result'] = "Updation failed";
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: tkreq_id,shipment_id,customer_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: tkreq_id,bid_value";
			}
			echo json_encode($msg);
		}
		
		/*******************************  Reject offer by transporter **********************************/				
		function reject_offer_by_transporter($shipment_id='',$tkreq_id='',$customer_id='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($tkreq_id)&& !empty($shipment_id)&&!empty($customer_id))
				{					
					$query = $this->db->query('select transporter_id,customer_id from truck_request where tkreq_id="'.$tkreq_id.'"');
					if($query->num_rows()>0)
					{	
						$result_data 	= $query->result_array();
						$transporter_id = $result_data[0]['transporter_id'];
									
						$query1 = $this->db->query('update truck_shipment set offer_requested="0" where shipment_id="'.$shipment_id.'"');
					
						$condition = array('user_id'=>$customer_id);
						$device_token = $this->Tran_model->get_device_token('device_token','user',$condition);												
						$device_token = $device_token[0]['device_token']; 							
						
						$condition1 		= array('user_id'=>$transporter_id);
						$transporter_name 	= $this->Tran_model->get_username('username','user',$condition1);
						$transporter_name   = $transporter_name[0]['username']; 
						
						$condition2 	= array('shipment_id'=>$shipment_id);
						$fields 		= array('pickup_location','dropup_location');
						$shipment_rs 	= $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
						$pickup_location= $shipment_rs[0]['pickup_location']; 
						$dropup_location= $shipment_rs[0]['dropup_location']; 
						
						$msgsender_id['user_id'] 	 = $customer_id;	
						$message = "$transporter_name has rejected a request of shipment (SHP-$shipment_id) for a location $pickup_location to $dropup_location";							
						
						$data = array(
									'sound'		=>1,
									'message'	=>$message,
									'notifykey'	=>'offer_rejected',
									'shipment_id'=>$shipment_id,
									'data'		=>$msgsender_id		
						);	
						$this->Tran_model->update_badge($customer_id);						
						if(!empty($device_token))
						{
							$this->Tran_model->send_android_notification($device_token,$data);																					
						}
						$this->db->query('delete from truck_request where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'"');
					
						$insertdata['user_id'] 			=  $customer_id ;
						$insertdata['message'] 			=  $message ;							
						$insertdata['type']    			= 'customer';
						$insertdata['shipment_id']  	= $shipment_id;
						$insertdata['created_date'] 	= date('Y-m-d H:i:s');
						$this->Tran_model->insert_data('truck_notification',$insertdata);	
						
						$msg['return'] 	=  1;
						$msg['result'] 	=  "Offer reject successfully";					
					}					
					else{
						$msg['return'] = 0;
						$msg['result'] = "Updation failed";
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: tkreq_id,shipment_id,customer_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: tkreq_id,bid_value,customer_id";
			}
			echo json_encode($msg);
		}
		
		/*******************************  Reject offer by transporter **********************************/
			
		/******************************* Accept request by transporter **********************************/
		function accept_requestby_transporter($customer_id='',$shipment_id='',$transporter_id='',$shipment_type='',$bid_value='',$truck_id='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($customer_id)&& !empty($shipment_id)&& !empty($transporter_id))
				{					
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($transporter_id);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					/************* check blocked status *************/
								$block_status = $this->Tran_model->check_block_status($customer_id);	
								if($block_status==0)
								{						
									$msg['return'] = 0;
									$msg['result'] = 'Customer have been blocked by admin.';		
									echo json_encode($msg);exit;
								}	
								/************* check blocked status *************/
					
					
					$postData['customer_id']			= $customer_id;		
					$postData['shipment_id']			= $shipment_id;		
					$postData['transporter_id']			= $transporter_id;							
					$postData['shipment_type']			= $shipment_type;							
					$postData['bid_value']				= $bid_value;	
					$postData['truck_id']				= $truck_id;						
					$postData['request_status']			= 1;
					$postData['status']					= 1;
					$postData['created_date'] 			= date('Y-m-d h:i:s');
					
					$query = $this->db->query('select * from truck_request where 
					(customer_id="'.$customer_id.'" and shipment_id="'.$shipment_id.'" and transporter_id="'.$transporter_id.'" and request_status=1 and shipment_type="'.$shipment_type.'") 
					OR (customer_id="'.$customer_id.'" and shipment_id="'.$shipment_id.'" and transporter_id="'.$transporter_id.'" and request_status=3 and shipment_type="'.$shipment_type.'")
					OR (customer_id="'.$customer_id.'" and shipment_id="'.$shipment_id.'" and transporter_id="'.$transporter_id.'" and request_status=4 and shipment_type="'.$shipment_type.'")');
					
					if($query->num_rows()>0)
					{
						$msg['return'] 	=  0;
						$msg['result'] 	=  "Shipment already applied";
						echo json_encode($msg);exit;
					}
					
					$query1 = $this->db->query('select shipment_id from truck_shipment where shipment_id="'.$shipment_id.'" ');
					if($query1->num_rows()==0)
					{
						$msg['return'] 	=  2;
						// $msg['status'] 	=  0;
						$msg['result'] 	=  "Shipment does not exist";
						echo json_encode($msg);exit;
						
					}else{
					
						$result_id = $this->Tran_model->insert_data('truck_request',$postData);					
						if($result_id)
						{									
							$condition = array('user_id'=>$customer_id);
							$device_token = $this->Tran_model->get_device_token('device_token','user',$condition);
							$device_token = $device_token[0]['device_token']; 
							
							$condition1 = array('user_id'=>$transporter_id);
							$transporter_name = $this->Tran_model->get_username('username','user',$condition1);
							$transporter_name = $transporter_name[0]['username']; 
							
							
							$condition2 = array('shipment_id'=>$shipment_id);
							$fields = array('pickup_location','dropup_location');
							$shipment_rs = $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
							$pickup_location = $shipment_rs[0]['pickup_location']; 
							$dropup_location = $shipment_rs[0]['dropup_location']; 
							
							$msgsender_id['user_id'] 	 = $customer_id;	
							// $message = "Your shipment(SH-".$shipment_id.") has been accepted by ".$transporter_name." ";
							if($shipment_type==1)
							{
								$this->Tran_model->update_badge($customer_id);
								$message = "$transporter_name has applied a bid on shipment (SHP-$shipment_id) for a location  $pickup_location to $dropup_location";							
								$msg['return'] 	=  1;
								$msg['result'] 	=  "Bid applied successfully";	
							}
							
							if($shipment_type==2)
							{
								$this->Tran_model->update_badge($customer_id);
								$message = "$transporter_name has accepted a offer of shipment (SHP-$shipment_id) for a location  $pickup_location to $dropup_location";							
								$msg['return'] 	=  1;
								$msg['result'] 	=  "Offer accepted successfully";	
							}
							
							$data = array(
										'sound'		=>1,
										'message'	=>$message,
										'notifykey'	=>'shipment_accept',
										'shipment_id'=>$shipment_id,
										'data'		=>$msgsender_id		
									);
									
							if(!empty($device_token))
							{								
								$result1 =  $this->Tran_model->send_android_notification($device_token,$data);																						
							}
							
							$insertdata['user_id'] 		=  $customer_id ;
							$insertdata['message'] 		=  $message ;							
							$insertdata['type']    		= 'customer';
							$insertdata['shipment_id'] 	=  $shipment_id ;
							$insertdata['status'] 		=  $shipment_type ;
							$insertdata['created_date'] = date('Y-m-d H:i:s');
							$result2 					=  $this->Tran_model->insert_data('truck_notification',$insertdata);			
										
						
						}else{
							$msg['return'] 	=  0;
							$msg['result'] 	=  "Shipment booking failed";	
						}
					}
						
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: customer_id,shipment_id,transporter_id,shipment_type,bid_value,truck_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: customer_id,shipment_id,transporter_id,shipment_type,bid_value";
			}
			echo json_encode($msg);
		}
		/*******************************  Accept request by transporter *****************************/
		

		/******************************* Book Shipment by customer **********************************/
		function book_shipment($tkreq_id='',$shipment_id='',$truck_id='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($tkreq_id)&& !empty($shipment_id))
				{					
					$query = $this->db->query('select request_status,tkreq_id,transporter_id,customer_id from truck_request where tkreq_id="'.$tkreq_id.'"' );
					if($query->num_rows()>0)
					{	
						$result_data 	= $query->result_array();
						if($result_data[0]['request_status'] == 5){
							$msg['return'] = 0;
							$msg['result'] = 'Request has been cancelled by Transpoter';		
							echo json_encode($msg);exit;
							
						}
						$transporter_id = $result_data[0]['transporter_id'];
						$customer_id 	= $result_data[0]['customer_id'];
						
						
						/************* check blocked status *************/
						$block_status = $this->Tran_model->check_block_status($customer_id);	
						if($block_status==0)
						{						
							$msg['return'] = 0;
							$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
							echo json_encode($msg);exit;
						}	
						/************* check blocked status *************/
						/************* check blocked status *************/
						$block_status = $this->Tran_model->check_block_status($transporter_id);	
						if($block_status==0)
						{						
							$msg['return'] = 0;
							$msg['result'] = 'This transporter has been blocked by admin.';		
							echo json_encode($msg);exit;
						}	
						/************* check blocked status *************/
						
					
						// $query = $this->db->query('update truck_request set request_status="3" where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'" and truck_id="'.$truck_id.'"');
						$query1 = $this->db->query('select bid_value from truck_request where tkreq_id="'.$tkreq_id.'" and shipment_type=1');
						if($query1->num_rows()>0)
						{
							$result_data = $query1->row_array();
							$bid_value =  $result_data['bid_value'];
							$query = $this->db->query('update truck_shipment set offered_price="'.$bid_value.'" where shipment_id="'.$shipment_id.'"');
						}
					
						$query_rs = $this->db->query('update truck_request set request_status="3" where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'"');
						
						if($query_rs)
						{	
						    /*************** send notificaion to other reject transprter ********************/
								$qs = $this->db->query('select tr.tkreq_id,u.device_token,tr.transporter_id from truck_request tr inner join user u on u.user_id=tr.transporter_id where tr.shipment_id="'.$shipment_id.'"  and request_status=1 group by transporter_id');
								if($qs->num_rows()>0)
								{
									$rs = $qs->result_array();
									
									$condition1 	= array('user_id'=>$customer_id);
									$customer_name 	= $this->Tran_model->get_username('username','user',$condition1);
									$customer_name  = $customer_name[0]['username']; 
									
									$condition2 = array('shipment_id'=>$shipment_id);
									$fields = array('pickup_location','dropup_location');
									$shipment_rs = $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
									$pickup_location = $shipment_rs[0]['pickup_location']; 
									$dropup_location = $shipment_rs[0]['dropup_location']; 	
									
									
									for($i=0;$i<count($rs);$i++)
									{
										$device_token = $rs[$i]['device_token'];										
										$transporter_ids = $rs[$i]['transporter_id'];
										$tkreq_id = $rs[$i]['tkreq_id'];
										
										// echo "<br>";
										
										$msgsender_id['user_id'] 	 = $transporter_ids;	
										
										$message = "$customer_name has cancelled request of shipment (SHP-$shipment_id) for a location $pickup_location to $dropup_location";
										$data = array(
													'sound'		=>1,
													'message'	=>$message,
													'notifykey'	=>'transporter_request_cancel',
													'shipment_id'=>$shipment_id,
													'request_status'=>5,
													'data'		=>$msgsender_id		
												);
										$this->Tran_model->update_badge($transporter_ids);			
										if(!empty($device_token))
										{							
											$this->Tran_model->send_android_notification($device_token,$data);										
										}
										$this->db->query('delete from truck_request where tkreq_id='.$tkreq_id);
											
										$insertdata_p['user_id'] 		=  $transporter_ids ;
										$insertdata_p['message'] 		=  $message ;							
										$insertdata_p['type']    		= 'transporter';
										$insertdata_p['shipment_id'] 	=  $shipment_id ;
										$insertdata_p['status'] 		=  'cancel' ;
										$insertdata_p['created_date'] = date('Y-m-d H:i:s');
										$this->Tran_model->insert_data('truck_notification',$insertdata_p);
										
									}
									
									
								}
								
							/*************** send notificaion to other reject transprter ********************/
						   
							
							
							// $this->db->query('delete from truck_request where shipment_id="'.$shipment_id.'"  and request_status=1');
							$query = $this->db->query('update truck_shipment set dispatch_status="3" where shipment_id="'.$shipment_id.'" ');							
							if($query)
							{					
								$condition = array('user_id'=>$transporter_id);
								$device_token = $this->Tran_model->get_device_token('device_token','user',$condition);
								$device_token = $device_token[0]['device_token']; 
								
								$rs_data1    = $this->Tran_model->get_user_detail($transporter_id);						
								$tran_email  = $rs_data1[0]['email'];	

								$condition2 	= array('user_id'=>$transporter_id);
								$trans_name 	= $this->Tran_model->get_username('username','user',$condition2);
								$trans_name     = $trans_name[0]['username']; 
							
								$condition1 	= array('user_id'=>$customer_id);
								$customer_name 	= $this->Tran_model->get_username('username','user',$condition1);
								$customer_name  = $customer_name[0]['username']; 
								
								$rs_data2    = $this->Tran_model->get_user_detail($customer_id);						
								$cust_email  = $rs_data2[0]['email'];
								
								$condition2 = array('shipment_id'=>$shipment_id);
								$fields = array('pickup_location','dropup_location');
								$shipment_rs = $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
								$pickup_location = $shipment_rs[0]['pickup_location']; 
								$dropup_location = $shipment_rs[0]['dropup_location']; 
								
								$msgsender_id['user_id'] 	 = $transporter_id;	
								//$message = "Shipment(SH-".$shipment_id.") has been booked by ".$customer_name." ";
								$message = "$customer_name has booked a shipment (SHP-$shipment_id) for a location $pickup_location to $dropup_location";
								
								$message3 = "You have booked a shipment (SHP-$shipment_id) for a location $pickup_location to $dropup_location with $customer_name (customer)";
								
								$message2 = "You have booked a shipment (SHP-$shipment_id) for a location $pickup_location to $dropup_location with $trans_name (transporter) ";
								
								$data = array(
											'sound'		=>1,
											'message'	=>$message,
											'notifykey'	=>'shipment_booked',
											'shipment_id'=>$shipment_id,
											'request_status'=>3,
											'data'		=>$msgsender_id		
										);
								$this->Tran_model->update_badge($transporter_id);
								// echo $cust_email." ".$message."".$customer_name;
								// echo "hello";
								$this->send_mail($cust_email,$message2,'customer',$customer_name,'book'); // send mail to customer
								$this->send_mail($tran_email,$message3,'transporter',$trans_name,'book');
								// exit;
								if(!empty($device_token))
								{							
									$this->Tran_model->send_android_notification($device_token,$data);										
								}
								
								$insertdata['user_id'] 		=  $transporter_id ;
								$insertdata['message'] 		=  $message ;							
								$insertdata['type']    		= 'transporter';
								$insertdata['shipment_id'] 	=  $shipment_id ;
								$insertdata['status'] 		=  3 ;
								$insertdata['created_date'] = date('Y-m-d H:i:s');
								$this->Tran_model->insert_data('truck_notification',$insertdata);											
							}
							$msg['return'] 	=  1;
							$msg['result'] 	=  "Request accept successfully";					
						}					
						else{
							$msg['return'] = 0;
							$msg['result'] = "Updation failed";
						}
					}else{						
						$msg['return'] = 0;
						$msg['result'] = "No record found";	
					}	
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: tkreq_id,shipment_id,truck_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: tkreq_id,shipment_id,truck_id";
			}
			echo json_encode($msg);
		}
		/*******************************  Accept request by customer **********************************/
					
			
		/********** shipment booking cancel by customer/transporter(After booking) readytodispatch ***********/
		function booking_cancel($tkreq_id='',$shipment_id='',$truck_id='',$type='',$cancel_status='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($tkreq_id)&& !empty($shipment_id)&& !empty($truck_id) && !empty($type)&& !empty($cancel_status))
				{					
					if($type=="2") // customer
					{
						$query22 = $this->db->query('select transporter_id,customer_id from truck_request where tkreq_id="'.$tkreq_id.'"');
							if($query22->num_rows()>0)
							{	
								$result_data22 	= $query22->result_array();
								$customer_id22 	= $result_data22[0]['customer_id'];
								$transporter_id22 	= $result_data22[0]['transporter_id'];
								/************* check blocked status *************/
								$block_status = $this->Tran_model->check_block_status($customer_id22);	
								if($block_status==0)
								{						
									$msg['return'] = 0;
									$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
									echo json_encode($msg);exit;
								}	
								$block_status = $this->Tran_model->check_block_status($transporter_id22);	
								if($block_status==0)
								{						
									$msg['return'] = 0;
									$msg['result'] = 'Transporter have been blocked by admin. ';		
									echo json_encode($msg);exit;
								}	
								/************* check blocked status *************/
								
						$shp_query = $this->db->query('select request_status from truck_request where request_status=5 and tkreq_id="'.$tkreq_id.'" ');
						if($shp_query->num_rows()>0)
						{
							$msg['return'] = 0;
							$msg['result'] = "Your shipment already cancelled";
							echo json_encode($msg);exit;
						}	
						
						
					
					
						if($cancel_status==3)
						{
							$msg['return'] = 1;
							$msg['result'] = "You can not cancel your booking";
							echo json_encode($msg);exit;
						}
						
						
						
						if($cancel_status==2)
						{
							$query = $this->db->query('select request_status,transporter_id,customer_id from truck_request where tkreq_id="'.$tkreq_id.'"');
							if($query->num_rows()>0)
							{	
								$result_data 	= $query->result_array();
								if($result_data[0]['request_status'] == 4)
								{
									$msg['return'] = 0;
									$msg['result'] = "You can not cancel your booking";
									echo json_encode($msg);exit;
								}else{
								$transporter_id = $result_data[0]['transporter_id'];
								$customer_id 	= $result_data[0]['customer_id'];
								}
								
							}	
							
							/************* check blocked status *************/
								$block_status = $this->Tran_model->check_block_status($customer_id);	
								if($block_status==0)
								{						
									$msg['return'] = 0;
									$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
									echo json_encode($msg);exit;
								}	
								/************* check blocked status *************/
							$rs_data1    = $this->Tran_model->get_user_detail($customer_id);								
							$cust_email  = $rs_data1[0]['email'];					
							$user_type = 'customer';
							$this->generate_pdf_cancel($transporter_id,$customer_id,$shipment_id,$cust_email,$user_type,$tkreq_id);
							
						}
						/************* check blocked status *************/
								$block_status = $this->Tran_model->check_block_status($customer_id22);	
								if($block_status==0)
								{						
									$msg['return'] = 0;
									$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
									echo json_encode($msg);exit;
								}	
								/************* check blocked status *************/
						
						$query_rs = $this->db->query('update truck_request set request_status="5" ,created_date="'.date('Y-m-d H:i:s').'" where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'" and truck_id="'.$truck_id.'" and request_status=3');
						/* if($query_rs) */
						if($this->db->affected_rows())
						{
							$query_rs = $this->db->query('update truck_shipment set dispatch_status="5" where shipment_id="'.$shipment_id.'" ');
							
							$query = $this->db->query('select request_status,transporter_id,customer_id from truck_request where tkreq_id="'.$tkreq_id.'"');
							if($query->num_rows()>0)
							{	
								$result_data 	= $query->result_array();
								if($result_data[0]['request_status'] == 4)
								{
									$msg['return'] = 0;
									$msg['result'] = "You can not cancel your booking";
									echo json_encode($msg);exit;
								}else{
								$transporter_id = $result_data[0]['transporter_id'];
								$customer_id 	= $result_data[0]['customer_id'];
								}
								/* $transporter_id = $result_data[0]['transporter_id'];
								$customer_id 	= $result_data[0]['customer_id']; */
								
								/************* check blocked status *************/
								$block_status = $this->Tran_model->check_block_status($customer_id);	
								if($block_status==0)
								{						
									$msg['return'] = 0;
									$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
									echo json_encode($msg);exit;
								}	
								/************* check blocked status *************/
								
								$query2 = $this->db->query('select shipment_type from truck_shipment where shipment_id='.$shipment_id);
								if($query2->num_rows()>0)
								{
									$result_data2   = $query2->result_array();
									$shipment_type = $result_data2[0]['shipment_type'];
									$this->db->query('update truck_shipment set dispatch_status="'.$shipment_type.'" where shipment_id="'.$shipment_id.'" ');
								
								}
								
								/*************** Send notificaion to transporter **************/
								$condition = array('user_id'=>$transporter_id);
								$device_token = $this->Tran_model->get_device_token('device_token','user',$condition);
								$device_token = $device_token[0]['device_token']; 
								
								$rs_data1    = $this->Tran_model->get_user_detail($transporter_id);						
								$tran_email  = $rs_data1[0]['email'];	

								$condition2 	= array('user_id'=>$transporter_id);
								$trans_name 	= $this->Tran_model->get_username('username','user',$condition2);
								$trans_name     = $trans_name[0]['username']; 
							
							
								$condition1 	= array('user_id'=>$customer_id);
								$customer_name 	= $this->Tran_model->get_username('username','user',$condition1);
								$customer_name  = $customer_name[0]['username']; 
								
								$condition2 	= array('shipment_id'=>$shipment_id);
								$fields 		= array('pickup_location','dropup_location');
								$shipment_rs 	= $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
								$pickup_location= $shipment_rs[0]['pickup_location']; 
								$dropup_location= $shipment_rs[0]['dropup_location']; 
								
								$msgsender_id['user_id'] = $transporter_id;	
								$message = "$customer_name has cancelled the booking of shipment (SHP-$shipment_id) for a location $pickup_location to $dropup_location";							
								$data = array(
											'sound'		=>1,
											'message'	=>$message,
											'notifykey'	=>'shipment_cancel_customer',
											'shipment_id'=>$shipment_id,
											'data'		=>$msgsender_id		
										);
								$this->Tran_model->update_badge($transporter_id);					
								if(!empty($device_token))
								{								
									$this->Tran_model->send_android_notification($device_token,$data);			
								}
								if($cancel_status!=2)
								{
									$this->send_mail($tran_email,$message,'transporter',$trans_name,'cancel');
								}		
								$insertdata['user_id'] 		=  $transporter_id ;
								$insertdata['message'] 		=  $message ;							
								$insertdata['type']    		= 'transporter';
								$insertdata['shipment_id'] 	=  $shipment_id ;
								$insertdata['status'] 		=  5 ;
								$insertdata['created_date'] = date('Y-m-d H:i:s');
								$this->Tran_model->insert_data('truck_notification',$insertdata);	
							}	
							
							/************* Send notificaion to transporter **************/	
							$msg['return'] 	=  1;
							$msg['result'] 	=  "Booking cancelled successfully";					
						}					
						else{
							$msg['return'] = 0;
							$msg['result'] = "Booking cancelled failed";
						}	
							}
					}
					
					if($type=="1") // transporter					
					{
						$query21 = $this->db->query('select transporter_id,customer_id from truck_request where tkreq_id="'.$tkreq_id.'"');
							if($query21->num_rows()>0)
							{	
								$result_data21 	= $query21->result_array();
								$transporter_id21 	= $result_data21[0]['transporter_id'];
								$customer_id21 	= $result_data21[0]['customer_id'];
								/************* check blocked status *************/
								$block_status = $this->Tran_model->check_block_status($transporter_id21);	
								if($block_status==0)
								{						
									$msg['return'] = 0;
									$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
									echo json_encode($msg);exit;
								}	
								$block_status = $this->Tran_model->check_block_status($customer_id21);	
								if($block_status==0)
								{						
									$msg['return'] = 0;
									$msg['result'] = 'Customer have been blocked by admin.';		
									echo json_encode($msg);exit;
								}	
								/************* check blocked status *************/
						$shp_query = $this->db->query('select request_status from truck_request where request_status=5 and tkreq_id="'.$tkreq_id.'" ');
						if($shp_query->num_rows()>0)
						{
							$msg['return'] = 0;
							$msg['result'] = "Your shipment already cancelled";
							echo json_encode($msg);exit;
						}	
						if($shp_query->num_rows()>0)
						{
							$msg['return'] = 1;
							$msg['result'] = "You can not cancel your booking";
							echo json_encode($msg);exit;
						}
						
						if($cancel_status==3)
						{
							$msg['return'] = 1;
							$msg['result'] = "You can not cancel your booking";
							echo json_encode($msg);exit;
						}
						
						if($cancel_status==2)
						{
							$query = $this->db->query('select request_status,transporter_id,customer_id from truck_request where tkreq_id="'.$tkreq_id.'"');
							if($query->num_rows()>0)
							{	
								$result_data 	= $query->result_array();
								if($result_data[0]['request_status'] == 4)
								{
									$msg['return'] = 0;
									$msg['result'] = "You can not cancel your booking";
									echo json_encode($msg);exit;
								}else{
								$transporter_id = $result_data[0]['transporter_id'];
								$customer_id 	= $result_data[0]['customer_id'];
								}
							}	
							/************* check blocked status *************/
								$block_status = $this->Tran_model->check_block_status($transporter_id);	
								if($block_status==0)
								{						
									$msg['return'] = 0;
									$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
									echo json_encode($msg);exit;
								}	
								/************* check blocked status *************/
							$rs_data1    = $this->Tran_model->get_user_detail($transporter_id);								
							$trans_email  = $rs_data1[0]['email'];
							$user_type = 'transporter';
							$this->generate_pdf_cancel($transporter_id,$customer_id,$shipment_id,$trans_email,$user_type,$tkreq_id);
							
						}
						
						/************* check blocked status *************/
								$block_status = $this->Tran_model->check_block_status($transporter_id21);	
								if($block_status==0)
								{						
									$msg['return'] = 0;
									$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
									echo json_encode($msg);exit;
								}	
								/************* check blocked status *************/
						
						$query_rs = $this->db->query('update truck_request set request_status="5"  ,created_date="'.date('Y-m-d H:i:s').'" where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'" and truck_id="'.$truck_id.'" and request_status=3');
						$query_rs1 = $this->db->query('update truck_request set request_status="5"  ,created_date="'.date('Y-m-d H:i:s').'" where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'" and truck_id="'.$truck_id.'" and request_status=1');
						
						if(isset($query_rs) || isset($query_rs1))
						{	
							$query = $this->db->query('select shipment_type from truck_shipment where shipment_id='.$shipment_id);
							if($query->num_rows()>0)
							{
								$result_data   = $query->result_array();
								$shipment_type = $result_data[0]['shipment_type'];
								$query2 = $this->db->query('update truck_shipment set dispatch_status="'.$shipment_type.'" where shipment_id="'.$shipment_id.'" ');
								
								$query = $this->db->query('select request_status,customer_id,transporter_id from truck_request where tkreq_id="'.$tkreq_id.'"');
								if($query->num_rows()>0)
								{	
									$result_data 	= $query->result_array();
									if($result_data[0]['request_status'] == 4)
								{
									$msg['return'] = 0;
									$msg['result'] = "You can not cancel your booking";
									echo json_encode($msg);exit;
								}else{
								$transporter_id = $result_data[0]['transporter_id'];
								$customer_id 	= $result_data[0]['customer_id'];
								}
									/* $customer_id 	= $result_data[0]['customer_id'];
									$transporter_id = $result_data[0]['transporter_id']; */
									
									/************* check blocked status *************/
									$block_status = $this->Tran_model->check_block_status($transporter_id);	
									if($block_status==0)
									{						
										$msg['return'] = 0;
										$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
										echo json_encode($msg);exit;
									}	
									/************* check blocked status *************/	

										
									/************* Send notificaion to customer ************/
									$condition		= array('user_id'=>$customer_id);
									$device_token 	= $this->Tran_model->get_device_token('device_token','user',$condition);
									$device_token 	= $device_token[0]['device_token']; 
									
									$condition11 	= array('user_id'=>$customer_id);
									$customer_name 	= $this->Tran_model->get_username('username','user',$condition11);
									$customer_name  = $customer_name[0]['username']; 
									
									$rs_data2    = $this->Tran_model->get_user_detail($customer_id);						
									$cust_email  = $rs_data2[0]['email'];
									
									$condition1 	  = array('user_id'=>$transporter_id);
									$transporter_name = $this->Tran_model->get_username('username','user',$condition1);
									$transporter_name = $transporter_name[0]['username']; 
									
									$condition2 	= array('shipment_id'=>$shipment_id);
									$fields 		= array('pickup_location','dropup_location');
									$shipment_rs 	= $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
									$pickup_location= $shipment_rs[0]['pickup_location']; 
									$dropup_location= $shipment_rs[0]['dropup_location']; 
									
									$msgsender_id['user_id'] = $customer_id;											
									$message = "$transporter_name has cancelled your booking of shipment (SHP-$shipment_id)  for a location $pickup_location to $dropup_location";								
									$data = array(
												'sound'		=>1,
												'message'	=>$message,
												'notifykey'	=>'shipment_cancel_transporter',
												'shipment_id'=>$shipment_id,
												'data'		=>$msgsender_id		
											);
									if($cancel_status!=2)
									{	
										$this->send_mail($cust_email,$message,'customer',$customer_name,'cancel');
									}
									
									$this->Tran_model->update_badge($customer_id);							
									if(!empty($device_token))
									{									
										$this->Tran_model->send_android_notification($device_token,$data);																									
									}
									
									$insertdata['user_id'] 		=  $customer_id ;
									$insertdata['message'] 		=  $message ;							
									$insertdata['type']    		= 'customer';
									$insertdata['shipment_id'] 	=  $shipment_id ;
									$insertdata['status'] 		=  5 ;
									$insertdata['created_date'] = date('Y-m-d H:i:s');
									$this->Tran_model->insert_data('truck_notification',$insertdata);			
								}	
								/************* Send notificaion to customer **************/	
										
							}
							$msg['return'] 	=  1;
							$msg['result'] 	=  "Booking cancelled successfully";					
						}else{
						$msg['return'] = 0;
							$msg['result'] = "Booking cancelled failed";
						}					
					}					
					}
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: tkreq_id,shipment_id,truck_id,type";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: tkreq_id,shipment_id,truck_id,type";
			}
			echo json_encode($msg);
		}
		/*******************************  shipment cancel by customer **********************************/
		
		
		/******************************* Transporter request cancel by customer **********************************/
		function cancel_request($tkreq_id='',$shipment_id='',$truck_id='',$transporter_id='',$type='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($tkreq_id)&& !empty($shipment_id)&& !empty($truck_id) && !empty($type))
				{					
					if($type=="2") // customer
					{									
						$query = $this->db->query('select customer_id from truck_request  where tkreq_id="'.$tkreq_id.'" ');
						if($query->num_rows()>0)
						{	
							$result_data 	= $query->result_array();						
							$customer_id 	= $result_data[0]['customer_id'];
							
							/************* check blocked status *************/
							$block_status = $this->Tran_model->check_block_status($customer_id);	
							if($block_status==0)
							{						
								$msg['return'] = 0;
								$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
								echo json_encode($msg);exit;
							}	
							/************* check blocked status *************/	
									
							/************* Send notificaion to transporter ************/
							$condition1    	= array('user_id'=>$transporter_id);
							$device_token1 	= $this->Tran_model->get_device_token('device_token','user',$condition1);
							$device_token1 	= $device_token1[0]['device_token']; 
								
							$condition1 	= array('user_id'=>$customer_id);
							$customer_name 	= $this->Tran_model->get_username('username','user',$condition1);
							$customer_name  = $customer_name[0]['username']; 
							
							$condition2 	= array('shipment_id'=>$shipment_id);
							$fields 		= array('pickup_location','dropup_location');
							$shipment_rs 	= $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
							$pickup_location= $shipment_rs[0]['pickup_location']; 
							$dropup_location= $shipment_rs[0]['dropup_location']; 
							
							$msgsender_id1['user_id'] = $transporter_id;	
							$message1 = "$customer_name has cancelled the request for a location $pickup_location to $dropup_location";							
							$data1 = array(
										'sound'		=>1,
										'message'	=>$message1,
										'notifykey'	=>'shipment_request_cancel',
										'shipment_id'=>$shipment_id,
										'data'		=>$msgsender_id1		
									);
							$this->Tran_model->update_badge($transporter_id);	
							if(!empty($device_token1))
							{															
								$this->Tran_model->send_android_notification($device_token1,$data1);																													
							}
							$this->db->query('delete from truck_request where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'" and truck_id="'.$truck_id.'" and request_status=0');
							
							$insertdata1['user_id'] 	 = $transporter_id ;
							$insertdata1['message'] 	 = $message1 ;							
							$insertdata1['type']    	 = 'transporter';
							$insertdata1['shipment_id']  = $shipment_id;
							$insertdata1['created_date'] = date('Y-m-d H:i:s');	
							$this->Tran_model->insert_data('truck_notification',$insertdata1);		
							/************* Send notificaion to transporter ************/
					
							$msg['return'] 	=  1;
							$msg['result'] 	=  "Transporter request cancelled successfully";					
						}					
						else{
							$msg['return'] = 0;
							$msg['result'] = "Transporter request cancelled failed";
						}						
					}
					
					if($type=="1") // transporter					
					{
						$this->db->query('update truck_request set request_status="5" where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'" and truck_id="'.$truck_id.'" and request_status=3');
						$this->db->query('update truck_request set request_status="5" where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'" and truck_id="'.$truck_id.'" and request_status=1');
						
						$query = $this->db->query('select transporter_id,customer_id from truck_request  where tkreq_id="'.$tkreq_id.'" ');
						if($query->num_rows()>0)
						{	
							$result_data 	= $query->result_array();						
							$transporter_id = $result_data[0]['transporter_id'];	
							$customer_id	= $result_data[0]['customer_id'];
							
							/************* check blocked status *************/
							$block_status = $this->Tran_model->check_block_status($transporter_id);	
							if($block_status==0)
							{						
								$msg['return'] = 0;
								$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
								echo json_encode($msg);exit;
							}	
							/************* check blocked status *************/	
							
							/************* Send notificaion to customer ************/
							$condition1    	= array('user_id'=>$customer_id);
							$device_token1 	= $this->Tran_model->get_device_token('device_token','user',$condition1);
							$device_token1 	= $device_token1[0]['device_token']; 
								
							$condition1 	  = array('user_id'=>$transporter_id);
							$transporter_name = $this->Tran_model->get_username('username','user',$condition1);
							$transporter_name = $transporter_name[0]['username']; 
							
							$condition2 	= array('shipment_id'=>$shipment_id);
							$fields 		= array('pickup_location','dropup_location');
							$shipment_rs 	= $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
							$pickup_location= $shipment_rs[0]['pickup_location']; 
							$dropup_location= $shipment_rs[0]['dropup_location']; 
							
							$msgsender_id1['user_id'] = $transporter_id;	
							$message1 = "$transporter_name has cancelled the request of shipment (SHP-$shipment_id) for a location $pickup_location to $dropup_location";							
							$data1 = array(
										'sound'		=>1,
										'message'	=>$message1,
										'notifykey'	=>'shipment_request_cancel',
										'shipment_id'=>$shipment_id,
										'data'		=>$msgsender_id1		
									);
							$this->Tran_model->update_badge($customer_id);			
							if(!empty($device_token1))
							{															
								$this->Tran_model->send_android_notification($device_token1,$data1);																													
							}
							$this->db->query('delete from truck_request where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'" and truck_id="'.$truck_id.'" and request_status=0');
							
							$insertdata1['user_id'] 	 = $transporter_id ;
							$insertdata1['message'] 	 = $message1 ;							
							$insertdata1['type']    	 = 'transporter';
							$insertdata1['shipment_id']  = $shipment_id;
							$insertdata1['created_date'] = date('Y-m-d H:i:s');	
							$this->Tran_model->insert_data('truck_notification',$insertdata1);		
							/************* Send notificaion to transporter ************/
					    }
						if($type)
						{	
							$query = $this->db->query('select shipment_type from truck_shipment where shipment_id='.$shipment_id);
							if($query->num_rows()>0)
							{
								$result_data   = $query->result_array();
								$shipment_type = $result_data[0]['shipment_type'];
								$query2 = $this->db->query('update truck_shipment set dispatch_status="'.$shipment_type.'" where shipment_id="'.$shipment_id.'" ');
								
							}
						
							$msg['return'] 	=  1;
							$msg['result'] 	=  "Booking cancelled successfully";					
						}					
						else{
							$msg['return'] = 0;
							$msg['result'] = "Booking cancelled failed";
						}					
					}
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: tkreq_id,shipment_id,truck_id,type";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: tkreq_id,shipment_id,truck_id,type";
			}
			echo json_encode($msg);
		}
		/*******************************  Transporter request  by customer **********************************/
		
		
		/******************************* Myshipment(Transporter) **********************************/
		function myshipment_transport($user_id = '')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id))
				{					
					
					$block_status = $this->Tran_model->check_block_status($user_id);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
						
					$query = $this->db->query('select tr.tkreq_id,tr.shipment_id,tr.customer_id,tr.transporter_id,tr.startup_status, tr.truck_id,tr.bid_value,tr.request_status,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.pickup_date as pd,ts.load_type,ts.capacity,ts.material_type,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.platitude,ts.plongitude,ts.pickup_city,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.offered_price,ts.distance,ts.dispatch_status,ts.pickup_lat_long,ts.dropup_lat_long,tl.driver_name,tl.truck_code,tl.contact_number,tl.registration_no ,u.username as transporter_name,u.company_name as transporter_company from truck_request tr inner join truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id and u.user_id = tr.transporter_id where (tr.transporter_id="'.$user_id.'" and tr.request_status!=2) and (tr.transporter_id="'.$user_id.'" and tr.request_status!=5) and (tr.transporter_id="'.$user_id.'" and tr.request_status!=6) order by ts.pickup_date');
					// $query = $this->db->query('select tr.tkreq_id,tr.shipment_id,tr.customer_id,tr.transporter_id,tr.truck_id,tr.bid_value,tr.request_status,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.pickup_date,ts.load_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.platitude,ts.plongitude,ts.offered_price,u.username as transporter_name from truck_request tr inner join truck_shipment ts INNER JOIN user u on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id and u.user_id = tr.transporter_id where  (tr.transporter_id="'.$user_id.'" and tr.request_status!=5) and (tr.transporter_id="'.$user_id.'" and tr.request_status!=6)');
					
					// echo 'select tr.tkreq_id,tr.shipment_id,tr.customer_id,tr.transporter_id,tr.truck_id,tr.bid_value,tr.request_status,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.pickup_date,ts.load_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.platitude,ts.plongitude,ts.offered_price,tl.driver_name,tl.contact_number,tl.registration_no ,u.username as transporter_name from truck_request tr inner join truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id and u.user_id = tr.transporter_id where  (tr.transporter_id="'.$user_id.'" and tr.request_status!=5) and (tr.transporter_id="'.$user_id.'" and tr.request_status!=6)';
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();
						
						// echo "<pre>";
						// print_r($result_data);exit;
						
						for($i=0;$i<count($result_data);$i++)
						{
							$customer_id = $result_data[$i]['customer_id'];
							$shipment_id = $result_data[$i]['shipment_id'];
							// $dispatch_status = $result_data[$i]['dispatch_status'];
							
							$time_status = $this->Tran_model->get_booking_cancel_before_time($shipment_id);
							$result_data[$i]['cancel_status'] = $time_status  ;
							
							
							$query = $this->db->query('select mobile_number,username,company_name from user where user_id="'.$customer_id.'"');
							if($query->num_rows()>0)
							{
								$data = $query->row_array();								
								$result_data[$i]['username']	 = $data['username'];
								$result_data[$i]['mobile_number']= $data['mobile_number'];	
								$result_data[$i]['customer_company']= $data['company_name'];									
							}
						}
						
						
						foreach($result_data as $subKey => $subArray)
						{
							  if(($subArray['dispatch_status'] == 1 && strtotime($subArray['pd']) < strtotime(date('Y-m-d')) ) ||  ( $subArray['dispatch_status'] == 2 && strtotime($subArray['pd']) < strtotime(date('Y-m-d'))  ))
							  
							  // if(($subArray['dispatch_status'] == 1  ||   $subArray['dispatch_status'] == 2))
							  {
							    unset($result_data[$subKey]);
								$newarray = array_values($result_data);
							  }else{
								$newarray = array_values($result_data);
							  }
						}
						$msg['return'] 	=  1;
						$msg['result'] 	=  "success";
						$msg['data'] 	=  $newarray;										
					}					
					else{
						$msg['return'] = 0;
						$msg['result'] = "No shipment available";
					}
				
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id";
			}
			echo json_encode($msg);
		}
		/*******************************  My Home Screen Shipment(Transporter) **********************************/
		
		/******************************* Home Screen(driver) **********************************/
		function myshipment_driver($truck_id = '')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($truck_id))
				{					
					// $query = $this->db->query('select tr.tkreq_id,tr.shipment_id,tr.customer_id,tr.transporter_id,tr.truck_id,tr.request_status,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.pickup_date,ts.load_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,tl.driver_name,tl.contact_number,tl.registration_no ,u.username as transporter_name from truck_request tr inner join truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id and u.user_id = tr.transporter_id where tr.truck_id="'.$truck_id.'" and tr.request_status!=1 and tr.request_status!=2 and tr.request_status!=5');
					$query = $this->db->query('select tr.tkreq_id,tr.shipment_id,tr.customer_id,tr.transporter_id,tr.truck_id,tr.request_status,tr.startup_status,tr.bid_value,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dlatitude,ts.dlongitude,ts.	pickup_lat_long,ts.dropup_lat_long,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.load_type,ts.capacity,ts.material_type,ts.distance,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,tl.driver_name,tl.registration_no ,u.username as transporter_name,u.mobile_number as contact_number from truck_request tr inner join truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id and u.user_id = tr.transporter_id where (tr.truck_id="'.$truck_id.'" and tr.request_status=3) OR (tr.truck_id="'.$truck_id.'" and tr.request_status=4)');
					
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();
						for($i=0;$i<count($result_data);$i++)
						{
							$customer_id = $result_data[$i]['customer_id'];
							
							$user_id = $this->Tran_model->get_user_detail($customer_id);
							// echo "<pre>";
							// print_r($user_id);
							$result_data[$i]['username'] =  $user_id[0]['customer_name'];
							$result_data[$i]['mobile_number'] =  $user_id[0]['mobile_number'];
							
							
						}
						// $query->free_result();
						$msg['return'] 	=  1;
						$msg['result'] 	=  "success";
						$msg['data'] 	=  $result_data;										
					}					
					else{
						$msg['return'] = 0;
						$msg['result'] = "No record found";
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: truck_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: truck_id";
			}
			echo json_encode($msg);
		}
		/******************************* Home screen(Driver) **********************************/	
		
		
		/******************************* Shipment startup by driver ********************************/
		function shipment_startup($tkreq_id='',$shipment_id='',$truck_id='', $driver_id = '',$driver_name = '', $contact_number = '')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($tkreq_id)&& !empty($shipment_id)&& !empty($truck_id))
				{				
					$shp_query = $this->db->query('select request_status from truck_request where request_status=5 and tkreq_id="'.$tkreq_id.'" ');
					if($shp_query->num_rows()>0)
					{
						$msg['return'] = 0;
						$msg['result'] = "Your shipment has been cancelled";
						echo json_encode($msg);exit;
					}
					
					$query = $this->db->query('select tkreq_id,customer_id,transporter_id from truck_request where tkreq_id="'.$tkreq_id.'"');
					if($query->num_rows()>0)
					{	
						$result_data 	= $query->result_array();
						$customer_id 	= $result_data[0]['customer_id'];
						$transporter_id = $result_data[0]['transporter_id'];			
						
						$block_status = $this->Tran_model->check_block_status($customer_id);	
						if($block_status==0)
						{						
							$msg['return'] = 0;
							$msg['result'] = 'Customer has been blocked by admin';		
							echo json_encode($msg);exit;
						}	
						
						$block_status1 = $this->Tran_model->check_block_status($transporter_id);	
						if($block_status1==0)
						{						
							$msg['return'] = 0;
							$msg['result'] = 'Transporter has been blocked by admin';		
							echo json_encode($msg);exit;
						}
						
						$query_rs = $this->db->query('update truck_request set startup_status="1" where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'" and truck_id="'.$truck_id.'"');
						if($query_rs)
						{	
							
							/************* Send notificaion to customer ************/
							$condition = array('user_id'=>$customer_id);
							$device_token = $this->Tran_model->get_device_token('device_token','user',$condition);
							$device_token = $device_token[0]['device_token']; 
							
							$condition1 	  = array('truck_id'=>$truck_id);
						
									$array_data = array(
												'driver_name'=>$driver_name,
												'driver_id'=>$driver_id,
												'contact_number'=>$contact_number
											); 
							
							$this->Tran_model->update_selected_data('truck_list',$array_data,$condition1);
							$registration_no  = $this->Tran_model->get_username('registration_no','truck_list',$condition1);
							$registration_no  = $registration_no[0]['registration_no']; 
							
							$condition2 	= array('shipment_id'=>$shipment_id);
							$fields 		= array('pickup_location','dropup_location');
							$shipment_rs 	= $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
							$pickup_location= $shipment_rs[0]['pickup_location']; 
							$dropup_location= $shipment_rs[0]['dropup_location']; 
							
							$msgsender_id['user_id'] 	 = $customer_id;	
							// $message = "Your shipment (ShipmentId-$shipment_id) has been startup by driver for a location $pickup_location to $dropup_location";							
							
							$message = "Driver of truck no. ($registration_no) is on the way to pickup your shipment (ShipmentId-$shipment_id) for a location $pickup_location to $dropup_location";
							$data = array(
										'sound'		=>1,
										'message'	=>$message,
										'notifykey'	=>'shipment_startup',
										'shipment_id'=>$shipment_id,
										'driver_name'=>$driver_name,
										'contact_number'=>$contact_number,
										'data'		=>$msgsender_id		
									);
							$this->Tran_model->update_badge($customer_id);			
							if(!empty($device_token))
							{								
								$this->Tran_model->send_android_notification($device_token,$data);		
								
							}							
							$insertdata['user_id'] 		=  $customer_id ;
							$insertdata['shipment_id'] 	=  $shipment_id ;
							$insertdata['message'] 		=  $message ;							
							$insertdata['type']    		= 'customer';
							$insertdata['status'] 		=  3 ;
							$insertdata['created_date'] = date('Y-m-d H:i:s');
							$this->Tran_model->insert_data('truck_notification',$insertdata);
							/************* Send notificaion to customer **************/						
							
							
							/************* Send notificaion to transporter ************/
							$condition = array('user_id'=>$transporter_id);
							$device_token = $this->Tran_model->get_device_token('device_token','user',$condition);
							$device_token = $device_token[0]['device_token']; 
							
							$condition1 	  = array('truck_id'=>$truck_id);
									$array_data = array(
												'driver_name'=>$driver_name,
												'driver_id'=>$driver_id,
												'contact_number'=>$contact_number
											); 
							
							$this->Tran_model->update_selected_data('truck_list',$array_data,$condition1);
							$registration_no  = $this->Tran_model->get_username('registration_no','truck_list',$condition1);
							$registration_no  = $registration_no[0]['registration_no']; 
							
							$condition2 	= array('shipment_id'=>$shipment_id);
							$fields 		= array('pickup_location','dropup_location');
							$shipment_rs 	= $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
							$pickup_location= $shipment_rs[0]['pickup_location']; 
							$dropup_location= $shipment_rs[0]['dropup_location']; 
							
							$msgsender_id['user_id'] 	 = $transporter_id;	
							// $message = "Your shipment (ShipmentId-$shipment_id) has been startup by driver for a location $pickup_location to $dropup_location";							
							
							$message = "Your driver of truck no. ($registration_no) is on the way to pickup a shipment (ShipmentId-$shipment_id) for a location $pickup_location to $dropup_location";
							$data = array(
										'sound'		=>1,
										'message'	=>$message,
										'notifykey'	=>'shipment_startup',
										'shipment_id'=>$shipment_id,
										'driver_name'=>$driver_name,
										'contact_number'=>$contact_number,
										'data'		=>$msgsender_id		
									);
							$this->Tran_model->update_badge($transporter_id);			
							if(!empty($device_token))
							{								
								$this->Tran_model->send_android_notification($device_token,$data);																							
							}
							$insertdata1['user_id'] 		=  $transporter_id ;
							$insertdata1['shipment_id'] 	=  $shipment_id ;
							$insertdata1['message'] 		=  $message ;							
							$insertdata1['type']    		= 'transporter';
							$insertdata1['status'] 			=  3;
							$insertdata1['created_date'] 	= date('Y-m-d H:i:s');
							$this->Tran_model->insert_data('truck_notification',$insertdata1);							
							/************* Send notificaion to transporter **************/						
							
							$msg['return'] 	=  1;
							$msg['result'] 	=  "Shipment startup successfully";					
							
						}					
						else{
							$msg['return'] = 0;
							$msg['result'] = "Updation failed";
						}
					}else{						
						$msg['return'] = 0;
						$msg['result'] = "No record found";	
					}	
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: tkreq_id,shipment_id,truck_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: tkreq_id,shipment_id,truck_id";
			}
			echo json_encode($msg);
		}
		/*******************************  shipment dispatched by driver **********************************/
		
		
		/******************************* Shipment Dispatched by driver ********************************/
		function shipment_dispatched($tkreq_id='',$shipment_id='',$truck_id='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($tkreq_id)&& !empty($shipment_id)&& !empty($truck_id))
				{					
					
					$shp_query = $this->db->query('select request_status from truck_request where request_status=5 and tkreq_id="'.$tkreq_id.'" ');
					if($shp_query->num_rows()>0)
					{
						$msg['return'] = 0;
						$msg['result'] = "Your shipment has been cancelled";
						echo json_encode($msg);exit;
					}
					
					$query = $this->db->query('select tkreq_id,customer_id,transporter_id from truck_request where tkreq_id="'.$tkreq_id.'"');
					if($query->num_rows()>0)
					{	
						$result_data 	= $query->result_array();
						$customer_id 	= $result_data[0]['customer_id'];
						$transporter_id = $result_data[0]['transporter_id'];						
						
						$block_status = $this->Tran_model->check_block_status($customer_id);	
						if($block_status==0)
						{						
							$msg['return'] = 0;
							$msg['result'] = 'Customer has been blocked by admin';		
							echo json_encode($msg);exit;
						}	
						
						$block_status1 = $this->Tran_model->check_block_status($transporter_id);	
						if($block_status1==0)
						{						
							$msg['return'] = 0;
							$msg['result'] = 'Transporter has been blocked by admin';		
							echo json_encode($msg);exit;
						}
						
					$query_rs = $this->db->query('update truck_request set request_status="4" where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'" and truck_id="'.$truck_id.'" and request_status=3');
						if($query_rs)
						{	
							$query = $this->db->query('update truck_shipment set dispatch_status="4",pickup_date="'.date('Y-m-d').'" where shipment_id="'.$shipment_id.'" and dispatch_status=3');
							if($query)
							{
								/************* Send notificaion to customer ************/
								$condition = array('user_id'=>$customer_id);
								$device_token = $this->Tran_model->get_device_token('device_token','user',$condition);
								$device_token = $device_token[0]['device_token']; 
								
								$condition1 	  = array('truck_id'=>$truck_id);
								$registration_no  = $this->Tran_model->get_username('registration_no','truck_list',$condition1);
								$registration_no  = $registration_no[0]['registration_no']; 
								
								$condition2 	= array('shipment_id'=>$shipment_id);
								$fields 		= array('pickup_location','dropup_location');
								$shipment_rs 	= $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
								$pickup_location= $shipment_rs[0]['pickup_location']; 
								$dropup_location= $shipment_rs[0]['dropup_location']; 
								
								$msgsender_id['user_id'] 	 = $customer_id;	
								$message = "Your shipment (ShipmentId-$shipment_id) has been dispatched by the $registration_no for a location $pickup_location to $dropup_location";							
								$data = array(
											'sound'		=>1,
											'message'	=>$message,
											'notifykey'	=>'shipment_dispatched',
											'shipment_id'=>$shipment_id,
											'data'		=>$msgsender_id		
										);
								$this->Tran_model->update_badge($customer_id);			
								if(!empty($device_token))
								{								
									$this->Tran_model->send_android_notification($device_token,$data);		
								}
								
								$insertdata['user_id'] 		=  $customer_id ;
								$insertdata['shipment_id'] 	=  $shipment_id ;
								$insertdata['message'] 		=  $message ;							
								$insertdata['type']    		= 'customer';
								$insertdata['status'] 		=  4 ;
								$insertdata['created_date'] = date('Y-m-d H:i:s');
								$this->Tran_model->insert_data('truck_notification',$insertdata);
								$this->db->query('update truck_notification set status=4 where shipment_id='.$shipment_id);	
								/************* Send notificaion to customer **************/
						
								/************* Send notificaion to transporter ************/
								$condition1 = array('user_id'=>$transporter_id);
								$device_token1 = $this->Tran_model->get_device_token('device_token','user',$condition1);
								$device_token1 = $device_token1[0]['device_token']; 
								
								$condition3 	= array('user_id'=>$customer_id);
								$customer_name  = $this->Tran_model->get_username('username','user',$condition3);
								$customer_name  = $customer_name[0]['username']; 
								
								$msgsender_id1['user_id'] 	 = $transporter_id;	
								$message1 = "Shipment (SHP-$shipment_id) of $customer_name for a location $pickup_location to $dropup_location has been dispatched by $registration_no";							
								$data1 = array(
											'sound'		=>1,
											'message'	=>$message1,
											'notifykey'	=>'shipment_dispatched',
											'shipment_id'=>$shipment_id,
											'data'		=>$msgsender_id1		
										);
								$this->Tran_model->update_badge($transporter_id);			
								if(!empty($device_token1))
								{															
									$this->Tran_model->send_android_notification($device_token1,$data1);																														
								}
								
								$insertdata1['user_id'] 	= $transporter_id ;
								$insertdata1['shipment_id'] =  $shipment_id ;
								$insertdata1['message'] 	= $message1 ;
								$insertdata1['status'] 		=  4 ;								
								$insertdata1['type']    	= 'transporter';
								$insertdata1['created_date']= date('Y-m-d H:i:s');
								$this->Tran_model->insert_data('truck_notification',$insertdata1);
								$this->db->query('update truck_notification set status=4 where shipment_id='.$shipment_id);									
								/************* Send notificaion to transporter ************/
								
								$msg['return'] 	=  1;
								$msg['result'] 	=  "Request Dispatched successfully";					
							}	
						}					
						else{
							$msg['return'] = 0;
							$msg['result'] = "Updation failed";
						}
					}else{						
						$msg['return'] = 0;
						$msg['result'] = "No record found";	
					}	
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: tkreq_id,shipment_id,truck_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: tkreq_id,shipment_id,truck_id";
			}
			echo json_encode($msg);
		}
		/*******************************  shipment dispatched by driver **********************************/
		
	
		/******************************* shipment delivered by driver ********************************/
		function shipment_delivered($tkreq_id='',$shipment_id='',$truck_id='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($tkreq_id)&& !empty($shipment_id)&& !empty($truck_id))
				{					
					/************ checkout user shipment cancelled ****************/
					$shp_query = $this->db->query('select request_status from truck_request where request_status=5 and tkreq_id="'.$tkreq_id.'" ');
					if($shp_query->num_rows()>0)
					{
						$msg['return'] = 0;
						$msg['result'] = "Your shipment has been cancelled";
						echo json_encode($msg);exit;
					}
					
					
					/************ checkout user shipment cancelled ****************/					
					$query = $this->db->query('select tkreq_id,customer_id,transporter_id from truck_request where tkreq_id="'.$tkreq_id.'"');
					if($query->num_rows()>0)
					{	
						$result_data 	= $query->result_array();
						$customer_id	= $result_data[0]['customer_id'];
						$transporter_id = $result_data[0]['transporter_id'];
						
						
						/******************** check out block user **********************/
						$block_status = $this->Tran_model->check_block_status($customer_id);	
						if($block_status==0)
						{						
							$msg['return'] = 0;
							$msg['result'] = 'Customer has been blocked by admin';		
							echo json_encode($msg);exit;
						}	
						
						$block_status1 = $this->Tran_model->check_block_status($transporter_id);	
						if($block_status1==0)
						{						
							$msg['return'] = 0;
							$msg['result'] = 'Transporter has been blocked by admin';		
							echo json_encode($msg);exit;
						}
						/******************** check out block user **********************/
						
						
						$query_rs = $this->db->query('update truck_request set request_status="6",created_date="'.date('Y-m-d H:i:s').'"  where tkreq_id="'.$tkreq_id.'" and shipment_id="'.$shipment_id.'" and truck_id="'.$truck_id.'" and request_status=4');
						if($query_rs)
						{	
							$query = $this->db->query('update truck_shipment set dispatch_status="6" ,dropup_date="'.date('Y-m-d').'" where shipment_id="'.$shipment_id.'" and dispatch_status=4');
							if($query)
							{
								/************* Send notificaion to customer ************/
								$condition = array('user_id'=>$customer_id);
								$device_token = $this->Tran_model->get_device_token('device_token','user',$condition);
								$device_token = $device_token[0]['device_token']; 						

								$rs_data1 = $this->Tran_model->get_user_detail($customer_id);
								// echo "<pre>";
								// print_r($rs_data1);
								$cust_email = $rs_data1[0]['email'];

								$condition2 	= array('shipment_id'=>$shipment_id);
								$fields 		= array('pickup_location','dropup_location');
								$shipment_rs 	= $this->Tran_model->get_shipment_location($fields,'truck_shipment',$condition2);
								$pickup_location= $shipment_rs[0]['pickup_location']; 
								$dropup_location= $shipment_rs[0]['dropup_location']; 
								
								$msgsender_id['user_id'] 	 = $customer_id;	
								$message = "Your shipment has been delivered successfully for a location $pickup_location to $dropup_location";							
								$data = array(
											'sound'		=>1,
											'message'	=>$message,
											'notifykey'	=>'shipment_delivered',
											'shipment_id'=>$shipment_id,
											'data'		=>$msgsender_id	,
											'transporter_id'=>$transporter_id												
										);
								$this->Tran_model->update_badge($customer_id);			
								if(!empty($device_token))
								{								
									$this->Tran_model->send_android_notification($device_token,$data);																						
								}
									
								$insertdata['user_id'] 		=  $customer_id ;
								$insertdata['message'] 		=  $message ;							
								$insertdata['type']    		= 'customer';
								$insertdata['shipment_id'] 	=  $shipment_id ;
								$insertdata['status'] 		=  6 ;
								$insertdata['created_date'] = date('Y-m-d H:i:s');
								
								$this->Tran_model->insert_data('truck_notification',$insertdata);
								$this->db->query('update truck_notification set status=6 where shipment_id='.$shipment_id);									
								/************* Send notificaion to customer **************/
						
								/************* Send notificaion to transporter ************/
								$condition1 = array('user_id'=>$transporter_id);
								$device_token1 = $this->Tran_model->get_device_token('device_token','user',$condition1);
								$device_token1 = $device_token1[0]['device_token']; 
								
								$rs_data = $this->Tran_model->get_user_detail($transporter_id);
								$trans_email = $rs_data[0]['email'];
								
								$condition11 	  = array('truck_id'=>$truck_id);
								$registration_no  = $this->Tran_model->get_username('registration_no','truck_list',$condition11);
								$registration_no  = $registration_no[0]['registration_no']; 
								
								$condition3 	= array('user_id'=>$customer_id);
								$customer_name  = $this->Tran_model->get_username('username','user',$condition3);
								$customer_name  = $customer_name[0]['username']; 
								
								$msgsender_id1['user_id'] 	 = $transporter_id;	
								$message1 = "Shipment (SHP-$shipment_id) of $customer_name  for a location $pickup_location to $dropup_location has been delivered successfully by $registration_no";							
								$data1 = array(
											'sound'		=>1,
											'message'	=>$message1,
											'notifykey'	=>'shipment_delivered',
											'shipment_id'=>$shipment_id,
											'data'		=>$msgsender_id1,
											'transporter_id'=>$transporter_id	
										);
								$this->Tran_model->update_badge($transporter_id);	
								if(!empty($device_token1))
								{															
									$this->Tran_model->send_android_notification($device_token1,$data1);						
								}
								
								$insertdata1['user_id'] 	 = $transporter_id ;
								$insertdata1['message'] 	 = $message1 ;							
								$insertdata1['type']    	 = 'transporter';
								$insertdata1['shipment_id']  =  $shipment_id ;
								$insertdata1['status'] 		 =  6 ;
								$insertdata1['created_date'] = date('Y-m-d H:i:s');	
								$this->Tran_model->insert_data('truck_notification',$insertdata1);
								$this->db->query('update truck_notification set status=6 where shipment_id='.$shipment_id);									
								/************* Send notificaion to transporter ************/
								
								
								/************** send email ***************************/
									
									$this->generate_pdf_cod($transporter_id,$customer_id,$shipment_id,$trans_email,$cust_email,$tkreq_id);
									
									if(isset($transporter_id))
									{
										// $this->generate_pdf_cod($transporter_id,$customer_id,$shipment_id,$cust_email);
									
										// $config = Array(
													 // 'protocol' => 'smtp', //sendmail
													 // 'smtp_host' => 'localhost',									
													 // 'smtp_port' => 465,// 465 587
													 // 'smtp_user' => 'riya.sen485@gmail.com', // change it to yours
													 // 'smtp_pass' => '7ESx686WY', // change it to yours
													 // 'mailtype' => 'html',
													 // 'charset' => 'iso-8859-1',
													 // 'wordwrap' => TRUE
												// ); 	
												
										// $this->load->library('email', $config);
										// $this->email->set_mailtype("html");
										// $this->email->set_newline("\r\n");
										// $this->email->from('riya.sen485@gmail.com', "Admin Team");
										// $message2 = "Shipment booked by customer ";
										// $this->email->to($trans_email);			
										// $this->email->subject("Confirmation Shipment Delivered111");
										// $this->email->message($message2);
										
										// if($this->email->send())
										// {  
											// $msg['return'] = 1;
											// $msg['message'] = "Mail sent successfully";   
										// }
										// else
										// {
											// $data['message'] = "Sorry Unable to send email"; 
											// $msg['return'] = 0;
											// $msg['error'] = show_error($this->email->print_debugger());
										// }
										
									}
									
									if(isset($customer_id))
									{
										// $this->generate_pdf_cod($transporter_id,$customer_id,$shipment_id,$trans_email);
									
										// $config = Array(
													 // 'protocol' => 'smtp', //sendmail
													 // 'smtp_host' => 'localhost',									
													 // 'smtp_port' => 465,// 465 587
													 // 'smtp_user' => 'riya.sen485@gmail.com', // change it to yours
													 // 'smtp_pass' => '7ESx686WY', // change it to yours
													 // 'mailtype' => 'html',
													 // 'charset' => 'iso-8859-1',
													 // 'wordwrap' => TRUE
												// ); 	
												
										// $this->load->library('email', $config);
										// $this->email->set_mailtype("html");
										// $this->email->set_newline("\r\n");
										// $this->email->from('riya.sen485@gmail.com', "Admin Team");
										// $message2 = "Shipment booked by customer ";
										// $this->email->to($trans_email);			
										// $this->email->subject("Confirmation Shipment Delivered222");
										// $this->email->message($message2);
											
										// if($this->email->send())
										// {  
											// $msg['return'] = 1;
											// $msg['message'] = "Mail sent successfully";   
										// }
										// else
										// {
											// $data['message'] = "Sorry Unable to send email"; 
											// $msg['return'] = 0;
											// $msg['error'] = show_error($this->email->print_debugger());
										// }		
									} 
								
								
								/*************** send email ***********************/
								
								$msg['return'] 	=  1;
								$msg['result'] 	=  "Request delivered successfully";	
							}
											
						}					
						else{
							$msg['return'] = 0;
							$msg['result'] = "Updation failed";
						}
					}else{						
						$msg['return'] = 0;
						$msg['result'] = "No record found";	
					}	
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: tkreq_id,shipment_id,truck_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: tkreq_id,shipment_id,truck_id";
			}
			echo json_encode($msg);
		}
		/*******************************  shipment delivered by driver **********************************/
		
		
		
		/******************************* history(Transporter) **********************************/
		function history_transport($user_id = '')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id))
				{	
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($user_id);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					$query = $this->db->query('select tr.tkreq_id,tr.shipment_id,tr.customer_id,tr.startup_status,tr.transporter_id,tr.truck_id,tr.bid_value,tr.request_status,tr.created_date,ts.shipment_id,ts.pickup_location,ts.pickup_city,ts.platitude,ts.plongitude,ts.dropup_location,ts.dropup_city,ts.dlatitude,ts.dlongitude,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.load_type,ts.material_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance,tl.driver_name,tl.contact_number,tl.registration_no ,tl.truck_code,u.username,u.mobile_number,u.rating ,u.company_name as customer_company from truck_request tr inner join truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id and u.user_id = tr.customer_id where (tr.transporter_id="'.$user_id.'" and tr.request_status=5) OR (tr.transporter_id="'.$user_id.'" and tr.request_status=6) order by created_date desc');
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();
						
						for($i=0;$i<count($result_data);$i++)
						{
							$transporter_id = $result_data[$i]['transporter_id'];							
							$user_id = $this->Tran_model->get_user_detail($transporter_id);							
							// $result_data[$i]['mobile_number'] =  $user_id[0]['mobile_number'];
							// $result_data[$i]['username'] =  $user_id[0]['customer_name'];
							$result_data[$i]['transporter_company'] =  $user_id[0]['company_name'];							
						}
						$msg['return'] 	=  1;
						$msg['result'] 	=  "success";
						$msg['data'] 	=  $result_data;										
					}					
					else{
						$msg['return'] = 0;
						$msg['result'] = "No record found";
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id";
			}
		
			echo json_encode($msg);
		}
		/******************************* History (Transporter) **********************************/
		
		
		/******************************* history(Customer) **********************************/
		function history_customer($user_id = '')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id))
				{					
					$query = $this->db->query('select tr.tkreq_id,tr.shipment_id,tr.customer_id,tr.transporter_id,tr.startup_status,tr.truck_id,tr.bid_value,tr.request_status,tr.created_date,ts.shipment_id,ts.pickup_location,ts.pickup_city,ts.platitude,ts.plongitude,ts.dropup_location,ts.dropup_city,ts.dlatitude,ts.dlongitude,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.load_type,ts.material_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance,tl.driver_name,tl.contact_number,tl.registration_no ,u.username as transporter_name,u.mobile_number,u.rating,u.company_name as transporter_company from truck_request tr inner join truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id and u.user_id = tr.transporter_id where (tr.customer_id="'.$user_id.'" and tr.request_status=5) OR (tr.customer_id="'.$user_id.'" and tr.request_status=6) order by created_date desc');
					if($query->num_rows()>0)
					{
						
						/************* check blocked status *************/
						$block_status = $this->Tran_model->check_block_status($user_id);	
						if($block_status==0)
						{						
							$msg['return'] = 0;
							$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
							echo json_encode($msg);exit;
						}	
						/************* check blocked status *************/	
						
						$result_data = $query->result_array();
						for($i=0;$i<count($result_data);$i++)
						{
							$customer_id = $result_data[$i]['customer_id'];							
							$user_id = $this->Tran_model->get_user_detail($customer_id);							
							// $result_data[$i]['mobile_number'] =  $user_id[0]['mobile_number'];
							// $result_data[$i]['username'] =  $user_id[0]['customer_name'];
							$result_data[$i]['customer_company'] =  $user_id[0]['company_name'];							
						}
						$msg['return'] 	=  1;
						$msg['result'] 	=  "success";
						$msg['data'] 	=  $result_data;										
					}					
					else{
						$msg['return'] = 0;
						$msg['result'] = "No record found";
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id";
			}
			echo json_encode($msg);
		}
		/******************************* History (Customer) **********************************/
		
		
		/******************************* History(Driver) **********************************/
		function history_driver($user_id = '')
		{
			if(!empty($_REQUEST) && isset($_REQUEST)) // user_id as truck id
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id))
				{					
					$query = $this->db->query('select tr.tkreq_id,tr.shipment_id,tr.customer_id,tr.transporter_id,tr.truck_id,tr.bid_value,tr.request_status,tr.created_date,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.pickup_city,ts.dropup_city,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.platitude,ts.plongitude,ts.dlongitude,ts.dlatitude,ts.load_type,ts.capacity,ts.material_type,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance,ts.dispatch_status,tl.driver_name,tl.registration_no ,u.username as transporter_name,u.mobile_number as contact_number,u.company_name as transporter_company from truck_request tr inner join truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id and u.user_id = tr.transporter_id where 					
					(tr.truck_id="'.$user_id.'" and tr.request_status=5 ) OR (tr.truck_id="'.$user_id.'" and tr.request_status=6 and ts.dispatch_status!=1) order by created_date desc');
					
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();
						for($i=0;$i<count($result_data);$i++)
						{
							$customer_id = $result_data[$i]['customer_id'];							
							$user_id = $this->Tran_model->get_user_detail($customer_id);							
							$result_data[$i]['mobile_number'] =  $user_id[0]['mobile_number'];
							$result_data[$i]['username'] =  $user_id[0]['customer_name'];
							$result_data[$i]['customer_company'] =  $user_id[0]['company_name'];							
						}
						
						foreach($result_data as $subKey => $subArray)
						{
							  if($subArray['dispatch_status'] == 1 || $subArray['dispatch_status'] == 2){
							    unset($result_data[$subKey]);
								$newarray = array_values($result_data);
							  }else{
								$newarray = array_values($result_data);
							  }
						}
						
						$msg['return'] 	=  1;
						$msg['result'] 	=  "success";
						$msg['data'] 	=  $newarray;										
					}					
					else{
						$msg['return'] = 0;
						$msg['result'] = "No record found";
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id";
			}
		
			echo json_encode($msg);
		}
		/******************************* History (Driver) **********************************/
		
		/******************************* Send Message *************************************/
		function send_message($send_by = '',$send_to='',$message='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($send_by) && !empty($send_to)&& !empty($message))
				{
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($send_by);	
					if($block_status==0)
					{							
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					$postData['send_by'] = $send_by;
					$postData['send_to'] = $send_to;										
					$postData['message'] = $message;
					$postData['created_date'] 	= date('Y-m-d H:i:s');
									
					$result_id = $this->Tran_model->insert_data('truck_chat',$postData);
					if($result_id)
					{
						$condition    =  array('user_id'=>$send_to);						
						$result_data  =  $this->Tran_model->get_device_token('device_token','user',$condition);
						$device_token =  $result_data[0]['device_token'];
						
						$fields 	  =  array('username','image');
						$condition    =  array('user_id'=>$send_by);						
						$result_data  =  $this->Tran_model->get_device_token($fields,'user',$condition);
						$sender_name  =  $result_data[0]['username'];
						// $sender_image =  $result_data[0]['image'];
						
						$msg1 = $sender_name . " sent a new message ";
						$created_date = date('d M Y h:i:s A',strtotime($postData['created_date']));
							// DATE_FORMAT(ts.created_date,'%d %b %Y %H:%i:%s')
						$data = array(
										'sound'	=> 1,
										'send_by'=>	$send_by,
										'send_to'=>	$send_to,
										'message'=> $msg1,
										'text'=> $message,	
										'sender_name'=>$sender_name,
										'date'=>$created_date,	
										'notifykey'=>'send_message',									
								);
						// echo "<pre>";		
						// print_r($data);
						$result_data = $this->Tran_model->send_android_notification($device_token,$data);
						$msg['return'] = 1;
						$msg['result'] = "Message sent successfully";
						$msg['data']   = $data;
						
					}else{
						$msg['return'] = 0;
						$msg['result'] = "Insertion failed";
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: send_by,send_to,message";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: send_by,send_to,message";
			}
			echo json_encode($msg);
		}
		/******************************* Send Message **********************************/
		
		
		/******************************* Get Message ******************************************/
		function get_message($send_by='',$send_to='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($send_by) && !empty($send_to))
				{
					$query = $this->db->query("select *,DATE_FORMAT(created_date,'%d %b %Y %h:%i:%s %p') as created_dates,created_date from truck_chat where send_to='".$send_to."' and send_by='".$send_by."' or send_to='".$send_by."' and send_by='".$send_to."' order by  created_date  asc");
		
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();
						for($i=0;$i<count($result_data);$i++)
						{
							// $result_data[$i]['created_date'] = date('d-m-Y h:i:s a',strtotime($result_data[$i]['created_date']));
							$result_data[$i]['message'] =  $result_data[$i]['message'];
							// $result_data[$i]['message'] =  urldecode($result_data[$i]['message']);
						}
						// echo "<pre>";
						// print_r($result_data);
						$msg['return'] = 1;
						$msg['result'] = "Message sent successfully";
						$msg['data'] = $result_data;
					}else{
						$msg['return'] = 0;
						$msg['result'] = "No data found";
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: send_by,send_to";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: send_by,send_to";
			}
			echo json_encode($msg);
		}
		/******************************* Get Message **********************************/
		
		/******************************* Rating to Transporter ******************************************/
		function rating($customer_id = '',$transporter_id='',$rating='',$comment='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($customer_id) && !empty($transporter_id)&& !empty($rating))
				{
					// $query = $this->db->query('select * from rating where customer_id="'.$customer_id.'" and transporter_id="'.$transporter_id.'"');
					// if($query->num_rows()>0)
					// {
						// $msg['return'] = 1;
						// $msg['result'] = "Already rated";
							
					// }else{
						$postData['customer_id'] 	= $customer_id;
						$postData['transporter_id'] = $transporter_id;										
						$postData['rating'] 		= $rating;
						$postData['comment'] 		= $comment;
						$postData['created_date'] 	= date('Y-m-d H:i:s');
										
						$result_id = $this->Tran_model->insert_data('rating',$postData);
						if($result_id)
						{				
							$query = $this->db->query('select count(*) as total_count,sum(rating) as total_rating from rating where transporter_id="'.$transporter_id.'"');
							if($query->num_rows()>0)
							{
								$result_data = $query->row_array();
								$total_count = $result_data['total_count'];
								$total_rating= $result_data['total_rating'];								
								$avg_rating  = round($total_rating/$total_count);
								
								$query = $this->db->query('update user set rating ="'.$avg_rating.'" where user_id="'.$transporter_id.'"');
								// echo "<pre>";
								// print_r($result_data);								
							}
							$msg['return'] = 1;
							$msg['result'] = "Rated successfully";
						}else{
							$msg['return'] = 0;
							$msg['result'] = "Rating failed";
						}
					// }	
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: customer_id,transporter_id,rating,comment";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: customer_id,transporter_id,rating,comment";
			}
			echo json_encode($msg);
		}
		
		
		function get_comments($transporter_id='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($transporter_id))
				{
					// $query = $this->db->query('select * from rating where transporter_id="'.$transporter_id.'" order by created_date');
					
					$query = $this->db->query('select r.customer_id,r.transporter_id,r.rating,r.comment,r.created_date,coalesce(u.username,"") as username from rating r left join user u on r.customer_id=u.user_id where transporter_id="'.$transporter_id.'" order by created_date');
					if($query->num_rows()>0)
					{
						$result_data = $query->result_array();
						for($i=0;$i<count($result_data);$i++)
						{
							$create_date = $result_data[$i]['created_date'];
							$result_data[$i]['created_date'] = date("d-m-Y h:i:s A",strtotime($create_date));
							
						}	
						$msg['return'] = 1;
						$msg['result'] = "success";
						$msg['data']   = $result_data;						
					}else{
						$msg['return'] = 0;
						$msg['result'] = 'No rating found';
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: transporter_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: transporter_id";
			}
			echo json_encode($msg);
		}
		
		
		/******************************* Reterive password ****************************/
		function retrive_password($email='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($email))
				{
					$postData['email'] = $email;
					$query = $this->db->query('select password from user where email = "'.$email.'" ');
					if($query->num_rows()==0)
					{
						$msg['return'] = 0;
						$msg['message'] = "Email id does not exists";   
					}
					else
					{
						$query = $this->db->query('select password,username from user where email = "'.$email.'"');
						if($query->num_rows()>0)
						{
						
							$result_data = $query->result_array();
							$password = $result_data[0]['password'];
							// echo "<br>";
							$name = $result_data[0]['username'];
							
							$config = Array(
								'protocol' => 'smtp',
								 // 'mailpath' => '/usr/sbin/sendmail',
								 // 'smtp_host' => 'smtp.gmail.com',
								 // 'smtp_host' => 'ssl://smtp.gmail.com',
								'smtp_host' => 'ssl://smtp.googlemail.com', 
								'smtp_port' => 465,// 465 587
								'smtp_user' => 'support@oonir.com', // change it to yours
								'smtp_pass' => '4R!Ut-nDFgHI', // change it to yours
								'mailtype' => 'html',
								'charset' => 'iso-8859-1',
								'wordwrap' => TRUE
							); 	
								$data = array(
										'userName'=> $name,
										'password'=> $password,
										
							 );
							$this->email->set_mailtype("html");
							$body = $this->load->view('ViewEmail',$data,TRUE);
							$this->load->library('email', $config);
							$this->email->set_newline("\r\n");
							$this->email->from('support@oonir.com', "Oonir Support Team");
							$this->email->to($email);
							$this->email->subject("Forget Password Request");
							// $this->email->message("Hello " . $name);
							
							// $this->email->message(" Hello $name \n Your Password is $password");
							$this->email->message($body);   							
							if($this->email->send())
							{  
								$msg['return'] = 1;
								$msg['message'] = "Password sent successfully";   
							}
							else
							{
								$msg['return'] = 0;
								$msg['error'] = show_error($this->email->print_debugger());
								$data['message'] = "Sorry Unable to send email..."; 
							}							
						}
						else
						{
							$msg['return'] = 0;
							$msg['message'] = "no data found";   
						}
											
					}
						
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : email';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] =  "please provide parameter: email";
			}
			echo json_encode($msg);
		}
		
		
		/*********************************** view shipment listing ************************/
		function view_shipment_list($source='',$destination='',$distance='',$page_num='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($distance))
				{									
					$distance = $distance / 1.6093442;							
					if(!empty($source) && empty($destination) )
					{
						$location = $this->Tran_model->get_lat_long($source);										
						$status     = $location['status'];
						$platitude  = $location['lat'];					
						$plongitude = $location['long'];
						if($status=='-1')
						{
							$msg['return'] 	=  0;
							$msg['result'] 	=  "Shipment location not found";
							echo json_encode($msg);exit;
						}
						
						$query = $this->db->query('select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.material_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance,DATE_FORMAT(ts.created_date,"%d %b %Y") as created_date
												   ,SQRT(POW(69.1*(ts.platitude- '.$platitude.'),2)+ POW(69.1*('.$plongitude.'-ts.plongitude)* COS(ts.platitude/57.3),2)) AS distances from  truck_shipment ts where (ts.dispatch_status=1 OR ts.dispatch_status=2) and date_format(ts.pickup_date,"%Y-%m-%d") >="'.date('Y-m-d').'"HAVING distances < '.$distance.' order by ts.created_date desc');
						
							
					}						
					
					if(!empty($destination)&& empty($source))
					{
						$location = $this->Tran_model->get_lat_long($destination);										
						$status     = $location['status'];
						$dlatitude  = $location['lat'];					
						$dlongitude = $location['long'];
						if($status=='-1')
						{
							$msg['return'] 	=  0;
							$msg['result'] 	=  "Shipment location not found";
							echo json_encode($msg);exit;
						}
						
						$query = $this->db->query('select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.material_type,ts.capacity,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance ,DATE_FORMAT(ts.created_date,"%d %b %Y") as created_date
								 ,SQRT(POW(69.1*(ts.dlatitude- '.$dlatitude.'),2)+ POW(69.1*('.$dlongitude.'-ts.dlongitude)* COS(ts.dlatitude/57.3),2)) AS distances  from  truck_shipment ts where  (ts.dispatch_status=1 OR ts.dispatch_status=2) and date_format(ts.pickup_date,"%Y-%m-%d") >="'.date('Y-m-d').'" HAVING distances < '.$distance.'   order by ts.created_date desc');
															
					}
					
					if(!empty($source) && !empty($destination))
					{						
						$query = $this->db->query('select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.capacity,ts.material_type,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance ,DATE_FORMAT(ts.created_date,"%d %b %Y") as created_date
								from  truck_shipment ts where  ts.pickup_location="'.$source.'" and  ts.dropup_location="'.$destination.'" and (ts.dispatch_status=1 OR ts.dispatch_status=2) and date_format(ts.pickup_date,"%Y-%m-%d") >="'.date('Y-m-d').'" order by ts.created_date desc');					
														 
					}
					
					
					if(empty($source) && empty($destination)){							
						$query = $this->db->query('select ts.user_id as customer_id,ts.shipment_id,ts.pickup_location,ts.dropup_location,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dropup_city,ts.dlatitude,ts.dlongitude,ts.load_type,ts.capacity,ts.material_type,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.distance,DATE_FORMAT(ts.created_date,"%d %b %Y") as created_date
								from  truck_shipment ts where (ts.dispatch_status=1 OR ts.dispatch_status=2) and date_format(ts.pickup_date,"%Y-%m-%d") >="'.date('Y-m-d').'" order by ts.created_date desc');					
							
					}	
					
					if($query->num_rows()>0)
					{
						$newarray = $query->result_array();
						$msg['total_record'] = count($newarray);
						if(!empty($newarray))
						{
							if(!empty($page_num)){
								$num_rec_per_page=10; 	
								// echo "page num = ".$page_num."<br>";	
								$start_from = ($page_num-1) * $num_rec_per_page; 
								$end        = $start_from +$num_rec_per_page; 
								$count      = 1;
								for($i=$start_from;$i<$end;$i++)
								{									
									if(!empty($newarray[$i])){
											// echo "i = ".$i."<br>";
											$newarray1[] = $newarray[$i];	
										}else{
											// $user3 ='';	
											$count++;
										}
								}
								if($count>=11)
								{
									$newarray1='';
								}
								
								if(!empty($newarray1)){									
									$newarray1;
								}else{
									$newarray1 ='';
								}
							}else{
								$newarray1 = $newarray;
							}
						}			
						
						
						$msg['return'] 	=  1;
						$msg['result'] 	=  "success";					
						$msg['data'] 	=  $newarray1;							
					}					
					else{
						$msg['return'] = 0;
						$msg['result'] = "No shipment available";
						//$msg['truck_count'] = $this->Tran_model->get_truck_count($user_id);
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: source,destination,distance";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: source,destination,distance";
			}
			echo json_encode($msg);
		}
		
		function php()
		{
			echo phpinfo();
		}
		
		
		/******************************* Home Screen Shipment(Transporter) **********************************/
		function generate_pdf_cod($transporter_id='',$customer_id='',$shipment_id='',$trans_email='',$cust_email='',$tkreq_id='')
		{			
			extract($_REQUEST);
			$query = $this->db->query('select username,mobile_number,location from user where user_id='.$transporter_id);
			if($query->num_rows()>0)
			{
				
				$result_data1 		= $query->result_array();
					
				$transporter_name_full 	= $result_data1[0]['username'];
				if (strlen($transporter_name_full) > 10)
				{
					// $transporter_name = substr($transporter_name_full, 0, 35).'..';
					$transporter_name = $transporter_name_full;
				}else{
						$transporter_name = $transporter_name_full;
				}
				$transporter_number = $result_data1[0]['mobile_number'];
				$transporter_location = $result_data1[0]['location'];
				// echo "<pre>";
				// print_r($result_data1);
				
			}
			
			$query1 = $this->db->query('select username,mobile_number,location from user where user_id='.$customer_id);
			if($query1->num_rows()>0)
			{
				$result_data 		= $query1->result_array();
				$customer_name_full = $result_data[0]['username'];
				if (strlen($customer_name_full) > 10)
				{
					// $customer_name = substr($customer_name_full, 0, 15) .'..';
					$customer_name = $customer_name_full;
				}else{
						$customer_name = $customer_name_full;
				}
				$customer_number	= $result_data[0]['mobile_number'];
				$customer_location 	= $result_data[0]['location'];
				
				// echo "<pre>";
				// print_r($result_data1);exit;
				
			}
			
			$query2 = $this->db->query('select *,date_format(pickup_date,"%d-%m-%Y") as pickup_date,date_format(dropup_date,"%d-%m-%Y") as dropup_date from truck_shipment where shipment_id='.$shipment_id);
			// $query2 = $this->db->query('select ts.*,tl.registration_no from truck_shipment ts INNER JOIN truck_list tl on ts.truck_id=tl.truck_id where shipment_id='.$shipment_id);
					
			if($query2->num_rows()>0)
			{
				
				$result_data2		= $query2->result_array();
				// echo "<pre>";
				// print_r($result_data2);
				// exit;
				$pickup_location 	= $result_data2[0]['pickup_location'];
				$dropup_location    = $result_data2[0]['dropup_location'];
				$pickup_date	    = $result_data2[0]['pickup_date'];
				$load_type	    	= $result_data2[0]['load_type'];
				$capacity	        = $result_data2[0]['capacity'];
				$offered_price	    = $result_data2[0]['offered_price'];
				$distance			= $result_data2[0]['distance'];
				$dropup_date	    = $result_data2[0]['dropup_date'];
				$material_type	    = $result_data2[0]['material_type'];
			
			}
			
			$query3 = $this->db->query('select tl.driver_name,tl.registration_no,tl.contact_number from truck_request tr INNER JOIN truck_list tl on tr.truck_id=tl.truck_id where tkreq_id='.$tkreq_id);
				
			if($query3->num_rows()>0)
			{
				$result_data3	= $query3->result_array();
				$driver_name 	= $result_data3[0]['driver_name'];
				$registration_no= $result_data3[0]['registration_no'];
				$driver_number	= $result_data3[0]['contact_number'];			
			}
		
				$date = date('Y-m-d'); 
				
				$PATH = "application/fpdf/fpdf.php";			
				$this->load->file($PATH, true);		
				$pdf = new FPDF();
				$pdf->AddPage();
				
				/**************  Header ***********/
				 $pdf->SetXY(5,5);		
				 $pdf->Image('application/upload/image/logo.png',10,6,35);			
				 // $pdf->SetFont('Arial','B',15);			
				 $pdf->Cell(80,15); 			
				 $pdf->Ln(20);
				
				/*************** Header ***********/
				
				$pdf->SetXY(5,15);		
				$pdf->SetFillColor(230,230,0);
				$pdf->SetDrawColor(0,80,180);			
				$pdf->SetFont('Arial','B');
				$pdf->Cell(200,8,"Shipment Delivered Invoice ",1,1,'C');
				
				
				
				/* ################## Transporter #################### */
				$pdf->SetXY(10,20);			
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B');
				$pdf->Cell(15,15,"Transporter ",0,0,'L');
				
				if(strlen($transporter_name)>20)
				{
					$lenght = strlen($transporter_name);
					$start_name = substr($transporter_name,0,20);
					$remain_name = substr($transporter_name,20,$lenght);
					
					
					$pdf->SetXY(10,22);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Name",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(5,25,': '.$start_name,0,0,'L');
					$pdf->SetXY(45,27);			
					$pdf->Cell(5,25,': '.$remain_name,0,0,'L');
					$pdf->SetTextColor(0,0,0);
					
					$pdf->SetXY(10,35);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Mobile Number",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(5,25,': '.$transporter_number,0,0,'L');
					$pdf->SetTextColor(0,0,0);
					
					
				}else{
						
					$pdf->SetXY(10,22);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Name",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(5,25,': '.$transporter_name,0,0,'L');
					$pdf->SetTextColor(0,0,0);
					
					$pdf->SetXY(10,27);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Mobile Number",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(5,25,': '.$transporter_number,0,0,'L');
					$pdf->SetTextColor(0,0,0);
				}

				
				
				// $pdf->SetXY(10,30);			
				// $pdf->SetTextColor(0,0,0);
				// $pdf->SetFont('Arial','B');
				// $pdf->Cell(35,25,"Location",0,0,'L');
				// $pdf->SetFont('Arial','');
				// $pdf->Cell(5,25,': '.$transporter_location,0,0,'L');
				// $pdf->SetTextColor(0,0,0);						
				/* ################## Transporter #################### */
				
				
				/* ################# Customer #################### */
				$pdf->SetXY(120,20);			
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B');
				$pdf->Cell(15,15,"Customer ",0,0,'L');
				
				if(strlen($transporter_name)>20)
				{
					$lenght = strlen($transporter_name);
					$start_name = substr($transporter_name,0,20);
					$remain_name = substr($transporter_name,20,$lenght);
					
					$pdf->SetXY(120,22);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Name",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(15,25,': '.$start_name,0,0,'L');	
					$pdf->SetXY(155,27);					
					$pdf->Cell(15,25,': '.$remain_name,0,0,'L');	
					
					$pdf->SetXY(120,35);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Mobile Number",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(15,25,': '.$customer_number,0,0,'L');
					
				}else{
					
					$pdf->SetXY(120,22);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Name",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(15,25,': '.$customer_name,0,0,'L');							
					
					$pdf->SetXY(120,27);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Mobile Number",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(15,25,': '.$customer_number,0,0,'L');
					
				}	
					
				
				// $pdf->SetXY(140,30);			
				// $pdf->SetTextColor(0,0,0);
				// $pdf->SetFont('Arial','B');
				// $pdf->Cell(35,25,"Location",0,0,'L');
				// $pdf->SetFont('Arial','');
				// $pdf->Cell(15,25,': '.$customer_location,0,0,'L');
				/* ####################### customer ################# */
				
				
				$pdf->SetXY(5,55);		
				$pdf->SetFillColor(230,230,0);
				$pdf->SetDrawColor(0,80,180);			
				$pdf->SetFont('Arial','B');
				$pdf->Cell(200,8,"Shipment Detail ",1,1,'C');
				
				
				/* ################# Shipment #################### */
					$pdf->SetXY(10,60);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(15,15,"Shipment Detail : ",0,0,'L');
						
					
					$pdf->SetXY(10,62);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Shipment Id",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(15,25,': SHP-'.$shipment_id,0,0,'L');
					
					$pdf->SetXY(10,67);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Pickup Location",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(15,25,': '.$pickup_location,0,0,'L');
					
					$pdf->SetXY(10,72);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Delivery Location",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$dropup_location,0,0,'L');
					
					$pdf->SetXY(10,77);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Pickup Date",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$pickup_date,0,0,'L');
					
					$pdf->SetXY(10,82);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Delivery Date",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$dropup_date,0,0,'L');
					
					$pdf->SetXY(10,87);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Amount",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': Rs. '.$offered_price,0,0,'L');
					
					$pdf->SetXY(10,92);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Weight",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$capacity." Ton",0,0,'L');		

					$pdf->SetXY(10,97);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Distance",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$distance,0,0,'L');		
					
					$pdf->SetXY(10,102);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Load type",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$load_type,0,0,'L');
				
					
					$pdf->SetXY(10,107);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Driver Name",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$driver_name,0,0,'L');
					
					$pdf->SetXY(10,112);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Truck No.",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$registration_no,0,0,'L');
					
					$pdf->SetXY(10,117);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Driver Contact",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$driver_number,0,0,'L');
					
					$pdf->SetXY(10,122);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Material Type",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$material_type,0,0,'L');
				
					
				/* ####################### shipment ################# */
				
				/* ####################### shipment ################# */
			
				/************* Footer ***********/				
				$pdf->SetXY(10,150);		
				$pdf->SetFillColor(230,230,0);
				$pdf->SetDrawColor(0,0,0);			
				$pdf->SetFont('Arial','B');
				// $pdf->Cell(150,40,"DECLARATION: We here by inform that this invoice \n\n
				// shows the actual </br>price of the goods described incluve of taxes and that all ",1,1,'LRTB');
				
				$pdf->Multicell(0,3,"\n DECLARATION: \n\n 
										We declare that this invoice shows the actual price of shipment transportation \n
										described inclusive of taxes and that all particulars are true and correct. \n										
										\n
										CUSTOMER ACKNOWLEGEMENT : 
										\n\n I here by confirm that i further understand with term and condition \n
										\n\n",1,1,'');										
				
			$random_no = $this->Tran_model->random_string();	
			$filename = "invoiceSH-$random_no$shipment_id.pdf";
			$pdfdoc = $pdf->Output("", "S");
			$attachment = chunk_split(base64_encode($pdfdoc));
			
			$new_dir = dirname(dirname(__FILE__))."/upload/pdf/".$filename;
			move_uploaded_file($pdfdoc, $new_dir);
			// echo $output1 = $pdf->Output();
			// exit;
			$fp = fopen($new_dir,"wb");
			fwrite($fp,$pdfdoc);
			fclose($fp);
						
			$config = Array(
						 'protocol' => 'smtp', //sendmail
						 'smtp_host' => 'localhost',									
						 'smtp_port' => 465,// 465 587
						 'smtp_user' => 'support@oonir.com', // change it to yours riya.sen485@gmail.com
						 'smtp_pass' => '4R!Ut-nDFgHI', // change it to yours 7ESx686WY
						 'mailtype' => 'html/text',
						 'charset' => 'iso-8859-1',
						 'wordwrap' => TRUE
					); 	
			// $message2 = "invoice sent ";
			
			if(isset($trans_email))
			{	
				// echo "inside 111";
				$message2  = "<body style='background-color: #F1E4E4;padding:15px;border-radius:3px'>";
				$message2 .= "<p>Hello $transporter_name_full,</p>";
				$message2 .="<p>Thank you for using oonir app, Please find the attachment for your invoice. </p></br><br>";
				$message2 .="<p>Thank you,</p>";
				$message2 .="<p>Oonir Support Team</p>";
				$message2 .="<p>Address :</p>";
				$message2 .="<p>26/3/2555, Sapthagiri colony,<br /> BV Nagar, Mini bypass road,<br /> opp GVRR College Nellore, <br />Pincode 524001,<br /> Contact Number : +91-8612313177<br /><br /><br /></p>";
				$message2 .="<p>Disclaimer : This is system auto generated email. Please do not reply on this email. If you have any concern then please contact us on helpdesk@oonir.com
				</p>";	
				$message2 .="</body>";
				
				$new_pdf = $new_dir;
				$this->load->library('email', $config);
				$this->email->set_mailtype("html");
				$this->email->set_newline("\r\n");			
				$this->email->from('support@oonir.com', "Oonir Support Team");			
				$this->email->to($trans_email);	
				//$this->email->to('testteam47@gmail.com');	
				$this->email->subject("Shipment Delivered Invoice");
				$this->email->message($message2);
				$this->email->attach($new_pdf);
				if($this->email->send())
				{  
					$msg['return'] = 1;
					$msg['message'] = "Mail sent successfully";   
				}
				else
				{
					$data['message'] = "Sorry Unable to send email"; 
					$msg['return'] = 0;
					$msg['error'] = show_error($this->email->print_debugger());
				}
			}

			if(isset($cust_email))
			{	
				$message3  = "<body style='background-color: #F1E4E4;padding:15px;border-radius:3px'>";
				$message3 .= "<p>Hello $customer_name_full,</p>";
				$message3 .="<p>Thank you for using oonir app, Please find the attachment for your invoice. </p></br><br>";
				$message3 .="<p>Thank you,</p>";
				$message3 .="<p>Oonir Support Team</p>";
				$message3 .="<p>Address :</p>";
				$message3 .="<p>26/3/2555, Sapthagiri colony,<br /> BV Nagar, Mini bypass road,<br /> opp GVRR College Nellore, <br />Pincode 524001,<br /> Contact Number : +91-8612313177<br /><br /><br /></p>";
				$message3 .="<p>Disclaimer : This is system auto generated email. Please do not reply on this email. If you have any concern then please contact us on helpdesk@oonir.com
				</p>";	
				$message3 .="</body>";

				$this->load->library('email', $config);
				$this->email->set_mailtype("html");
				$this->email->set_newline("\r\n");					
				$this->email->from('support@oonir.com', "Oonir Support Team");			
				$this->email->to($cust_email);					
				$this->email->subject("Shipment Delivered Invoice");
				$this->email->message($message3);
				// $this->email->attach($new_dir);
				if($this->email->send())
				{  
					$msg['return'] = 1;
					$msg['message'] = "Mail sent successfully";   
				}
				else
				{
					$data['message'] = "Sorry Unable to send email"; 
					$msg['return'] = 0;
					$msg['error'] = show_error($this->email->print_debugger());
				}
			}
			
			
			/***********************************/
		
			
			// echo json_encode($msg);
		
		}

		
		/******************************* Update driver position ******************************************/
		function update_driver_position($user_id='',$latitude='',$longitude='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST)) // user_id as truckid
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id) && !empty($latitude) && !empty($longitude) )
				{
					$query = $this->db->query("update truck_list set latitude='".$latitude."' ,longitude='".$longitude."' where truck_id='".$user_id."'");
					
					if($query)
					{
						$query1 = $this->db->query('select latitude,longitude from truck_list where truck_id='.$user_id);
						if($query1->num_rows()>0)
						{
							$result_data = $query1->row_array();
							// echo "<pre>";
							// print_r($result_data);						
						}
						$msg['return'] = 1;
						$msg['result'] = "Position updated successfully";
						$msg['data'] = $result_data;
					}else{
						$msg['return'] = 0;
						$msg['result'] = "No data found";
					}
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id,latitude,longitude";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id,latitude,longitude";
			}
			echo json_encode($msg);
		}
		/******************************* Update driver position **********************************/
		
		/******************************* Update driver position ******************************************/
		function get_driver_position($user_id='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST)) // user_id as truckid
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id))
				{
					// $query1 = $this->db->query('select latitude,longitude from truck_list where truck_id='.$user_id);
					$query1 = $this->db->query('select * from driver_report where truck_id="'.$user_id.'" order by rp_id desc limit 1');
					if($query1->num_rows()>0)
					{
						$result_data = $query1->row_array();
						// echo "<pre>";
						// print_r($result_data);						
					}else{
						$msg['return'] 	= 0;
					$msg['result'] 	= "failure";
					echo json_encode($msg);exit;
					}
					$msg['return'] 	= 1;
					$msg['result'] 	= "success";
					$msg['data'] 	= $result_data;
				
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id";
			}
			echo json_encode($msg);
		}
		
		/******************************* avg_shipment_price **********************************/
		function avg_shipment_price($pickup_location='',$dropup_location='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($pickup_location)&& !empty($dropup_location))
				{
					// echo 'select  max(offered_price) as max_price, min(offered_price) as min_price,round(avg(offered_price),2) as avg_price from truck_shipment where pickup_location="'.$pickup_location.'" and dropup_location="'.$dropup_location.'" and created_date >= now()-interval 3 month';
					$query = $this->db->query('select  max(offered_price) as max_price, min(offered_price) as min_price,round(avg(offered_price),2) as avg_price from truck_shipment where pickup_location="'.$pickup_location.'" and dropup_location="'.$dropup_location.'" and created_date >= DATE_SUB(now(), INTERVAL 3 MONTH) ');
					if($query->num_rows()>0)
					{						
						$result_data = $query->row_array();
						if($result_data['max_price']=='')
						{
							$result_data['max_price'] ='';	
						}
						
						if($result_data['min_price']=='')
						{
							$result_data['min_price'] ='';	
						}
						
						if($result_data['avg_price']=='')
						{
							$result_data['avg_price'] ='';	
						}
						// echo "<pre>";
						// print_r($result_data);
						$msg['return'] 	= 1;
						$msg['result'] 	= "success";
						$msg['data'] 	= $result_data;
					}else{
						$msg['return'] = 0;
						$msg['result'] = "No record found";
					}
					
				}else{
					$msg['return'] = 0;
					$msg['result'] = "please provide parameter: user_id";
				}
			}else{
				$msg['return'] = 0;
				$msg['result'] = "please provide parameter: user_id";
			}
			echo json_encode($msg);
		}	
	
		/************************************** Login  *********************************/
		function logout($user_id = '')
		{		
			if(!empty($_REQUEST) && isset($_REQUEST))
			{			
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id))
				{						
					$data =  array('device_token'=>'');
					$query = $this->db->update('user',$data,array('user_id'=>$user_id));	
					
					if($query)
					{				
						$msg['return'] = 1;
						$msg['result'] = 'Logout successfully';								
					}else{
						$msg['return'] = 0;
						$msg['result'] = 'Failed to logout';						
					}
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : user_id';					
				}
				
			}else{
				$msg['return'] = 0;
				$msg['result'] =  "please provide parameter: user_id";
			}	
			echo json_encode($msg);
		}
		
		
		function get_badge($user_id='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{			
				extract($_REQUEST);
				$postData =array();
				if(!empty($user_id))
				{					
					$query = $this->db->query('select badge from user where user_id="'.$user_id.'"');
					if($query->num_rows()>0)
					{
						$result = $query->row_array();
						$badge = $result['badge'];
						$msg['return'] 	= 1;
						$msg['result'] 	= "success";
						$msg['data'] 	= $badge;
					}	
				}else{
					$msg['return'] = 0;
					$msg['result'] =  "please provide parameter: user_id";
				}
			}	
			echo json_encode($msg);
		}
		
		
		function clear($email_id)
		{
			extract($_REQUEST);
			$this->db->query('delete from user where email="'.$email_id.'" ');
		}
		
		function get_shipment_detail($shipment_id='')
		{
			
			if(!empty($_REQUEST) && isset($_REQUEST))
			{			
				extract($_REQUEST);
				$postData =array();
				if(!empty($shipment_id))
				{
						// $query = $this->db->query('select tr.tkreq_id,tr.shipment_id,tr.customer_id,tr.transporter_id,tr.truck_id,tr.request_status,tr.startup_status,tr.bid_value,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dlatitude,ts.dlongitude,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.load_type,ts.capacity,ts.material_type,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,tl.driver_name,tl.registration_no ,u.username as transporter_name,u.mobile_number as contact_number from truck_request tr inner join truck_shipment ts INNER JOIN truck_list tl INNER JOIN user u on ts.shipment_id=tr.shipment_id and tl.truck_id=tr.truck_id and u.user_id = tr.transporter_id where (tr.truck_id="'.$truck_id.'" and tr.request_status=3) OR (tr.truck_id="'.$truck_id.'" and tr.request_status=4)');
					
					
					$query = $this->db->query('select tr.tkreq_id,tr.request_status ,tr.shipment_type,tr.truck_id,tr.bid_value,ts.shipment_id,ts.pickup_location,ts.dropup_location,ts.pickup_city,ts.dropup_city,ts.platitude,ts.plongitude,ts.dlatitude,ts.dlongitude,DATE_FORMAT(ts.pickup_date,"%d %b %Y") as pickup_date,ts.load_type,ts.capacity,ts.material_type,ts.tracking,ts.sharing,ts.shipment_type,ts.offered_price,ts.dispatch_status,u.user_id as transporter_id,u.username as transporter_name,if(u.rating=0,"-1",u.rating) as rating,u.mobile_number from truck_request tr inner join truck_shipment ts INNER JOIN user u on ts.shipment_id=tr.shipment_id and u.user_id = tr.transporter_id where  tr.shipment_id="'.$shipment_id.'"');		
					
					if($query->num_rows()>0)
					{
						$result = $query->row_array();						
						$msg['return'] 	= 1;
						$msg['result'] 	= "success";
						$msg['data'] 	= $result;
					}else{
						$msg['return'] 	= 1;
						$msg['result'] 	= "success";				
					}	
				}else{
					$msg['return'] = 0;
					$msg['result'] =  "please provide parameter: shipment_id";
				}
			}	
			echo json_encode($msg);
														
			
		}
		
		function generate_pdf_cancel($transporter_id='',$customer_id='',$shipment_id='',$email='',$user_type='',$tkreq_id='')
		{			
			extract($_REQUEST);
			$query = $this->db->query('select username,mobile_number,location from user where user_id='.$transporter_id);
			if($query->num_rows()>0)
			{
				$result_data1 		= $query->result_array();
				$transporter_name_full 	= $result_data1[0]['username'];
				if (strlen($transporter_name_full) > 10)
				{
					// $transporter_name = substr($transporter_name_full, 0, 35).'..';
					$transporter_name =  $transporter_name_full;
				}else{
					$transporter_name =  $transporter_name_full;
					
				}
				$transporter_number = $result_data1[0]['mobile_number'];
				$transporter_location = $result_data1[0]['location'];
				// echo "<pre>";
				// print_r($result_data1);
				
			}
			
			$query1 = $this->db->query('select username,mobile_number,location from user where user_id='.$customer_id);
			if($query1->num_rows()>0)
			{
				$result_data 		= $query1->result_array();
				$customer_name_full 		= $result_data[0]['username'];
				if (strlen($customer_name_full) > 10)
				{
					// $customer_name = substr($customer_name_full, 0, 20) . '..';
					$customer_name =  $customer_name_full;
				}else{
					
					$customer_name =  $customer_name_full;
				}
				$customer_number	= $result_data[0]['mobile_number'];
				$customer_location 	= $result_data[0]['location'];
					
			}
			
			$query2 = $this->db->query('select *,date_format(pickup_date,"%d-%m-%Y") as pickup_date,date_format(dropup_date,"%d-%m-%Y") as dropup_date from truck_shipment where shipment_id='.$shipment_id);
			// $query2 = $this->db->query('select ts.*,tl.registration_no from truck_shipment ts INNER JOIN truck_list tl on ts.truck_id=tl.truck_id where shipment_id='.$shipment_id);
					
			if($query2->num_rows()>0)
			{
				$result_data2		= $query2->result_array();
				$pickup_location 	= $result_data2[0]['pickup_location'];
				$dropup_location    = $result_data2[0]['dropup_location'];
				$pickup_date	    = $result_data2[0]['pickup_date'];
				$load_type	    	= $result_data2[0]['load_type'];
				$capacity	        = $result_data2[0]['capacity']." Tons";
				$offered_price	    = "Rs. ".$result_data2[0]['offered_price'];
				$offered_price2	    =  $result_data2[0]['offered_price'];
				$distance			= $result_data2[0]['distance'];
				$dropup_date	    = $result_data2[0]['dropup_date'];			
				$material_type	    = $result_data2[0]['material_type'];			
			}
			
			$cancel_price = "Rs. ".($offered_price2 * 5)/100;
			
			$query3 = $this->db->query('select tl.driver_name,tl.registration_no,tl.contact_number from truck_request tr INNER JOIN truck_list tl on tr.truck_id=tl.truck_id where tkreq_id='.$tkreq_id);
					
			if($query3->num_rows()>0)
			{
				$result_data3	= $query3->result_array();
				$driver_name 	= $result_data3[0]['driver_name'];
				$registration_no= $result_data3[0]['registration_no'];
				$driver_number	= $result_data3[0]['contact_number'];			
			}
		
			$date = date('Y-m-d'); 
				
			
			$PATH = "application/fpdf/fpdf.php";			
				$this->load->file($PATH, true);		
				$pdf = new FPDF();
				$pdf->AddPage();
				
				/**************  Header ***********/
				 $pdf->SetXY(5,5);		
				 $pdf->Image('application/upload/image/logo.png',10,6,35);			
				 // $pdf->SetFont('Arial','B',15);			
				 $pdf->Cell(80,15); 			
				 $pdf->Ln(20);
				
				/*************** Header ***********/
				
				$pdf->SetXY(5,15);		
				$pdf->SetFillColor(230,230,0);
				$pdf->SetDrawColor(0,80,180);			
				$pdf->SetFont('Arial','B');
				$pdf->Cell(200,8,"Shipment Cancel Invoice ",1,1,'C');
				
				
				
				/* ################## Transporter #################### */
				$pdf->SetXY(10,20);			
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B');
				$pdf->Cell(15,15,"Transporter ",0,0,'L');
				
				if(strlen($transporter_name)>20)
				{
					$lenght = strlen($transporter_name);
					$start_name = substr($transporter_name,0,20);
					$remain_name = substr($transporter_name,20,$lenght);
					$pdf->SetXY(10,22);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Name",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(5,25,': '.$start_name,0,0,'L');
					$pdf->SetXY(47,25);			
					$pdf->Cell(5,30,$remain_name,0,0,'L');
					$pdf->SetTextColor(0,0,0);
					
					$pdf->SetXY(10,35);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Mobile Number",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(5,25,': '.$transporter_number,0,0,'L');
					$pdf->SetTextColor(0,0,0);
					
				}else{
					$pdf->SetXY(10,22);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Name",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(5,25,': '.$transporter_name,0,0,'L');
					$pdf->SetTextColor(0,0,0);	

					$pdf->SetXY(10,27);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Mobile Number",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(5,25,': '.$transporter_number,0,0,'L');
					$pdf->SetTextColor(0,0,0);					
					
				}				
				
				
				
				// $pdf->SetXY(10,30);			
				// $pdf->SetTextColor(0,0,0);
				// $pdf->SetFont('Arial','B');
				// $pdf->Cell(35,25,"Location",0,0,'L');
				// $pdf->SetFont('Arial','');
				// $pdf->Cell(5,25,': '.$transporter_location,0,0,'L');
				// $pdf->SetTextColor(0,0,0);						
				/* ################## Transporter #################### */
				
				
				/* ################# Customer #################### */
				$pdf->SetXY(120,20);			
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B');
				$pdf->Cell(15,15,"Customer ",0,0,'L');
				
				if(strlen($customer_name)>20)
				{
					$lenght = strlen($customer_name);
					$start_name = substr($customer_name,0,20);
					$remain_name = substr($customer_name,20,$lenght);
					
					$pdf->SetXY(120,22);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Name",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(15,25,': '.$start_name,0,0,'L');							
					$pdf->SetXY(155,25);			
					$pdf->Cell(15,30,$remain_name,0,0,'L');
					
					$pdf->SetXY(120,35);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Mobile Number",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(15,25,': '.$customer_number,0,0,'L');
				}else{
					
					$pdf->SetXY(120,22);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Name",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(15,25,': '.$customer_name,0,0,'L');							
					
					$pdf->SetXY(120,27);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(35,25,"Mobile Number",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(15,25,': '.$customer_number,0,0,'L');
				}

				
				// $pdf->SetXY(140,30);			
				// $pdf->SetTextColor(0,0,0);
				// $pdf->SetFont('Arial','B');
				// $pdf->Cell(35,25,"Location",0,0,'L');
				// $pdf->SetFont('Arial','');
				// $pdf->Cell(15,25,': '.$customer_location,0,0,'L');
				/* ####################### customer ################# */
				
				
				$pdf->SetXY(5,55);		
				$pdf->SetFillColor(230,230,0);
				$pdf->SetDrawColor(0,80,180);			
				$pdf->SetFont('Arial','B');
				$pdf->Cell(200,8,"Shipment Detail ",1,1,'C');
				
				
				/* ################# Shipment #################### */
				
					$pdf->SetXY(10,60);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(15,15,"Shipment Detail : (Cancel by $user_type on $date )",0,0,'L');
						
					
					$pdf->SetXY(10,62);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Shipment Id",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(50,25,': SHP-'.$shipment_id,0,0,'L');
					
					$pdf->SetXY(10,67);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Pickup Location",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(15,25,': '.$pickup_location,0,0,'L');
					
					$pdf->SetXY(10,72);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Dropoff Location",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$dropup_location,0,0,'L');
					
					$pdf->SetXY(10,77);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Pickup Date",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$pickup_date,0,0,'L');
					
					$pdf->SetXY(10,82);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Delivery Date",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': N/A',0,0,'L');
					
					$pdf->SetXY(10,87);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Amount",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$offered_price,0,0,'L');
					
					$pdf->SetXY(10,92);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Weight",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$capacity,0,0,'L');										
					
					$pdf->SetXY(10,97);		
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Distance",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$distance,0,0,'L');	
					
					$pdf->SetXY(10,102);	
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Load type",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$load_type,0,0,'L');
					
					/* $cancel_price = "Rs. ".($offered_price*5)/100; */
					/* $cancel_price = $offered_price*5; */
					$pdf->SetXY(10,107);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Cancellation Charge",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$cancel_price,0,0,'L');
					
					$pdf->SetXY(10,112);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Driver Name",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$driver_name,0,0,'L');
					
					$pdf->SetXY(10,117);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Truck No.",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$registration_no,0,0,'L');
					
					$pdf->SetXY(10,122);			
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Driver Contact",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$driver_number,0,0,'L');
					
					$pdf->SetXY(10,127);		
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','B');
					$pdf->Cell(50,25,"Material Type",0,0,'L');
					$pdf->SetFont('Arial','');
					$pdf->Cell(20,25,': '.$material_type,0,0,'L');	
					
					
				/* ####################### shipment ################# */
				
				/* ####################### shipment ################# */
			
				/************* Footer ***********/				
				$pdf->SetXY(10,150);		
				$pdf->SetFillColor(230,230,0);
				$pdf->SetDrawColor(0,0,0);			
				$pdf->SetFont('Arial','B');
				// $pdf->Cell(150,40,"DECLARATION: We here by inform that this invoice \n\n
				// shows the actual </br>price of the goods described incluve of taxes and that all ",1,1,'LRTB');
				
				$pdf->Multicell(0,3,"\n DECLARATION: \n\n 
										We declare that this invoice shows the actual price of shipment transportation \n
										described inclusive of taxes and that all particulars are true and correct. \n										
										\n
										CUSTOMER ACKNOWLEGEMENT : 
										\n\n I here by confirm that i further understand with term and condition \n
										\n\n",1,1,'');
				
				
			$random_no = $this->Tran_model->random_string();	
			$filename = "invoiceSH-$random_no$shipment_id.pdf";
			$pdfdoc = $pdf->Output("", "S");
			$attachment = chunk_split(base64_encode($pdfdoc));
			
			$new_dir = dirname(dirname(__FILE__))."/upload/pdf/".$filename;
			move_uploaded_file($pdfdoc, $new_dir);
			 $output1 = $pdf->Output();
			// exit;
			$fp = fopen($new_dir,"wb");
			fwrite($fp,$pdfdoc);
			fclose($fp);
						
			$config = Array(
						 'protocol' => 'smtp', //sendmail
						 'smtp_host' => 'localhost',									
						 'smtp_port' => 465,// 465 587
						 'smtp_user' => 'support@oonir.com', // change it to yours riya.sen485@gmail.com
						 'smtp_pass' => '4R!Ut-nDFgHI', // change it to yours 7ESx686WY
						 'mailtype' => 'html/text',
						 'charset' => 'iso-8859-1',
						 'wordwrap' => TRUE
					); 
					// echo "type ".$type;
			if($user_type=='customer')
			{			
				// echo $user_type;
				
				$message2  = "<body style='background-color: #F1E4E4;padding:15px;border-radius:3px'>";
				$message2 .= "<p>Hello $customer_name_full,</p>";
				$message2 .="<p>Thank you for using oonir app, Please find the attachment for your invoice. </p></br><br>";
				$message2 .="<p>Thank you,</p>";
				$message2 .="<p>Oonir Support Team</p>";
				$message2 .="<p>Address :</p>";
				$message2 .="<p>26/3/2555, Sapthagiri colony, <br />BV Nagar, Mini bypass road,<br /> opp GVRR College Nellore,<br />Pincode 524001,<br /> Contact Number : +91-8612313177<br /><br /></p>";
				$message2 .="<p>Disclaimer : This is system auto generated email. Please do not reply on this email. If you have any concern then please contact us on helpdesk@oonir.com
				</p>";	
				$message2 .="</body>";				
			}

			if($user_type=='transporter')
			{	
				// echo $user_type;
				$message2  = "<body style='background-color: #F1E4E4;padding:15px;border-radius:3px'>";
				$message2 .= "<p>Hello $customer_name_full,</p>";
				$message2 .="<p>Thank you for using oonir app, Please find the attachment for your invoice. </p></br><br>";
				$message2 .="<p>Thank you,</p>";
				$message2 .="<p>Oonir Support Team</p>";
				$message2 .="<p>Address :</p>";
				$message2 .="<p>26/3/2555, Sapthagiri colony,<br /> BV Nagar, Mini bypass road,<br /> opp GVRR College Nellore,<br /> Pincode 524001,<br /> Contact Number : +91-8612313177</p><br /><br />";
				$message2 .="<p>Disclaimer : This is system auto generated email. Please do not reply on this email. If you have any concern then please contact us on helpdesk@oonir.com
				</p>";	
				$message2 .="</body>";
			}	//exit;		
			$this->load->library('email', $config);
			$this->email->set_mailtype("html");
			$this->email->set_newline("\r\n");			
			$this->email->from('support@oonir.com', "Oonir Support Team");			
			$this->email->to($email);	
			//$this->email->to('testteam47@gmail.com');	
			$this->email->subject("Shipment Cancel Invoice");
			$this->email->message($message2);
			$this->email->attach($new_dir);
			if($this->email->send())
			{  
				$msg['return'] = 1;
				$msg['message'] = "Mail sent successfully";   
			}
			else
			{
				$data['message'] = "Sorry Unable to send email"; 
				$msg['return'] = 0;
				$msg['error'] = show_error($this->email->print_debugger());
			}
			/***********************************/
			
		}
		
		function send_mail($email='',$message='',$user_type='',$name='',$type='')
		{						
			$config = Array(
						 'protocol' => 'smtp', //sendmail
						 'smtp_host' => 'localhost',									
						 'smtp_port' => 465,// 465 587
						 'smtp_user' => 'support@oonir.com', // change it to yours riya.sen485@gmail.com
						 'smtp_pass' => '4R!Ut-nDFgHI', // change it to yours 7ESx686WY
						 'mailtype' => 'html/text',
						 'charset' => 'iso-8859-1',
						 'wordwrap' => TRUE
			); 
			if($user_type=='customer')
			{	
				$data = array(
						// 'message'=> $message2,
						'message'=> $message,
						'name'=>$name	,
						'type'=>$type						
					);
				// echo $user_type;
				// $message2  = "<p>Hello $name,</p>";
				// $message2 .="<p>Thank you for using oonir app, Your Shipment has been booked . </p></br><br>";
				// $message2 .=  $message."</br></br>";
				// $message2 .="<p>Thank you,</p>";
				// $message2 .="<p>Oonir Support Team</p>";
			}

			if($user_type=='transporter')
			{	
				$data = array(
						// 'message'=> $message2,
						'message'=> $message,
						'name'=>$name,
						'type'=>$type						
						);
				// echo $user_type;
				// $message2  = "<p>Hello $name,</p>";
				// $message2 .="<p>Thank you for using oonir app, Please find the attachment for your invoice. </p></br><br>";
				// $message2 .=  $message."</br></br>";
				// $message2 .="<p>Thank you,</p>";
				// $message2 .="<p>Oonir Support Team</p>";
			}	
				
				// echo "email = ".$email. " message ".$message2;
			
			$this->email->set_mailtype("html");
			$body = $this->load->view('ViewBookShipment',$data,TRUE);
			$this->load->library('email', $config);
			$this->email->set_mailtype("html");
			$this->email->set_newline("\r\n");			
			$this->email->from('support@oonir.com', "Oonir Support Team");			
			$this->email->to($email);	
			//$this->email->to('testteam47@gmail.com');
			if($type=="book")
			{			
				$this->email->subject("Shipment Booked");
			}
			if($type=="cancel")
			{
				$this->email->subject("Shipment Cancel");
			}
			$this->email->message($body);
			
			if($this->email->send())
			{  
				$msg['return'] = 1;
				$msg['message'] = "Mail sent successfully"; 
				// echo "done";exit;	
			}
			else
			{
				// echo "failed";exit;
				$data['message'] = "Sorry Unable to send email"; 
				$msg['return'] = 0;
				$msg['error'] = show_error($this->email->print_debugger());
			}
			
		}
		
		function view_email()
		{
				$body = $this->load->view('ViewBookShipment');
		}
	
	
		/************************************** Add driver report *********************************/
		function add_driver_report($truck_id = '',$driver_id='',$driver_name='',$address='',$latitude='',$longitude='', $transporter = '')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($truck_id)&&!empty($address)&&!empty($latitude)&&!empty($longitude))
				{					
					
					$query = $this->db->query('select * from driver_report order by rp_id desc limit 1');
					if($query->num_rows()>0 )
					{
						
						$result_data1  = $query->row_array();
						if($driver_id == $result_data1['driver_id'] && $truck_id  == $result_data1['truck_id']  )
						{
							$lat1  = $result_data1['latitude'];
							$lon1 = $result_data1['longitude'];
							$strt_time = $result_data1['created_date'];
							$end_time	= date('Y-m-d H:i:s');
							$timestamp1 = strtotime($strt_time);
							$timestamp2 = strtotime($end_time);
							 $datediff = $timestamp2 - $timestamp1;
							$minutes = floor($datediff/(60)); 
							if($minutes < 12){
								$msg['return'] = 0;
								$msg['result'] = 'Insertion failed';
								echo json_encode($msg);exit;
							}
							
							$hour = abs($timestamp2 - $timestamp1)/(60*60);
							// echo "<br>";
							$lat2   = $latitude;
							$lon2   = $longitude;	
							
							$theta = $lon1 - $lon2;
							$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
							$dist = acos($dist);
							$dist = rad2deg($dist);
							$miles = $dist * 60 * 1.1515;
							// $unit = strtoupper($unit);
							$distance  = round(($miles * 1.609344),2);
							// echo "<br>";
							$speed =  round($distance/$hour,2);
						}else{
						
							$speed = '';
							$distance = '';	
						
						}
						// echo "<pre>";
						// print_r($result_data1);
					}else{
						
						$speed = '';
						$distance = '';	
						
					}
					
					// $postData['created_date'] 	= date('Y-m-d H:i:s');
					
					// exit;	  
					$postData['truck_id'] 		= $truck_id;
					$postData['driver_id']		= $driver_id;
					$postData['driver_name']	= $driver_name;
					$postData['trans_name']	= $transporter;
					$postData['address']		= $address;
					$postData['latitude']	    = $latitude;
					$postData['longitude'] 	    = $longitude;	
					$postData['distance'] 	    = $distance;
					$postData['speed'] 	    	= $speed;														
					$postData['created_date'] 	= date('Y-m-d H:i:s');
					
					$result_id = $this->Tran_model->insert_data('driver_report',$postData);
					if($result_id)
					{
						$msg['return'] = 1;
						$msg['result'] = 'Report added successfully';					
					}else{
						$msg['return'] = 0;
						$msg['result'] = 'Insertion failed';					
					}
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : truck_id,driver_id,driver_name,address,latitude,longitude';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] = 'parameter missing : truck_id,driver_id,driver_name,address,latitude,longitude';					
			}
			echo json_encode($msg);
		}

        /******************************* Truck list **********************************/
	function driver_report($driver_code = '',$start_date='',$end_date='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($driver_code))
				{	
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($driver_code);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					// $condition = array('user_id'=> $user_id);		
					// $result_data = $this->Tran_model->select_single_row_data('truck_list',$condition,0,0);	
					$query = $this->db->query('select *,count(created_date) as count,DATE_FORMAT(created_date,"%d %M %Y %W") as create_date,DATE_FORMAT(created_date,"%Y-%m-%d") as date from driver_report where DATE_FORMAT(created_date,"%Y-%m-%d") >= "'.$start_date.'" AND DATE_FORMAT(created_date,"%Y-%m-%d") <= "'.$end_date.'" and driver_id="'.$driver_code.'" group by DATE_FORMAT(created_date,"%Y-%m-%d") order by DATE_FORMAT(created_date,"%Y-%m-%d") desc' );
					if($query->num_rows()>0)				
					{					
						$result_data = $query->result_array();	
						for($i=0;$i<count($result_data);$i++)
						{	
							$date = $result_data[$i]['created_date'];
							$result_data[$i]['time'] =  date('h:i a', strtotime($date));

						}	
						// echo "<pre>";
						// print_r($result_data);exit;
						
						$msg['return'] 	= 1;
						$msg['result'] 	= 'success';
						$msg['data'] 	= $result_data;						
					}else{						
						$msg['return'] = 0;
						$msg['result'] = 'No report found';						
					} 		
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : user_id';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] =  'please provide parameter: user_id';
			}
			echo json_encode($msg);
		} 
				function truck_report($truck_code = '',$start_date='',$end_date='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($truck_code))
				{	
					/************* check blocked status *************/
					$block_status = $this->Tran_model->check_block_status($truck_code);	
					if($block_status==0)
					{						
						$msg['return'] = 0;
						$msg['result'] = 'You have been blocked by admin. Please goto contact us screen';		
						echo json_encode($msg);exit;
					}	
					/************* check blocked status *************/
					
					// $condition = array('user_id'=> $user_id);		
					// $result_data = $this->Tran_model->select_single_row_data('truck_list',$condition,0,0);	
					$query = $this->db->query('select *,count(created_date) as count,DATE_FORMAT(created_date,"%d %M %Y %W") as create_date,DATE_FORMAT(created_date,"%Y-%m-%d") as date from driver_report where DATE_FORMAT(created_date,"%Y-%m-%d") >= "'.$start_date.'" AND DATE_FORMAT(created_date,"%Y-%m-%d") <= "'.$end_date.'" and truck_id="'.$truck_code.'" group by DATE_FORMAT(created_date,"%Y-%m-%d") order by DATE_FORMAT(created_date,"%Y-%m-%d") desc' );
					if($query->num_rows()>0)				
					{					
						$result_data = $query->result_array();	
						for($i=0;$i<count($result_data);$i++)
						{	
							$date = $result_data[$i]['created_date'];
							$result_data[$i]['time'] =  date('h:i a', strtotime($date));

						}	
						// echo "<pre>";
						// print_r($result_data);exit;
						
						$msg['return'] 	= 1;
						$msg['result'] 	= 'success';
						$msg['data'] 	= $result_data;						
					}else{						
						$msg['return'] = 0;
						$msg['result'] = 'No report found';						
					} 		
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : user_id';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] =  'please provide parameter: user_id';
			}
			echo json_encode($msg);
		}
		/******************************* driver report **********************************/
		
		
		/******************************* Driver Detail list **********************************/
		function truck_report_detail($truck_code = '',$start_date='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($truck_code))
				{						
					// echo 'select *,DATE_FORMAT(created_date,"%d %M %Y %W") as create_date from driver_report where DATE_FORMAT(created_date,"%Y-%m-%d") like "%'.$start_date.'%" and truck_id="'.$driver_id.'" order by DATE_FORMAT(created_date,"%Y-%m-%d")';
					
					
					// $query = $this->db->query('select *,DATE_FORMAT(created_date,"%d %M %Y %W") as create_date from driver_report where DATE_FORMAT(created_date,"%Y-%m-%d") like "%'.$start_date.'%" and truck_id="'.$driver_id.'" order by DATE_FORMAT(created_date,"%Y-%m-%d")' );
					
					$query = $this->db->query('select *,DATE_FORMAT(created_date,"%d %M %Y %W") as create_date from driver_report where DATE_FORMAT(created_date,"%Y-%m-%d") like "%'.$start_date.'%" and truck_id="'.$truck_code.'" order by  created_date desc' );
					if($query->num_rows()>0)				
					{					
						$result_data = $query->result_array();	
						for($i=0;$i<count($result_data);$i++)
						{	
							$date = $result_data[$i]['created_date'];
							$result_data[$i]['time'] =  date('h:i a', strtotime($date));

						}	
						// echo "<pre>";
						// print_r($result_data);exit;
						
						$msg['return'] 	= 1;
						$msg['result'] 	= 'success';
						$msg['data'] 	= $result_data;						
					}else{						
						$msg['return'] = 0;
						$msg['result'] = 'No report found';						
					} 		
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : user_id';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] =  'please provide parameter: user_id';
			}
			echo json_encode($msg);
		}
		
		function driver_report_detail($driver_code = '',$start_date='')
		{
			if(!empty($_REQUEST) && isset($_REQUEST))
			{		
				extract($_REQUEST);
				$postData =array();
				if(!empty($driver_code))
				{						
					// echo 'select *,DATE_FORMAT(created_date,"%d %M %Y %W") as create_date from driver_report where DATE_FORMAT(created_date,"%Y-%m-%d") like "%'.$start_date.'%" and truck_id="'.$driver_id.'" order by DATE_FORMAT(created_date,"%Y-%m-%d")';
					
					
					// $query = $this->db->query('select *,DATE_FORMAT(created_date,"%d %M %Y %W") as create_date from driver_report where DATE_FORMAT(created_date,"%Y-%m-%d") like "%'.$start_date.'%" and truck_id="'.$driver_id.'" order by DATE_FORMAT(created_date,"%Y-%m-%d")' );
					
					$query = $this->db->query('select *,DATE_FORMAT(created_date,"%d %M %Y %W") as create_date from driver_report where DATE_FORMAT(created_date,"%Y-%m-%d") like "%'.$start_date.'%" and driver_id="'.$driver_code.'" order by  created_date desc' );
					if($query->num_rows()>0)				
					{					
						$result_data = $query->result_array();	
						for($i=0;$i<count($result_data);$i++)
						{	
							$date = $result_data[$i]['created_date'];
							$result_data[$i]['time'] =  date('h:i a', strtotime($date));

						}	
						// echo "<pre>";
						// print_r($result_data);exit;
						
						$msg['return'] 	= 1;
						$msg['result'] 	= 'success';
						$msg['data'] 	= $result_data;						
					}else{						
						$msg['return'] = 0;
						$msg['result'] = 'No report found';						
					} 		
				}else{
					$msg['return'] = 0;
					$msg['result'] = 'parameter missing : user_id';					
				}	
			}else{
				$msg['return'] = 0;
				$msg['result'] =  'please provide parameter: user_id';
			}
			echo json_encode($msg);
		}
		
		
			
	} // End of class


?>