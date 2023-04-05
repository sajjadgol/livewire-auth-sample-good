@section('page_title')
    Order Details
@endsection
<div class="container-fluid my-sm-n6 py-4"> 
  
    <div class="d-flex m-3">
        <div class="ms-auto d-flex">
            <div class="pe-4 mt-1 position-relative">
                <a onclick="history.back()" class="btn btn-icon btn-outline-dark ms-2"  >
                    <i class="material-icons text-xs position-relative">arrow_back_ios</i>
                    Back
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <x-loder></x-loder>
        <div class="col-lg-8 col-md-6 col-12 mx-auto">
            <div class="card mb-4">
                <div class="card-header p-3 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="w-50">
                            <h6>Order Details</h6>
                            <p class="text-sm mb-0">
                           Order no. <b>{{$order->order_number}}</b> from <b>{{ $order->created_at->format(config('app_settings.date_format.value').' '.config('app_settings.time_format.value')) }}</b>
                            </p>                            
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn btn-outline-{{$statusLabels[$order->order_status]}} dropdown-toggle" type="button" id="statusMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                {{  ucfirst(str_replace('_', ' ', $order->order_status)) }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="statusMenuButton">
                                @foreach($allStatus as $status) 
                                    <li ><a  href="javascript:void(0);"  class="dropdown-item change_status" data-value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status))}}</a></li>
                                @endforeach
                            </ul>
                       </div>

                    </div>
                </div>
                <div class="card-body p-3 pt-0">
                    <hr class="horizontal dark mt-0 mb-4">
                    <h6 class="mb-3">Items</h6>
                        <x-table wire:loading.table>
                            <x-slot name="head">
                                <x-table.heading> Name
                                </x-table.heading>
                                <x-table.heading> Qty
                                </x-table.heading>
                                <x-table.heading> Amuont
                                </x-table.heading>
                            </x-slot>
                            <x-slot name="body">
                                @foreach($order->orderProducts as $product) 
                                <x-table.row wire:key="row-{{$product->product_id }}">
                                   <x-table.cell>                                        
                                        <div class="d-flex">
                                            <div>
                                                @if ($product->image)
                                                    <img src="{{ Storage::disk(config('app_settings.filesystem_disk.value'))->url($product->image->image_path) }} " alt="picture"
                                                    class="avatar avatar-xl me-3">
                                                @else
                                                    <img src="{{ Storage::disk(config('app_settings.filesystem_disk.value'))->url(config('app_settings.product_image.value')) }}" alt="avatar"
                                                    class="avatar avatar-xl me-3">
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="text-lg mb-0 mt-2">{{ $product->product_name }}</h6>
                                                @if(!empty(json_decode($product->add_on_items, true)))
                                                    <p class="text-sm mb-3"><b>Addon Options:</b>
                                                            @php $addons = json_decode($product->add_on_items, true) @endphp
                                                            @foreach($addons as $addon)
                                                            <br><span>{{$addon['name']}} ({{\Utils::ConvertPrice($addon['price'])}})<b>,</b></span> 
                                                            @endforeach
                                                    </p>
                                                @endif
                                                @if(!empty(json_decode($product->remove_addon_items, true)))
                                                    <p class="text-sm mb-3"><b>Remove Options:</b>
                                                            @php $removeAddons = json_decode($product->remove_addon_items, true) @endphp
                                                            @foreach($removeAddons as $rAddon)
                                                            <br><span>{{$rAddon['name']}}<b>,</b></span>
                                                            @endforeach
                                                    </p>
                                                @endif
                                            </div>                                             
                                        </div>
                                    </x-table.cell>                                    
                                    <x-table.cell> {{  $product->qty }}</x-table.cell>                               
                                    <x-table.cell>{{  \Utils::ConvertPrice($product->total_amount) }}</x-table.cell>                              
                                </x-table.row>
                                @endforeach
                           </x-slot>
                        </x-table>
                    <hr class="horizontal dark mt-4 mb-4">                    
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12">
                            <h6 class="mb-3">Payment details</h6>                           
                            <ul class="list-group list-group-flush list my--3">
                                <li class="list-group-item px-0 border-0">
                                    <div class="row align-items-center">
                                        <div class="col  text-center">
                                            <p class="text-xs font-weight-bold mb-0">Method</p>                                           
                                        </div>
                                        <div class="col text-center">
                                            <p class="text-xs font-weight-bold mb-0">Amount</p>
                                        </div>
                                        <div class="col text-center">
                                            <p class="text-xs font-weight-bold mb-0">Status</p>
                                        </div>
                                    </div>
                                    <hr class="horizontal dark mt-3 mb-1">
                                </li>
                            @foreach($order->TransactionHistory as $transaction)

                                <li class="list-group-item px-0 border-0">
                                    <div class="row align-items-center">
                                        <div class="col  text-center">                                            
                                            <h6 class="text-sm font-weight-normal mb-0">{{ strtoupper($transaction->payment_method_code) }}</h6>
                                        </div>
                                        <div class="col text-center">
                                            <h6 class="text-sm font-weight-normal mb-0">{{ \Utils::ConvertPrice($transaction->amount) }}</h6>
                                        </div>
                                        <div class="col text-center dropdown">
                                            <button class="btn btn-outline-{{$orderPaymentStatusLabel[str_replace('-', '_',$transaction->status)]}} btn-sm dropdown-toggle m-0" type="button" id="statusButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                {{  ucfirst(str_replace('_', ' ', $transaction->status)) }}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="statusButton">
                                                @foreach($orderPaymentStatus as $paymentStatus) 
                                                    <li ><a  href="javascript:void(0);"  class="dropdown-item change_payment_status" data-value="{{ $paymentStatus }}" data-id = {{$transaction->id}}>{{ ucfirst(str_replace('_', ' ', $paymentStatus))}}</a></li>
                                                @endforeach
                                            </ul>
                                       </div>
                                    </div>
                                    <hr class="horizontal dark mt-3 mb-1">
                                </li> 
                            @endforeach 

                            </ul>
                        </div>

                        <div class="col-lg-6 col-md-6 col-12 ms-auto">
                            <h6 class="mb-3">Order Summary</h6>
                          

                            <div class="d-flex justify-content-between">
                                
                                <span class="mb-2 text-sm">
                                    Item Amount:
                                </span>
                                <span class="text-dark font-weight-bold ms-2">{{  \Utils::ConvertPrice($order->sub_total_amount) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="mb-2 text-sm">
                                    Delivery Amount:
                                </span>
                                <span class="text-dark ms-2 font-weight-bold">{{  \Utils::ConvertPrice($order->delivery_amount) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-sm">
                                    Taxes Amount:
                                </span>
                                <span class="text-dark ms-2 font-weight-bold">{{  \Utils::ConvertPrice($order->tax_amount) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <span class="mb-2 text-lg">
                                    Total Amount:
                                </span>
                                <span class="text-dark text-lg ms-2 font-weight-bold">{{  \Utils::ConvertPrice($order->total_amount) }}</span>
                            </div>
                            
                        </div>
                        

                    </div>

                    
                </div>
                
            </div>
            <div class="row">
                <div class="col-lg-4 col-lg-6 col-12 ">
                    <div class="card mb-4">
                        <div class="card-header p-3 pb-0">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <h6 class="mb-3">Customer Details</h6>
                                </div>
                                <div class="col-6 text-end">
                                @role('Admin')
                                    <a  href="{{ route('message-management', ['order_id' => $order->id, 'role' => 'Customer', 'order_number' => $order->order_number]) }}" class="btn btn-outline-info btn-sm mb-0" >
                                        Message
                                    </a>
                                @endrole
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3 pt-0">                    
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <span class="mb-2 text-sm">
                                            Name:
                                        </span>
                                   
                                        <span class="text-dark font-weight-bold ms-2">
                                        @role('Admin')
                                            <a href="{{ route('view-user', $order->user) }}">{{$order->user->name}}</a>
                                        @endrole   
                                        @role('Provider')
                                            {{$order->user->name}}
                                        @endrole  
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="mb-2 text-sm">
                                            Phone:
                                        </span>
                                        <span class="text-dark font-weight-bold ms-2">{{$order->user->phone}}</span>
                                    </div>
                                 </div>
                            </div>
                        </div>
                    </div>
                </div>
        
                <div class="col-lg-4 col-lg-6 col-12 ">
                    <div class="card mb-4">
                        <div class="card-header p-3 pb-0">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <h6 class="mb-3">Store Details</h6>
                                </div>
                                <div class="col-6 text-end">
                                @role('Admin')
                                    <a  href="{{ route('message-management', ['order_id' => $order->id, 'role' => 'Provider', 'order_number' => $order->order_number]) }}" class="btn btn-outline-info btn-sm mb-0" >
                                        Message
                                    </a>
                                 @endrole
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3 pt-0">                    
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <span class="mb-2 text-sm">
                                            Name: 
                                        </span>
                                        <span class="text-dark font-weight-bold ms-2">
                                        @role('Admin')<a href="{{  route('edit-store' , $order->store) }}">{{ $order->store->name}}</a>@endrole </span>
                                        @role('Provider'){{ $order->store->name}}@endrole
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="mb-2 text-sm">
                                            Address:
                                        </span>
                                        <span class="text-dark font-weight-bold ms-2">{{$order->store->storeAddress->address_line_1}}</span>
                                    </div> 
                                    <div class="d-flex justify-content-between">
                                        <span class="mb-2 text-sm">
                                            Phone:
                                        </span>
                                        <span class="text-dark font-weight-bold ms-2">{{$order->store->phone}}</span>
                                    </div> 
                                    <div class="d-flex justify-content-between">
                                        <span class="mb-2 text-sm">
                                            Email:
                                        </span>
                                        <span class="text-dark font-weight-bold ms-2">{{$order->store->email}}</span>
                                    </div> 
        
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        

        <div class="col-lg-4 col-md-6 col-12  mx-auto">

        @if($order->is_scheduled) 
            <div class="card mb-4">
                <div class="card-header p-3 pb-0">
                    <h6 class="mb-3">Schedule Details</h6>
                </div>
                <div class="card-body p-3 pt-0">                    
                    <div class="row">                        
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="mb-2 text-sm">
                                    Schedule Date:
                                </span>
                                <span class="text-dark font-weight-bold ms-2">{{ $order->scheduled_time->format(config('app_settings.date_format.value')) }}</span>
                            </div>                            
                        </div>  
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="mb-2 text-sm">
                                    Schedule Time:
                                </span>
                                <span class="text-dark font-weight-bold ms-2">{{ $order->scheduled_time->format(config('app_settings.time_format.value')) }}</span>
                            </div>                            
                        </div>                         
                    </div>
                </div>
            </div>
        @endif

            <div class="card mb-4">
                <div class="card-header p-3 pb-0">                    
                    <div class="row">
                        <div class="col-6 d-flex align-items-center">
                            <h6 class="mb-3">Driver Details</h6>
                        </div>
                        @if($driver)
                            <div class="col-6 text-end">
                            @role('Admin')
                                <a  href="{{ route('message-management', ['order_id' => $order->id, 'role' => 'Driver', 'order_number' => $order->order_number]) }}" class="btn btn-outline-primary btn-sm mb-0" >
                                    Message
                                </a>
                                @endrole
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body p-3 pt-0">                    
                    <div class="row">
                        @if($driver)
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="mb-2 text-sm">
                                    Name:
                                </span>
                                <span class="text-dark font-weight-bold ms-2">{{$driver->name}}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="mb-2 text-sm">
                                    Phone
                                </span>
                                <span class="text-dark font-weight-bold ms-2">{{$driver->phone}}</span>
                            </div> 
                        </div>
                        @else
                            <div class="col-12">
                                @if($order->order_status != "accepted")
                                    <p class="text-center">No driver assigned</p> 
                                @else
                                    <div class="text-center">
                                        <button type="button" class="btn bg-gradient-dark mb-0 me-4" data-bs-toggle="modal" data-bs-target="#addModalDriver">
                                            Add Driver
                                        </button>                    
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header p-3 pb-0">
                    <h6 class="mb-3">Delivery Address</h6>
                </div>
                <div class="card-body p-3 pt-0">                    
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                
                                <span class="mb-2 text-sm">
                                    Name:
                                </span>
                                <span class="text-dark font-weight-bold ms-2">{{ $order->getShippingAddress()['first_name'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="mb-2 text-sm">
                                    Phone
                                </span>
                                <span class="text-dark font-weight-bold ms-2">{{ $order->getShippingAddress()['phone'] }}</span>
                            </div> 

                            <div class="d-flex justify-content-between">
                                <span class="mb-2 text-sm">
                                    Address
                                </span>
                                <span class="text-dark font-weight-bold ms-2">{{ $order->getShippingAddress()['line_2_number_street'] }}</span>
                            </div> 

                        </div>
                    </div>
                </div>
            </div>


            <div class="card mb-4">
                <div class="card-header p-3 pb-0">
                    <h6 class="mb-3">Track order </h6>
                </div>
                <div class="card-body p-3 pt-0">                    
                    <div class="timeline timeline-one-side">
                        
                    @foreach ($order->orderUpdateHistory as $history) 
                       
                        <div class="timeline-block mb-3">
                            <span class="timeline-step">
                                <i class="material-icons text-secondary text-lg">notifications</i>
                            </span>
                            <div class="timeline-content">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">
                                    @if($history->title) 
                                        {{$history->title}} 
                                    @else   
                                       Order has been changed <b>{{ ucfirst($history->old_status) }}</b> to <b>{{  ucfirst($history->new_status) }}</b>.
                                    @endif
                                </h6>
                                <p class="text-secondary font-weight-normal text-xs mt-1 mb-0">{{$history->created_at->format(config('app_settings.date_format.value').' '.config('app_settings.time_format.value'))}} <span class="text-right">{{ $history->user->name }}</span></p>
                            </div>
                        </div>
                    @endforeach    
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="addModalDriver" tabindex="-1" role="dialog" aria-labelledby="exampleModalSignTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-m modal-dialog-scrollable " role="document">
            
        <div  class="modal-content">
            <div class="card-header pb-0 text-left">
                <h5 class="">Add Driver</h5>
                <p class="mb-0">Find a driver are not associated with order.</p>
            </div>
            <div class="modal-body p-0">
                <div class="card card-plain">            
                    <div class="card-body pb-3">    
    
                        <div class="input-group input-group-outline my-3 focused is-focused">
                            <label class="form-label">Search Provider</label>
                            <input type="text" wire:model.debounce.500ms="search"  id ="search" placeholder="Search by Name, Mobile Number"  class="form-control">
                        </div>  
        
                        @if($search != '')
                        @if(!empty($searchResultDrivers))
                                @foreach ($searchResultDrivers as $spKey => $searchDrivers) 
                                    <ul class="list-group list-group-flush list my--3">
                                        <li style="cursor: pointer;" class="list-group-item px-0 border-0" wire:click="selectedUser({{ $searchDrivers->user_id }})">
                                            <div class="row align-items-center searchResult">
                                                <div class="col-auto">                                            
                                                    @if($selected_user_id == $searchDrivers->user_id)
                                                        <span class="material-symbols-outlined text-success">
                                                            check_circle
                                                        </span>
                                                    @else
                                                        <span class="material-symbols-outlined">
                                                            radio_button_unchecked
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col">
                                                    <h6 class="text-sm font-weight-normal mb-0 searchProviders">{{ $searchDrivers->user->name }}</h6>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                @endforeach
                        @endif
                            @if($searchResultDrivers->count() == 0)
                                <p class="text-sm text-center">
                                    No record matched!
                                </p>
                            @endif
                        @endif              
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"  wire:loading.attr="disabled"  wire:click.prevent="resetField()" class="btn btn-light " data-bs-dismiss="modal">Close</button>
                <button type="button"  wire:loading.attr="disabled"  class="btn bg-gradient-dark submit" id="submitDriver"  @if(!$selected_user_id) disabled @endif wire:click="$emit('driverSubmit')"  >Submit</button>
            </div>
        
            </div>
        </div>
        </div>
    </div>

</div>
@push('js')
 
<script>
    $(function() {
        $('body').on('click', '.change_status', function() {
            var $this = $(this);
            var $value = $this.data('value');
            window.livewire.emit('statusUpdate', $value);
        });
    });
</script>
<script>
    $(function() {
        $('body').on('click', '.change_payment_status', function() {
            var $this = $(this);
            var $value = $this.data('value');
            var $id = $this.data('id');
            window.livewire.emit('paymentStatusUpdate', $value ,$id);
          
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#submitDriver').on('click',function(){
            $('#addModalDriver').modal('hide');
        });
    });
</script>
@endpush
