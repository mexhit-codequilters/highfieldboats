<?php
// Add to functions.php or as a separate plugin

// 1. Create database table for client submissions
function create_client_submissions_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'client_submissions';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        civility varchar(10) NOT NULL,
        first_name varchar(100) NOT NULL,
        last_name varchar(100) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(50),
        address_street text,
        address_code varchar(20),
        address_locality varchar(100),
        address_state varchar(100),
        address_country varchar(100),
        message text,
        agency_id mediumint(9),
        agency_name varchar(255),
        receive_newsletter tinyint(1) DEFAULT 0,
        status varchar(20) DEFAULT 'new',
        assigned_to bigint(20),
        notes text,
        follow_up_date datetime,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'create_client_submissions_table');

// 2. Handle form submission
function handle_contact_form_submission() {
    global $wpdb;
    
    if (!wp_verify_nonce($_POST['contact_simple_nonce'], 'contact_simple_nonce')) {
        wp_redirect(add_query_arg('contact_status', 'nonce_fail', wp_get_referer()));
        exit;
    }
    
    $data = $_POST['model_contact'];
    
    // Validate required fields
    $required = ['first_name', 'last_name', 'email'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            wp_redirect(add_query_arg('contact_status', 'validation_fail', wp_get_referer()));
            exit;
        }
    }
    
    // Insert into database
    $table_name = $wpdb->prefix . 'client_submissions';
    $result = $wpdb->insert(
        $table_name,
        array(
            'civility' => sanitize_text_field($data['civility']),
            'first_name' => sanitize_text_field($data['first_name']),
            'last_name' => sanitize_text_field($data['last_name']),
            'email' => sanitize_email($data['email']),
            'phone' => sanitize_text_field($data['phone']),
            'address_street' => sanitize_text_field($data['address_street']),
            'address_code' => sanitize_text_field($data['address_code']),
            'address_locality' => sanitize_text_field($data['address_locality']),
            'address_state' => sanitize_text_field($data['address_state']),
            'address_country' => sanitize_text_field($data['address_country']),
            'message' => sanitize_textarea_field($data['message']),
            'agency_id' => intval($data['agency_id']),
            'receive_newsletter' => isset($data['receive_newsletter']) ? 1 : 0,
            'status' => 'new'
        )
    );
    
    if ($result === false) {
        wp_redirect(add_query_arg('contact_status', 'db_error', wp_get_referer()));
    } else {
        // Send notification email
        $to = get_option('admin_email');
        $subject = 'New Client Submission';
        $message = "A new client has submitted information:\n\n";
        $message .= "Name: {$data['first_name']} {$data['last_name']}\n";
        $message .= "Email: {$data['email']}\n";
        $message .= "Phone: {$data['phone']}\n";
        wp_mail($to, $subject, $message);
        
        wp_redirect(add_query_arg('contact_status', 'ok', wp_get_referer()));
    }
    
    exit;
}
add_action('admin_post_nopriv_submit_contact_simple', 'handle_contact_form_submission');
add_action('admin_post_submit_contact_simple', 'handle_contact_form_submission');

// 3. Create admin menu and page
function client_management_menu() {
    add_menu_page(
        'Client Management',
        'Client Management',
        'manage_options',
        'client-management',
        'client_management_page',
        'dashicons-businessperson',
        30
    );
    
    add_submenu_page(
        'client-management',
        'All Submissions',
        'All Submissions',
        'manage_options',
        'client-management',
        'client_management_page'
    );
    
    add_submenu_page(
        'client-management',
        'Add New Client',
        'Add New Client',
        'manage_options',
        'add-client',
        'add_client_page'
    );
    
    add_submenu_page(
        'client-management',
        'Agencies',
        'Agencies',
        'manage_options',
        'manage-agencies',
        'manage_agencies_page'
    );
    
    add_submenu_page(
        'client-management',
        'Settings',
        'Settings',
        'manage_options',
        'client-settings',
        'client_settings_page'
    );
}
add_action('admin_menu', 'client_management_menu');

