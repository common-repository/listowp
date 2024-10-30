{{ if (data.due) { }}
<div class="lo-task__meta lo-task__meta--date {{= data.due_past ? 'lo-task__meta--expired' : 'lo-task__meta--scheduled' }}" data-listo="item-due">
    <div class="lo-task__meta-info lo-tip lo-tip--right" aria-label="{{= data.due_formatted_long_time }}{{ if (data.rrule_label) { }} • {{= data.rrule_label }}{{ } }}">
        {{ if (data.rrule) { }}
        <i class="fa-solid fa-repeat"></i>
        {{ } else if (data.due_past) { }}
        <i class="fa-solid fa-circle-exclamation"></i>
        {{ } else { }}
        <i class="fa-solid fa-calendar-days"></i>
        {{ } }}
        {{= data.due_this_year ? data.due_formatted : data.due_formatted_long }}
    </div>

    <?php echo Listo_Frontend_Assets::get_instance()->get_template('item_due_popup'); ?>
</div>
{{ } else { }}
<div class="lo-task__meta lo-task__meta--date" data-listo="item-due">
    <div class="lo-task__meta-info lo-tip lo-tip--right" aria-label="<?php echo esc_attr__('No due date', 'listowp'); ?>">
        <i class="fa-regular fa-calendar"></i>
    </div>

    <?php echo Listo_Frontend_Assets::get_instance()->get_template('item_due_popup'); ?>
</div>
{{ } }}
