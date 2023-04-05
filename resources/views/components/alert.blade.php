@if (Session::has('status'))
    <div class="alert alert-success alert-dismissible text-white mx-4" role="alert">
        <span class="text-sm">{{ Session::get('status') }}</span>
        <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"
            aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @elseif (Session::has('error'))
    <div class="alert alert-danger alert-dismissible text-white mx-4" role="alert">
        <span class="text-sm">{{ Session::get('error') }}</span>
        <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"
            aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
                       