// 4. Main client management page
function client_management_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'client_submissions';
    
    // Handle actions (delete, change status, etc.)
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
        if ($_GET['action'] == 'delete') {
            $wpdb->delete($table_name, array('id' => $id));
            echo '<div class="notice notice-success is-dismissible"><p>Client deleted successfully.</p></div>';
        } elseif ($_GET['action'] == 'changestatus' && isset($_GET['status'])) {
            $status = sanitize_text_field($_GET['status']);
            $wpdb->update($table_name, array('status' => $status), array('id' => $id));
            echo '<div class="notice notice-success is-dismissible"><p>Status updated successfully.</p></div>';
        }
    }
    
    // Get all submissions
    $submissions = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
    
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Client Submissions</h1>
        <a href="<?php echo admin_url('admin.php?page=add-client'); ?>" class="page-title-action">Add New Client</a>
        
        <div class="tablenav top">
            <div class="alignleft actions">
                <select name="filter_status">
                    <option value="">All Statuses</option>
                    <option value="new">New</option>
                    <option value="contacted">Contacted</option>
                    <option value="qualified">Qualified</option>
                    <option value="converted">Converted</option>
                    <option value="rejected">Rejected</option>
                </select>
                <input type="button" name="filter_action" id="filter-action" class="button" value="Filter">
            </div>
            <br class="clear">
        </div>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Agency</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($submissions): ?>
                    <?php foreach ($submissions as $submission): ?>
                    <tr>
                        <td><?php echo $submission->id; ?></td>
                        <td>
                            <strong><a href="<?php echo admin_url('admin.php?page=client-management&view=details&id=' . $submission->id); ?>">
                                <?php echo $submission->first_name . ' ' . $submission->last_name; ?>
                            </a></strong>
                        </td>
                        <td><?php echo $submission->email; ?></td>
                        <td><?php echo $submission->phone; ?></td>
                        <td>
                            <?php 
                            if ($submission->address_locality) {
                                echo $submission->address_locality;
                                if ($submission->address_state) echo ', ' . $submission->address_state;
                                if ($submission->address_country) echo ', ' . $submission->address_country;
                            }
                            ?>
                        </td>
                        <td><?php echo $submission->agency_id ? 'Agency #' . $submission->agency_id : 'N/A'; ?></td>
                        <td>
                            <select class="status-select" data-id="<?php echo $submission->id; ?>">
                                <option value="new" <?php selected($submission->status, 'new'); ?>>New</option>
                                <option value="contacted" <?php selected($submission->status, 'contacted'); ?>>Contacted</option>
                                <option value="qualified" <?php selected($submission->status, 'qualified'); ?>>Qualified</option>
                                <option value="converted" <?php selected($submission->status, 'converted'); ?>>Converted</option>
                                <option value="rejected" <?php selected($submission->status, 'rejected'); ?>>Rejected</option>
                            </select>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($submission->created_at)); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=client-management&view=details&id=' . $submission->id); ?>">View</a> |
                            <a href="<?php echo admin_url('admin.php?page=add-client&id=' . $submission->id); ?>">Edit</a> |
                            <a href="<?php echo admin_url('admin.php?page=client-management&action=delete&id=' . $submission->id); ?>" onclick="return confirm('Are you sure you want to delete this client?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No client submissions found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.status-select').change(function() {
            var id = $(this).data('id');
            var status = $(this).val();
            
            $.post(ajaxurl, {
                action: 'update_client_status',
                id: id,
                status: status,
                nonce: '<?php echo wp_create_nonce('update_client_status'); ?>'
            }, function(response) {
                if (response.success) {
                    // Optionally show a success message
                }
            });
        });
    });
    </script>
    <?php
}

