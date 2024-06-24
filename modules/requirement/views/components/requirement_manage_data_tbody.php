<?php foreach($data as $key => $value): ?>
<tr>
    <td><?= $key+1 ?></td>
    <?php foreach($fields as $k => $v): ?>
    <td><?= $value[$v['field_name_slug']] ?></td>
    <?php endforeach; ?>
    <td><?= $value['interview_time'] ?></td>
    <td><?= $value['action'] ?></td>
</tr>
<?php endforeach; ?>