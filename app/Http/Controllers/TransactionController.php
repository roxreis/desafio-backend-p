<?php 

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Repositories\TransactionRepository;


class TransactionController extends Controller
{
    /**
    * @var TransactionRepository
    */

    protected $repository;

    public function __construct(TransactionRepository $repository)
    {
      $this->repository = $repository;
    }

    public function transaction(Request $request)
    { 
      $this->validate($request, [
        'payer_id' => 'required|numeric',
        'payee_id' => 'required|numeric',
        'value'    => 'required|numeric'
      ]);

      $fields = $request->only(['payer_id', 'payee_id', 'value']);
      $result = $this->repository->transactionValidator($fields);
      return response()->json($result); 
    }
  
}
