<?php
$availablePages = get_pages();
?>
<h1 class="pageTitle">Program: <?php echo esc_html($props['program_info']['program_title']);?></h1>
<h3 style="padding-left: 2%">Link:
    <?php if (!isset($networkInfo['link_page_program']) || $networkInfo['link_page_program'] == null) {
        echo "(not set)";
    } else {
        echo "<a href='".esc_html($networkInfo['link_page_program'])."'>".esc_html($networkInfo['link_page_program'])."</a>";
    }
    ?>
</h3>
<?php
if (!isset($networkInfo['link_page_program']) ||  $networkInfo['link_page_program'] == null) {
    ?>
    <form method="POST" id="createForm" hiddent></form>
    <div id="choiceBox">
        <h2 class="boxTitle"><?php echo esc_html(__('Select or Create page for program', 'empowercast-network'));?></h2>
        <p style="color: black; font-weight: bold"><?php echo esc_html(__('Create new page: Create a new page linking to the the current program', 'empowercast-network'));?></p>
        <p style="color: black; font-weight: bold"><?php echo esc_html(__('Select existing page: Link a pre-created page to the current program', 'empowercast-network'));?></p>
        <br>
        <button class="warningButton" onclick="createPage(<?php echo esc_html($props['program_info']['program_id']); ?>, 'Program','createForm', '<?php echo esc_html($props['program_info']['program_title']); ?>')">
            <?php echo esc_html(__('Create New Page', 'empowercast-network'));?>
        </button>
        <a href="#TB_inline?&width=500&height=300&inlineId=selectPageBox" class="thickbox" title="Empowercast Network plugin"><button class="primaryButton"><?php echo esc_html(__('Select Existing Page', 'empowercast-network'));?></button></a>
    </div>

    <div class="selectPageBox" id="selectPageBox" style="display: none">
        <h2 class="thickboxTitle"><?php echo esc_html(__('Page To Link', 'empowercast-network'));?></h2>
        <form method="POST" id="selectForm">
            <p style="color: black; font-weight: bold"><?php echo esc_html(__('Select an existing page to link to current program', 'empowercast-network'));?></p>
            <br>
            <select class="dropdownChoice" name="pageID">
                <?php
                for ($i = 0; $i < count($availablePages); ++$i) {
                    ?>
                    <option
                        value="<?php echo esc_html($availablePages[$i]->ID); ?>"><?php echo esc_html($availablePages[$i]->post_title); ?></option>
                    <?php
                }
                ?>

            </select>
            <br>
            <p style="color: black; font-weight: bold"><?php echo esc_html(__('Remember to put this short code on your new page', 'empowercast-network'));?></p>
            <br>
            <input readonly value='<?php echo esc_html($props['program_info']['shortcode']);?>'>
            <input name="target" value="Program" hidden>
            <input name="targetId" value="<?php echo esc_html($props['program_info']['program_id']); ?>" hidden>
            <input name="redirectUrl" value="false" hidden>
        </form>

        <button type="submit" class="primaryButton" onclick="directStatus('Manage Program', 'selectForm', true)"><?php echo esc_html(__('Set Page', 'empowercast-network'));?></button>
        <p class="cancelButton" onclick="tb_remove();"><?php echo esc_html(__('Cancel', 'empowercast-network'));?></p>
    </div>
<?php
} else{
    ?>
    <div class="messageBox" id="choiceBox">
        <h2 class="boxTitle"><?php echo esc_html(__('Change Page or Unlink the page', 'empowercast-network'));?></h2>
        <p style="color: black; font-weight: bold"><?php echo esc_html(__('Change the link page to another link', 'empowercast-network'));?></p><br>
        <p style="color: black; font-weight: bold"><?php echo esc_html(__('Unlink the page linked to the current page', 'empowercast-network'));?></p>
        <br>
        <a href="#TB_inline?&width=500&height=300&inlineId=selectPageBox" class="thickbox" title="Empowercast Network plugin"><button class="primaryButton"><?php echo esc_html(__('Change Page', 'empowercast-network'));?></button></a>
        <a href="#TB_inline?&width=600&height=200&inlineId=confirmUnlink" class="thickbox" title="Empowercast Network plugin"><button class="warningButton"><?php echo esc_html(__('Unlink page', 'empowercast-network'));?></button></a>
        <a href="#TB_inline?&width=500&height=200&inlineId=confirmRemoval" class="thickbox" title="Empowercast Network plugin"><button class="warningButton"><?php echo esc_html(__('Remove program', 'empowercast-network'));?></button></a>
    </div>

    <div class="selectPageBox" id="selectPageBox" style="display: none">
        <h2 class="thickboxTitle"><?php echo esc_html(__('List Page Box', 'empowercast-network'));?></h2>
        <form method="POST" id="changeForm">
            <select class="dropdownChoice" name="pageID">
                <?php
                for ($i = 0; $i < count($availablePages); ++$i) {
                    ?>
                    <option
                        value="<?php echo esc_html($availablePages[$i]->ID); ?>"><?php echo esc_html($availablePages[$i]->post_title); ?></option>
                    <?php
                }
                ?>
            </select>
            <br>
            <p style="color: black; font-weight: bold"><?php echo esc_html(__('Remember to put this short code on your new page', 'empowercast-network'));?></p>
            <input readonly value='<?php echo esc_html($props['program_info']['shortcode']);?>'>
            <input name="target" value="Program" hidden>
            <input name="targetId" value="<?php echo esc_html($props['program_info']['program_id']); ?>" hidden>
            <input name="redirectUrl" value="false" hidden>
        </form>

        <button type="submit" class="ppn-back-button" onclick="directStatus('Manage Program', 'changeForm', true)"><?php echo esc_html(__('Save', 'empowercast-network'));?></button>
        <p class="ppn-back-button" onclick="tb_remove()"><?php echo esc_html(__('Cancel', 'empowercast-network'));?></p>
    </div>

    <div class="confirmUnlink" id="confirmUnlink" style="display: none">
        <h2 class="thickboxTitle"><?php echo esc_html(__('Confirm Unlink', 'empowercast-network'));?></h2>
        <form method="POST" id="createForm">
            <input name="target" value="Program" hidden>
            <input name="targetId" value="<?php echo esc_html($props['program_info']['program_id']); ?>" hidden>
            <input name="redirectUrl" value="false" hidden>
        </form>

        <p style="color: black; font-weight: bold"><?php echo esc_html(__('Are you sure you want to unlink the current page off the program?', 'empowercast-network'));?></p><br>
        <button type="submit" class="warningButton" onclick="confirmUnlink('createForm');directStatus('Manage Program', 'createForm')"><?php echo esc_html(__('Unlink page', 'empowercast-network'));?></button>
        <p class="ppn-back-button" onclick="tb_remove();"><?php echo esc_html(__('Cancel', 'empowercast-network'));?></p>
    </div>

    <div class="confirmRemoval" id="confirmRemoval" style="display: none;">
        <h2 class="thickboxTitle"><?php echo esc_html(__('Confirm removal of program from your Network', 'empowercast-network')); ?></h2>
        <form method="POST" id="removeForm" action="">
            <input name="target" value="Program" hidden>
            <input name="targetId" class="removeProgram" id="removeProgram"
                <?php
                echo 'value = ' . esc_html($props['program_info']['program_id']);
                ?>
                   hidden>
            <input name="redirectUrl" value="false" hidden>
        </form>
        <p><?php echo esc_html(__('Are you sure you want to remove this program off of your network?')); ?></p>
        <button type="submit" class="warningButton" onclick="confirmRemovalOfProgram(<?php echo esc_html($props['program_info']['program_id']); ?>);"><?php echo esc_html(__('Remove program', 'empowercast-network')); ?>
    </div>
<?php
}
?>
    <form method="POST" id="manageForm">
       <a class="ppn-back-button" href="<?php echo admin_url("admin.php?page=". urlencode(empowercast_admin_get_page()) ."&status=Select+Choice"); ?>">
        <p><?php echo '&#8592; ' . esc_html(__('Back', 'empowercast-network'));?></p></a>
    </form>
<?php add_thickbox(); ?>

<?php

if (isset($_POST['program_id']))
{
    $apiArray = empowercast_get_api_array();
    $apiUrl = $apiArray[0];
    $networkInfo = get_option('empowercast_network');
    $networkInfo['network_title'] = get_option('empowercast_network_title');
    $networkInfo['network_id'] = get_option('empowercast_network_id');
    $networkInfo['program_id'] = $_POST['program_id'];
    $results = EmpowercastNetworkDataBus::removeSpecificProgramInNetwork($apiUrl, false, $networkInfo, true);
    echo json_encode($results);
    exit;
}
?>
