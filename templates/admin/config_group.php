<div class="loa-panel" data-id="{{= data.id }}">
    <div class="loa-panel__header">
        <div class="loa-panel__name">
            <i class="{{= data.icon }}"></i><span>{{= data.title }}</span>
        </div>
        {{ if(data.description) { }}
        <div class="loa-panel__desc">{{= data.description }}</div>
        {{ } }}
    </div>
    <div class="loa-panel__content">
        {{= data.content }}
    </div>
</div>
