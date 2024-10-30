<div
    class="lo-list{{= data.count_items_due > 0 ? ' lo-list--due' : '' }}"
    data-listo="collection"
    data-id="{{= data.id }}"
    data-title="{{- data.title }}"
    data-description="{{- data.description }}"
    data-smart="{{= +data.smart }}"
    data-due="{{= +data.count_items_due }}"
    data-due-formatted="{{= +data.count_items_due_formatted }}"
    >
    <div class="lo-list__inner">
        <i class="lo-list__icon {{- data.icon }}" data-field="icon" style="--test:{{- data.color }}">
            <span data-field="initials">{{- data.initials || '' }}</span>
            <span class="lo-list__icon-count" data-field="count_items_due"{{= data.count_items_due > 0 ? '' : ' style="display:none"' }}>
                {{= data.count_items_due_formatted }}
            </span>
        </i>
        <div class="lo-list__name">
            <span data-field="title">{{- data.title }}</span>
            <div class="lo-list__count">
                <span>
                    <i class="<?php echo Listo_Model_Collections::meta_icon('all');?>"></i>
                    <span data-field="count_items">{{= data.count_items_formatted }}</span>
                </span>
                <span>
                    <i class="<?php echo Listo_Model_Collections::meta_icon('done');?>"></i>
                    <span data-field="count_items_done">{{= data.count_items_done_formatted }}</span>
                </span>
            </div>
        </div>
    </div>
</div>

<?php
