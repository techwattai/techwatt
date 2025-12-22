<?php
//////////////// Admin Menus -> Pages Callback Functions /////////////////////
function techwatt_dashboard_page() {
    global $wpdb;
    $counts = techwatt_get_counts();

    $total_projects = $counts['projects'];
    $total_published_projects = $counts['published_projects'];
    $total_bookings = $counts['bookings'];
    $total_children = $counts['children'];
    $total_bootcamp = $counts['bootcamp'];
    $total_futureclub = $counts['futureclub'];
    $total_testimonies = $counts['testimonies'];
    $total_cost = $counts['totalcost'];
    $total_paid = $counts['totalpaid'];
    $total_outstanding = $counts['totaloutstanding'];

    $p1 = '<p style="display:flex;margin:10px 0;"><span style="flex:2;font-weight:500;">';
    $p2 = '</span><span style="flex:1;">'; $p3 = '</span></p>';

    echo '<div class="wrap"><h1>Techwatt Dashboard</h1><p style="width:80%;">Welcome to the TechWatt Dashboard. Below is an overview of key statistics to help you manage bookings, students, projects, testimonials, payments, Bootcamp applicants, FIC applicants, and system settings.</p></div>';

    echo '<div style="display:flex;gap:20px;flex-direction:row;width:98%;">';
        
        echo '<div style="flex:1;border:dotted #ccc 3px;padding:20px;">';
            echo '<h4 style="margin:0 0 15px 0;">Statistics</h4>';
            echo $p1.'Total Booking/Accounts:'.$p2.$total_bookings.$p3;
            echo $p1.'Registered Kids:'.$p2.$total_children.$p3;
            echo $p1.'Submitted Projects:'.$p2.$total_projects.$p3;
            echo $p1.'Bootcamp Enrollments:'.$p2.$total_bootcamp.$p3;
            echo $p1.'Future Innovators Clubs (FIC) Applicants:'.$p2.$total_futureclub.$p3;
            echo $p1.'Submitted Testimonies:'.$p2.$total_testimonies.$p3;
            echo $p1.'Total Course Sales:'.$p2.DEFAULT_CURRENCY_SYMBOL.number_format($total_cost,2).$p3;
            echo $p1.'Total Course Cash-in:'.$p2.DEFAULT_CURRENCY_SYMBOL.number_format($total_paid,2).$p3;
            echo $p1.'Total Course Oustanding:'.$p2.DEFAULT_CURRENCY_SYMBOL.number_format($total_outstanding,2).$p3;
        echo '</div>';

        echo '<div style="flex:1;border:dotted #ccc 3px;padding:1px;"><img src="https://progmatech.com.ng/adverts/ads-1.webp" style="width:100%;height:auto;"></div>';

    echo '</div>';
}
//////////////////

