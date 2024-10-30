<div class="lo-task{{= +data.status ? ' lo-task--done' : '' }}" data-listo="item"
     data-id="{{= data.id }}" data-rrule_id="{{= data.rrule_id }}" data-status="{{= data.status }}" data-collection="{{= data.collection }}">
    <div class="lo-task__name" data-listo="item-content">
        <div class="lo-task__name-title">
            <span data-listo="title">{{- data.title }}</span>
        </div>
        {{ if (data.description) { }}
        <div class="lo-task__name-desc" style="display:none">
            <div data-listo="description">{{= data.description
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/\r?\n/g, '<br>') }}
            </div>
        </div>
        {{ } else { }}
        <div class="lo-task__name-desc lo-task__name-desc--empty" style="display:none">
            <div data-listo="description"><?php echo __('No description','listowp');?></div>
        </div>
        {{ } }}
        <div class="lo-task__edit" data-listo="edit-item-form" style="display:none">
            <div class="lo-task__edit-form">
                <input class="lo-input" type="text" name="title" value="{{- data.title }}" placeholder="<?php echo esc_attr__('List name', 'listowp'); ?>.." />
                <textarea class="lo-input" name="description" rows="1" placeholder="<?php echo esc_attr__('Description', 'listowp' ); ?>..">{{- data.description }}</textarea>
            </div>
            <div class="lo-task__edit-actions">
                <div class="lo-tasks__delete lo-task__delete">
                    <div class="lo-tasks__delete-opt" data-listo="delete-item-form" style="display:none">
                        <span href="#" data-listo="delete-item-confirm"><?php echo __('Confirm', 'listowp'); ?></span>
                    </div>
                    <span href="#" class="lo-tasks__delete-toggle" data-listo="delete-item">
                        <div class="lo-tip lo-tip--left" aria-label="<?php echo esc_attr__('Delete', 'listowp'); ?>"></div>
                        <i class="fa-solid fa-trash-can"></i>
                    </span>
                    <span href="#" class="lo-tasks__delete-close" data-listo="delete-item-cancel" style="display:none">
                        <div class="lo-tip lo-tip--bottom" aria-label="<?php echo esc_attr__('Cancel', 'listowp'); ?>"></div>
                        <i class="fa-solid fa-xmark"></i>
                    </span>
                </div>

                <button class="lo-btn lo-btn--default lo-btn--link" data-listo="cancel"><?php echo __('Cancel', 'listowp'); ?></button>
                <button class="lo-btn lo-btn--default lo-btn--action" data-listo="save"><?php echo __('Save', 'listowp'); ?></button>
            </div>
        </div>

        <div class="lo-task__name-meta">
            <?php echo Listo_Frontend_Assets::get_instance()->get_template('item_due'); ?>
            {{ if (data.collection_title) { }}
            <div class="lo-task__icon lo-tip lo-tip--right" data-listo="item-collection" data-collection="{{- data.collection_id }}"
                    aria-label="{{- data.collection_title }}">
                {{ if (data.collection_icon) { }}
                <i class="{{- data.collection_icon }}"></i>
                {{ } else { }}
                <span>{{- data.collection_initials || '' }}</span>
                {{ } }}
            </div>
            {{ } }}
            <div class="lo-task__meta-actions" data-listo="meta-actions">
                <!-- Read more -->
                <button
                    class="lo-readmore lo-btn lo-tip lo-tip--bottom"
                    data-listo="description-toggle"
                    aria-label="<?php echo esc_attr__('Show details', 'listowp'); ?>"
                    aria-label-alt="<?php echo esc_attr__('Hide details', 'listowp'); ?>"
                    >
                    <i class="fa-solid fa-circle-info"></i>
                </button>

                <!-- Edit button -->
                <div class="lo-task__edit-toggle lo-tip lo-tip--bottom" data-listo="edit-item" aria-label="<?php echo esc_attr__('Edit', 'listowp'); ?>">
                    <i class="fa-solid fa-pencil"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Check -->
    <div class="lo-task__check-wrapper">
        <div class="lo-task__check" data-listo="item-check">
            <i class="fa-regular fa-circle"></i>
        </div>
    </div>
</div>
