@section('page_title')
  User Requests
@endsection
<div class="container-fluid py-4" wire:init="init">
    <div class="row mt-4">
        <div class="col-12">
            <div class="card custom-card">
                <!-- Card header -->
                @include('livewire.tickets.filter')
                <!-- Card header end -->
                <div class="card-body pt-0"> 
                <x-table>

                    <x-slot name="head">
                        <x-table.heading>Title
                        </x-table.heading> 
                        <x-table.heading>Customer Name
                        </x-table.heading>     
                        <x-table.heading> Status
                        </x-table.heading>               
                        <x-table.heading sortable wire:click="sortBy('created_at')"
                        :direction="$sortField === 'created_at' ? $sortDirection : null"> Date
                        </x-table.heading>                          
                        <x-table.heading>Actions</x-table.heading>
                    </x-slot>
                    <x-slot name="body">
                        @foreach ($tickets as $ticket)
                        <x-table.row wire:key="row-{{ $ticket->id }}">
                            <x-table.cell>{{ $ticket->title }} <br>
                                @if($ticket->category->name == 'update-mobile-number')
                                    <em> @php $newMobile = json_decode($ticket->content, true) @endphp
                                    {{  $ticket->user->country_code.' '.substr($ticket->user->phone , +(strlen($ticket->user->country_code)))  }} <b>-></b> {{ $newMobile['country_code']  }} {{ $newMobile['mobile']  }} 
                                    </em>
                                @endif
                            </x-table.cell> 
                            <x-table.cell><a href="{{ route('view-user',  $ticket->user) }}">{{ $ticket->user->name }}</a></x-table.cell> 
                            <x-table.cell>{{ ucfirst($ticket->status) }}</x-table.cell> 
                            <x-table.cell>{{ $ticket->created_at }}</x-table.cell> 
                            <x-table.cell>
                                <div class="dropdown dropup dropleft">
                                    <button class="btn bg-gradient-default" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="material-icons">
                                            more_vert
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @if($ticket->status == 'open')
                                            <li><a class="dropdown-item text-success"  data-original-title="Approve" title="Approve" wire:click="statusUpdate({{ $ticket }}, 'closed')">Approve</a></li>
                                            <li><a class="dropdown-item text-warning "  data-original-title="Reject" title="Reject" wire:click="statusUpdate({{ $ticket }}, 'rejected')">Reject</a></li>
                                            <li><hr></li>
                                        @endif
                                        <li><a class="dropdown-item text-danger"  data-original-title="Remove" title="Remove" wire:click="destroyConfirm({{ $ticket->id }})">Delete</a></li>
                                   </ul>
                                </div>   

                            </x-table.cell>
                        </x-table.row>
                        @endforeach
                    </x-slot>
                </x-table>
                @if($tickets && $tickets->total() > 10)
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
                            @if ($tickets)
                            <div id="datatable-bottom">
                                {{ $tickets->links() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                @if($tickets && $tickets->total() == 0)
                    <div>
                        <p class="text-center">No records found!</p>
                    </div>
                @endif
            </div>
            </div>
        </div>
    </div>
    <x-loder ></x-loder>
</div>
 