<?php

function empowercast_metabox_save($post_ID)
{
    $MetaMarks = ( !empty($_POST['MetaMarks']) ? $_POST['MetaMarks'] : false);
    $Episodes = ( !empty($_POST['Empowercast']) ? $_POST['Empowercast'] : false);
    if( $Episodes )
    {
        foreach( $Episodes as $feed_slug => $Empowercast )
        {
            $field = '_'.$feed_slug.':metamarks';
            delete_post_meta( $post_ID, $field);

            if( !empty($Empowercast['change_podcast']) || !empty($Empowercast['new_podcast']) )
            {
                // No URL specified, then it's not really a podcast to save
                if( $Empowercast['url'] == '' )
                    continue; // go to the next media file

                if( !empty($MetaMarks[ $feed_slug ]) )
                {
                    $MetaMarkData = $MetaMarks[ $feed_slug ];
                    // Loop through, and convert position and duration to seconds, if specified with 00:00:00
                    foreach( $MetaMarkData as $index => $row )
                    {
                        $MetaMarkData[ $index ]['position'] = empowercast_raw_duration( $row['position'] );
                        $MetaMarkData[ $index ]['duration'] = empowercast_raw_duration( $row['duration'] );
                    }
                    reset($MetaMarkData);

                    foreach( $MetaMarkData as $index => $row )
                    {
                        if( empty($MetaMarkData[ $index ]['type']) && empty($MetaMarkData[ $index ]['position']) && empty($MetaMarkData[ $index ]['duration']) && empty($MetaMarkData[ $index ]['link']) && empty($MetaMarkData[ $index ]['value']) )
                        {
                            unset($MetaMarkData[ $index ]);
                        }
                    }
                    reset($MetaMarkData);

                    if( count($MetaMarkData) > 0 )
                    {
                        if( !empty($Empowercast['new_podcast']) )
                        {
                            add_post_meta($post_ID, $field, $MetaMarkData, true);
                        }
                        else
                        {
                            update_post_meta($post_ID, $field, $MetaMarkData);
                        }
                    }
                    else // Delete them from the database...
                    {
                        delete_post_meta($post_ID, $field );
                    }
                }
            }
        } // Loop through posted episodes...
    }
    return $post_ID;
}

function empowercast_metamarks_addrow() // Called by AJAX call
{
    $feed_slug = $_POST['feed_slug'];
    $next_row = $_POST['next_row'];
    $html = empowercast_metamarks_editrow_html($feed_slug, $next_row, null, true);
    echo $html;
    exit;
}

