<?php
/**
 * Theme setup and assets
 */

if ( ! defined( 'JEANNEAU_LITE_VERSION' ) ) {
    define( 'JEANNEAU_LITE_VERSION', '1.0.0' );
}

add_action( 'after_setup_theme', function() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'jeanneau-lite-theme' ),
    ) );
});

add_action( 'wp_enqueue_scripts', function() {
    // Theme stylesheet
    wp_enqueue_style( 'jeanneau-lite-style', get_stylesheet_uri(), array(), JEANNEAU_LITE_VERSION );
    // No external CSS detected from uploaded files
    // UI layer
/* BEGIN: User build assets */
// Ensure jQuery is present for the vendor bundles
wp_enqueue_script( 'jquery' );

// Public path expected by the bundles is "/build/"
$build_uri = get_template_directory_uri() . '/build';

// CSS (load base/app first, then chunk styles)
if ( file_exists( get_template_directory() . '/build/app.d0b73c3c.css' ) ) {
    wp_enqueue_style( 'bundle-app-css', $build_uri . '/app.d0b73c3c.css', array(), null );
}
if ( file_exists( get_template_directory() . '/build/691.ff3cfb14.css' ) ) {
    wp_enqueue_style( 'bundle-691-css', $build_uri . '/691.ff3cfb14.css', array('bundle-app-css'), null );
}
if ( file_exists( get_template_directory() . '/build/268.be368f8d.css' ) ) {
    wp_enqueue_style( 'bundle-268-css', $build_uri . '/268.be368f8d.css', array('bundle-app-css'), null );
}

// JS (runtime first, then chunks, then app)
if ( file_exists( get_template_directory() . '/build/runtime.32cc791b.js' ) ) {
    wp_enqueue_script( 'bundle-runtime', $build_uri . '/runtime.32cc791b.js', array('jquery'), null, true );
}
if ( file_exists( get_template_directory() . '/build/268.9a434bd2.js' ) ) {
    wp_enqueue_script( 'bundle-268', $build_uri . '/268.9a434bd2.js', array('bundle-runtime','jquery'), null, true );
}
if ( file_exists( get_template_directory() . '/build/691.570663c4.js' ) ) {
    wp_enqueue_script( 'bundle-691', $build_uri . '/691.570663c4.js', array('bundle-runtime','jquery'), null, true );
}
if ( file_exists( get_template_directory() . '/build/732.a73f4830.js' ) ) {
    wp_enqueue_script( 'bundle-732', $build_uri . '/732.a73f4830.js', array('bundle-runtime','jquery'), null, true );
}
if ( file_exists( get_template_directory() . '/build/app.bab1e4dd.js' ) ) {
    wp_enqueue_script( 'bundle-app', $build_uri . '/app.bab1e4dd.js', array('bundle-runtime','jquery','bundle-268','bundle-691'), null, true );
}
/* END: User build assets */

wp_enqueue_style( 'jeanneau-lite-ui', get_template_directory_uri() . '/assets/css/ui.css', array('jeanneau-lite-style'), JEANNEAU_LITE_VERSION );
wp_enqueue_script( 'jeanneau-lite-ui', get_template_directory_uri() . '/assets/js/ui.js', array(), JEANNEAU_LITE_VERSION, true );

    // Small script to handle menu toggle
    wp_enqueue_script( 'jeanneau-lite-js', get_template_directory_uri() . '/theme.js', array(), JEANNEAU_LITE_VERSION, true );
});

/**
 * Helper to render the content from static HTML files.
 * We keep the <body> innerHTML only; header/footer are handled by WordPress.
 */
function jeanneau_lite_render_static( $slug ) {
    $file = get_template_directory() . '/static/' . $slug . '.html';
    if ( file_exists( $file ) ) {
        $html = file_get_contents( $file );
        // Print as-is. Consider sanitizing if mixing with user input.
        echo $html;
    } else {
        echo '<div class="container"><p>Static file not found: ' . esc_html( $slug ) . '</p></div>';
    }
}

// Create pages on theme activation
add_action('after_switch_theme', function () {
    $slugs = [
        'about-us' => 'About us',
        'powerboats' => 'Powerboats',
        'sailboats' => 'Sailboats',
        'cap-camarat' => 'Cap Camarat',
        'db-yachts' => 'DB Yachts',
        'merry-fisher' => 'Merry Fisher',
        'merry-fisher-sport' => 'Merry Fisher Sport',
        'sun-odyssey' => 'Sun Odyssey',
        'jeanneau-yachts' => 'Jeanneau Yachts',
        'sun-fast' => 'Sun Fast',
    ];
    foreach ($slugs as $slug => $title) {
        if (!get_page_by_path($slug, OBJECT, 'page')) {
            wp_insert_post([
                'post_title'   => $title,
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => '',
            ]);
        }
    }
    
    // Create contact submissions table
    jeanneau_create_contact_table();
});

