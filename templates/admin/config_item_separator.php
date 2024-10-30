<div class="loa-opt__separator" data-type="{{= data.type }}" data-id="{{= data.id }}" data-parent="{{= data.parent || '' }}">
    <div class="loa-opt__separator-name">
        {{= data.label }}
    </div>
    {{ if (data.description) { }}
    <div class="loa-opt__separator-desc">{{= data.description }}</div>
    {{ } }}
</div>
