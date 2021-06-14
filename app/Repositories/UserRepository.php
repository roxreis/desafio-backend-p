<?php 
namespace App\Repositories;

use App\Models\User;
use PHPUnit\Framework\InvalidDataProviderException;

class UserRepository 
{
  final public function datasValidated(array $fields)
  {
      $model = new User();
      $model->where('email', $fields['email'])->first();
      $model->where('cpf', $fields['cpf'])->first();
      $model->where('cnpj', $fields['cnpj'])->first();

      if ($model) {
        return throw new InvalidDataProviderException('Erro - usuário ja consta no banco de dados!');
      }
   
  }

  
}
