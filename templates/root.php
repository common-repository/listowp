<?php
if(apply_filters('listowp_is_pro', FALSE) && !apply_filters('listowp_is_pro_valid', FALSE)) {
    // This never happens in Free
    ?>
    <a class="lo-activate" href="<?php echo admin_url('admin.php?page=listowp-license');?>">Please activate your ListoWP Pro license!</a>
<?php } else {

	if(Listo_User::get_id()) { ?>
        <?php $color_theme = Listo_Config::get('default_theme_select'); ?>
        <?php
		if(defined('LISTOWP_DEMO_MODE')) {
            if(stristr($_SERVER['REQUEST_URI'],'dark')) {
                $color_theme = 'dark';
            }
        }
        ?>
        <div class="lo-listowp lo-<?php echo $color_theme ? esc_attr($color_theme) : 'light' ?>">
            <div class="lo-wrapper" data-listo="wrapper">
                {{ if (data.enable_collections) { }}
                <div class="lo-sidebar" data-listo="sidebar">
                    <div class="lo-sidebar__toggle lo-tip lo-tip--right" data-listo="sidebar-collapse"
                            aria-label="<?php echo esc_attr(__('Collapse', 'listowp')); ?>"
                            aria-label-alt="<?php echo esc_attr(__('Expand', 'listowp')); ?>">
                        <i class="fa-solid fa-angles-left"></i>
                    </div>
                    <div class="lo-lists lo-lists--smart" data-listo="panel">
                        <div class="lo-lists__inner" data-listo="smart-collections"></div>
                    </div>
                    <div class="lo-lists__form">
                        <div class="lo-lists__add-form" data-listo="new-collection-form" style="display:none">
                            <input class="lo-input" type="text" name="title" value=""
                                data-default-value=""
                                placeholder="<?php echo esc_attr(__('List name', 'listowp')); ?>" />
                            <div class="lo-lists__add-form-actions">
                                <button class="lo-btn lo-lists__add-form-action lo-tip lo-tip--bottom" data-listo="cancel" aria-label="<?php echo esc_attr(__('Cancel', 'listowp')); ?>"><i class="fa-regular fa-circle-xmark"></i></button>
                                <button class="lo-btn lo-lists__add-form-action lo-tip lo-tip--bottom" disabled data-listo="save" aria-label="<?php echo esc_attr(__('Add', 'listowp')); ?>"><i class="fa-solid fa-circle-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="lo-lists__add-wrapper" data-listo="new-collection" style="display:none">
                        <div class="lo-lists__add">
                            <i class="fa-solid fa-circle-plus"></i><?php echo __('New list','listowp'); ?>
                        </div>
                    </div>
                    <div class="lo-lists lo-lists--custom" data-listo="panel">
                        <div class="lo-lists__inner" data-listo="collections"></div>
                    </div>
                </div>
                {{ } }}
                <div class="lo-main" data-listo="panel">
                    <div class="lo-main__actions">
                        <button class="lo-sidebar__switch lo-btn" data-listo="sidebar-toggle"
                            data-label-lists="<?php echo esc_attr__('Lists', 'listowp'); ?>"
                            data-label-tasks="<?php echo esc_attr__('Tasks', 'listowp'); ?>">
                            <i class="fa-solid fa-list-ul"></i>
                            <span><?php echo esc_attr__('Lists', 'listowp'); ?></span>
                        </button>
                        <button
                                class="lo-wrapper__toggle lo-btn lo-tip lo-tip--left"
                                data-listo="wrapper-toggle"
                                aria-label="<?php echo esc_attr__('Toggle full screen', 'listowp'); ?>"
                        >
                            <i class="fa-solid fa-up-right-and-down-left-from-center"></i>
                        </button>
                        <button class="lo-pref__toggle lo-btn lo-tip lo-tip--left" data-listo="preferences-toggle" aria-label="<?php echo esc_attr__('Preferences', 'listowp'); ?>">
                            <i class="fa-solid fa-gear"></i>
                        </button>
                    </div>
                    <?php echo Listo_Frontend_Assets::get_instance()->get_template('collection_header'); ?>
                    <div class="lo-tasks__add-wrapper" data-listo="new-item">
                        <div class="lo-tasks__add">
                            <i class="fa-solid fa-circle-plus"></i>
                            <?php echo __('New task','listowp'); ?>
                        </div>
                    </div>
                    <div class="lo-tasks" data-listo="items"></div>
                </div>
                <?php echo Listo_Frontend_Assets::get_instance()->get_template('preferences'); ?>
            </div>
            <div class="lo-footer">
	            <?php
                if(apply_filters('listowp_demo_mode', FALSE)) {
                    // This file is not present in Free version
                    require_once('demo.php');
                }
                ?>
            </div>
        </div>
	<?php }

	Listo_User_Preferences::get_instance();
}
?>
