<div  class="flex-center position-ref full-height">
	<div class="card-header m-2">
		<h6 class="m-0">Payment amount</h6>
		<h4>                                
			<span class="text-dark text-lg font-weight-bold">{{  \Utils::ConvertPrice($amount) }}</span>		
		</h4>
	</div>
	<x-hyper-pay-form amount="{{ $amount }}" 
		merchantTransactionId="{{ $transaction_id }}"
		testMode="EXTERNAL"
		customer.givenName="{{ $details['first_name'] }}"
		customer.email="{{ $user->email }}"
		customer.surname="{{ $details['last_name'] }}"
		billing.street1="{{ $details['line_2_number_street'] }}"
		/>
</div>