// Add this function to your functions.php
function jeanneau_create_contact_table_manual() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'jeanneau_contact_submissions';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        civility varchar(10) NOT NULL,
        first_name varchar(100) NOT NULL,
        last_name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(50),
        address_street varchar(255),
        address_code varchar(20),
        address_locality varchar(100),
        address_country varchar(100),
        address_state varchar(100),
        message text,
        agency_id varchar(100),
        receive_newsletter tinyint(1) DEFAULT 0,
        submission_date datetime DEFAULT CURRENT_TIMESTAMP,
        status varchar(20) DEFAULT 'new',
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
    // Set option to track that table was created
    update_option('jeanneau_contact_table_created', '1');
}

// Call this on admin_init to ensure table exists
add_action('admin_init', function() {
    if (!get_option('jeanneau_contact_table_created')) {
        jeanneau_create_contact_table_manual();
    }
});

/**
 * Create contact submissions table
 */
function jeanneau_create_contact_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'jeanneau_contact_submissions';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        civility varchar(10) NOT NULL,
        first_name varchar(100) NOT NULL,
        last_name varchar(100) NOT NULL,
        email varchar(100) NOT NULL,
        phone varchar(50),
        address_street varchar(255),
        address_code varchar(20),
        address_locality varchar(100),
        address_country varchar(100),
        address_state varchar(100),
        message text,
        agency_id varchar(100),
        receive_newsletter tinyint(1) DEFAULT 0,
        submission_date datetime DEFAULT CURRENT_TIMESTAMP,
        status varchar(20) DEFAULT 'new',
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

/**
 * Handle Contact Us form submission
 */
// Replace your existing contact form handler with this:
add_action('admin_post_nopriv_submit_contact_simple', 'handle_contact_simple_debug');
add_action('admin_post_submit_contact_simple', 'handle_contact_simple_debug');

function handle_contact_simple_debug() {
    global $wpdb;
    
    // Debug: Log that function was called
    error_log('Contact form handler called');
    
    if ( ! isset($_POST['contact_simple_nonce']) || ! wp_verify_nonce($_POST['contact_simple_nonce'], 'contact_simple_nonce') ) {
        error_log('Nonce verification failed');
        wp_safe_redirect( add_query_arg('contact_status', 'nonce_fail', wp_get_referer() ?: home_url('/')) );
        exit;
    }

    $f = isset($_POST['model_contact']) ? (array) $_POST['model_contact'] : array();
    
    // Debug: Log received data
    error_log('Form data: ' . print_r($f, true));

    // Sanitize data
    $civility = sanitize_text_field($f['civility'] ?? '');
    $first    = sanitize_text_field($f['first_name'] ?? '');
    $last     = sanitize_text_field($f['last_name'] ?? '');
    $email    = sanitize_email($f['email'] ?? '');
    $phone    = sanitize_text_field($f['phone'] ?? '');
    $addr     = sanitize_text_field($f['address_street'] ?? '');
    $zip      = sanitize_text_field($f['address_code'] ?? '');
    $city     = sanitize_text_field($f['address_locality'] ?? '');
    $country  = sanitize_text_field($f['address_country'] ?? '');
    $state    = sanitize_text_field($f['address_state'] ?? '');
    $message  = wp_kses_post($f['message'] ?? '');
    $agencyId = sanitize_text_field($f['agency_id'] ?? '');
    $newsletter = !empty($f['receive_newsletter']) ? 1 : 0;

    // Ensure table exists
    if (!get_option('jeanneau_contact_table_created')) {
        jeanneau_create_contact_table_manual();
    }

    // Insert into database
    $table_name = $wpdb->prefix . 'jeanneau_contact_submissions';
    
    $result = $wpdb->insert(
        $table_name,
        array(
            'civility' => $civility,
            'first_name' => $first,
            'last_name' => $last,
            'email' => $email,
            'phone' => $phone,
            'address_street' => $addr,
            'address_code' => $zip,
            'address_locality' => $city,
            'address_country' => $country,
            'address_state' => $state,
            'message' => $message,
            'agency_id' => $agencyId,
            'receive_newsletter' => $newsletter,
            'submission_date' => current_time('mysql')
        ),
        array(
            '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s'
        )
    );

    // Debug: Log database result
    if ($result === false) {
        error_log('Database insert failed: ' . $wpdb->last_error);
        wp_safe_redirect( add_query_arg('contact_status', 'db_error', wp_get_referer() ?: home_url('/')) );
        exit;
    } else {
        error_log('Database insert successful. ID: ' . $wpdb->insert_id);
    }

    // Send email (optional)
    $to = get_option('admin_email');
    $subject = 'New Dealer Contact Request';
    $body = "Title: $civility\nName: $first $last\nEmail: $email\nPhone: $phone\nAddress: $addr, $city $zip, $state, $country\nAgency: $agencyId\nNewsletter: $newsletter\n\nMessage:\n$message";
    $headers = array('Content-Type: text/plain; charset=UTF-8');

    if ( is_email($to) ) {
        wp_mail($to, $subject, $body, $headers);
        error_log('Email sent to: ' . $to);
    }

    // Redirect with success
    $redirect = wp_get_referer() ?: home_url('/');
    wp_safe_redirect( add_query_arg('contact_status', 'ok', $redirect) );
    exit;
}

