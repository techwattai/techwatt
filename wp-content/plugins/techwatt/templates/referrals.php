<h2 class="ps-mb-5"><span>My Referrals</span></h2>
<?php
$args = [
        'meta_key'   => 'refid',          // meta field where referrer ID is stored
        'meta_value' => $userID,     // match referrer
        'number'     => -1,               // get all referrals
        'fields'     => ['ID', 'display_name'], // useful fields
    ];

    $query = new WP_User_Query($args);
    $referrals = $query->get_results();
?>
<table>
<?php
$output = '';
if ( empty( $referrals ) ) { $output = '<tr><td colspan="5" align="center">No referral found.</td></tr>'; }
else{
    echo '<tr style="font-weight:bold;background:#f5f5f5;"><td>SN</td><td>Parent/Name</td><td>Registered Date</td><td></td></tr>';
    foreach ( $referrals as $referral ) {
        $regdate = get_user_meta($referral->ID, 'regdate', true) ?? '';
        $regdate = ($regdate != '') ? date('d M, Y', $regdate) : 'N/A';
        $output .= '<tr><td>'.$c++.'</td><td>'.$referral->display_name.'</td><td>'.$regdate.'</td><tr>';
    }
}
echo $output;
?>
</table>
