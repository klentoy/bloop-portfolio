<div class="wrap">
    <h1>Bloop Token</h1>
    <?php print_r($tokens); ?>
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <th scope="col" id="title" class="manage-column">Collection</th>
                <th scope="col" id="author" class="manage-column">Token Remarks</th>
                <th scope="col" id="date" class="manage-column">Shared By</th>
                <th scope="col" id="ridwpaid" class="manage-column"></th>
            </tr>
            <tbody>
                <?php foreach($tokens as $token){ ?>
                <tr>
                    <td><?php echo $token->post_title; ?></td>
                    <td><?php echo $token->remarks; ?></td>
                    <td><?php echo $token->first_name . " " . $token->last_name; ?></td>
                    <td></td>
                </tr>
                <?php } ?>
            </tbody>
        </thead>
    </table>
</div>