////////////////////////
function techwatt_register_students_page() {
    global $wpdb,$ArrayPackages,$ArrayCourses;
    $page = sanitize_text_field($_GET['page'] ?? '');
    echo '<div class="wrap"><h1>Students</h1></div>';

    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        $del_id = intval($_GET['del']);
        $user = get_user_by('id', $del_id);

        if ($user && in_array('student', (array) $user->roles)) {
            // Delete user and all associated usermeta
            require_once(ABSPATH . 'wp-admin/includes/user.php');
            wp_delete_user($del_id);

            echo '<div class="notice notice-success is-dismissible"><p>✅ Student (ID: ' . esc_html($del_id) . ') has been deleted successfully.</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>⚠️ Invalid student record or user not found.</p></div>';
        }
    }

    $rows_per_page = 20; $paging = Paginator(1,$rows_per_page);
    $args = array('role' => 'student', 'orderby' => 'registered','order' => 'DESC', 'number' => $rows_per_page, 'paged' => $paging["current_page"],);
    
    $students = get_users($args);
    $total_rows = count_users();
    $total_rows = $total_rows['avail_roles']['student'] ?? 0;
    $total_pages = ceil($total_rows / $rows_per_page);
    $c = ($paging["current_page"] - 1) * $rows_per_page + 1;

    //////// Display Students ///////////
    if ($students){
        echo '<table class="wp-list-table widefat fixed striped" style="width:98%;">';
        echo '<thead><tr><th width="5%" nowrap>SN</th><th>Parent Name</th><th>Email</th><th>Phone</th><th width="15%">Children</th><th  width="20%">Packages</th><th>Registered On</th><th width="5%"  nowrap>Action</th></tr></thead>';
        echo '<tbody>';
        foreach ($students as $user){
            $tw_userdate = get_user_meta($user->ID, 'tw_userdata', true);
            
            $countrycode = $tw_userdate['countrycode'] ?? '';
            $phone = $tw_userdate['phone'] ?? '';
            $MyPhone = ($phone != '') ? $countrycode . '-' . $phone : 'N/A';
            $parentname = $tw_userdate['parentname'] ?? 'N/A';
            $regdate = $tw_userdate['regdate'] ?? '';
            $createdDate = ($regdate != '') ? date('d M, Y h:i:A',$regdate):'N/A';
            $children = $tw_userdate['children'] ?? [];
            $childList = ''; $childInfos = '';

            if (is_array($children) && !empty($children)) {
                foreach ($children as $child) {                    
                    $childList .= '<b>'.esc_html($child['name']) . '</b> (<b>Child ID:</b> '.esc_html(strtoupper($child['id'])).', <b>Age:</b> '.esc_html($child['age']).')<br>';
                    
                    $childInfos = '(<b>Course:</b> '.esc_html($ArrayCourses[$child['course']] ?? 'N/A').', <b>Package:</b> '.esc_html(ucwords($child['package'] ?? 'N/A')).', <b>Cost:</b> '.DEFAULT_CURRENCY_SYMBOL.esc_html($child['cost']).', <b>Payment Status:</b> '.esc_html($child['paymentstatus']).')<br>';
                }
            }else{
                $childList = 'N/A'; $childInfos = 'N/A';
            }
            
            $lis = [ ['Send Email Message','mailto:'.$user->user_email], ['Call Phone Number','tel:'.$countrycode.$phone], ['Whatsapp Chat','https://wa.me/'.$countrycode.$phone], ['Delete Record','?page='.esc_html($page).'&del='.esc_html($user->ID),'return confirm(\'Are you sure you want to delete student with record id: '.esc_html($user->ID).'?\');'] ];
            $ActionBtn = SetDropDown($lis);

            echo '<tr>';
            echo '<td>' . esc_html($c++) . '</td>'; //$user->ID
            echo '<td>' . esc_html($parentname) . '</td>';
            echo '<td>' . esc_html($user->user_email) . '</td>';    
            echo '<td>' . esc_html($MyPhone) . '</td>';
            echo '<td>' . $childList . '</td>';
            echo '<td>' . $childInfos . '</td>';
            echo '<td>' . esc_html($createdDate) . '</td>';
            echo '<td>'.$ActionBtn.'</td>';
            //echo '<td><a href="?page='.esc_html($page).'&del='.esc_html($user->ID).'" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to delete student with record id: '.esc_html($user->ID).'?\');"><i class="bi bi-trash"></i></a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo Paginator(2,$rows_per_page,$total_pages,$paging["current_page"]);

    }else{
        echo '<div class="notice notice-info is-dismissible"><p>No students found.</p></div>';
    }
    //////// End Display Students ///////////
}

