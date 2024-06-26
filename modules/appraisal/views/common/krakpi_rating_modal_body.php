<div class="row">
    <div class="col-md-12">

        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Staff Name</th>
                    <th>Avarage Rating</th>
                    <th>Last Rating</th>
                    <th>Last Rating At</th>
                    <th>Total Rating</th>
                    <th>Total Rating Count</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $details->firstname . ' ' . $details->lastname ?></td>
                    <td><?= $details->average_rating ?></td>
                    <td><?= $details->last_rating ?></td>
                    <td><?= date('F d, Y H:iA', strtotime($details->last_rating_at)) ?></td>
                    <td><?= $details->total_rating ?></td>
                    <td><?= $details->total_rating_count ?></td>
                </tr>
            </tbody>
        </table>

    </div>
</div>