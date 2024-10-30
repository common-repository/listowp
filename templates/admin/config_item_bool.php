<div class="loa-opt loa-opt--check" data-type="{{= data.type }}" data-id="{{= data.id }}" data-parent="{{= data.parent || '' }}">
    <div class="loa-opt__form {{= data.pro ? 'disabled' : '' }}">
        <label class="loa-opt__label" for="{{= data.id }}">
            {{= data.label }}
        </label>
        <div class="loa-opt__form-input">
            <i class="loa-checkbox__control fa-solid fa-toggle-{{= data.value > 0 ? 'on' : 'off' }}" data-listo="checkbox"
                {{= data.readonly ? 'data-readonly' : '' }}></i>
            <input class="loa-checkbox" type="checkbox" id="{{= data.id }}" value="1" {{= data.value > 0 ? 'checked' : '' }}
                {{= data.readonly ? 'disabled="disabled"' : '' }} style="display:none" />
            <i class="fa-solid fa-circle-notch loa-spinner" data-loading style="display:none"></i>
            <i class="fa-solid fa-check loa-check" data-check style="display:none"></i>
            {{ if (data.description) { }}
            <div class="loa-opt__desc">{{= data.description }}</div>
            {{ } }}
        </div>
    </div>
</div>