function techwatt_bootcamp_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'bootcamp_registrations';
    $page = sanitize_text_field($_GET['page'] ?? '');

    echo '<div class="wrap"><h1>Bootcamp</h1></div>';

    ////////////// Check if Deleting////////////////
    $delid = sanitize_text_field($_GET['del'] ?? '');
    if (!empty($delid)) {
        $deleted = $wpdb->delete($table, ['id' => $delid], ['%d']);
        if ($deleted) {
            echo '<div class="notice notice-success is-dismissible"><p>✅ Registration deleted successfully.</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>⚠️ Error deleting registration.</p></div>';
        }
    }
    ////////////// End Check if Deleting////////////////
    
    //////////// Select and Display Records //////////////
    $rows_per_page = 20; $paging = Paginator(1,$rows_per_page);
    $total_rows = $wpdb->get_var("SELECT COUNT(*) FROM $table");
    $total_pages = ceil($total_rows / $rows_per_page);
    $offset = ($paging["current_page"] - 1) * $rows_per_page;
    $c = $offset + 1;

    //$results = $wpdb->get_results("SELECT * FROM $table");
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table ORDER BY id DESC LIMIT %d OFFSET %d", $rows_per_page, $offset));

    if ($results) {
        echo '<div class="notice notice-info"><p>Below is the list of people that joined bootcamp.</p></div>';
        echo '<table class="wp-list-table widefat fixed striped" style="width:98%;">';
        echo '<thead><tr><th nowrap width="5%">SN</th><th>Order ID</th><th>Name</th><th>Email Address</th><th>Phone</th><th>Bootcamp</th><th>Price</th><th nowrap>Payment Status</th><th>Date</th><th nowrap>Action</th></tr></thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            $lis = [ ['Send Email Message','mailto:'.$row->email], ['Call Phone Number','tel:'.$row->phone], ['Whatsapp Chat','https://wa.me/'.$row->phone], ['Delete Record','?page='.esc_html($page).'&del='.esc_html($row->id),'return confirm(\'Are you sure you want to delete student with record order id: '.esc_html($row->order_id).'?\');'] ];
            $ActionBtn = SetDropDown($lis);

            echo '<tr>';
            echo '<td>' . esc_html($c++) . '</td>';
            echo '<td>' . esc_html($row->order_id) . '</td>';
            echo '<td>' . esc_html($row->first_name . ' ' . $row->last_name) . '</td>';
            echo '<td>' . esc_html($row->email) . '</td>';
            echo '<td>' . esc_html($row->phone) . '</td>';
            echo '<td>' . esc_html(strtoupper($row->bootcamp_type)) . '</td>';
            echo '<td>' . esc_html(number_format($row->price, 2)) . '</td>';
            echo '<td>' . esc_html(ucfirst($row->payment_status)) . '</td>';
            echo '<td>' . esc_html($row->created_at) . '</td>';
            echo '<td>'.$ActionBtn.'</td>';
            //echo '<td><a href="?page='.esc_html($page).'&del='.esc_html($row->id).'" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to delete record order id: '.esc_html($row->order_id).'?\');"><i class="bi bi-trash"></i></a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo Paginator(2,$rows_per_page,$total_pages,$paging["current_page"]);
    } else {
        echo '<div class="notice notice-info is-dismissible"><p>No bootcamp registrations found.</p></div>';
    }
    /////////// End Select and Display Records //////////////
}

function techwatt_fclub_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'futureclub_registrations';
    $page = sanitize_text_field($_GET['page'] ?? '');
    echo '<div class="wrap"><h1>Future Innovators Club</h1></div>';

    ////////////// Check if Deleting////////////////
    $delid = sanitize_text_field($_GET['del'] ?? '');
    if (!empty($delid)) {
        $deleted = $wpdb->delete($table, ['id' => $delid], ['%d']);
        if ($deleted) {
            echo '<div class="notice notice-success is-dismissible"><p>✅ Registration deleted successfully.</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>⚠️ Error deleting registration.</p></div>';
        }
    }
    ////////////// End Check if Deleting ////////////////
    
    //////////// Select and Display Records //////////////
    $rows_per_page = 20; $paging = Paginator(1,$rows_per_page);
    $total_rows = $wpdb->get_var("SELECT COUNT(*) FROM $table");
    $total_pages = ceil($total_rows / $rows_per_page);
    $offset = ($paging["current_page"] - 1) * $rows_per_page;
    $c = $offset + 1;

    //$results = $wpdb->get_results("SELECT * FROM $table");
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table ORDER BY id DESC LIMIT %d OFFSET %d", $rows_per_page, $offset));

    if ($results) {
        echo '<div class="notice notice-info"><p>Below is the list of people that joined future innovators club.</p></div>';
        echo '<table class="wp-list-table widefat fixed striped" style="width:98%;">';
        echo '<thead><tr><th>Member ID</th><th>Name</th><th nowrap>Email Address</th><th>Phone</th><th width="20%">Address</th><th>Company</th><th>Profession</th><th>Date</th><th width="7%" nowrap>Action</th></tr></thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            //[[label,link,onclick,data=>[]], ]
            $lis = [ ['Send Email Message','mailto:'.$row->email], ['Call Phone Number','tel:'.$row->phone], ['Whatsapp Chat','https://wa.me/'.$row->phone], ['Delete Record','?page='.esc_html($page).'&del='.esc_html($row->id),'return confirm(\'Are you sure you want to delete record with member id: '.esc_html($row->memberid).'?\');'] ];
            $ActionBtn = SetDropDown($lis);

            echo '<tr>';
            echo '<td>' . esc_html($row->memberid) . '</td>';
            echo '<td>' . esc_html($row->first_name . ' ' . $row->last_name) . '</td>';
            echo '<td>' . esc_html($row->email) . '</td>';
            echo '<td>' . esc_html($row->phone) . '</td>';
            echo '<td>' . esc_html($row->address.' '.$row->country).'</td>';
            echo '<td>' . esc_html($row->company ?? 'N/A') . '</td>';
            echo '<td>' . esc_html($row->profession ?? 'N/A') . '</td>';
            echo '<td>' . esc_html($row->created_at) . '</td>';
            echo '<td align="center">' . $ActionBtn . '</td>';
            //echo '<td><a href="?page='.esc_html($page).'&del='.esc_html($row->id).'" class="btn btn-danger btn-xs" onclick="return confirm(\'Are you sure you want to delete record with member id: '.esc_html($row->memberid).'?\');"><i class="bi bi-trash"></i></a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo Paginator(2,$rows_per_page,$total_pages,$paging["current_page"]);
    } else {
        echo '<div class="notice notice-info is-dismissible"><p>No registrations found.</p></div>';
    }
    /////////// End Select and Display Records //////////////
}