// 5. AJAX handler for status updates
function update_client_status() {
    check_ajax_referer('update_client_status', 'nonce');
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'client_submissions';
    
    $id = intval($_POST['id']);
    $status = sanitize_text_field($_POST['status']);
    
    $result = $wpdb->update(
        $table_name,
        array('status' => $status),
        array('id' => $id)
    );
    
    if ($result !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_update_client_status', 'update_client_status');

// 6. Add/Edit client page
function add_client_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'client_submissions';
    
    $client = null;
    if (isset($_GET['id'])) {
        $client = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_GET['id']));
    }
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = array(
            'civility' => sanitize_text_field($_POST['civility']),
            'first_name' => sanitize_text_field($_POST['first_name']),
            'last_name' => sanitize_text_field($_POST['last_name']),
            'email' => sanitize_email($_POST['email']),
            'phone' => sanitize_text_field($_POST['phone']),
            'address_street' => sanitize_text_field($_POST['address_street']),
            'address_code' => sanitize_text_field($_POST['address_code']),
            'address_locality' => sanitize_text_field($_POST['address_locality']),
            'address_state' => sanitize_text_field($_POST['address_state']),
            'address_country' => sanitize_text_field($_POST['address_country']),
            'message' => sanitize_textarea_field($_POST['message']),
            'agency_id' => intval($_POST['agency_id']),
            'receive_newsletter' => isset($_POST['receive_newsletter']) ? 1 : 0,
            'status' => sanitize_text_field($_POST['status']),
            'notes' => sanitize_textarea_field($_POST['notes']),
            'assigned_to' => intval($_POST['assigned_to'])
        );
        
        if ($client) {
            // Update existing client
            $wpdb->update($table_name, $data, array('id' => $client->id));
            $message = 'Client updated successfully.';
        } else {
            // Insert new client
            $wpdb->insert($table_name, $data);
            $message = 'Client added successfully.';
        }
        
        echo '<div class="notice notice-success is-dismissible"><p>' . $message . '</p></div>';
        
        // Refresh client data
        if ($client) {
            $client = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $client->id));
        }
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo $client ? 'Edit Client' : 'Add New Client'; ?></h1>
        
        <form method="post">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <div class="postbox">
                                <h2 class="hndle"><span>Client Information</span></h2>
                                <div class="inside">
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row"><label for="civility">Title</label></th>
                                            <td>
                                                <select name="civility" id="civility">
                                                    <option value="mr" <?php selected($client ? $client->civility : '', 'mr'); ?>>Mr</option>
                                                    <option value="ms" <?php selected($client ? $client->civility : '', 'ms'); ?>>Ms</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="first_name">First Name *</label></th>
                                            <td>
                                                <input type="text" name="first_name" id="first_name" value="<?php echo $client ? esc_attr($client->first_name) : ''; ?>" class="regular-text" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="last_name">Last Name *</label></th>
                                            <td>
                                                <input type="text" name="last_name" id="last_name" value="<?php echo $client ? esc_attr($client->last_name) : ''; ?>" class="regular-text" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="email">Email *</label></th>
                                            <td>
                                                <input type="email" name="email" id="email" value="<?php echo $client ? esc_attr($client->email) : ''; ?>" class="regular-text" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="phone">Phone</label></th>
                                            <td>
                                                <input type="text" name="phone" id="phone" value="<?php echo $client ? esc_attr($client->phone) : ''; ?>" class="regular-text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="address_street">Address</label></th>
                                            <td>
                                                <input type="text" name="address_street" id="address_street" value="<?php echo $client ? esc_attr($client->address_street) : ''; ?>" class="regular-text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="address_code">Postal Code</label></th>
                                            <td>
                                                <input type="text" name="address_code" id="address_code" value="<?php echo $client ? esc_attr($client->address_code) : ''; ?>" class="regular-text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="address_locality">City</label></th>
                                            <td>
                                                <input type="text" name="address_locality" id="address_locality" value="<?php echo $client ? esc_attr($client->address_locality) : ''; ?>" class="regular-text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="address_state">State/Province</label></th>
                                            <td>
                                                <input type="text" name="address_state" id="address_state" value="<?php echo $client ? esc_attr($client->address_state) : ''; ?>" class="regular-text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="address_country">Country</label></th>
                                            <td>
                                                <input type="text" name="address_country" id="address_country" value="<?php echo $client ? esc_attr($client->address_country) : ''; ?>" class="regular-text">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="message">Message</label></th>
                                            <td>
                                                <textarea name="message" id="message" rows="5" class="large-text"><?php echo $client ? esc_textarea($client->message) : ''; ?></textarea>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="postbox-container-1" class="postbox-container">
                        <div class="meta-box-sortables">
                            <div class="postbox">
                                <h2 class="hndle"><span>Status</span></h2>
                                <div class="inside">
                                    <p>
                                        <label for="status">Status:</label>
                                        <select name="status" id="status">
                                            <option value="new" <?php selected($client ? $client->status : 'new', 'new'); ?>>New</option>
                                            <option value="contacted" <?php selected($client ? $client->status : '', 'contacted'); ?>>Contacted</option>
                                            <option value="qualified" <?php selected($client ? $client->status : '', 'qualified'); ?>>Qualified</option>
                                            <option value="converted" <?php selected($client ? $client->status : '', 'converted'); ?>>Converted</option>
                                            <option value="rejected" <?php selected($client ? $client->status : '', 'rejected'); ?>>Rejected</option>
                                        </select>
                                    </p>
                                    <p>
                                        <label for="assigned_to">Assigned To:</label>
                                        <?php 
                                        $users = get_users(array(
                                            'role__in' => ['administrator', 'editor', 'author']
                                        ));
                                        ?>
                                        <select name="assigned_to" id="assigned_to">
                                            <option value="0">— None —</option>
                                            <?php foreach ($users as $user): ?>
                                                <option value="<?php echo $user->ID; ?>" <?php selected($client ? $client->assigned_to : 0, $user->ID); ?>>
                                                    <?php echo $user->display_name; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </p>
                                    <p>
                                        <label for="agency_id">Agency ID:</label>
                                        <input type="number" name="agency_id" id="agency_id" value="<?php echo $client ? esc_attr($client->agency_id) : ''; ?>" class="small-text">
                                    </p>
                                    <p>
                                        <label for="receive_newsletter">
                                            <input type="checkbox" name="receive_newsletter" id="receive_newsletter" value="1" <?php checked($client ? $client->receive_newsletter : 0, 1); ?>>
                                            Receive Newsletter
                                        </label>
                                    </p>
                                    <p>
                                        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Client">
                                    </p>
                                </div>
                            </div>
                            
                            <div class="postbox">
                                <h2 class="hndle"><span>Notes</span></h2>
                                <div class="inside">
                                    <textarea name="notes" rows="10" class="large-text"><?php echo $client ? esc_textarea($client->notes) : ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php
}

