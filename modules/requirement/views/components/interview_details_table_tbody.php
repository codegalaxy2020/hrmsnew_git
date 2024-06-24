<?php
if(!empty($details)):
    foreach($details as $key => $val):
?>
<tr>
    <td><?= intval($key)+1 ?></td>
    <td><?= $val->firstname . ' ' . $val->lastname ?></td>
    <td><?= $val->comments ?></td>
    <td><?= date('F d, Y H:iA', strtotime($val->interview_datetime)) ?></td>
<?php
    endforeach;
endif;
?>