function techwatt_projects_page() {
    //edit.php?post_type=kids_project
    global $wpdb;
    echo '<div class="wrap"><h1>Projects</h1></div>';

    // Handle delete action
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        $post_id = intval($_GET['del']);
        wp_delete_post($post_id, true);
        echo '<div class="notice notice-success is-dismissible"><p>✅ Project deleted successfully.</p></div>';
    }

    ///////// SELECT /////////////////////////
    //$paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $rows_per_page = 10; $paging = Paginator(1,$rows_per_page);
    $paged = $paging["current_page"];
    $args = array('post_type' => 'kids_project', 'posts_per_page' => $rows_per_page, 'paged' => $paged, 'orderby' => 'date', 'order' => 'DESC',);

    $projects = new WP_Query($args);
    if ($projects->have_posts()) {
        echo '<table class="wp-list-table widefat fixed striped" style="width:98%;">';
        echo '<thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="15%">Project Title</th>
                    <th>Child Name</th>
                    <th>Categories</th>
                    <th>Thumbnail</th>
                    <th width="35%">Short Description</th>                    
                    <th>Created Date</th>
                    <th width="6%" nowrap>Actions</th>
                </tr>
              </thead><tbody>';

        $i = (($paged - 1) * $rows_per_page) + 1;

        while ($projects->have_posts()) {
            $projects->the_post();
            $id = get_the_ID();
            $title = get_the_title();
            $author = get_the_author();
            $status = get_post_status();
            $date = get_the_date('d M, Y');

            $edit_link = get_edit_post_link($id);
            $view_link = get_permalink($id);
            $delete_link = esc_url(add_query_arg(['page' => 'techwatt-projects', 'del' => $id]));

            $short = get_post_meta($id, '_tw_short_desc', true );
            $kidName = get_post_meta($id, '_tw_kid_name', true );
            $grade = get_post_meta( $id, '_tw_grade', true ); 
            $grade = (!empty($grade)) ? $grade: 'N/A';
            $external_link = get_post_meta( $id, '_tw_external_link', true );
            $created_date = get_post_meta( $id, '_tw_created_date', true );
            $created_date = (!empty($created_date)) ? $created_date:$date;
            $thumb_url = get_the_post_thumbnail_url( $id, 'thumbnail' );

            $cats = wp_get_post_terms( $id, 'project_category', array( 'fields' => 'ids' ) );
            if ( !empty($cats) ) {
                $term_names = array();
                foreach ( $cats as $term_id ) {
                    $term = get_term( $term_id, 'project_category' );
                    if ( !is_wp_error($term) && !empty($term->name) ) {
                        $term_names[] = $term->name;
                    }
                }
                $category_list = implode(', ', $term_names);
            } else {
                $category_list = '—';
            }


            $lis = [ ['Project External Link',esc_url($external_link)], ['Edit Project',esc_url($edit_link)], ['Delete Project',esc_url($delete_link),'return confirm(\'Are you sure you want to delete this project?\');'] ];
            $ActionBtn = SetDropDown($lis);

            echo '<tr>';
            echo '<td>' . esc_html($i++) . '</td>';
            echo '<td><strong>' . esc_html($title) . '</strong></td>';
            echo '<td>' . esc_html($kidName) . '</td>';
            echo '<td>' . esc_html($category_list) . '</td>';
            echo '<td><img src="' . esc_url($thumb_url) . '" alt="" style="width:50px;height:50px;object-fit:cover;border-radius:5px;"></td>';
            echo '<td>' . esc_html($short) . '</td>';
            echo '<td>' . esc_html($created_date) . '</td>';
            echo '<td>'.$ActionBtn.'</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        $total_pages = $projects->max_num_pages;
        echo Paginator(2,$rows_per_page,$total_pages,$paged);
        wp_reset_postdata();
    }else{
        echo '<div class="notice notice-info is-dismissible"><p>No projects found.</p></div>';
    }
}

