<?php 

namespace App\Repositories;

use App\Models\Customer;
use App\Models\Storekeeper;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Exceptions\TransactionDeniedException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\IdleServiceException;
use App\Exceptions\UserNotFoundException;
use App\Services\MockyService;
use App\Events\SendNotification;



class TransactionRepository 
{
  public function transactionValidator(array $data)
  {  
    $user = $this->getUser($data);
    if (!$user) {
      throw new UserNotFoundException('User/Payee Not Found', 404);
    }
 
    if ($this->checkOrderIsASelfPayment($data)){
      throw new TransactionDeniedException('You can not self payment', 401);
    }
 
    if ($this->checkWhoBeOrderPayee($data)) {
      throw new TransactionDeniedException('You can not be able to make payments, just receive.', 401);
    }

    $payerWallet = $this->getWalletCustomer($data);
    $payeeWallet = $this->getWalletStorekeeper($data);
    
    if (!$this->checkUserBalance($payerWallet, $data['value'])) {
      throw new InsufficientBalanceException('Insufficient balance to this transfer.', 422);
    }

    if (!$this->isServiceAbleToMakeTransaction()) {
      throw new IdleServiceException('Service is not responding. Try again later.');
    }

    return $this->makeTransaction($payeeWallet, $payerWallet, $data);
    
  }

  public function getUser($data): bool
  {
    $costumer = Customer::where('id',$data['payee_id'])->first();
    $storekeeper = Storekeeper::where('id',$data['payee_id'])->first();
    if ($costumer != null || $storekeeper != null){
      return true;
    } else {
      return false;
    }
  }

  public function checkWhoBeOrderPayee($data): bool
  {
    $userPayee = Storekeeper::where('id',$data['payer_id'])->first();
    if ($userPayee->type == 'Storekeeper') {
      return true;
    } else {
      return false;
    }
  }


  public function checkOrderIsASelfPayment($data): bool
  {
    if ($data['payer_id'] === $data['payee_id']) {
    return true;
    } else {
      return false;
    }
  }

  public function getWalletCustomer($data)
  {
    $payerWallet = Wallet::where('user_id',$data['payer_id'])->first();
    if ($payerWallet != null){
      return $payerWallet;
    } else {
      return $this->orderToCreateWalletCustomer($data);       
    }

  }

  public function getWalletStorekeeper($data)
  {
    $payeeWallet = Wallet::where('user_id',$data['payee_id'])->first();
    if ($payeeWallet != null){
      return $payeeWallet;
    } else {
      return $this->orderToCreateWalletStorekeeper($data); ;
    }
  }
  
  public function orderToCreateWalletCustomer($data)
  {
    $payerWallet = new Wallet;
    $payerWallet = $payerWallet->createCustomerWallet($data);
    return $payerWallet = Wallet::where('user_id',$data['payer_id'])->first(); 
     
  }

  public function orderToCreateWalletStorekeeper($data)
  {
    $payeeWallet = new Wallet;
    $payeeWallet = $payeeWallet->createStorekeeperWallet($data);
    return $payeeWallet = Wallet::where('user_id',$data['payee_id'])->first();  
          
  }

  private function checkUserBalance(Wallet $payerWallet, $value)
  {
    return $payerWallet->balance >= $value;
  }

  private function makeTransaction($payeeWallet, $payerWallet, array $data)
  {
    
      $transaction = Transaction::create([
        'payer_wallet_id' => $payerWallet->id,
        'payee_wallet_id' => $payeeWallet->id,
        'value' => $data['value']
      ]);
      
      $transaction->walletPayer->withdraw($data['value'], $payerWallet->id);
      $transaction->walletPayee->deposit($data['value'], $payeeWallet->id);

     event(new SendNotification($transaction));

     return $transaction;
       
  }

  private function isServiceAbleToMakeTransaction(): bool
  {
      $service = app(MockyService::class)->authorizeTransaction();
      return $service['message'] == 'Autorizado';
  }


}

