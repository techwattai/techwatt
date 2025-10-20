<?php
function tw_kids_project_default_categories() {
    $ArrayCourses = [
        "robotics"=>"Robotics",
        "python4ai"=>"Python for AI",
        "cv4kids"=>"Computer vision for Kids",
        "ai4kids"=>"AI for Kids",
        "dataanalysis"=>"Introduction to Data Analysis",
        "gameprog"=>"Game Programming",
        "webdesign"=>"Website Designing"
    ];
    return apply_filters('tw_kids_project_categories', $ArrayCourses);
}

/* ---------- Register CPT & Taxonomy ---------- */
function tw_register_kids_project_cpt() {
    $labels = array(
        'name'               => __( 'Kids Projects', 'tw' ),
        'singular_name'      => __( 'Kids Project', 'tw' ),
        'add_new'            => __( 'Add New Project', 'tw' ),
        'add_new_item'       => __( 'Add New Kids Project', 'tw' ),
        'edit_item'          => __( 'Edit Kids Project', 'tw' ),
        'new_item'           => __( 'New Kids Project', 'tw' ),
        'all_items'          => __( 'All Kids Projects', 'tw' ),
        'view_item'          => __( 'View Project', 'tw' ),
        'search_items'       => __( 'Search Projects', 'tw' ),
        'not_found'          => __( 'No projects found', 'tw' ),
        'not_found_in_trash' => __( 'No projects found in Trash', 'tw' ),
        'menu_name'          => __( 'Kids Projects', 'tw' ),
    );

    register_post_type( 'kids_project', array(
        'labels'             => $labels,
        'public'             => false,          // not publicly queryable by default
        'show_ui'            => true,
        'show_in_menu'       => true,
        'capability_type'    => 'post',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'author' ),
        'has_archive'        => false,
        'show_in_rest'       => true,
    ) );

    register_taxonomy( 'project_category', 'kids_project', array(
        'labels' => array(
            'name' => __( 'Project Categories', 'tw' ),
            'singular_name' => __( 'Project Category', 'tw' ),
        ),
        'hierarchical' => true,
        'show_ui'      => true,
        'show_in_rest' => true,
    ) );

    // ✅ Ensure default categories exist (safe to run anytime)
    $existing_terms = get_terms(array(
        'taxonomy' => 'project_category',
        'hide_empty' => false,
        'fields' => 'slugs',
    ));

    if ( empty($existing_terms) || is_wp_error($existing_terms) ) {
        $default_cats = tw_kids_project_default_categories();
        foreach ($default_cats as $slug => $name) {
            if ( ! term_exists($name, 'project_category') ) {
                wp_insert_term($name, 'project_category', ['slug' => sanitize_title($slug)]);
            }
        }
    }

}
add_action( 'init', 'tw_register_kids_project_cpt' );

/* Seed categories on plugin activation //////
function tw_kids_projects_activate() {
    $cats = tw_kids_project_default_categories();
    foreach ( $cats as $slug => $name ) {
        if ( ! term_exists( $name, 'project_category' ) ) {
            wp_insert_term( $name, 'project_category', [ 'slug' => sanitize_title( $slug ) ] );
        }
    }
}
register_activation_hook( PS_PLUGIN_FILE, 'tw_kids_projects_activate' );
*/