function empowercast_metamarks_editrow_html($feed_slug, $next_row, $data = null, $new = false)
{
    $feed_slug = esc_attr($feed_slug);
    $MarkTypes = empowercast_metamarks_get_types();
    if( !is_array($data) )
    {
        $data = array();
        $data['type'] = '';
        $data['position'] = '';
        $data['duration'] = '';
        $data['link'] = '';
        $data['value'] = '';
    }
    $data['position'] = empowercast_readable_duration($data['position']);
    $data['duration'] = empowercast_readable_duration($data['duration']);
    if( $data['position'] == '0:00' )
        $data['position'] = '';
    if( $data['duration'] == '0:00' )
        $data['duration'] = '';

    if ($data['position']) {
        $pos = $data['position'];
    } else {
        $pos = "Time";
    }
    if ($data['type']) {
        $type = ucfirst($data['type']);
    } else {
        $type = "Type";
    }
    if (!$new) {
        $class = ' class="pp-hidden-settings"';
        $option = 'Edit';
    } else {
        $class = '';
        $option = 'Save';
    }
    $html = '<div class="pp-metamarks-row" id="empowercast_metamarks_row_'. esc_attr($feed_slug) .'_'. esc_attr($next_row) .'">';
    $html .= '<div class="metamark-top-section id="metamark-top-section-' . esc_attr($feed_slug) .'-'. esc_attr($next_row) . '">';
    $html .= '<div id="pp-metamark-preview-pos-' . esc_attr($feed_slug) .'-'. esc_attr($next_row) . '" class="pp-metamark-preview-pos-">' . esc_html($pos) . '</div>';
    $html .= '<div id="pp-metamark-preview-type--' . esc_attr($feed_slug) .'-'. esc_attr($next_row) . '" class="pp-metamark-preview-type-">' . esc_html($type) . '</div>';
    $html .= '<div class="pp-metamark-delete"><a href="" onclick="return empowercast_metamarks_deleterow(\'empowercast_metamarks_row_'. esc_js($feed_slug) .'_'. esc_js($next_row) .'\');" title="'. __('Delete', 'empowercast') .'">';
    $html .= __('Delete', 'empowercast') . '</a></div><div class="pp-metamark-edit"><a href="" id="pp-toggle-metamark-'. esc_attr($next_row) . '-' . esc_attr($feed_slug) . '" title="'. __($option, 'empowercast') .'" onclick="empowercast_toggleMetamarksSettings(this); return false;">' . __($option, 'empowercast') . '</a></div>';
    $html .= '</div><div id="pp-hide-metamark-' . esc_attr($feed_slug) .'-'. esc_attr($next_row) . '"' . $class . '>';
    $html .= '<div class="pp-section-container"><div class="empowercast-label-container" id="pp-type-label' . esc_attr($feed_slug) .'-'. esc_attr($next_row) . '"><label class="pp-ep-box-label" style="width: 100%;" for="pp-metamark-type-'. esc_attr($feed_slug) .'_'. esc_attr($next_row) .'">' . __('Type', 'empowercast') . '</label><select id="pp-metamark-type-'. esc_attr($feed_slug) .'-'. esc_attr($next_row) .'" class="pp-ep-box-input" style="width: 100%;" type="text" title="'. __('Type', 'empowercast') .'" name="MetaMarks['.esc_attr($feed_slug).']['.esc_attr($next_row).'][type]">';
    $html .= empowercast_print_options( array(''=>'Select Type')+ $MarkTypes, esc_html($data['type']), true);
    $html .= '</select></div><div class="empowercast-label-container" id="pp-pos-label' .  esc_attr($feed_slug) .'-'. esc_attr($next_row) . '"><label class="pp-ep-box-label" for="pp-metamark-pos-'.  esc_attr($feed_slug) .'-'. esc_attr($next_row) .'">' . __('Position', 'empowercast') . '</label>';
    $html .= '<input id="pp-metamark-pos-'. esc_attr($feed_slug) .'-'. esc_attr($next_row) .'" class="pp-ep-box-input" style="width: 100%;" type="text" title="'. __('Position', 'empowercast') .'" name="MetaMarks['.esc_attr($feed_slug).']['.esc_attr($next_row).'][position]" value="' .htmlspecialchars($data['position']) .'" placeholder="'. htmlspecialchars(__('Position', 'empowercast'))  .'" /></div>';
    $html .= '<div class="empowercast-label-container" id="pp-dur-label' .  esc_attr($feed_slug) .'-'. esc_attr($next_row) . '"><label class="pp-ep-box-label" for=\"pp-metamark-dur-'.  esc_attr($feed_slug) .'-'. esc_attr($next_row) .'\">' . __('Duration', 'empowercast') . '</label>';
    $html .= '<input id="pp-metamark-dur-'. esc_attr($feed_slug) .'-'. esc_attr($next_row) .'" class="pp-ep-box-input" style="width: 100%;" type="text" title="'. __('Duration', 'empowercast') .'" name="MetaMarks['.esc_attr($feed_slug).']['.esc_attr($next_row).'][duration]" value="' .htmlspecialchars($data['duration']) .'" placeholder="'. htmlspecialchars(__('Duration', 'empowercast'))  .'" /></div></div>';
    $html .= '<div class="pp-section-container"><div class="empowercast-label-container" style="width: 100%;"><label class="pp-ep-box-label" for="pp-metamark-link-' . esc_attr($feed_slug) .'-'. esc_attr($next_row) .'">' . __('Link', 'empowercast') . '</label><input id="pp-metamark-link-'. esc_attr($feed_slug) .'-'. esc_attr($next_row) .'" class="pp-ep-box-input" style="width: 100%;" type="text" title="'. __('Link', 'empowercast') .'" name="MetaMarks['.esc_attr($feed_slug).']['.esc_attr($next_row).'][link]" value="' .htmlspecialchars($data['link']) .'" placeholder="'. htmlspecialchars(__('Link', 'empowercast'))  .'" /></div></div>';
    $html .= '<div class="pp-section-container" id="pp-value-container-' . esc_attr($feed_slug) .'-'. esc_attr($next_row) . '"><div class="empowercast-label-container" style="width: 100%;"><label class="pp-ep-box-label" for="pp-metamark-val-' . esc_attr($next_row) .'">' . __('Value', 'empowercast') . '</label><textarea id="pp-metamark-val-'. esc_attr($feed_slug) .'-'. esc_attr($next_row) .'" class="pp-ep-box-input" style="width: 100%;" name="MetaMarks['.esc_attr($feed_slug).']['.esc_attr($next_row).'][value]" title="'. __('Value', 'empowercast') .'" placeholder="'. htmlspecialchars(__('Value', 'empowercast'))  .'">' .htmlspecialchars($data['value']) .'</textarea></div></div>';


    $html .= '</div></div>';
    $html .= "\n";
    return $html;
}

