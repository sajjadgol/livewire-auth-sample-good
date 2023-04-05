@section('page_title')
    Messages
@endsection
<div class="container-fluid py-4" wire:init="init">
    <div class="row mt-4">
        <div class="{{ $conversationOrderId == 0 ? 'col-12' : 'col-8' }} ">
            <div class="card custom-card">
                <!-- Card header -->
                @include('livewire.messages.filter')
                <!-- Card header end -->
                <div class="card-body pt-0"> 
                <x-table>
                    <x-slot name="head">
                        <x-table.heading sortable wire:click="sortBy('order_number')"
                            :direction="$sortField === 'order_number' ? $sortDirection : null">Order Number
                        </x-table.heading> 
                        <x-table.heading> Role
                        </x-table.heading>                       
                       <x-table.heading sortable wire:click="sortBy('created_at')"
                            :direction="$sortField === 'created_at' ? $sortDirection : null">
                            Creation Date
                        </x-table.heading>
                        <x-table.heading>Actions</x-table.heading>
                    </x-slot>

                    <x-slot name="body">
                        @foreach ($messages as $message) 
                         <x-table.row wire:key="row-{{ $message->id }}">
                            <x-table.cell class="p-2"><a href="{{ route('order-details', $message->order) }}">#{{ $message->order_number }}</a></x-table.cell> 
                            <x-table.cell class="p-2">{{ $message->role }}</x-table.cell>                            
                            <x-table.cell class="p-2">{{ $message->created_at->format(config('app_settings.date_format.value'))  }}</x-table.cell>
                            <x-table.cell class="p-2">
                                @can('show-message')
                                <a  class="conversation-messages-view" href="javascript:;" wire:click="conversation({{$message->order_id}}, '{{$message->role}}', '{{$message->order_number}}')">
                                    <span class="material-symbols-outlined">
                                        chat
                                    </span>
                                </a>  
                                @if($conversationOrderId == $message->order_id && $message->role == $this->conversationRole)
                                    <span class="material-symbols-outlined">
                                        arrow_right_alt
                                    </span>   
                                @endif  
                                @endcan            
                            </x-table.cell>
                        </x-table.row>
                        @endforeach
                    </x-slot>
                </x-table>
                @if($messages && $messages->total() > 0)
                <div class="row mx-2">
                    <div class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start"><div class="dataTables_length" id="kt_ecommerce_sales_table_length">
                        <label>
                            <select  wire:model="perPage"  name="kt_ecommerce_sales_table_length" aria-controls="kt_ecommerce_sales_table" class="form-select form-select-sm form-select-solid">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </label>
                    </div>
                    </div>
                    <div class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                        <div class="dataTables_paginate paging_simple_numbers" id="kt_ecommerce_sales_table_paginate">
                            @if ($messages)
                            <div id="datatable-bottom">
                                {{ $messages->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                @if($messages && $messages->total() == 0)
                    <div>
                        <p class="text-center">No records found!</p>
                    </div>
                @endif
            </div>
            </div>
        </div>

        @if($conversationOrderId)
            <div class="col-4" >
                <div class="card ">
                    <div class="card-header d-flex align-items-center py-3">
                        <div class="d-block d-md-flex align-items-center">
                           <div class="mx-0 mx-md-3">
                                <a href="javascript:;" class="text-dark font-weight-800 text-sm">Message for Order number #{{$this->conversationOrderNumber}}</a>
                                <small class="d-block text-muted">{{$this->conversationRole}}</small>
                            </div>
                        </div>
                        <div class="text-end ms-auto">
                            <button wire:loading.attr="disabled" type="button" wire:click="destroyConversationConfirm('{{$conversationOrderId}}', '{{$conversationRole}}')" class="btn btn-sm bg-gradient-primary mb-0">
                               Delete
                            </button>
                        </div>
                    </div>
                    <hr class="dark horizontal">
                    <div class="card-body pt-3">  

                        <div class="conversation-messages h-full w-full overflow-y-auto" id="conversation-messages">
                            @foreach ($conversationMessages as  $conversations)                  
                                <div class="d-flex mt-3">
                                    <div class="flex-shrink-0">
                                        @if ($conversations->sender->profile_photo)
                                            <img src="{{ Storage::disk(config('app_settings.filesystem_disk.value'))->url($conversations->sender->profile_photo) }}" alt="avatar" class="avatar rounded-circle me-3" >
                                                @else
                                            <img src="{{ asset('assets') }}/img/default-avatar.png" alt="avatar" class="avatar rounded-circle me-3" >
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="h6 mt-0"><a href="{{ route('view-user',  $conversations->sender) }}">{{$conversations->sender->name}} @if($conversations->sender->id == auth()->user()->id) (You) @endif</a></h6>
                                        <p class="text-sm"> 

                                        @php $allowed = array('jpg','png','gif', 'pdf', 'jpeg');
                                        $ext = pathinfo($conversations->image, PATHINFO_EXTENSION); 
                                        @endphp
                                        @if(in_array( $ext, $allowed ) )
                                            <a target="_blank" href="{{ Storage::disk(config('app_settings.filesystem_disk.value'))->url($conversations->image)}}">   
                                                <img src="{{ Storage::disk(config('app_settings.filesystem_disk.value'))->url($conversations->image)}}" alt="picture"
                                                class="avatar avatar-xl me-2 rounded-3"> 
                                            </a>
                                        @else
                                            {{ $conversations->message }}
                                        @endif      

                                        </p>
                                        <div class="d-flex">
                                            <div>
                                                <i class="material-icons text-sm me-1 cursor-pointer">calendar_month</i>
                                            </div>
                                            <span class="text-sm me-2">{{ $conversations->created_at->format(config('app_settings.date_format.value').' '.config('app_settings.time_format.value'))  }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    

                        <div class="d-flex mt-4">
                            <div class="flex-shrink-0">
                            @if (auth()->user()->profile_photo)
                                <img src="{{ Storage::disk(config('app_settings.filesystem_disk.value'))->url(auth()->user()->profile_photo) }}" alt="avatar" class="avatar rounded-circle me-3" >
                                    @else
                                <img src="{{ asset('assets') }}/img/default-avatar.png" alt="avatar" class="avatar rounded-circle me-3" >
                            @endif
                                
                            </div>

                            <form wire:submit.prevent="send" class="d-flex w-100" onsubmit="conversationScroll()">
                                <div class="flex-grow-1 my-auto">                                   
                                    <div class="input-group input-group-static">
                                        <textarea  wire:model="textMessage"  class="form-control" placeholder="Write your comment" rows="4" spellcheck="false"></textarea>
                                    </div>
                                </div>
                                <button wire:loading.attr="disabled"  class="btn bg-gradient-primary btn-sm mt-auto mb-0 ms-2" id="conversation-send" type="submit" name="button"><i class="material-icons text-sm">send</i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        </div>
    </div>
    <x-loder ></x-loder>
</div>

@push('js') 
<script>
    
    $('.conversation-messages-view').click(function() {
        setTimeout(conversationScroll, 1000);       
    });
    
    function conversationScroll() {   
        const element = document.getElementById('conversation-messages');
        element.scrollTop = element.scrollHeight;
    }
</script>
@endpush
