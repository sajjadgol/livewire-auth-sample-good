
<span class="inline-flex rounded-md">
    <button
        {{ $attributes->merge([
            'type' => 'button',
            'class' => 'btn m-0 ms-2 duration-150 ease-in-out' . ($attributes->get('disabled') ? ' opacity-75 cursor-not-allowed' : ''),
        ]) }}
    >
        {{ $slot }}
    </button>
</span>