function empowercast_metamarks_print_rss2($episode_data)
{
    $MetaRecords = empowercast_metamarks_get($episode_data['id'], $episode_data['feed'] );
    foreach( $MetaRecords as $index => $MetaMark )
    {
        echo "\t\t";
        echo '<rawvoice:metamark type="'. esc_attr($MetaMark['type']) .'"';
        if( !empty($MetaMark['duration']) )
            echo ' duration="'. esc_attr($MetaMark['duration']) .'"';
        if( !empty($MetaMark['position']) )
            echo ' position="'. esc_attr($MetaMark['position']) .'"';
        if( !empty($MetaMark['link']) )
            echo ' link="'. esc_attr($MetaMark['link']) .'"';

        $value = trim($MetaMark['value']);
        if( $value == '' ) {
            echo ' />';
        } else {
            echo '>';
            echo htmlspecialchars($value);
            echo '</rawvoice:metamark>';
        }
        echo PHP_EOL;
    }
    //If the user opted to use the podcast index soundbite tag, we need to print these as well
    if (!empty($episode_data['pci_soundbites'])) {
        foreach( $MetaRecords as $index => $MetaMark )
        {
            echo "\t\t";
            echo '<podcast:soundbite';
            if( !empty($MetaMark['position']) )
                echo ' startTime="'. esc_attr($MetaMark['position']) .'"';
            if( !empty($MetaMark['duration']) )
                echo ' duration="'. esc_attr($MetaMark['duration']) .'"';

            //For the node value, we'll first check the value, and if that's empty we'll check the link
            $value = trim($MetaMark['value']);
            if( !empty($value)) {
                echo '>';
                echo htmlspecialchars($value);
                echo '</podcast:soundbite>';
            } elseif (!empty($MetaMark['link'])) {
                echo '>';
                echo htmlspecialchars($MetaMark['link']);
                echo '</podcast:soundbite>';
            } else {
                echo ' />';
            }
            echo PHP_EOL;
        }
    }
}

function empowercast_metamarks_get_types()
{
    $types = array();
    $types['audio'] = 'Audio';
    $types['video'] = 'Video';
    $types['image'] = 'Image';
    $types['comment'] = 'Comment';
    $types['tag'] = 'Tag';
    $types['ad'] = 'Advertisement';
    $types['lowerthird'] = 'Lower Third';
    return $types;
}


function empowercast_metamarks_get($post_id, $feed_slug)
{
    $return = array();
    if( $post_id )
    {
        $return = get_post_meta($post_id, '_'. $feed_slug .':metamarks', true);
        if( $return == false )
            $return  = array();
    }

    return $return;
}