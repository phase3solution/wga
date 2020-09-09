<?php 
$args = ['id' =>'wga-user', 'box_title' =>'User Options', 'title' =>'', 'context' =>'side', 'priority' =>'default'];

new WPOrg_Meta_Box($args);
class WPOrg_Meta_Box {
    protected $_meta_box;
    function __construct($args) {
        $this->_meta_box = $args;
        add_action('add_meta_boxes', [$this, 'add']);
        add_action('save_post', [$this, 'save']);
    }
    public function add() {
        $screens = ['sfiats', 'post'];
        foreach ($screens as $screen) {
            add_meta_box(
                $this->_meta_box['id'],          // Unique ID
                $this->_meta_box['box_title'], // Box title
                [$this, 'html'],   // Content callback, must be of type callable
                $screen,                  // Post type
                $this->_meta_box['context'],
                $this->_meta_box['priority']
            );
        }
    }
    public function save($post_id) {
        if (array_key_exists($this->_meta_box['id'], $_POST)) {
            update_post_meta(
                $post_id,
                $this->_meta_box['id'],
                $_POST[$this->_meta_box['id']]
            );
        }
    }
    public function html($post) {
        $value = get_post_meta($post->ID, $this->_meta_box['id'], true);
        $options = advisory_generate_sfiats_form_users(); 
        if (!empty($this->_meta_box['title'])) echo '<label for="'.$this->_meta_box['id'].'">'.$this->_meta_box['title'].'</label>';
        echo '<select name="'.$this->_meta_box['id'].'" id="'.$this->_meta_box['id'].'" class="postbox">';
            if (!empty($options)) {
                foreach ($options as $option_id => $option_text) {
                    echo '<option value="'.$option_id.'" '.selected($value, $option_id).'>'.$option_text.'</option>';
                }
            }
        echo '</select>';
    }
}



$prefix='wga-';
$meta_boxes = [
    [
        'id'        => 'additional_opts',
        'title'     => 'Additional Options',
        'pages'     => ['sfiats'],
        'context'   => 'side', // normal, side
        'priority'  => 'default',
        'fields'    => [
            [
                'id' => $prefix .'user', 
                'type' => 'text', 
                'desc' => '', 
                'std' => '', 
                'title' => 'User',
                // 'options' => advisory_generate_sfiats_form_users()
            ],
        ]
    ]
];
// foreach ($meta_boxes as $meta_box) { $my_box = new My_meta_box($meta_box); }
class My_meta_box {
    protected $_meta_box;
    // create meta box based on given data
    function __construct($meta_box) {
        $this->_meta_box = $meta_box;
        add_action('admin_menu', array(&$this, 'add'));
        add_action('save_post', array(&$this, 'save'));
    }
    /// Add meta box for multiple post types
    function add() {
        foreach ($this->_meta_box['pages'] as $page) {
            add_meta_box($this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show'), $page, $this->_meta_box['context'], $this->_meta_box['priority']);
        }
    }
    // Callback function to show fields in meta box
    function show() {
        global $post;
        // Use nonce for verification
        echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
        echo '<table class="form-table">';
        foreach ($this->_meta_box['fields'] as $field) {
            // get current post meta data
            $meta = get_post_meta($post->ID, $field['id'], true);
            echo '<tr>';
            if ($field['type'] == 'checkbox') {
                $colspan = ' colspan="2"';
            } else if (empty($field['name'])) {
                $colspan = ' colspan="2"';
            } else {
                echo '<th style="width:20%"><label for="', $field['id'], '">', $field['title'], '</label></th>';
                $colspan = '';
            }
            switch ($field['type']) {
                case 'text':
                    echo '<td'. $colspan .'>';
                    echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : @$field['std'], '" size="30" style="width:97%" />',
                        '<br />', @$field['desc'];
                        echo '</td>';
                    break;
                case 'date':
                    echo '<td'. $colspan .'>';
                    echo '<input type="date" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : @$field['std'], '" size="30" style="width:97%" />',
                        '<br />', @$field['desc'];
                        echo '</td>';
                    break;
                // word press editor on text area
                case 'textarea':
                    echo '<td'. $colspan .'>';
                    $content = $meta ? $meta : $field['std'];
                    $editor_id = $field['id'];
                    $settings = array( 'media_buttons' => true, 'tinymce' => true );
                    wp_editor( $content, $editor_id, $settings );
                    echo '</td>';
                    break;
                case 'select':
                    if (!empty($field['title'])) {
                        echo '<td><strong>'. $field['title'] .' : <strong></td>';
                        echo '<td'. $colspan .'>';
                    } else echo '<td'. $colspan .'>';
                    echo '<select name="', $field['id'], '" id="', $field['id'], '" style="width:100%">';
                    foreach ($field['options'] as $option_id => $option_text) {
                        $selected = $meta == $option_id ? ' selected' : '';
                        echo '<option'.$selected.' value="'.$option_id.'">'.$option_text.'</option>';
                    }
                    echo '</select>';
                    echo '</td>';
                    break;
                case 'radio':
                    echo '<td'. $colspan .'>';
                    foreach ($field['options'] as $option) {
                        echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
                    }
                    echo '</td>';
                    break;
                case 'checkbox':
                    echo '<td'. $colspan .'>';
                    echo '<label for="'. $field['id'] .'"><input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' /> '. $field['name'] .'</label>';
                    echo '</td>';
                    break;
            }
            echo '</tr>';
        }
        echo '</table>';
    }
    // Save data from meta box
    function save($post_id) {
        // verify nonce
        // if (!wp_verify_nonce($_POST['mytheme_meta_box_nonce'], basename(__FILE__))) { return $post_id; }
        // check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }
        // check permissions
        if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) { return $post_id; }
        } elseif (!current_user_can('edit_post', $post_id)) { return $post_id; }
        foreach ($this->_meta_box['fields'] as $field) {
            $old = get_post_meta($post_id, $field['id'], true);
            $new = $_POST[$field['id']] ?? false;
            if ($new && $new != $old) { update_post_meta($post_id, $field['id'], $new); } 
            elseif ('' == $new && $old) { delete_post_meta($post_id, $field['id'], $old); }
        }
    }
}