// 7. Settings page
function client_settings_page() {
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
        update_option('client_management_notification_email', sanitize_email($_POST['notification_email']));
        update_option('client_management_default_status', sanitize_text_field($_POST['default_status']));
        update_option('client_management_auto_assign', intval($_POST['auto_assign']));
        
        echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully.</p></div>';
    }
    
    $notification_email = get_option('client_management_notification_email', get_option('admin_email'));
    $default_status = get_option('client_management_default_status', 'new');
    $auto_assign = get_option('client_management_auto_assign', 0);
    
    ?>
    <div class="wrap">
        <h1>Client Management Settings</h1>
        
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="notification_email">Notification Email</label></th>
                    <td>
                        <input type="email" name="notification_email" id="notification_email" value="<?php echo esc_attr($notification_email); ?>" class="regular-text">
                        <p class="description">Email address to receive notifications of new client submissions.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="default_status">Default Status</label></th>
                    <td>
                        <select name="default_status" id="default_status">
                            <option value="new" <?php selected($default_status, 'new'); ?>>New</option>
                            <option value="contacted" <?php selected($default_status, 'contacted'); ?>>Contacted</option>
                            <option value="qualified" <?php selected($default_status, 'qualified'); ?>>Qualified</option>
                        </select>
                        <p class="description">Default status for new client submissions.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="auto_assign">Auto Assign To</label></th>
                    <td>
                        <?php 
                        $users = get_users(array(
                            'role__in' => ['administrator', 'editor', 'author']
                        ));
                        ?>
                        <select name="auto_assign" id="auto_assign">
                            <option value="0">— None —</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user->ID; ?>" <?php selected($auto_assign, $user->ID); ?>>
                                    <?php echo $user->display_name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">Automatically assign new clients to this user.</p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="save_settings" class="button button-primary" value="Save Settings">
            </p>
        </form>
    </div>
    <?php
}