/* ---------- Metabox for project details ---------- */
function tw_add_kids_project_metabox() {
    add_meta_box('tw_kids_project_meta',
        __( 'Project Details', 'tw' ),
        'tw_kids_project_metabox_cb',
        'kids_project',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'tw_add_kids_project_metabox' );

function tw_kids_project_metabox_cb( $post ) {
    wp_nonce_field( 'tw_kids_project_save', 'tw_kids_project_nonce' );

    $short = get_post_meta( $post->ID, '_tw_short_desc', true );
    $kid_name = get_post_meta( $post->ID, '_tw_kid_name', true );
    $grade = get_post_meta( $post->ID, '_tw_grade', true );
    $external_link = get_post_meta( $post->ID, '_tw_external_link', true );
    $created_date = get_post_meta( $post->ID, '_tw_created_date', true );
    // CHANGED: get assigned category IDs
    $cats = wp_get_post_terms( $post->ID, 'project_category', array( 'fields' => 'ids' ) );

    // category options from our default array
    $categories = tw_kids_project_default_categories();
?>
    <p>
        <label><strong><?php _e('Short Description','tw'); ?></strong></label><br>
        <textarea name="tw_short_desc" rows="4" style="width:100%;"><?php echo esc_textarea( $short ); ?></textarea>
    </p>

    <p>
        <label><strong><?php _e("Project Category",'tw'); ?></strong></label><br>
        <select name="tw_project_category" style="width:100%;">
            <option value=""><?php _e('Select category','tw'); ?></option>
            <?php
            // Populate from taxonomy terms if exist, otherwise fallback to default array
            $terms = get_terms( array('taxonomy'=>'project_category','hide_empty'=>false) );
            if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                foreach ( $terms as $t ) {
                    // CHANGED: option value is term_id and selection checks compare IDs
                    echo '<option value="'.esc_attr($t->term_id).'" '.(in_array($t->term_id, $cats) ? 'selected' : '').'>'.esc_html($t->name).'</option>';
                }
            } else {
                // fallback: ensure default array terms exist and fetch them
                $default = tw_kids_project_default_categories();
                foreach ( $default as $slug => $name ) {
                    $term = get_term_by('slug', sanitize_title($slug), 'project_category');
                    if (!$term) {
                        $new = wp_insert_term($name, 'project_category', ['slug' => sanitize_title($slug)]);
                        if (!is_wp_error($new) && isset($new['term_id'])) {
                            $term_id = $new['term_id'];
                        } else {
                            continue;
                        }
                    } else {
                        $term_id = $term->term_id;
                    }
                    echo '<option value="'.esc_attr($term_id).'" '.(in_array($term_id, $cats) ? 'selected' : '').'>'.esc_html($name).'</option>';
                }
            }
            ?>
        </select>
    </p>

    <p>
        <label><strong><?php _e("Kid's Name",'tw'); ?></strong></label><br>
        <input type="text" name="tw_kid_name" value="<?php echo esc_attr( $kid_name ); ?>" style="width:100%;">
    </p>

    <p>
        <label><strong><?php _e('Grade','tw'); ?></strong></label><br>
        <input type="text" name="tw_grade" value="<?php echo esc_attr( $grade ); ?>" style="width:100%;">
    </p>

    <p>
        <label><strong><?php _e('External Link','tw'); ?></strong></label><br>
        <input type="url" name="tw_external_link" value="<?php echo esc_attr( $external_link ); ?>" style="width:100%;">
    </p>

    <p>
        <label><strong><?php _e('Created Date','tw'); ?></strong></label><br>
        <input type="date" name="tw_created_date" value="<?php echo esc_attr( $created_date ); ?>">
    </p>

    <p>
        <em><?php _e('Project image uses the Featured Image.','tw'); ?></em>
    </p>
    <?php
}

