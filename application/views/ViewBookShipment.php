<html>
<body style="background-color: #F1E4E4;padding:15px;border-radius:3px">
		<div>
		   <div style="font-size: 26px;font-weight: 700;letter-spacing: -0.02em;line-height: 32px;color: #41637e;font-family: sans-serif;text-align: center" align="center" id="emb-email-header"><img style="border: 0;-ms-interpolation-mode: bicubic;display: block;Margin-left: auto;Margin-right: auto;max-width: 152px" src="<?php echo site_url();?>application/upload/image/logo.png" alt="" width="" height=""></div>
		   
		   
			<p style="Margin-top: 0;color: #565656;font-family: Georgia,serif;font-size: 16px;line-height: 25px;Margin-bottom: 25px">Hello <?php echo $name;?>,</p> 
			
			<p style="Margin-top: 0;color: #565656;font-family: Georgia,serif;font-size: 16px;line-height: 25px;Margin-bottom: 25px">				
			<?php if($type=="book")
				{?>
				Thank you for using oonir app, Your shipment has been booked. <br /><br />
				 <?php echo $message; ?><br /><br />
			<?php } ?>	 
				
			<?php if($type=="cancel")
				{?>
				Thank you for using oonir app, Your shipment has been cancelled. <br /><br />
				 <?php echo $message; ?><br /><br />
			<?php } ?>	 
			
				Thank you,<br />
				Oonir Support Team,<br />
				Address :<br />26/3/2555, Sapthagiri colony, <br />
				BV Nagar, Mini bypass road, <br />
				opp GVRR College Nellore,<br />
				Pincode 524001<br />
				Contact Number : +91-8612313177<br /><br />

				Disclaimer : This is system auto generated email. Please do not reply on this email. If you have any concern then please contact us on helpdesk@oonir.com
				
			</p>
		</div>
</body>
</html>
<style>

	body{
		background-color: #F1E4E4;
	}
</style>