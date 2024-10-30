{{ for (let i = 0; i < data.actions.length; i++) { }}
<button class="listo-btn {{= data.actions[i].primary ? 'listo-btn--primary' : '' }}"
        data-listo="btn-{{= data.actions[i].type }}">
    {{= data.actions[i].label }}
</button>
{{ } }}