// 8. Add dashboard widget
function client_management_dashboard_widget() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'client_submissions';
    
    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $new = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'new'");
    $contacted = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'contacted'");
    $converted = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'converted'");
    
    ?>
    <div class="client-dashboard-widget">
        <div class="client-stats">
            <div class="stat">
                <span class="number"><?php echo $total; ?></span>
                <span class="label">Total Clients</span>
            </div>
            <div class="stat">
                <span class="number"><?php echo $new; ?></span>
                <span class="label">New</span>
            </div>
            <div class="stat">
                <span class="number"><?php echo $contacted; ?></span>
                <span class="label">Contacted</span>
            </div>
            <div class="stat">
                <span class="number"><?php echo $converted; ?></span>
                <span class="label">Converted</span>
            </div>
        </div>
        <div class="client-actions">
            <a href="<?php echo admin_url('admin.php?page=client-management'); ?>" class="button button-primary">View All Clients</a>
            <a href="<?php echo admin_url('admin.php?page=add-client'); ?>" class="button">Add New Client</a>
        </div>
    </div>
    <style>
    .client-dashboard-widget .client-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    .client-dashboard-widget .stat {
        text-align: center;
        flex: 1;
    }
    .client-dashboard-widget .number {
        display: block;
        font-size: 24px;
        font-weight: bold;
    }
    .client-dashboard-widget .label {
        font-size: 12px;
        color: #666;
    }
    .client-dashboard-widget .client-actions {
        display: flex;
        gap: 10px;
    }
    </style>
    <?php
}

function add_client_dashboard_widget() {
    wp_add_dashboard_widget(
        'client_management_dashboard_widget',
        'Client Management Overview',
        'client_management_dashboard_widget'
    );
}
add_action('wp_dashboard_setup', 'add_client_dashboard_widget');

// 9. Add status column to admin list if viewing submissions
function add_client_status_column($columns) {
    $columns['client_status'] = 'Status';
    return $columns;
}

function display_client_status_column($column, $post_id) {
    if ($column === 'client_status') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'client_submissions';
        $client = $wpdb->get_row($wpdb->prepare("SELECT status FROM $table_name WHERE id = %d", $post_id));
        
        if ($client) {
            echo ucfirst($client->status);
        }
    }
}

// Only add these if we're in the admin area
if (is_admin()) {
    add_filter('manage_posts_columns', 'add_client_status_column');
    add_action('manage_posts_custom_column', 'display_client_status_column', 10, 2);
}
?>

<!-- Your existing frontend form code goes here, with the following addition to the status message section: -->

<?php if ($contact_status): ?>
<div style="text-align: center; padding: 15px; margin: 20px; border-radius: 5px;">
    <?php if ($contact_status === 'ok'): ?>
    <div style="background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px;">
        <strong>Success!</strong> Your message has been sent successfully. We'll contact you soon.
    </div>
    <?php elseif ($contact_status === 'nonce_fail'): ?>
    <div style="background: #f8d7da; color: #721c24; border: 1px solid #f1aeb5; padding: 10px;">
        <strong>Error!</strong> Security check failed. Please try again.
    </div>
    <?php elseif ($contact_status === 'db_error'): ?>
    <div style="background: #f8d7da; color: #721c24; border: 1px solid #f1aeb5; padding: 10px;">
        <strong>Error!</strong> There was a problem saving your message. Please try again.
    </div>
    <?php elseif ($contact_status === 'validation_fail'): ?>
    <div style="background: #f8d7da; color: #721c24; border: 1px solid #f1aeb5; padding: 10px;">
        <strong>Error!</strong> Please fill in all required fields.
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>