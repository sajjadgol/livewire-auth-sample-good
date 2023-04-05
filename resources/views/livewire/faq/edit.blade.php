@section('page_title')
    Edit FAQ
@endsection
<div class="container-fluid py-4 bg-gray-200">
    <div class="row mb-5">
        <div class="col-lg-9 col-12 mx-auto position-relative">
            @if (session('status'))
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-success alert-dismissible text-white mt-3" role="alert">
                        <span class="text-sm">{{ Session::get('status') }}</span>
                        <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            @endif
           
            <!-- Card Basic Info -->
            <div class="card mt-4" id="basic-info">
                <div class="card-body pt-5">
                    <form wire:submit.prevent="edit">

                        <div class="row ">
                            <div class="col-12  mb-4">
                                <div class="input-group input-group-static">
                                    <label>Title *</label>
                                    <input wire:model.lazy="faq.title" type="text" class="form-control" placeholder="Enter a Title">
                                </div>
                                @error('faq.title')
                                <p class='text-danger inputerror'>{{ $message }} </p>
                                @enderror
                            </div>

                            <div class="col-12  mb-4">
                                <div class="input-group input-group-static">
                                    <label>Description *</label>
                                    <div wire:ignore class="h-100 m-2 me-1 ms-auto w-100">
                                        <div x-data x-ref="quill" x-init="quill = new Quill($refs.quill, {theme: 'snow'});
                                                quill.on('text-change', function () {
                                                $dispatch('quill-text-change', quill.root.innerHTML);
                                               });"
                                               
                                            x-on:quill-text-change.debounce.200ms="@this.set('faq.descriptions', $event.detail)">
        
                                            {!! $faq->descriptions !!}
                                        </div>
                                    </div>
                                </div>
                                @error('faq.descriptions')
                                <p class='text-danger inputerror'>{{ $message }} </p>
                                @enderror
                            </div>        
                            
                            <div class="col-12  mb-4">
                                <div class="input-group input-group-static">
                                    <label >Role *</label>
                                    <select class="form-control input-group input-group-dynamic" wire:model.lazy="faq.role_type"  id="projectName" onfocus="focused(this)" onfocusout="defocused(this)">
                                        <option value=''>Choose Your Role</option>
                                        @foreach ($role as $value)
                                        <option value="{{ strtolower($value['name']) }}">{{ $value['name']}}</option>
                                        @endforeach
                                     </select>
                                </div>
                                @error('faq.role_type')
                                <p class='text-danger inputerror'>{{ $message }} </p>
                                @enderror
                            </div> 
                            
                            <div class="col-12 ">
                                <div class="form-group">
                                    
                                    <div class="form-check">
                                        <input wire:model="faq.status"  wire:loading.attr="disabled"  class="form-check-input" type="checkbox"  id="flexCheckFirst">
                                        <label class="form-check-label" for="flexCheckFirst">Status</label>
                                     </div>
                                </div>
                                @error('faq.status')
                                <p class='text-danger inputerror'>{{ $message }} </p>
                                @enderror
                            </div>    

                        </div>
        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-end mt-4">
                                    <a  href="{{ route('faq-management') }}" class="btn btn-light m-0">Cancel</a>
                                    <button  wire:loading.attr="disabled"  type="submit" name="submit" wire:click = "saveForm" class="btn bg-gradient-dark m-0 ms-2">
                                        <span wire:loading.remove wire:target="saveForm">Update FAQ</span>
                                        <span wire:loading wire:target="saveForm"><x-buttonSpinner></x-buttonSpinner></span>
                                        </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
 
        </div>
    </div>
</div>
@push('js') 
<script src="{{ asset('assets') }}/js/plugins/quill.min.js"></script>
@endpush