/* Save meta on admin save_post */
function tw_save_kids_project_meta( $post_id ) {
    if ( ! isset( $_POST['tw_kids_project_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['tw_kids_project_nonce'], 'tw_kids_project_save' ) ) return;
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
    if ( ! current_user_can('edit_post', $post_id ) ) return;

    if ( isset( $_POST['tw_short_desc'] ) ) {
        update_post_meta( $post_id, '_tw_short_desc', sanitize_textarea_field( $_POST['tw_short_desc'] ) );
    } else {
        delete_post_meta( $post_id, '_tw_short_desc' );
    }

    if ( isset( $_POST['tw_kid_name'] ) ) {
        update_post_meta( $post_id, '_tw_kid_name', sanitize_text_field( $_POST['tw_kid_name'] ) );
    }

    if ( isset( $_POST['tw_grade'] ) ) {
        update_post_meta( $post_id, '_tw_grade', sanitize_text_field( $_POST['tw_grade'] ) );
    }

    if ( isset( $_POST['tw_external_link'] ) ) {
        update_post_meta( $post_id, '_tw_external_link', esc_url_raw( $_POST['tw_external_link'] ) );
    }

    if ( isset( $_POST['tw_created_date'] ) ) {
        update_post_meta( $post_id, '_tw_created_date', sanitize_text_field( $_POST['tw_created_date'] ) );
    }

    // project category (set taxonomy).....
    if ( ! empty( $_POST['tw_project_category'] ) ) {
        $val = $_POST['tw_project_category'];

        // CHANGED: expect / prefer term ID. If numeric treat as ID; otherwise try slug/name/create.
        if ( is_numeric( $val ) ) {
            $term = get_term( intval( $val ), 'project_category' );
            if ( $term && ! is_wp_error( $term ) ) {
                wp_set_post_terms( $post_id, array( intval($val) ), 'project_category' );
            }
        } else {
            // val may be slug or name - find or create and then set by term_id
            $catslug = sanitize_title($val);
            $term = get_term_by( 'slug', $catslug, 'project_category' );
            if ( ! $term ) {
                $term = get_term_by( 'name', sanitize_text_field( $val ), 'project_category' );
            }
            if ( ! $term ) {
                $new = wp_insert_term( sanitize_text_field($val), 'project_category', array( 'slug' => $catslug ) );
                if ( ! is_wp_error( $new ) && isset($new['term_id']) ) {
                    wp_set_post_terms( $post_id, array( intval($new['term_id']) ), 'project_category' );
                }
            } else {
                wp_set_post_terms( $post_id, array( intval($term->term_id) ), 'project_category' );
            }
        }
    } else {
        wp_set_post_terms( $post_id, array(), 'project_category' );
    }
}
add_action( 'save_post_kids_project', 'tw_save_kids_project_meta' );

/* Frontend shortcodes & AJAX handlers - Helper: returns project array data */
function tw_get_project_data( $post_id ) {
    $post = get_post( $post_id );
    if ( ! $post || $post->post_type !== 'kids_project' ) return false;

    $short = get_post_meta( $post_id, '_tw_short_desc', true );
    $kid_name = get_post_meta( $post_id, '_tw_kid_name', true );
    $grade = get_post_meta( $post_id, '_tw_grade', true );
    $external_link = get_post_meta( $post_id, '_tw_external_link', true );
    $created_date = get_post_meta( $post_id, '_tw_created_date', true );
    // CHANGED: get both names and ids so display logic can still use names but form can use ids
    $cats_names = wp_get_post_terms( $post_id, 'project_category', array('fields'=>'names') );
    $cats_ids   = wp_get_post_terms( $post_id, 'project_category', array('fields'=>'ids') );
    $thumbnail = get_the_post_thumbnail( $post_id, 'medium' );

    return array(
        'ID' => $post_id,
        'title' => get_the_title( $post_id ),
        'content' => apply_filters( 'the_content', $post->post_content ),
        'short' => $short,
        'kid_name' => $kid_name,
        'grade' => $grade,
        'external_link' => $external_link,
        'created_date' => $created_date,
        'categories' => $cats_names,   // keep compatibility
        'category_ids' => $cats_ids,   // CHANGED: new - first id can be used by form
        'thumbnail' => $thumbnail,
        'author' => $post->post_author,
        'edit_link' => get_edit_post_link( $post_id ),
    );
}

/* Shortcode: form for add/edit - Usage: [kids_project_form id="123"] (omit id to create new) */
function tw_kids_project_form_shortcode( $atts ) {
    //global $children;
    $atts = shortcode_atts( array( 'id' => 0,'isadmin'=>'1' ), $atts, 'kids_project_form' );
    $id = intval( $atts['id'] ); $isadmin = boolval( $atts['isadmin'] );
    
    if ( ! is_user_logged_in() ) {
        return '<p>Please log in to add or edit projects.</p>';
    }

    $current_user = wp_get_current_user();
    $userID = $current_user->ID;
    $children = get_user_meta($userID, 'children',true);

    $data = array(
        'title' => '',
        'short' => '',
        'kid_name' => '',
        'grade' => '',
        'external_link' => '',
        'created_date' => '',
        'category' => '',
        'category_id' => 0,
    );

    if ( $id ) {
        $post = get_post( $id );
        if ( ! $post || $post->post_type !== 'kids_project' ) {
            return '<p>Invalid project ID.</p>';
        }
        // allow edit only for author or users who can edit others' posts
        if ( $post->post_author != $current_user->ID && ! current_user_can( 'edit_others_posts' ) ) {
            return '<p>You do not have permission to edit this project.</p>';
        }

        $data = tw_get_project_data( $id );
        // CHANGED: standardize selected category id (first assigned)
        $data['category_id'] = !empty($data['category_ids'][0]) ? intval($data['category_ids'][0]) : 0;
    }

    // Build form HTML
    ob_start();
    ?>
    <form id="tw-kids-project-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url('admin-ajax.php') ); ?>" style="width:100%;">
        <div id="tw-kp-message" style="display:none;margin-top:10px;"></div>
        <?php wp_nonce_field( 'tw_kids_project_frontend', 'tw_kids_project_frontend_nonce' ); ?>
        <input type="hidden" name="action" value="tw_submit_kids_project">
        <input type="hidden" name="project_id" value="<?php echo esc_attr( $id ); ?>">
        
        <div id="kpform" style="display:flex;gap:20px;flex-direction:row;">
            <div style="flex:1;">
                <p><label>Project Title *<span class="break"></span>
                <input type="text" name="project_title" value="<?php echo esc_attr( $data['title'] ); ?>" required style="width:100%"></label></p>

                <p><label>Short Description *<span class="break"></span>
                    <textarea name="project_short" rows="4" style="width:100%"><?php echo esc_textarea( $data['short'] ); ?></textarea>
                </label></p>

                <p><label>Project Category *<span class="break"></span>
                    <?php
                    // CHANGED: pass the selected term ID to the dropdown helper
                    echo tw_get_project_category_dropdown(intval($data['category_id']));
                    ?></label>
                </p>

                <?php if($isadmin === '1'):  ?>
                <p><label>Grade<span class="break"></span>
                    <input type="text" name="grade" value="<?php echo esc_attr($data['grade']); ?>" style="width:100%"></label>
                </p>
                <?php endif; ?>
            </div>

        <div style="flex:1;">
        
        <p><label>Kid's Name *<span class="break"></span>
            <?php
            if(isset($children) && !empty($children)){
                        echo '<select name="kid_name">';
                        echo '<option value="">Choose a name</option>';
                        foreach($children as $childKey=>$child){
                            $sel = (strtolower($data['kid_name']) === strtolower($child["name"])) ? 'selected' : '';
                            echo '<option value="'.esc_attr($child["name"]).'" '.$sel.'>'.esc_html($child["name"]).'</option>';
                        }
                        echo '</select>';
                        //$child["id"].........
            }else{
                echo '<input type="text" name="kid_name" value="'.esc_attr($data['kid_name']).'" style="width:100%"></label>';
            }
            ?>            
        </p>

        <p><label>Project Url Link * <small><i>(External url to your project)</i></small><span class="break"></span>
            <input type="url" name="external_link" value="<?php echo esc_attr($data['external_link']); ?>" style="width:100%"></label>
        </p>

        <p><label>Created Date<span class="break"></span>
            <input type="date" name="created_date" value="<?php echo esc_attr($data['created_date']); ?>"></label>
        </p>

        <p><label>Project Image * <small><i>(featured image)</i></small><span class="break"></span>
            <input type="file" name="project_image" accept="image/*"></label>
        </p>
        </div>
        </div>
        <p style="margin-top:20px;"><button type="submit" class="button">Submit Project</button></p>
    </form>

    <script>
    (function(){
        const form = document.getElementById('tw-kids-project-form');
        if(!form) return;

        form.addEventListener('submit', async function(e){
            e.preventDefault();
            const msg = document.getElementById('tw-kp-message');
            const btn = form.querySelector('button[type="submit"]');
            const orig = btn.textContent;
            btn.disabled = true; btn.textContent = 'Saving...';
            msg.style.display = 'none';

            const fd = new FormData(form);
            const url = form.getAttribute("action");

            try { 
                const res = await fetch(url, { method: 'POST', body: fd });
                const json = await res.json();
                
                if ( json.success ) {
                    msg.style.cssText = 'background:#d4edda;color:#155724;padding:10px;border-radius:4px;';
                    msg.innerHTML = '✅ ' + (json.data.message || 'Saved');
                    msg.style.display = 'block';
                    form.reset(); //for new project clear form (optional)
                } else {
                    msg.style.cssText = 'background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;';
                    msg.innerHTML = '❌ ' + (json.data && json.data.message ? json.data.message : 'Error saving');
                    msg.style.display = 'block';
                }
            } catch (err) {
                msg.style.cssText = 'background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;';
                msg.innerHTML = '⚠️ ' + (err.message || 'Network error');
                msg.style.display = 'block';
            } finally {
                btn.disabled = false; btn.textContent = orig;
            }
        });
    })();
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'kids_project_form', 'tw_kids_project_form_shortcode' );

/* ---------- AJAX: handle frontend form submission ---------- */
add_action( 'wp_ajax_tw_submit_kids_project', 'tw_submit_kids_project' );
//add_action( 'wp_ajax_nopriv_tw_submit_kids_project', 'tw_submit_kids_project' );

function tw_submit_kids_project() {
    // require login for creation/editing
    if (!is_user_logged_in() ) {
        wp_send_json_error( array( 'message' => 'You must be logged in to add a project.' ) ); return;
    }

    if ( ! isset( $_POST['tw_kids_project_frontend_nonce'] ) || ! wp_verify_nonce( $_POST['tw_kids_project_frontend_nonce'], 'tw_kids_project_frontend' ) ) {
        wp_send_json_error( array( 'message' => 'Permission denied.' ) ); return;
    }

    $current_user = wp_get_current_user();
    $project_id = isset( $_POST['project_id'] ) ? intval( $_POST['project_id'] ) : 0;

    $title = sanitize_text_field( $_POST['project_title'] ?? '' );
    $short = sanitize_textarea_field( $_POST['project_short'] ?? '' );
    $kid_name = sanitize_text_field( $_POST['kid_name'] ?? '' );
    $grade = sanitize_text_field( $_POST['grade'] ?? '' );
    $external_link = esc_url_raw( $_POST['external_link'] ?? '' );
    $created_date = sanitize_text_field( $_POST['created_date'] ?? '' );
    // CHANGED: accept category as ID or string (but prefer ID)
    $cat_raw = $_POST['project_category'] ?? '';

    if (empty($title)){ wp_send_json_error( array( 'message' => 'Title is required.')); return; }
    if (empty($short)){ wp_send_json_error( array( 'message' => 'Project short description is required.')); return; }
    if (empty($kid_name)){ wp_send_json_error( array( 'message' => 'Kid\'s name is required.')); return; }
    if (empty($external_link)){ wp_send_json_error( array( 'message' => 'Project url is required.')); return; }
    if (empty( $_FILES['project_image'] ) && empty( $_FILES['project_image']['name'] ) ) {
        //wp_send_json_error( array( 'message' => 'Project featured image is required.')); return;
    }

    $post_data = array(
        'post_title' => $title,
        'post_content' => wp_kses_post( wp_unslash( $_POST['project_short'] ?? '' ) ),
        'post_type' => 'kids_project',
        'post_status' => 'publish',
    );

    if ( $project_id ) {
        // update
        $post = get_post( $project_id );
        if ( ! $post || $post->post_type !== 'kids_project' ) {
            wp_send_json_error( array( 'message' => 'Invalid project.' ) );
        }
        // only author or users with edit_others_posts can update
        if ( $post->post_author != $current_user->ID && ! current_user_can( 'edit_others_posts' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied.' ) );
        }
        $post_data['ID'] = $project_id;
        $post_data['post_status'] = $post->post_status;
        $new_id = wp_update_post( $post_data, true );
        if ( is_wp_error( $new_id ) ) {
            wp_send_json_error( array( 'message' => $new_id->get_error_message() ) );
        } else {
            $pid = $project_id;
        }
    } else {
        // create
        $post_data['post_author'] = $current_user->ID;
        $pid = wp_insert_post( $post_data, true );
        if ( is_wp_error( $pid ) ) {
            wp_send_json_error( array( 'message' => $pid->get_error_message() ) );
        }
    }

    // save meta
    update_post_meta( $pid, '_tw_short_desc', $short );
    update_post_meta( $pid, '_tw_kid_name', $kid_name );
    update_post_meta( $pid, '_tw_grade', $grade );
    update_post_meta( $pid, '_tw_external_link', $external_link );
    update_post_meta( $pid, '_tw_created_date', $created_date );

    // category (set taxonomy) - CHANGED to use term IDs consistently
    if ( ! empty( $cat_raw ) ) {
        if ( is_numeric( $cat_raw ) ) {
            $term = get_term( intval( $cat_raw ), 'project_category' );
            if ( $term && ! is_wp_error( $term ) ) {
                wp_set_post_terms( $pid, array( intval($cat_raw) ), 'project_category' );
            }
        } else {
            // if it's not numeric, it's probably a slug or name - try to find by slug/name, else create then use ID
            $cat_slug = sanitize_title( $cat_raw );
            $term = get_term_by( 'slug', $cat_slug, 'project_category' );
            if ( ! $term ) {
                $term = get_term_by( 'name', sanitize_text_field( $cat_raw ), 'project_category' );
            }

            if ( ! $term ) {
                $new = wp_insert_term( ucwords( str_replace( '-', ' ', $cat_slug ) ), 'project_category', array( 'slug' => $cat_slug ) );
                if ( ! is_wp_error( $new ) && isset( $new['term_id'] ) ) {
                    wp_set_post_terms( $pid, array( intval($new['term_id']) ), 'project_category' );
                }
            } else {
                wp_set_post_terms( $pid, array( intval($term->term_id) ), 'project_category' );
            }
        }
    } else {
        // if empty, clear
        wp_set_post_terms( $pid, array(), 'project_category' );
    }


    // handle image upload if provided
    if ( ! empty( $_FILES['project_image'] ) && ! empty( $_FILES['project_image']['name'] ) ) {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );

        // allow only images
        $attachment_id = media_handle_upload( 'project_image', $pid );
        if ( ! is_wp_error( $attachment_id ) ) {
            set_post_thumbnail( $pid, $attachment_id );
        }
    }

    wp_send_json_success( array( 'message' => 'Project saved successfully.', 'project_id' => $pid ) );
}

/* Shortcode: grid display ----- [kids_projects_grid limit="8" columns="4" user_id=""] */
function tw_kids_projects_grid_shortcode( $atts ) {
    $atts = shortcode_atts( array('limit' => 8, 'columns' => 4, 'user_id' => 0, ), $atts, 'kids_projects_grid' );

    $args = array(
        'post_type' => 'kids_project',
        'posts_per_page' => intval( $atts['limit'] ),
        'orderby' => 'date',
        'order' => 'DESC',
    );

    if ( ! empty( $atts['user_id'] ) ) {
        $args['author'] = intval( $atts['user_id'] );
    }

    $q = new WP_Query( $args );

    ob_start();
    if ( $q->have_posts() ) {
        $col = max(1, min(6, intval($atts['columns'])) );
        echo '<div class="tw-kp-grid" style="display:grid;grid-template-columns:repeat('.$col.',1fr);gap:16px;">';
        while ( $q->have_posts() ) {
            $q->the_post();
            $pid = get_the_ID();
            $data = tw_get_project_data( $pid );

            $projectUrl = $data['external_link'];
            if (empty($projectUrl)){ $projectUrl = '#'; }
            ?>
            <div class="tw-kp-item" style="border:1px solid #eee;padding:12px;">
                <div class="tw-kp-thumb"><?php echo $data['thumbnail'] ?: '<div style="width:100%;height:140px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;color:#999;">No image</div>'; ?></div>
                <h3 style="margin:8px 0;"><a href="<?php _e($projectUrl,'tw'); ?>"><?php echo esc_html( $data['title'] ); ?></a></h3>
                <div style="font-size:14px;color:#444;"><?php echo esc_html( $data['short'] ); ?></div>
                <div style="margin-top:8px;font-size:13px;color:#666;">By: <?php echo esc_html( $data['kid_name'] ); ?> | Grade: <?php echo esc_html( $data['grade'] ); ?></div>
                <?php if ( ! empty( $data['external_link'] ) ) : ?>
                    <div style="margin-top:8px;"><a href="<?php _e($projectUrl,'tw'); ?>" target="_blank" rel="noopener">View Project</a></div>
                <?php endif; ?>
            </div>
            <?php
        }
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning">No projects found.</div>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'kids_projects_grid', 'tw_kids_projects_grid_shortcode' );

/* [kids_projects_list user_id=""] */
function tw_kids_projects_list_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'user_id' => 0,'limit' => 10 ), $atts, 'kids_projects_list' );

    $args = array(
        'post_type'      => 'kids_project', 'posts_per_page' => intval( $atts['limit'] ),
        'orderby'        => 'date', 'order'          => 'DESC'
    );

    if ( ! empty( $atts['user_id'] ) ) $args['author'] = intval( $atts['user_id'] );

    $q = new WP_Query( $args );
    ob_start();
    if ( $q->have_posts() ) {
        echo '<ul class="tw-kp-list" style="list-style:none;padding:0;margin:0;">';
        while ( $q->have_posts() ) {
            $q->the_post();
            $pid = get_the_ID();
            $d = tw_get_project_data($pid);
            $limitXters = 45;

            $short_trimmed = (strlen($d['short']) > $limitXters) ? substr($d['short'], 0, $limitXters) . '...' : $d['short'];
            $cats = !empty($d['categories']) ? implode(', ', $d['categories']) : 'N/A';
            $projectUrl = $d['external_link'];
            if (empty($projectUrl)){ $projectUrl = '#'; }

            $popup_html = "<b>Description:</b><br>" . wp_kses_post($d['short']) .
              "<p style='padding:10px 0 0 0;'><a href='{$projectUrl}' target='_blank'>View Project</a></p>";
            ?>
            <li class="tw-kp-item" style="display:inline-block;margin:5px 5px 5px 0;width:48%;border:1px solid #eee;padding:10px 10px 8px 10px;position:relative;">
            <div style="display:flex;align-items:flex-start;gap:12px;">
                <div class="tw-kp-thumb" style="width:80px;flex-shrink:0;">
                    <?php echo ($d['thumbnail'])? str_replace('<img ', '<img style="width:80px;height:80px;object-fit:cover;" ', $d['thumbnail']) : '<div style="width:80px;height:80px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;font-size:12px;color:#999;">No Image</div>'; ?>
                </div>
                <div class="tw-kp-details" style="flex:1;">
                    <h4 style="margin:0 0 4px;color:#f60;font-size:18px;"><a href="<?php _e($projectUrl); ?>" target="_Blank"><?php echo esc_html($d['title']); ?></a></h4>
                    <p style="margin:0;font-size:13px;color:#555;">
                        <strong class="bi bi-person-circle"></strong> <?php echo esc_html($d['kid_name']); ?>
                        <strong style="margin-left:8px;">Category:</strong> <?php echo esc_html($cats); ?>
                    </p>
                    <p style="margin:4px 0;font-size:13px;color:#444;">
                        <?php echo esc_html($short_trimmed); ?>
                        <?php if (strlen($d['short']) > $limitXters): ?>
                            <span class="tw-info-icon bi bi-info-circle" data-full="<?php echo esc_attr($popup_html); ?>" style="cursor:pointer;color:#0073aa;margin-left:3px;" title="View full description"></span>
                        <?php endif; ?>
                    </p>
                    <a href="<?php _e(home_url('/portal?edit-project&projid='.$pid),'tw')?>" class="bi bi-pencil-square" style="cursor:pointer;color:#666;position:absolute;top:5px;right:5px;" title="Edit project"></a>
                </div>
            </div>
            </li>
            <?php
        }
        echo '</ul>';
    } else {
        echo '<p>No projects found.</p>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'kids_projects_list', 'tw_kids_projects_list_shortcode' );

/* Shortcode: single project view -  [kids_project_view id="123"] */
function tw_kids_project_view_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'id' => 0 ), $atts, 'kids_project_view' );
    $id = intval( $atts['id'] );
    if ( ! $id ) return '<p>No project ID provided.</p>';
    $data = tw_get_project_data( $id );
    if ( ! $data ) return '<p>Invalid project.</p>';

    ob_start();
    echo '<div class="tw-kp-single">';
    echo '<div class="tw-kp-thumb">'.$data['thumbnail'].'</div>';
    echo '<h2>'.esc_html($data['title']).'</h2>';
    echo '<div>'.wp_kses_post($data['content']).'</div>';
    echo '<p><strong>Kid:</strong> '.esc_html($data['kid_name']).' | <strong>Grade:</strong> '.esc_html($data['grade']).'</p>';
    if ( ! empty($data['external_link']) ) {
        echo '<p><a href="'.esc_url($data['external_link']).'" target="_blank">External link</a></p>';
    }
    echo '</div>';
    return ob_get_clean();
}
add_shortcode( 'kids_project_view', 'tw_kids_project_view_shortcode' );
/////////////////////////////

// CHANGED: dropdown helper now uses term IDs as values and accepts selected term ID
function tw_get_project_category_dropdown( $selected_id = 0 ) {
    $terms = get_terms( array('taxonomy'=>'project_category','hide_empty'=>false) );
    if ( is_wp_error($terms) || empty($terms) ) return '';
    $out = '<select name="project_category" style="width:100%">';
    $out .= '<option value="">Select category</option>';
    foreach ( $terms as $t ) {
        // selected compares IDs
        $sel = selected( intval($selected_id), intval($t->term_id), false );
        $out .= '<option value="'.esc_attr(intval($t->term_id)).'" '.$sel.'>'.esc_html($t->name).'</option>';
    }
    $out .= '</select>';
    return $out;
}
///////////////
// Shortcode to count number of kids_project posts
function tw_kids_projects_count_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'user_id'   => 0,
        'category'  => '', // can be slug or ID
        'status'    => 'publish',
    ), $atts, 'kids_projects_count' );

    $args = array(
        'post_type'      => 'kids_project',
        'post_status'    => $atts['status'],
        'posts_per_page' => -1,
        'fields'         => 'ids',
    );

    // Filter by author (user)
    if ( ! empty( $atts['user_id'] ) ) {
        $args['author'] = intval( $atts['user_id'] );
    }

    // Filter by category (taxonomy)
    if ( ! empty( $atts['category'] ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'project_category',
                'field'    => is_numeric( $atts['category'] ) ? 'term_id' : 'slug',
                'terms'    => $atts['category'],
            ),
        );
    }

    $query = new WP_Query( $args );
    $count = $query->found_posts;
    wp_reset_postdata();

    return $count;
}
add_shortcode( 'kids_projects_count', 'tw_kids_projects_count_shortcode' );

?>
