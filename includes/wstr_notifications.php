<?php
class Wstr_notifications
{
    function __construct()
    {
        add_action('after_theme_setup', [$this, 'so_create_notifications_table']);

        add_shortcode('notifications_display', [$this, 'wstr_notifications_display']);
        add_action('wp_ajax_wstr_decline_notifications', [$this, 'wstr_decline_notifications']);


        add_action('wp_ajax_wstr_get_notifications_using_ajax', [$this, 'wstr_get_notifications_using_ajax']);

        add_shortcode('notifications_popup', [$this, 'wstr_notifications_popup']);

        add_action('wp_ajax_wstr_clear_notifications', [$this, 'wstr_clear_notifications']);
    }

    /**
     * Create notifications table on theme activation
     * @return void
     **/
    public function so_create_notifications_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wstr_notifications';

        //drop old table
        $wpdb->query("DROP TABLE IF EXISTS wp_wstr_notifications");
        // $wpdb->query("DROP TABLE IF EXISTS wp_so_notifications");

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
            id bigint(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
            receiver_id bigint(10) unsigned NOT NULL,
            sender_id bigint(10) unsigned NOT NULL,
            notif_type varchar(50) NOT NULL,
            target_id varchar(25) NOT NULL,
            seen tinyint(1) NOT NULL DEFAULT 0,
            sent_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_notif_receiver_id (receiver_id),
            INDEX idx_notif_sender_id (sender_id),
            INDEX idx_notif_sent_at (sent_at)
        ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    /**
     * Function to add notifications to database
     * @param int $sender
     * @param int $receiver
     * @param string $notification_type
     * @return string
     **/
    public function wstr_notification_handler($sender, $receiver, $notification_type, $post_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wstr_notifications';
        $current_datetime = new DateTime();

        $ntfy_data_arr = [
            'receiver_id' => $receiver,
            'sender_id' => $sender,
            'notif_type' => $notification_type,
            // 'sent_at' => current_time('mysql'),
            'sent_at' => $current_datetime->format('Y/m/d H:i:s'),
        ];

        //if notification is about a post than set the the post_id as the target_id else set the sender_id as the target_id
        if ($post_id == null) {
            $ntfy_data_arr += ['target_id' => $sender];
        } else {
            $ntfy_data_arr += ['target_id' => $post_id];
        }

        $wpdb->insert(
            $table_name,
            $ntfy_data_arr,
            array(
                '%d',
                // receiver
                '%d',
                // sender
                '%s',
                // message_type
                '%s',
                '%s' // created_timestamp
            )
        );

        // Return success or error message
        if ($wpdb->insert_id) {
            return "success";
        } else {
            return "error";
        }
    }

    /**
     * Function to pull notifications
     * @param int $limit
     * @param string $unseen_notifications
     * @return array<array>
     **/
    public function wstr_notifications_display($limit = -1, $unseen_notifications = 'all')
    {

        global $wpdb;

        $notifications_array = array();

        // User ID of the receiver
        $receiverUserID = get_current_user_id();


        // Table name
        $tableName = $wpdb->prefix . 'wstr_notifications';

        // SQL query to retrieve rows
        $query = $wpdb->prepare(
            "SELECT * FROM $tableName WHERE receiver_id = %d ORDER BY id DESC",
            $receiverUserID
        );

        // Execute the query
        $rows = $wpdb->get_results($query);

        ob_start();
?>
        <input type="button" id="clear-notifications" value="Clear Notifications">
        <?php
        if ($rows) {
            foreach ($rows as $row) {

                $sender = $row->sender_id;

                // // Check if the sender_id exists
                $user_data = get_userdata($sender);
                if ($user_data === false) {
                    // User does not exist. Continue to next iteration.
                    continue;
                }

                $message_type = $row->notif_type;
                $timestamp = $row->sent_at;
                $notification_id = $row->id;
                $notification_seen = $row->seen;
                $target_id = $row->target_id;

                $sender_data = get_userdata($sender);
                $sender_name = $sender_data->data->display_name;

                $receiver = $row->receiver_id;
                $receiver_data = get_userdata($receiver);
                $receiver_name = $receiver_data->data->display_name;

                $seen = $row->seen;

                $timestamp_datetime = new DateTime($timestamp);
                $current_datetime = new DateTime();
                $time_difference = $current_datetime->diff($timestamp_datetime);
                $elapsed = "";

                $sender_image_id = (int) get_user_meta($sender, 'ws_profile_pic', true);
                $sender_image = '';
                if ($sender_image_id) {
                    $sender_image =  wp_get_attachment_url($sender_image_id);
                } else {
                    $sender_image = get_avatar_url($sender_data->data->ID);
                }

                //Add values to elapsed accordinlg to the time difference
                if ($time_difference->y > 0) {
                    $elapsed = $time_difference->y . " years ago";
                } elseif ($time_difference->m > 0) {
                    $elapsed = $time_difference->m . " months ago";
                } elseif ($time_difference->d > 0) {
                    $elapsed = $time_difference->d . " days ago";
                } elseif ($time_difference->h > 0) {
                    $elapsed = $time_difference->h . " hours ago";
                } elseif ($time_difference->i > 0) {
                    $elapsed = $time_difference->i . " mins ago";
                } else {
                    $elapsed = "just now";
                }

                $message = '';
                // //Add messages according to message type
                switch ($message_type) {
                    case "offer":
                        $message = "Sent an offer.";
                        $notification_url = home_url('/my-account/?tab=sellers-central') . ($target_id ? '#offer-' . $target_id : '');
                        break;

                    case "new-orders":
                        $message = "Sent an order";
                        // $notification_url = add_query_arg('order_id', $target_id, home_url('/my-account/'));
                        $notification_url = home_url('/my-account/?tab=sellers-central') . ($target_id ? '#order-' . $target_id : '');
                        break;
                    case "my-offer":
                        $message = "Sent an offer.";
                        // $notification_url = home_url('/chat/');
                        $notification_url = home_url('/my-account/?tab=my-offers') . ($target_id ? '#offer-' . $target_id : '');

                        break;

                        // case "new-message":
                        //     $message = "Sent a message";
                        //     // $notification_url = home_url('/chat/');
                        //     $notification_url = add_query_arg('uid', $sender, home_url('/chat/'));
                        //     break;



                        // case "profile-view":
                        //     $message = "Viewed your profile";
                        //     // $notification_url = home_url($sender_username);
                        //     break;

                        // case "like":
                        //     $message = "Liked your post";
                        //     $notification_url = get_permalink((int) $target_id);
                        //     break;

                        // case "comment-post":
                        //     $message = "Commented on your post";
                        //     $notification_url = get_permalink((int) $target_id);
                        //     break;

                        // case "msg-request":
                        //     $message = "Requested to chat";
                        //     $notification_url = add_query_arg('request', $sender, home_url('/chat/'));
                        //     break;

                        // case "order-shipped":
                        //     $message = "Your order has been shipped, Tracking Id: " . $target_id;
                        //     $notification_url = home_url('/orders/');
                        //     break;

                        // case "seller-review":
                        //     // $message = $sender_username . " left a review";
                        //     $notification_url = get_author_posts_url(get_current_user_id()) . '?view=review';
                        //     break;

                    default:
                        $error = 1;
                }

                // if ($error === 0) {
                //     // Populate the array with notification data
                //     $notifications_array[] = array(
                //         'id' => $notification_id,
                //         // 'sender_display_name' => $sender_display_name,
                //         // 'sender_profile_img_url' => $sender_profile_img_url,
                //         'elapsed_time' => $elapsed,
                //         'notification_url' => $notification_url,
                //         'message' => $message,
                //         'is_seen' => $notification_seen
                //     );
                // }
                $seen_class = '';
                $seen_class = $seen == 0 ? 'unseen' : 'seen';

        ?>
                <div class="notification <?php echo $seen_class ?>">
                    <a href="<?php echo $notification_url ?: '' ?>">
                        <img src="<?php echo $sender_image ?: '' ?>" />
                        <h5><?php echo $sender_name ?: '' ?> </h5>
                        <p><?php echo $message ?: '' ?></p>
                        <small><?php echo $elapsed ?: '' ?> </small>
                    </a>
                </div>
                <hr>
        <?php

            }
            return ob_get_clean();
        }

        //Seperate the unseen notifications
        // $unseen_notifications_array = array_filter($notifications_array, function ($notification) {
        //     return $notification['is_seen'] == 0;
        // });

        // // Apply the limit and slice the array accordingly
        // if ($limit > 0) {
        //     if ($unseen_notifications == 'unseen') {
        //         $unseen_notifications_array = array_slice($unseen_notifications_array, 0, $limit);
        //     } else {
        //         $notifications_array = array_slice($notifications_array, 0, $limit);
        //     }
        // }

        // //Return arrays accordingly
        // if ($unseen_notifications == 'unseen') {
        //     return $unseen_notifications_array;
        // } else {
        //     return $notifications_array;
        // }
    }

    public function wstr_get_notifications_using_ajax()
    {
        global $wpdb;

        $notifications_array = [];

        // User ID of the receiver
        $receiverUserID = get_current_user_id();


        // Table name
        $tableName = $wpdb->prefix . 'wstr_notifications';

        // SQL query to retrieve rows
        $query = $wpdb->prepare(
            "SELECT * FROM $tableName WHERE receiver_id = %d ORDER BY sent_at DESC",
            $receiverUserID
        );

        $error = 0;
        // Execute the query
        $rows = $wpdb->get_results($query);
        if ($rows) {
            foreach ($rows as $row) {

                $sender = $row->sender_id;

                // // Check if the sender_id exists
                $user_data = get_userdata($sender);
                if ($user_data === false) {
                    // User does not exist. Continue to next iteration.
                    continue;
                }

                $message_type = $row->notif_type;
                $timestamp = $row->sent_at;
                $notification_id = $row->id;
                $notification_seen = $row->seen;
                $target_id = $row->target_id;

                $sender_data = get_userdata($sender);
                $sender_name = $sender_data->data->display_name;

                $receiver = $row->receiver_id;
                $receiver_data = get_userdata($receiver);
                $receiver_name = $receiver_data->data->display_name;

                $timestamp_datetime = new DateTime($timestamp);
                $current_datetime = new DateTime();
                $time_difference = $current_datetime->diff($timestamp_datetime);
                $elapsed = "";

                $sender_image_id = (int) get_user_meta($sender, 'ws_profile_pic', true);
                $sender_image = '';
                if ($sender_image_id) {
                    $sender_image =  wp_get_attachment_url($sender_image_id);
                } else {
                    $sender_image = get_avatar_url($sender_data->data->ID);
                }

                //Add values to elapsed accordinlg to the time difference
                if ($time_difference->y > 0) {
                    $elapsed = $time_difference->y . " years ago";
                } elseif ($time_difference->m > 0) {
                    $elapsed = $time_difference->m . " months ago";
                } elseif ($time_difference->d > 0) {
                    $elapsed = $time_difference->d . " days ago";
                } elseif ($time_difference->h > 0) {
                    $elapsed = $time_difference->h . " hours ago";
                } elseif ($time_difference->i > 0) {
                    $elapsed = $time_difference->i . " mins ago";
                } else {
                    $elapsed = "just now";
                }

                $message = '';
                // //Add messages according to message type
                switch ($message_type) {
                    case "offer":
                        $message = "Sent an offer.";
                        $notification_url = home_url('/my-account/?tab=sellers-central') . ($target_id ? '#offer-' . $target_id : '');
                        break;

                    case "new-orders":
                        $message = "Sent an order";
                        // $notification_url = add_query_arg('order_id', $target_id, home_url('/my-account/'));
                        $notification_url = home_url('/my-account/?tab=sellers-central') . ($target_id ? '#order-' . $target_id : '');
                        break;
                    case "my-offer":
                        $message = "Sent an offer.";
                        // $notification_url = home_url('/chat/');
                        $notification_url = home_url('/my-account/?tab=my-offers') . ($target_id ? '#offer-' . $target_id : '');
                        break;


                    default:
                        $error = 1;
                }

                if ($error === 0) {
                    // Populate the array with notification data
                    $notifications_array[] = array(
                        'id' => $notification_id,
                        'sender_name' => $sender_name,
                        'sender_image' => $sender_image,
                        'elapsed_time' => $elapsed,
                        'notification_url' => $notification_url,
                        'message' => $message,
                        'is_seen' => $notification_seen
                    );
                }
            }
            wp_send_json_success($notifications_array);
        }
    }

    public function wstr_decline_notifications()
    {
        global $wpdb;
        $notification_id = sanitize_text_field($_POST['notification_id']);

        if (!$notification_id) {
            wp_send_json_error('Missing notification id');
        }
        $table_name = $wpdb->prefix . 'wstr_notifications';

        $update = $wpdb->query($wpdb->prepare("UPDATE $table_name SET seen='1' WHERE id=$notification_id"));
        if ($update) {
            wp_send_json_success(true);
        } else {
            wp_send_json_error('Something went wrong. Please try again later.');
        }
        die();
    }

    public function wstr_notifications_popup()
    {
        ob_start();
        ?>
        <div id="notifcations-popup-container">

        </div>

<?php
        return ob_get_clean();
    }

    public function wstr_clear_notifications()
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $table_name = $wpdb->prefix . 'wstr_notifications';
        $update_all = $wpdb->query($wpdb->prepare("UPDATE  $table_name SET seen =%d WHERE receiver_id = %d", 1, $user_id));
        if ($update_all) {
            return wp_send_json(true);
        } else {
            return wp_send_json(false);
        }
    }
}
global $notifcations;
$notifcations = new Wstr_notifications();