function techwatt_testimonials_page() {
    wp_redirect(admin_url('edit.php?post_type=testimony')); exit;
}

function techwatt_payments_page(){
    global $wpdb,$ArrayPackages,$ArrayCourses;
    $table = $wpdb->prefix . 'course_payments';
    $page = sanitize_text_field($_GET['page'] ?? '');
    echo '<div class="wrap" style="margin-bottom:10px;"><h1>Course Payments</h1>This page lists all course payment transactions made by members.</div>';
    ////////////// Check if Deleting////////////////
    $delid = sanitize_text_field($_GET['del'] ?? '');
    if (!empty($delid)) {
        $deleted = $wpdb->delete($table, ['id' => $delid], ['%d']);
        if ($deleted) {
            echo '<div class="notice notice-success is-dismissible"><p>✅ Payment record deleted successfully.</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>⚠️ Error deleting payment record.</p></div>';
        }
    }
    ////////////// End Check if Deleting ////////////////
    //////////// Select and Display Records //////////////
    $rows_per_page = 20; $paging = Paginator(1,$rows_per_page);
    $total_rows = $wpdb->get_var("SELECT COUNT(*) FROM $table");
    $total_pages = ceil($total_rows / $rows_per_page);
    $offset = ($paging["current_page"] - 1) * $rows_per_page;
    $c = $offset + 1;
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table ORDER BY id DESC LIMIT %d OFFSET %d", $rows_per_page, $offset));
    if ($results) {
        echo '<table class="wp-list-table widefat fixed striped" style="width:98%;">';
        echo '<thead><tr><th nowrap width="5%">SN</th><th>Ref. No</th><th>Parent Name/ID</th><th>Child ID</th><th>Course</th><th>Amount Paid</th><th>Payment Status</th><th>Payment Date</th></thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            $lis = [ ['Delete Record','?page='.esc_html($page).'&del='.esc_html($row->id),'return confirm(\'Are you sure you want to delete payment record with Ref. ID: #'.esc_html($row->refno).'?\');'] ];
            $ActionBtn = SetDropDown($lis);

            $shortrefno = (strlen($row->refno) > 15) ? substr($row->refno,0,15).'...' : $row->refno;

            echo '<tr>';
            echo '<td>' . esc_html($c++) . '</td>';
            echo '<td><a href="javascript:alert(\'Stripe reference number: '.esc_html($row->refno).'\');" title="'.esc_html($row->refno).'">' . esc_html($shortrefno) . '</a></td>';
            echo '<td>' . esc_html($row->parent_name) . '('.esc_html($row->userid).')</td>';
            echo '<td>' . esc_html(strtoupper($row->childid)) . '</td>';
            echo '<td>' . esc_html(ucwords($row->course) ?? 'N/A') . '</td>';
            echo '<td>' . esc_html(number_format($row->amount, 2)) .' ('.esc_html($row->currency).')'. '</td>';
            echo '<td>' . PS_Status(ucfirst($row->payment_status),strtolower($row->payment_status)) . '</td>';
            echo '<td>' . esc_html($row->created_at) . '</td>';
            //echo '<td>'.$ActionBtn.'</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo Paginator(2,$rows_per_page,$total_pages,$paging["current_page"]);
    } else {
        echo '<div class="notice notice-info is-dismissible"><p>No payment records found.</p></div>';
    }
    /////////// End Select and Display Records //////////////
}
?>