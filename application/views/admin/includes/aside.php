<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<aside id="menu" class="sidebar sidebar">
    <ul class="nav metis-menu" id="side-menu">
        <li class="tw-mt-[63px] sm:tw-mt-0 -tw-mx-2 tw-overflow-hidden sm:tw-bg-neutral-900/50">
            <div id="logo" class="tw-py-2 tw-px-2 tw-h-[63px] tw-flex tw-items-center">
                <?php echo get_company_logo(get_admin_uri() . '/', '!tw-mt-0')?>
            </div>
        </li>
        
        <?php
        hooks()->do_action('before_render_aside_menu');
        ?>
        <?php foreach ($sidebar_menu as $key => $item) {
             if ((isset($item['collapse']) && $item['collapse']) && count($item['children']) === 0) {
                 continue;
             } ?>
        <li class="menu-item-<?php echo $item['slug']; ?>"
            <?php echo _attributes_to_string(isset($item['li_attributes']) ? $item['li_attributes'] : []); ?>>
            <a href="<?php echo count($item['children']) > 0 ? '#' : $item['href']; ?>" aria-expanded="false"
                <?php echo _attributes_to_string(isset($item['href_attributes']) ? $item['href_attributes'] : []); ?>>
                <i class="<?php echo $item['icon']; ?> menu-icon"></i>
                <span class="menu-text">
                    <?php echo _l($item['name'], '', false); ?>
                </span>
                <?php if (count($item['children']) > 0) { ?>
                <span class="fa arrow pleft5"></span>
                <?php } ?>
                <?php if (isset($item['badge'], $item['badge']['value']) && !empty($item['badge'])) {?>
                <span
                    class="badge pull-right
               <?=isset($item['badge']['type']) && $item['badge']['type'] != '' ? "bg-{$item['badge']['type']}" : 'bg-info' ?>" <?=(isset($item['badge']['type']) && $item['badge']['type'] == '') ||
                        isset($item['badge']['color']) ? "style='background-color: {$item['badge']['color']}'" : '' ?>>
                    <?= $item['badge']['value'] ?>
                </span>
                <?php } ?>
            </a>
            <?php if (count($item['children']) > 0) { ?>
            <ul class="nav nav-second-level collapse" aria-expanded="false">
                <?php 
                foreach ($item['children'] as $submenu) {
                ?>
                <li class="sub-menu-item-<?php echo $submenu['slug']; ?>"
                    <?php echo _attributes_to_string(isset($submenu['li_attributes']) ? $submenu['li_attributes'] : []); ?>>
                    <a href="<?php echo $submenu['href']; ?>"
                        <?php echo _attributes_to_string(isset($submenu['href_attributes']) ? $submenu['href_attributes'] : []); ?>>
                        <?php if (!empty($submenu['icon'])) { ?>
                        <i class="<?php echo $submenu['icon']; ?> menu-icon"></i>
                        <?php } ?>
                        <span class="sub-menu-text">
                            <?php echo _l($submenu['name'], '', false); ?>
                        </span>
                    </a>
                    <?php if (isset($submenu['badge'], $submenu['badge']['value']) && !empty($submenu['badge'])) {?>
                    <span
                        class="badge pull-right
                        <?=isset($submenu['badge']['type']) && $submenu['badge']['type'] != '' ? "bg-{$submenu['badge']['type']}" : 'bg-info' ?>" <?=(isset($submenu['badge']['type']) && $submenu['badge']['type'] == '') ||
                            isset($submenu['badge']['color']) ? "style='background-color: {$submenu['badge']['color']}'" : '' ?>>
                        <?= $submenu['badge']['value'] ?>
                    </span>
                    <?php } ?>
                </li>
                <?php
                        } ?>
            </ul>
            <?php } ?>
            
        </li>
        
        <?php hooks()->do_action('after_render_single_aside_menu', $item); ?>
        <?php
         } ?>
         
        <li class="menu-item-cost_tracking">
            <a href="#" aria-expanded="true">
                <i class="fa fa-balance-scale menu-icon"></i>
                <span class="menu-text">
                    Cost Tracking 
                </span>
                <span class="fa arrow pleft5"></span>
            </a>
            <ul class="nav nav-second-level" aria-expanded="true" style="">
            <?php if(is_admin()){?>
                <li class="sub-menu-item-cost_tracking">
                    <a href="<?=base_url();?>admin/hr_profile/cost_tracking">
                      <i class="fa fa-american-sign-language-interpreting menu-icon"></i>
                        <span class="sub-menu-text">
                            Cost Tracking   
                         </span>
                    </a>
                </li>
                <li class="sub-menu-item-cost_tracking">
                    <a href="<?=base_url();?>admin/hr_profile/add_components">
                        <i class="fa fa-american-sign-language-interpreting menu-icon"></i>
                        <span class="sub-menu-text">
                            Add Components 
                        </span>
                    </a>
                </li>
                     <?php } if(!is_admin()){?>
                <li class="sub-menu-item-cost_tracking">
                    <a href="<?=base_url();?>admin/hr_profile/allocate_cost_tracking">
                        <i class="fa fa-american-sign-language-interpreting menu-icon"></i>
                        <span class="sub-menu-text">
                            Allocated Cost Report                        
                        </span>
                    </a>
                </li>
                 <?php }?>
                
                 </ul>
                        
        </li>
        <?php if(is_admin()){?>
        <!-- <li class="menu-item-faculty">
            <a href="#" aria-expanded="true">
                <i class="fa fa-handshake menu-icon"></i>
                <span class="menu-text">
                Faculty / Coordinator 
                </span>
                <span class="fa arrow pleft5"></span>
            </a>
            <ul class="nav nav-second-level" aria-expanded="true" style="">
            
                <li class="sub-menu-item-faculty">
                    <a href="<?=base_url();?>admin/hr_profile/faculty">
                      <i class="fa fa-american-sign-language-interpreting menu-icon"></i>
                        <span class="sub-menu-text">
                            Add Faculty   
                         </span>
                    </a>
                </li>
                <li class="sub-menu-item-faculty">
                    <a href="<?=base_url();?>admin/hr_profile/manage_faculty">
                        <i class="fa fa-american-sign-language-interpreting menu-icon"></i>
                        <span class="sub-menu-text">
                            Manage Faculty 
                        </span>
                    </a>
                </li>
            </ul>
                        
        </li> -->

        <li class="menu-item-faculty">
            <a href="#" aria-expanded="true">
                <i class="fa fa-handshake menu-icon"></i>
                <span class="menu-text">
                Faculty / Coordinator 
                </span>
                <span class="fa arrow pleft5"></span>
            </a>
            <ul class="nav nav-second-level" aria-expanded="true" style="">
                <?php if(is_admin()){?>
                <li class="sub-menu-item-faculty">
                    <a href="<?=base_url();?>admin/roles">
                      <i class="fa fa-adjust menu-icon"></i>
                        <span class="sub-menu-text">
                            Role Manage   
                         </span>
                    </a>
                </li>
                <li class="sub-menu-item-faculty">
                    <a href="<?=base_url();?>admin/hr_profile/manage_course">
                      <i class="fa fa-graduation-cap menu-icon"></i>
                        <span class="sub-menu-text">
                        Course Manage   
                         </span>
                    </a>
                </li>
                <li class="sub-menu-item-faculty">
                    <a href="<?=base_url();?>admin/hr_profile/manage_faculty">
                      <i class="fa fa-address-book menu-icon"></i>
                        <span class="sub-menu-text">
                          Faculty & Coordinators
                         </span>
                    </a>
                </li>
                
                <?php }?>
                <?php if(!is_admin()){?>
                <li class="sub-menu-item-faculty">
                    <a href="<?=base_url();?>admin/hr_profile/manage_tasks">
                        <i class="fa-regular fa-circle-check menu-icon"></i>
                        <span class="sub-menu-text">
                        Assign Tasks 
                        </span>
                    </a>
                </li>
                <li class="sub-menu-item-faculty">
                    <a href="<?=base_url();?>admin/hr_profile/manage_course">
                        <i class="fa-regular fa-circle-check menu-icon"></i>
                        <span class="sub-menu-text">
                        Assign Courses 
                        </span>
                    </a>
                </li>
                
                <?php }?>
                <li class="sub-menu-item-faculty">
                    <a href="<?=base_url();?>admin/hr_profile/docsmgt">
                        <i class="fa-regular fa-circle-check menu-icon"></i>
                        <span class="sub-menu-text">
                        Document Management
                        </span>
                    </a>
                </li>
                <li class="sub-menu-item-discussion">
                    <a href="<?=base_url();?>admin/hr_profile/discussion_forums">
                        <i class="fa-regular fa-circle-check menu-icon"></i>
                        <span class="sub-menu-text">
                        Discussion Forums
                        </span>
                    </a>
                </li>                
                <li class="sub-menu-item-faculty">
                    <a href="<?php echo admin_url('utilities/calendar'); ?>">
                        <i class="fa-regular fa-circle-check menu-icon"></i>
                        <span class="sub-menu-text">
                        Personal Calender 
                        </span>
                    </a>
                </li>
            </ul>
                        
        </li>

        <li class="menu-item-faculty">
            <a href="<?=base_url();?>admin/hr_profile/competency" aria-expanded="true">
                <i class="fa fa-comment-dots menu-icon"></i>
                <span class="menu-text">
                Competency Management 
                </span>
                
            </a>
        </li>

        <li class="menu-item-faculty">
            <a href="<?=base_url();?>admin/hr_profile/chat" aria-expanded="true">
                <i class="fa fa-comment-dots menu-icon"></i>
                <span class="menu-text">
                Chat 
                </span>
                
            </a>
        </li>
        <?php } ?>

        <?php if(is_admin()){?>
        <li class="menu-item-faculty">
            <a href="#" aria-expanded="true">
                <i class="fa fa-users menu-icon"></i>
                <span class="menu-text">
                Vendor / Customer  
                </span>
                <span class="fa arrow pleft5"></span>
            </a>
            <ul class="nav nav-second-level" aria-expanded="true" style="">
            
                <li class="sub-menu-item-faculty">
                    <a href="<?=base_url();?>admin/clients">
                      <i class="fa fa-american-sign-language-interpreting menu-icon"></i>
                        <span class="sub-menu-text">
                            Add Vendor / Customer  
                         </span>
                    </a>
                </li>
                <li class="sub-menu-item-faculty">
                    <a href="<?=base_url();?>admin/contracts">
                        <i class="fa fa-american-sign-language-interpreting menu-icon"></i>
                        <span class="sub-menu-text">
                        contracts 
                        </span>
                    </a>
                </li>
                 </ul>
                        
        </li>
        <?php } ?>
        
        <?php hooks()->do_action('after_render_aside_menu'); ?>
        <?php $this->load->view('admin/projects/pinned'); ?>
    </ul>
</aside>
<!--<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>-->
<script>
// $(document).ready(function() {
//     function checkTrainingSchedule() {
//         var csrf_token = $('meta[name="csrf_token"]').attr('content');
//         $.ajax({
//             // url: '<?php echo base_url();?>admin/Dashboard/checkTrainingSchedule',
//             type: 'POST',
//             dataType: 'json',
//             headers: {'X-CSRF-TOKEN': csrf_token},
//             success: function(response) {
//                 console.log('Server response:', response);
//             },
//             error: function(error) {
//                 console.error('Error:', error);
//             }
//         });
//     }

//     // Trigger the function initially
//     checkTrainingSchedule();

//     // Set up a setInterval to periodically call the function
//     setInterval(checkTrainingSchedule, 200000); // 30 minutes in milliseconds
// });
</script>