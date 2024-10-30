<div class="listo_admin_item" data-type="{{= data.type }}" data-id="{{= data.id }}" data-parent="{{= data.parent || '' }}">
    {{ if (data.description) { }}
    <p class="description">{{= data.description }}</p>
    {{ } }}
</div>
