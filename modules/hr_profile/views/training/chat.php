<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php  init_head(); ?>

<style>
        .card {
    background: #fff;
    transition: .5s;
    border: 0;
    margin-bottom: 30px;
    border-radius: .55rem;
    position: relative;
    width: 100%;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 10%);
}
.chat-app .people-list {
    width: 280px;
    position: absolute;
    left: 0;
    top: 0;
    padding: 20px;
    z-index: 7
}

.chat-app .chat {
    margin-left: 280px;
    border-left: 1px solid #eaeaea
}

.people-list {
    -moz-transition: .5s;
    -o-transition: .5s;
    -webkit-transition: .5s;
    transition: .5s
}

.people-list .chat-list li {
    padding: 10px 15px;
    list-style: none;
    border-radius: 3px
}

.people-list .chat-list li:hover {
    background: #efefef;
    cursor: pointer
}

.people-list .chat-list li.active {
    background: #efefef
}

.people-list .chat-list li .name {
    font-size: 15px
}

.people-list .chat-list img {
    /* width: 45px; */
    border-radius: 50%
}

.people-list img {
    float: left;
    border-radius: 50%
}

.people-list .about {
    float: left;
    padding-left: 8px
}

.people-list .status {
    color: #999;
    font-size: 13px
}

.chat .chat-header {
    padding: 15px 20px;
    border-bottom: 2px solid #f4f7f6
}

.chat .chat-header img {
    float: left;
    border-radius: 40px;
    width: 40px
}

.chat .chat-header .chat-about {
    float: left;
    padding-left: 10px
}

.chat .chat-history {
    padding: 20px;
    border-bottom: 2px solid #fff
}

.chat .chat-history ul {
    padding: 0
}

.chat .chat-history ul li {
    list-style: none;
    margin-bottom: 30px
}

.chat .chat-history ul li:last-child {
    margin-bottom: 0px
}

.chat .chat-history .message-data {
    margin-bottom: 15px
}

.chat .chat-history .message-data img {
    border-radius: 40px;
    /* width: 40px */
}

.chat .chat-history .message-data-time {
    color: #434651;
    padding-left: 6px;
    font-size: 10px;
}

.chat .chat-history .message {
    color: #444;
    padding: 18px 20px;
    line-height: 26px;
    font-size: 16px;
    border-radius: 7px;
    display: inline-block;
    position: relative
}

.chat .chat-history .message:after {
    bottom: 100%;
    left: 7%;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
    border-bottom-color: #fff;
    border-width: 10px;
    margin-left: -10px
}

.chat .chat-history .my-message {
    background: #efefef
}

.chat .chat-history .my-message:after {
    bottom: 100%;
    left: 30px;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
    border-bottom-color: #efefef;
    border-width: 10px;
    margin-left: -10px
}

.chat .chat-history .other-message {
    background: #e8f1f3;
    text-align: right
}

.chat .chat-history .other-message:after {
    border-bottom-color: #e8f1f3;
    left: 93%
}

.chat .chat-message {
    padding: 20px
}

.online,
.offline,
.me {
    margin-right: 2px;
    font-size: 8px;
    vertical-align: middle
}

.online {
    color: #86c541
}

.offline {
    color: #e47297
}

.me {
    color: #1d8ecd
}

.float-right {
    float: right
}

.clearfix:after {
    visibility: hidden;
    display: block;
    font-size: 0;
    content: " ";
    clear: both;
    height: 0
}

@media only screen and (max-width: 767px) {
    .chat-app .people-list {
        height: 465px;
        width: 100%;
        overflow-x: auto;
        background: #fff;
        left: -400px;
        display: none
    }
    .chat-app .people-list.open {
        left: 0
    }
    .chat-app .chat {
        margin: 0
    }
    .chat-app .chat .chat-header {
        border-radius: 0.55rem 0.55rem 0 0
    }
    .chat-app .chat-history {
        height: 300px;
        overflow-x: auto
    }
}

@media only screen and (min-width: 768px) and (max-width: 992px) {
    .chat-app .chat-list {
        height: 650px;
        overflow-x: auto
    }
    .chat-app .chat-history {
        height: 600px;
        overflow-x: auto
    }
}

@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) and (orientation: landscape) and (-webkit-min-device-pixel-ratio: 1) {
    .chat-app .chat-list {
        height: 480px;
        overflow-x: auto
    }
    .chat-app .chat-history {
        height: calc(100vh - 350px);
        overflow-x: auto
    }
}
.h5 {
    text-align: center;
    padding: 10px;
    background: #ffa6a6;
    font-size: 25px;
}
.chat-app {
    height: 590px;
}
.chat-history {
    height: 310px;
    overflow-y: auto;
    display: flex;
    flex-direction: column-reverse;
}


    </style>
<div id="wrapper">
	<div class="content">
		<div class="row">
       
