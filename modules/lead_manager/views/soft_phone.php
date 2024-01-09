<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div class="container ui-widget-content" id="soft-phone-draggable">
	<div class="call ringing -ringing" id="ringing-soft-phone">
		<div class="head_bell">
			<img src="<?php echo base_url('modules/lead_manager/assets/icons/bell.svg'); ?>">
		</div>
		<div class="details" id="calling-info">
			Incomming call...
			<p>Zonvoir</p>
		</div>
		
		<ul class="actions">
			<li class="action pic"> <a id="accept" href="javascript:void(0);"><i class="fa fa-phone"></i></a></li>
			<li class="action cut"> <a id="refuse" href="javascript:void(0);"><i class="fa fa-phone decline-icon"></i></a></li>
		</ul>
	</div>
	<div class="call speaking flipback" id="speaking-soft-phone">
		<div class="head_bell">
			<div class="sound"><span class="fa-stack"><i class="fa fa-microphone fa-stack-1x"></i><i class="fa fa-ban fa-stack-1x hidden"></i></span></div>
		</div>
		<div class="details" id="caller-info"><h4><i class="fa fa-phone"></i> - Sia</h4></div>
			<ul class="action_dial">
				<li class="action cut"> <a id="drop" href="javascript:void(0);"><i class="fa fa-phone decline-icon"></i></a></li>
			</ul>
		</div>
		<div class="call dialing -dialing" id="dialing-soft-phone">
			<div class="head_bell">
				<img src="<?php echo base_url('modules/lead_manager/assets/icons/bell.svg'); ?>">
			</div>
			<div class="details" id="calling-info">Connecting<img src="<?php echo base_url('modules/lead_manager/assets/icons/calling.gif'); ?>"> <span>+919453974798</span></div>
			<ul class="actions">
				<li class="action_dial cut"> <a id="disconnect" href="javascript:void(0);"><i class="fa fa-phone decline-icon"></i></a></li>
			</ul>
		</div>
	</div>
	<input type="hidden" value="<?php echo $staffPhoneNumber; ?>" id="staffPhoneNumber" name="staffPhoneNumber">
	<?php echo strval($staffPhone); ?>