<?php 

namespace App\Http\Controllers;

use App\Exceptions\IdleServiceException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\TransactionDeniedException;
use App\Exceptions\UserNotFoundException;
use Illuminate\Http\Request;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\Log;

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
      try {
        $result = $this->repository->transactionValidator($fields);
        return response()->json($result);
      } catch (UserNotFoundException | InsufficientBalanceException $exception) {
        return response()->json(['errors' => ['main' => $exception->getMessage()]], $exception->getCode());
      } catch (TransactionDeniedException | IdleServiceException $exception) {
        return response()->json(['errors' => ['main' => $exception->getMessage()]], 401);
      } catch (\Exception $exception) {
        Log::critical('[Transaction Wrong]', [
            'message' => $exception->getMessage()
        ]);
      }
    }
}