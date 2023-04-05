@section('page_title')
    @if($this->account_status == 'waiting')
        Unverified {{Str::ucfirst($this->filter['role'] == '' ? 'User' :  $this->filter['role']) }}s 
    @else
        {{Str::ucfirst($this->filter['role'] == '' ? 'User' :  $this->filter['role']) }}s
    @endif
@endsection
<div class="container-fluid py-2" wire:init="init">
    <div class="row mt-4">
        <div class="col-12">
          <x-alert></x-alert> 
        <div class="card custom-card">
            <!-- Card header -->
            @include('livewire.user-management.filter')
            <!-- Card header end -->
            <div class="card-body pt-0">    
                <x-table id="users-list">
                    <x-slot name="head">
                        <x-table.heading> Photo
                        </x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('name')"
                            :direction="$sortField === 'name' ? $sortDirection : null"> Name
                        </x-table.heading>
                        <x-table.heading>Phone
                        </x-table.heading>
                        <x-table.heading>Status
                        </x-table.heading>
                        @if(strtolower($this->filter['role']) == 'driver')
                            <x-table.heading>Online/Offline
                            </x-table.heading>
                        @endif
                       
                        @if(empty($this->filter['role']))
                            <x-table.heading>Role
                            </x-table.heading>
                        @endif
                        <x-table.heading sortable wire:click="sortBy('created_at')"
                            :direction="$sortField === 'created_at' ? $sortDirection : null">
                            Creation Date
                        </x-table.heading>
                     
                        <x-table.heading>Actions</x-table.heading>
                    
                    </x-slot>

                    <x-slot name="body">
                        @foreach ($users as $user)
                        <x-table.row wire:key="row-{{ $user->id }}">
                            <x-table.cell class="position-relative text-sm font-weight-normal align-middle">
                                @if ($user->profile_photo)
                                <img src="{{ Storage::disk(config('app_settings.filesystem_disk.value'))->url($user->profile_photo)}}" alt="picture"
                                    class="avatar avatar-sm mt-2 ">
                                @else
                                <img src="{{ asset('assets') }}/img/default-avatar.png" alt="avatar"
                                    class="avatar avatar-sm mt-2 ">
                                @endif
                            </x-table.cell>
                            <x-table.cell><a href="{{ route('view-user', $user) }}">{{ $user->name }}</a></x-table.cell>                          
                            <x-table.cell>+{{$user->country_code}} {{ substr($user->phone , +(strlen($user->country_code)))  }}</x-table.cell>
                            <x-table.cell> 
                            @if ($user->id != auth()->id() || $user->id  != 1)
                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input" wire:loading.attr="disabled"  type="checkbox" id="flexSwitchCheckDefault35"  wire:change="statusUpdate({{ $user->id }},{{ $user->status}})"
                                        @if($user->status) checked="" @endif   @if($this->account_status == 'waiting') disabled @endif>
                                </div>
                                <br>
                                @if( isset($user->driver->account_status) && $user->driver->account_status == 'suspended' )
                                <div class="text-xxs text-warning text-center" >Suspended</div>
                                @endif
                            @endif
                            </x-table.cell>
                            @if(!empty($this->filter['role']) && strtolower($this->filter['role']) == 'driver')
                            <x-table.cell>
                                <span class="badge badge-dot me-4">
                                   @if($user->driver && $user->driver->is_live)
                                    <i class="bg-success"></i>
                                    <span class="text-dark text-xs">Online</span>
                                    @else
                                    <i class="bg-danger"></i>
                                    <span class="text-dark text-xs">Offline</span>
                                    @endif
                                </span>
                            </x-table.cell>
                            @endif

                            @if(empty($this->filter['role']))
                                <x-table.cell>{{ $user->getRoleNames()->implode(',') }}
                                </x-table.cell>
                            @endif
                            <x-table.cell>{{ $user->created_at->format(config('app_settings.date_format.value')) }}</x-table.cell>
                            <x-table.cell>                              
                            
                            @if($this->account_status == 'waiting')
                                @if ($user->driver->account_status != 'approved')                                    
                                <button type="button" class="btn btn-success btn-link" data-original-title="Approve" title="Approve" 
                                wire:click="applicationConfirm({{ $user->id }}, 'approved')">
                                    <i class="material-icons">check</i>
                                    <div class="ripple-container"></div>
                                </button>
                                @endif

                                @if ($user->driver->account_status != 'rejected')   
                                <button type="button" class="btn btn-danger btn-link" data-original-title="Reject" title="Reject"
                                wire:click="applicationConfirm({{ $user->id }}, 'rejected')">
                                    <i class="material-icons">close</i>
                                    <div class="ripple-container"></div>
                                </button>
                                @endif
                            @else
                                <div class="dropdown dropup dropleft">
                                    <button class="btn bg-gradient-default" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="material-icons">
                                            more_vert
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
 
                                        @can('view-user')
                                            <li><a class="dropdown-item"  data-original-title="view" title="view" href="{{ route('view-user', $user) }}">Edit</a></li>
                                        @endcan
                                        @can('revenues-management')
                                            @if(strtolower($this->filter['role']) == 'driver')
                                                <li><a class="dropdown-item"  data-original-title="user-revenue" title="user-revenue" href="{{ route('revenues-management', ['type' => strtolower($this->filter['role']),'id' => $user->id]) }}">Revenues</a></li>
                                            @endif
                                        @endcan
                                        @if ($user->id != auth()->id() || $user->id  != 1)
                                            <li><a class="dropdown-item text-danger"  data-original-title="Remove" title="Remove" wire:click="destroyConfirm({{ $user->id }})">Delete</a></li>
                                        @endif 
                                    </ul>
                                </div>
                            @endif    
                            </x-table.cell>
                        </x-table.row>
                        @endforeach
                    </x-slot>
                </x-table>

                    @if($users && $users->total() > 10)
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
                                    @if ($users)
                                    <div id="datatable-bottom">
                                        {{ $users->links() }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                
                    @if($users && $users->total() == 0)
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
 