/**
 * Add admin menu for contact submissions
 */
add_action('admin_menu', function() {
    add_menu_page(
        'Contact Submissions',
        'Contact Submissions', 
        'manage_options',
        'jeanneau-contacts',
        'jeanneau_contacts_admin_page',
        'dashicons-email-alt',
        30
    );
});

/**
 * Admin page to display contact submissions
 */
function jeanneau_contacts_admin_page() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'jeanneau_contact_submissions';
    
    // Handle status updates
    if (isset($_POST['update_status']) && isset($_POST['submission_id']) && isset($_POST['new_status'])) {
        $submission_id = intval($_POST['submission_id']);
        $new_status = sanitize_text_field($_POST['new_status']);
        
        $wpdb->update(
            $table_name,
            array('status' => $new_status),
            array('id' => $submission_id),
            array('%s'),
            array('%d')
        );
        
        echo '<div class="notice notice-success"><p>Status updated successfully!</p></div>';
    }
    
    // Handle deletion
    if (isset($_POST['delete_submission']) && isset($_POST['submission_id'])) {
        $submission_id = intval($_POST['submission_id']);
        
        $wpdb->delete(
            $table_name,
            array('id' => $submission_id),
            array('%d')
        );
        
        echo '<div class="notice notice-success"><p>Submission deleted successfully!</p></div>';
    }
    
    // Get all submissions
    $submissions = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submission_date DESC");
    
    ?>
    <div class="wrap">
        <h1>Contact Submissions</h1>
        
        <?php if (empty($submissions)): ?>
            <p>No contact submissions found.</p>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $submission): ?>
                        <tr>
                            <td><?php echo esc_html(date('M j, Y g:i A', strtotime($submission->submission_date))); ?></td>
                            <td><?php echo esc_html($submission->civility . ' ' . $submission->first_name . ' ' . $submission->last_name); ?></td>
                            <td><a href="mailto:<?php echo esc_attr($submission->email); ?>"><?php echo esc_html($submission->email); ?></a></td>
                            <td><?php echo esc_html($submission->phone); ?></td>
                            <td><?php echo esc_html($submission->address_country); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo esc_attr($submission->status); ?>">
                                    <?php echo esc_html(ucfirst($submission->status)); ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="button" onclick="toggleDetails(<?php echo $submission->id; ?>)">View Details</button>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="submission_id" value="<?php echo $submission->id; ?>">
                                    <select name="new_status" onchange="this.form.submit()">
                                        <option value="">Change Status</option>
                                        <option value="new" <?php selected($submission->status, 'new'); ?>>New</option>
                                        <option value="contacted" <?php selected($submission->status, 'contacted'); ?>>Contacted</option>
                                        <option value="completed" <?php selected($submission->status, 'completed'); ?>>Completed</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                                <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this submission?');">
                                    <input type="hidden" name="submission_id" value="<?php echo $submission->id; ?>">
                                    <input type="submit" name="delete_submission" value="Delete" class="button button-small">
                                </form>
                            </td>
                        </tr>
                        <tr id="details-<?php echo $submission->id; ?>" style="display:none;">
                            <td colspan="7">
                                <div style="padding: 15px; background: #f9f9f9;">
                                    <h4>Full Details</h4>
                                    <p><strong>Address:</strong> <?php echo esc_html($submission->address_street . ', ' . $submission->address_locality . ' ' . $submission->address_code . ', ' . $submission->address_state . ', ' . $submission->address_country); ?></p>
                                    <p><strong>Agency ID:</strong> <?php echo esc_html($submission->agency_id); ?></p>
                                    <p><strong>Newsletter:</strong> <?php echo $submission->receive_newsletter ? 'Yes' : 'No'; ?></p>
                                    <?php if ($submission->message): ?>
                                        <p><strong>Message:</strong></p>
                                        <div style="background: white; padding: 10px; border-left: 3px solid #0073aa;">
                                            <?php echo nl2br(esc_html($submission->message)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <style>
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-new { background: #fff2cc; color: #8a6d3b; }
        .status-contacted { background: #d4edda; color: #155724; }
        .status-completed { background: #cce5ff; color: #004085; }
    </style>
    
    <script>
        function toggleDetails(id) {
            var row = document.getElementById('details-' + id);
            if (row.style.display === 'none') {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        }
    </script>
    <?php
}