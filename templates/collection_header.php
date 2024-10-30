<div class="lo-tasks__header" data-listo="collection-header" style="display:none">
    <div class="lo-list__info">
        <!-- collection info -->
        <div class="lo-list__info-data" data-listo="collection-info" data-id="">
            <div class="lo-list__info-header">
                <?php echo Listo_Frontend_Assets::get_instance()->get_template('collection_custom_icon'); ?>
                <div class="lo-list__info-heading">
                    <div class="lo-list__info-title">
                        <span data-listo="title"></span>
                    </div>
                    <div class="lo-list__info-desc" data-listo="description" data-placeholder="<?php echo esc_attr__('No description', 'listowp'); ?>"></div>
                </div>
            </div>
            <div class="lo-list__info-count lo-list__count">
                <span class="lo-tip lo-tip--bottom" aria-label="<?php echo esc_attr__('Open tasks', 'listowp'); ?>">
                    <i class="<?php echo Listo_Model_Collections::meta_icon('all');?>"></i>
                    <span data-field="count_items"></span>
                </span>
                <span class="lo-tip lo-tip--bottom" data-listo="toggle-completed" aria-label="<?php echo esc_attr__('Done tasks', 'listowp'); ?>" style="cursor:pointer">
                    <i class="<?php echo Listo_Model_Collections::meta_icon('done');?>"></i>
                    <span data-field="count_items_done"></span>
                </span>
                <span class="lo-tip lo-tip--bottom" aria-label="<?php echo esc_attr__('Due tasks', 'listowp'); ?>">
                    <i class="<?php echo Listo_Model_Collections::meta_icon('due');?>"></i>
                    <span data-field="count_items_due"></span>
                </span>
                <button class="lo-readmore lo-btn lo-tip lo-tip--right" data-listo="description-toggle" style="display:none"
                        aria-label="<?php echo esc_attr__('Show full description', 'listowp'); ?>"
                        aria-label-alt="<?php echo esc_attr__('Hide full description', 'listowp'); ?>">
                    <i class="fa-solid fa-circle-info"></i>
                </button>
                <button class="lo-list__edit lo-btn lo-tip" data-listo="edit-collection" aria-label="<?php echo esc_attr__('Edit', 'listowp'); ?>">
                    <i class="fa-solid fa-pencil"></i>
                </button>
            </div>
        </div>
        <!-- edit collection form -->
        <div class="lo-tasks__edit" data-listo="edit-collection-form" style="display:none">
            <div class="lo-tasks__edit-form">
                <input class="lo-input" type="text" name="title" placeholder="<?php echo esc_attr__('List name', 'listowp'); ?>" />
                <span></span>
                <textarea class="lo-input" name="description" rows="1" placeholder="<?php echo esc_attr__('Description', 'listowp' ); ?>"></textarea>
            </div>
            <div class="lo-tasks__edit-actions">
                <div class="lo-tasks__delete" style="display:none">
                    <div class="lo-tasks__delete-opt" data-listo="delete-collection-form" style="display:none">
                        <button class="lo-btn" data-listo="delete-collection-confirm"><?php echo __('Delete list and tasks', 'listowp'); ?></button>
                    </div>
                    <button class="lo-tasks__delete-toggle lo-btn" data-listo="delete-collection">
                        <span class="lo-tip lo-tip--right" aria-label="<?php echo esc_attr__('Delete', 'listowp'); ?>"></span>
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                    <button class="lo-tasks__delete-close lo-btn" data-listo="delete-collection-cancel" style="display:none">
                        <span class="lo-tip lo-tip--right" aria-label="<?php echo esc_attr__('Cancel', 'listowp'); ?>"></span>
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <button class="lo-btn lo-btn--default lo-btn--link" data-listo="cancel"><?php echo __('Cancel', 'listowp'); ?></button>
                <button class="lo-btn lo-btn--default lo-btn--action" data-listo="save"><?php echo __('Save', 'listowp'); ?></button>
            </div>
        </div>
    </div>
    <!-- new item form -->
    <div class="lo-tasks__form">
        <div class="lo-tasks__add-form" data-listo="new-item-form" style="display:none">
            <input class="lo-input" type="text" name="title" value=""
                    data-default-value=""
                    placeholder="<?php echo esc_attr__('Task title', 'listowp'); ?>" />
            <div class="lo-tasks__add-form-actions">
                <button class="lo-tasks__add-form-action lo-btn lo-tip" data-listo="cancel"
                        aria-label="<?php echo esc_attr__('Cancel', 'listowp'); ?>">
                    <i class="fa-regular fa-circle-xmark"></i>
                </button>
                <button class="lo-tasks__add-form-action lo-btn lo-tip" disabled data-listo="save"
                        aria-label="<?php echo esc_attr__("Add", 'listowp'); ?>">
                    <i class="fa-solid fa-circle-plus"></i>
                </button>

            </div>
        </div>
    </div>
</div>
