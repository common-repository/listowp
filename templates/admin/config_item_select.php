<div class="loa-opt loa-opt--select" data-type="{{= data.type }}" data-id="{{= data.id }}" data-parent="{{= data.parent || '' }}">
    <div class="loa-opt__form">
        <label class="loa-opt__label" for="{{= data.id }}">
            {{= data.label }}
        </label>
        <div class="loa-opt__form-input">
            <select class="loa-opt__input loa-input" id="{{= data.id }}" >
                {{ for ( let key in data.options ) { }}
                    <option value="{{- key }}" {{ if(key==data.value) { }}selected{{ } }}>{{- data.options[key] }}</option>
                {{ } }}
            </select>

            {{ if (data.description) { }}
            <div class="loa-opt__desc">{{= data.description }}</div>
            {{ } }}
            <i class="fa-solid fa-circle-notch loa-spinner" data-loading style="display:none"></i>
            <i class="fa-solid fa-check loa-check" data-check style="display:none"></i>
        </div>
        <div class="loa-opt__form-actions">

            <button class="loa-opt__action loa-btn" data-id="{{= data.id }}" data-btn="cancel"
                    data-tooltip="<?php echo esc_attr(__('Cancel', 'listowp')); ?>" style="display:none">
                <i class="fa fa-xmark"></i>
            </button>
            <button class="loa-opt__action loa-btn loa-btn--action" data-id="{{= data.id }}" data-btn="save"
                    data-tooltip="<?php echo esc_attr(__('Save', 'listowp')); ?>" style="display:none">
                <i class="fa fa-check"></i>
            </button>
        </div>
    </div>
</div>
