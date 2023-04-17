
<div wire:ignore class="m-2 me-1 ms-auto w-100">
    <div x-data x-ref="quill" x-init="quill = new Quill($refs.quill, {theme: 'snow',});
            quill.on('text-change', function () {
            $dispatch('quill-text-change', quill.root.innerHTML);
        });"
        x-on:quill-text-change.debounce.2000ms="@this.set('description', $event.detail)">

        {!! $slot !!}
    </div>
</div>
