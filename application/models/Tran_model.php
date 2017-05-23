<?php
	// LIVE
	class Tran_model extends CI_Model
	{
		function __construct()
		{
			parent::__construct();
		
		}
		
		function select_all_data($table_name)
		{
			 $query = $this->db->get($table_name);
			 return $query->result_array();
		}
		
		function select_single_row_data($table_name,$condition,$limit,$offset)
		{
			// echo "<pre>";
			// print_r($condition);
			$query = $this->db->get_where($table_name,$condition,$limit,$offset);
			return  $query->result_array();
			// return  $query->result();
					
		}
		
		function select_single_row($table_name,$condition,$limit,$offset)
		{
			// echo "<pre>";
			// print_r($condition);
			$query = $this->db->get_where($table_name,$condition,$limit,$offset);
			return  $query->row();
			// return  $query->result();
					
		}
		
		
		function insert_data($table_name,$array_data)
		{
			$query = $this->db->insert($table_name,$array_data);	
			$insert_id = $this->db->insert_id();
			return $insert_id;
		}
		
		function update_selected_data($table_name,$array_data,$condition)
		{
			$query = $this->db->update($table_name, $array_data,$condition );
			return $query;
		}
		
		function get_selected_data($field_names,$table_name,$condition)
		{
			$field_name = implode(',',$field_names);
			$this->db->select($field_name);
			$this->db->where($condition);
			$query = $this->db->get($table_name);
			return $query->result_array();
		}
		
		function get_username($field_name,$table_name,$condition)
		{
			$this->db->select($field_name);
			$this->db->where($condition);
			$query = $this->db->get($table_name);
			return $query->result_array();
		}
		
		function get_device_token($field_name,$table_name,$condition)
		{
			$this->db->select($field_name);
			$this->db->where($condition);
			$query = $this->db->get($table_name);
			return $query->result_array();
		}
		
		function get_device_type($field_name,$table_name,$condition)
		{
			$this->db->select($field_name);
			$this->db->where($condition);
			$query = $this->db->get($table_name);
			return $query->result_array();
		}
		
		function delete_data($table_name,$condition_array)
		{
			$result = $this->db->delete($table_name,$condition_array); 
			return $result;
			
		}
		function image_upload()
		{
			$UNEEQUE = uniqid();
			$finalimage = $UNEEQUE.'_img';
			$config = array(
					'upload_path' => dirname(dirname(__FILE__))."\upload\user_image",
					'allowed_types' => "gif|jpg|png|jpeg|pdf",
					'overwrite' => TRUE,
					'max_size' => "2048000", 
					'file_name' => $finalimage,
					// 'max_height' => "768",
					// 'max_width' => "1024"
				);
			$this->load->library('upload', $config);
			if($this->upload->do_upload())
			{
				$data = array('upload_data' => $this->upload->data());	
				$msg['return'] = 1;
				$msg['result'] = 'success';	
				$msg['data'] = $data ;						
			}
			else
			{
				$error = array('error' => $this->upload->display_errors());
				$msg['return'] = 0;
				$msg['result'] = 'failed';
				$msg['data'] = $this->upload->display_errors();								
				// echo json_encode($msg);exit;
			}

		
		}
		
		function get_request_status($customer_id,$shipment_id,$user_id)
		{
			
			// $query = $this->db->query('select * from truck_request where 
						// (customer_id="'.$customer_id.'" and shipment_id="'.$shipment_id.'" and  request_status=1)
					// OR (customer_id="'.$customer_id.'" and shipment_id="'.$shipment_id.'" and  request_status=3)
					// OR (customer_id="'.$customer_id.'" and shipment_id="'.$shipment_id.'" and  request_status=4)
					// OR (customer_id="'.$customer_id.'" and shipment_id="'.$shipment_id.'" and  request_status=0)						
				// ');
				
				$query = $this->db->query('select * from truck_request where 
						(customer_id="'.$customer_id.'" and shipment_id="'.$shipment_id.'" and transporter_id="'.$user_id.'" and request_status=1)
						OR (customer_id="'.$customer_id.'" and shipment_id="'.$shipment_id.'"  and transporter_id="'.$user_id.'" and  request_status=3)
						OR (customer_id="'.$customer_id.'" and shipment_id="'.$shipment_id.'"  and transporter_id="'.$user_id.'" and  request_status=4)
						OR (customer_id="'.$customer_id.'" and shipment_id="'.$shipment_id.'"  and request_status=0)						
				');
			if($query->num_rows()>0)
			{
				return 1;	
			}else{
				return 2;
			}	
			
		}
		
		function get_applied_transporter_count($user_id,$shipment_id)
		{
			$query =  $this->db->query('select count(*) as count from truck_request where shipment_id="'.$shipment_id.'" and customer_id="'.$user_id.'" and request_status=1');
			if($query->num_rows()>0)
			{
				$result_data = $query->result_array();
				return $count = $result_data[0]['count'];
				// echo "<pre>";
				// print_r($result_data);
			
			}	
			
		}
		
		function get_user_detail($user_id)
		{
			$query =  $this->db->query('select mobile_number,username as customer_name,email,company_name from user where  user_id="'.$user_id.'"');
			if($query->num_rows()>0)
			{
				return $result_data = $query->result_array();
				// return $count = $result_data[0]['mobile_number'];
				// echo "<pre>";
				// print_r($result_data);
			
			}	
			
		}
		
		function get_user_data($user_id)
		{
			$query =  $this->db->query('select username as customer_name from user where  user_id="'.$user_id.'"');
			if($query->num_rows()>0)
			{
				return $result_data = $query->row();
				// return $count = $result_data[0]['mobile_number'];
				// echo "<pre>";
				// print_r($result_data);
			
			}	
			
		}
		
		function random_string()
		{
			$character_set_array = array();
			$character_set_array[] = array('count' => 2, 'characters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSUVWXYZ');
			$character_set_array[] = array('count' => 2, 'characters' => '0123456789');
			$temp_array = array();
			foreach ($character_set_array as $character_set) {
				for ($i = 0; $i < $character_set['count']; $i++) {
					$temp_array[] = $character_set['characters'][rand(0, strlen($character_set['characters']) - 1)];
				}
			}
			shuffle($temp_array);
			return implode('', $temp_array);
		}
		
		function get_lat_long($location)
		{
			$address = urlencode($location);
			$request = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=" .urlencode($location). "&sensor=false");
			$json = json_decode($request, true);
			//echo "<pre>"; print_r($json); echo "</pre>";
			//die();
			if($json['status']=='ZERO_RESULTS')
			{
				$ret['status'] = '-1';
				$ret['lat'] = '-1';
				$ret['long'] = '-1';
			}
			else
			{
				$ret['status'] 	= '1';
				$ret['lat'] 	= $json['results'][0]['geometry']['location']['lat'];
				$ret['long'] 	= $json['results'][0]['geometry']['location']['lng'];
			}
			return $ret;
		}
		
		function get_truck_count($user_id)
		{
			$query = $this->db->query('select truck_id from truck_list where user_id='.$user_id); 
			if($query->num_rows()>0)
			{
				$result = '1';
			}else{
				$result = '2';
			}
			return $result;
			
		}
		
		
		function get_truck_num($truck_id)
		{
			$query = $this->db->query('select registration_no from truck_list where truck_id='.$truck_id); 
			if($query->num_rows()>0)
			{
				$rs = $query->row_array();
				$registration_no = $rs['registration_no'];
				$result = $registration_no;
			}else{
				$result = '';
			}
			return $result;
			
		}
		
		function get_truck_booking_status($truck_id)
		{
			$query = $this->db->query('select truck_id from truck_request where (truck_id="'.$truck_id.'" and request_status=3) OR (truck_id="'.$truck_id.'" and request_status=4) '); 
			if($query->num_rows()>0)
			{
				$result = '1';
			}else{
				$result = '2';
			}
			return $result;
			
		}
		
		function get_shipment_location($field_name,$table_name,$condition)
		{
			$this->db->select($field_name);
			$this->db->where($condition);
			$query = $this->db->get($table_name);
			return $query->result_array();
		}
		
		
		
		function send_iphone_notification($recivertok_id,$message,$notmessage='',$msgsender_id='')
		{  
	
			$PATH = dirname(dirname(__FILE__))."\pemfile\ck.pem";
			$this->load->file($PATH, true);
			$deviceToken = $recivertok_id;
			$passphrase = 123456;
			// $passphrase = 123456789;
			$message = $message;
			$ctx = stream_context_create();
			// stream_context_set_option($ctx, 'ssl', 'local_cert', 'Certificates.pem');
					stream_context_set_option($ctx, 'ssl', 'local_cert', $PATH);
			stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
			
			$fp = stream_socket_client(
										'ssl://gateway.sandbox.push.apple.com:2195', $err,
				$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			
			/*
			$fp = stream_socket_client(
										'ssl://gateway.push.apple.com:2195', $err,
				$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			*/

			if (!$fp)
				 exit("Failed to connect: $err $errstr" . PHP_EOL);

				$body['aps'] = array(
					'alert' => $message,
					'sound' => 'default',
					'Notifykey' => $notmessage, 
					'msgsender_id'=>$msgsender_id
				);

			$payload = json_encode($body);
			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
			$result = fwrite($fp, $msg, strlen($msg));

			// echo "<pre>";
			// print_r($body);
			/*
			if (!$result)
				echo 'Message not delivered' . PHP_EOL;
			else
				echo 'Message successfully delivered' . PHP_EOL;
			*/
			fclose($fp);
		}	
		  
		function update_badge($user_id)
		{	
			$query = $this->db->query('select badge from user where user_id="'.$user_id.'"');
			if($query->num_rows()>0)
			{
				$result = $query->row_array();
				// echo "<pre>";
				// print_r($result);
				$badge = $result['badge'];
				$badge = $badge +1;
				$this->db->query('update user set badge= "'.$badge.'" where user_id="'.$user_id.'" ');
				
				// $query1 = $this->db->query('select badge from user where user_id="'.$user_id.'"');
				// $result1 = $query1->row_array();
				// $final_badge = $result1['badge'];
			}
		
		}	
		
		function send_android_notification($device_token,$data)
		{
			if (!defined('API_ACCESS_KEY')) 
			{
				define('API_ACCESS_KEY','AIzaSyC_-Z8fN2ke0LoYfBiZJxYw5km-0gf0GMQ');
				// define('API_ACCESS_KEY','AIzaSyDnkQ6yIUd7yNqE7V2IU9Li80Y-My06Bxs');
				// AIzaSyBNDJ_XMq2yQvoiW0prf-j4pqgQP4pX1G0
			}
			$registrationIds = array($device_token);
				// echo "<pre>";
			// print_r($data);
			if($data['notifykey']!='send_message')
			{
				$user_id = $data['data']['user_id'];
				$query = $this->db->query('select badge from user where user_id="'.$user_id.'"');
				if($query->num_rows()>0)
				{
					$result = $query->row_array();
					$badge = $result['badge'];				
				}
				$data['badge'] = $badge;
			}
			// echo "<pre>";
			// print_r($data);
			$fields = array(
				'registration_ids' => $registrationIds,
				'data'	=> $data,
				//'badge' =>$badge
			);
			
			// echo "<pre>";
			// print_r($fields); 
			// exit;
			
			$headers = array(
				'Authorization: key=' . API_ACCESS_KEY,
				'Content-Type: application/json'
			);
			// echo "<br>";
			// print_r($headers); 
			 
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			$result = curl_exec($ch);
			
			if($result == FALSE) {
				die('Curl failed: ' . curl_error($ch));
			}
			
			// echo curl_error($ch);
			curl_close( $ch );
			// echo $result;
			// echo "<br>";
			return  $result;
			
		}
		
		function  check_block_status($user_id)
		{
			$query = $this->db->query('select user_status from user where user_id="'.$user_id.'"');
			if($query->num_rows()>0)
			{	
				$result = $query->row_array();					
				return $result['user_status'];								
			}else{
				return 1;
			}			
		}
		
		
		function get_booking_cancel_before_time($shipment_id)
		{
			$query = $this->db->select('pickup_date')->where('shipment_id',$shipment_id)->get('truck_shipment');
			if($query->num_rows()>0)
			{
				$result = $query->row_array();
				$pickup_date = $result['pickup_date'];
				// echo "Pickup date = ". $pickup_date."</br>";				
				// echo "Pickup date_str = ".  $str_pckup = strtotime('-24 hour',strtotime($pickup_date))."<br>";
				// echo "Pickup date_rev = ".  $str_pckup22 = date('Y-m-d H:i:s',strtotime('-24 hour',strtotime($pickup_date)))."<br>";
				
				// echo "Pickup date_str33 = ".  $str_pckup333 = strtotime('-6 hour',strtotime($pickup_date))."<br>";
				// echo "Pickup date_rev_33 = ".  $str_pckup33 = date('Y-m-d H:i:s',strtotime('-6 hour',strtotime($pickup_date)))."<br>";
				
				
				// echo "current_date =  ".	$current_date 	= date('Y-m-d H:i:s');echo "<br>";
				// echo "curr_date_str " .$curr_started_date = strtotime($current_date);echo "</br>";				
				// echo "curr_date_str2 = ".  $str_pckup2 = date('Y-m-d H:i:s',strtotime("+0 days",strtotime($current_date)))."<br>";
				// echo "<br>";
				
				/*******************************   Start   **********************/
				// echo "Pickup date = ". $pickup_date."</br>";				
				$str_pckup = strtotime('-24 hour',strtotime($pickup_date));
				$str_pckup22 = date('Y-m-d H:i:s',strtotime('-24 hour',strtotime($pickup_date)));
				
				$str_pckup333 = strtotime('-6 hour',strtotime($pickup_date));
				$str_pckup33 = date('Y-m-d H:i:s',strtotime('-6 hour',strtotime($pickup_date)));
				
				
				$str_pckup444 		= strtotime($pickup_date);
				$current_date444 	= strtotime(date('Y-m-d'));
				
				$current_date 	= date('Y-m-d H:i:s');
				$curr_started_date = strtotime($current_date);				
				$str_pckup2 = date('Y-m-d H:i:s',strtotime("+0 days",strtotime($current_date)));
				
				/*******************************   End   **********************/		
				if($curr_started_date > $str_pckup)
				{
					if($curr_started_date > $str_pckup333)
					{						
						if($str_pckup444==$current_date444)
						{
							return "4";// echo "you can not cancel shipment";
						}else if($str_pckup444 <= $current_date444){
							return "4";
						}						
						else{
							// echo $str_pckup444." == ".$current_date444;
							return "3";// echo "you can not cancel shipment";
						}	
					}else{
						// echo "you will be charged<br>";
						return "2";
					}	
				}else{
					// echo "No charged<br>";
					return "1";// No charged
				}
			}
		
		}
		
		
		
		

	}	



?>