<div class="container">
<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card chat-app">
            <div id="plist" class="people-list">
                <!-- <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control" placeholder="Search...">
                </div> -->
                <h5 class="h5">USERS</h5>
                <ul class="list-unstyled chat-list mt-2 mb-0">
                    <!-- <li class="clearfix">
                        <img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="avatar">
                        <div class="about">
                            <div class="name">Vincent Porter</div>
                            <div class="status"> <i class="fa fa-circle offline"></i> left 7 mins ago </div>                                            
                        </div>
                    </li> -->
                    <?php foreach ($staff_members as $staff): ?>
                    <li class="clearfix staff-item" data-staffid="<?= $staff['staffid'] ?>" data-firstname="<?= $staff['firstname'] ?>">
                        <!-- <img src="https://bootdey.com/img/Content/avatar/avatar2.png" alt="avatar"> -->
                        <?php echo staff_profile_image($staff['staffid'], ['img', 'img-responsive', 'staff-profile-image-small', 'tw-ring-1 tw-ring-offset-2 tw-ring-primary-500 tw-mx-1 tw-mt-2.5']); ?>
                        <div class="about">
                            <div class="name"><?= $staff['firstname'] ?></div>
                            <div class="status"> <i class="fa fa-circle <?php if($staff['is_login']!= 0)  {?>online<?php } else {?>offline<?php }?>"></i> <?php if($staff['is_login']!= 0)  {?>online<?php } else {?>offline<?php }?> </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="chat">
                <div class="chat-header clearfix">
                    <div class="row">
                        <div class="col-lg-6">
                            <!-- <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
                                <img src="https://bootdey.com/img/Content/avatar/avatar2.png" alt="avatar">
                            </a> -->
                            <div class="chat-about">
                                <h6 class="m-b-0"></h6>
                                <!-- <small>Last seen: 2 hours ago</small> -->
                            </div>
                        </div>
                        <div class="col-lg-6 hidden-sm text-right">
                            
                        </div>
                    </div>
                </div>
                <div class="chat-history">
                <ul class="m-b-0" id="chat-history-list">
                        <h4 id="hide" style="text-align: center;"><i class='fas fa-comment' style='font-size:48px;color:red'></i>CHAT</h4>
                    </ul>
                </div>
                <div class="chat-message clearfix" style="display:none;">
                    <div class="input-group mb-0">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-send"></i></span>
                        </div>
                        <input type="hidden" value="" id="recive" name="recive" />
                        <input type="text" style="width: 745px;border-radius: 6px;margin-right: 10px;" class="form-control" id="chat-message-input" placeholder="Enter text here...">                                    
                        <button class="btn btn-primary" onclick="sendMessage()">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
			</div>
		</div>
	</div>



<div id="modal_wrapper"></div>
	<?php init_tail(); ?>
	<?php 
	require('modules/hr_profile/assets/js/hr_record/hr_record_js.php');
	?>
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <script>
        var loggedInUserId = <?php echo json_encode($loggedInUserId); ?>;
        // alert(loggedInUserId);
        $(document).ready(function() {
            $('.staff-item').click(function() {
                $('#hide').hide();
                $('.chat-message').show();
                var staffId = $(this).data('staffid');
                // alert(staffId);
                var firstname = $(this).data('firstname');
                $('.chat-about h6').text(firstname);
                $('#recive').val(staffId);
                fetchChatHistory(staffId);
            });
        });

        function fetchChatHistory(receiverId) {
            // alert(loggedInUserId);
            // alert(own);
            // alert(receiverId);
            $.ajax({
                url: '<?php echo base_url("hr_profile/ajax_get_chat_history"); ?>',
                type: 'POST',
                data: { receiver_id: receiverId },
                success: function(data) {
                    updateChatHistory(data);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function updateChatHistory(chatHistory) {
            var chatHistoryList = $('#chat-history-list');
            chatHistoryList.empty();

            var obj = jQuery.parseJSON(chatHistory);

            // Iterate through chat history and append to the list
            for (var i = 0; i < obj.length; i++) {
                var message = obj[i].message;
                var timestamp = obj[i].timestamp;
                var senderId = obj[i].sender_id;
                var receiverId = obj[i].receiver_id;

                var listItem = '<li class="clearfix">';
                listItem += '<div class="message-data ' + (senderId == loggedInUserId ? 'text-right' : '') + '">';
                listItem += '<span class="message-data-time">' + timestamp + '</span>';
                // listItem += '<img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="avatar">';
                var profileImageSrc = (senderId == loggedInUserId) ? '<?php echo staff_profile_image(' + loggedInUserId + ', ["img", "img-responsive", "staff-profile-image-small", "tw-ring-1 tw-ring-offset-2 tw-ring-primary-500 tw-mx-1 tw-mt-2.5 ' + (senderId == loggedInUserId ? 'float-right' : '') + '"]); ?>' : '<?php echo staff_profile_image(' + receiverId + ', ["img", "img-responsive", "staff-profile-image-small", "tw-ring-1 tw-ring-offset-2 tw-ring-primary-500 tw-mx-1 tw-mt-2.5 ' + (senderId == loggedInUserId ? 'float-right' : '') + '"]); ?>';
                listItem += profileImageSrc;
                listItem += '</div>';
                listItem += '</div>';
                listItem += '<div class="message ' + (senderId == loggedInUserId ? 'my-message float-right' : 'other-message') + '">' + message + '</div>';
                listItem += '</li>';

                chatHistoryList.append(listItem);
            }
        }


        function sendMessage() {
            // var receiverId = getReceiverId(); // Implement this function to get the receiver's ID
            var message = $('#chat-message-input').val();
            // $('#recive').val(staffId);
            $.ajax({
                url: '<?php echo base_url("hr_profile/ajax_insert_chat"); ?>',
                type: 'POST',
                data: { receiver_id: $('#recive').val(), message: message },
                success: function(data) {
                    // If successful, fetch and display updated chat history
                    fetchChatHistory($('#recive').val());
                    $('#chat-message-input').val(''); // Clear the input field
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    </script>

</body>
</html>
