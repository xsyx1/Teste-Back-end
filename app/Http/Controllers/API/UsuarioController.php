<?php

namespace App\Http\Controllers\API;

use App\Models\Usuario;
use App\Models\Pessoa;
use App\Rules\CpfCnpj;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends BaseController
{

    public function index(Request $request)
    {
        $data =  Usuario::person()
            ->when(!empty($request->search), function ($q) use ($request) {
                return  $q->where(function ($query) use ($request) {
                    return $query->where('pessoas.nome', 'LIKE', "%$request->search%")
                        ->orWhere('pessoas.cpf_cnpj', 'LIKE', "%$request->search%");
                });
            })
            ->when(!empty($request->start_date), function ($q) use ($request) {
                return $q->whereDate('usuarios.created_at', $request->start_date);
            })
            ->orderBy('pessoas.nome')
            ->get();

        return $this->sendResponse($data);
    }

    public function store(Request $request)
    {
        $pessoa = Pessoa::where('cpf_cnpj', $request->cpf_cnpj)->first();

        Validator::make(
            $request->all(),
            $this->rules($request, $pessoa['id'] ?? null)
        )->validate();
        try {
            DB::transaction(function () use ($request) {
                $inputs = $request->all();

                $pessoa = Pessoa::updateOrCreate(
                    ['cpf_cnpj' => $request->cpf_cnpj],
                    $inputs
                );

                $inputs['pessoa_id'] = $pessoa->id;
                $inputs['nomeUsuario'] = $pessoa->nome;
                $inputs['senhaUsuario'] = bcrypt($request->input('senhaUsuario'));

                $user = Usuario::updateOrCreate(
                    ['pessoa_id' => $inputs['pessoa_id']],
                    $inputs
                );

                $success['token'] =  $user->createToken('MyApp')->accessToken;
                $success['name'] =  $user->nome;
                return $this->sendResponse($success, "Usuário criado com sucesso", 201);
            });
        } catch (Exception $e) {
            return $this->sendError("", $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $item = Usuario::person()->findOrFail($id);

        return $this->sendResponse($item);
    }

    public function destroy($id)
    {
        $item = Usuario::findOrFail($id);

        if (auth()->id() != $item->id) {
            try {
                $item->delete();
                return $this->sendResponse($item, "Usuário deletado com sucesso", 201);
            } catch (\Exception $e) {
                return $this->sendError("", $e->getMessage(), 500);
            }
        } else {
            return $this->sendError("", "Você não tem permissão para excluir esse usuario", 404);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomeUsuario' => 'required',
            'senhaUsuario' => 'required'
        ]);


        if ($validator->fails()) {
            return $this->sendError('Erro de validação', $validator->errors()->toArray(), 422);
        }

        $inputs = $request->all();

        $find = Usuario::where('nomeUsuario', $inputs['nomeUsuario'])->first();
        if (\Hash::check($inputs['senhaUsuario'], $find->senhaUsuario))
        {
            $token = $find->createToken(config('app.key'));

            return $this->sendResponse([
                'user' => $find->nomeUsuario,
                'token' => $token->accessToken
            ], "Login successfully.");
        } else {
            return $this->sendError('Usuario ou senha inválidos', [], 401);
        }

    }


    private function rules(Request $request, $primaryKey = null, bool $changeMessages = false)
    {
        $rules = [
            'nome' => ['required', 'max:120'],
            'cpf_cnpj' => ['required', 'max:14', new CpfCnpj],
            'senhaUsuario' => ['required', 'min:8'],
        ];

        $messages = [];

        return !$changeMessages ? $rules : $messages;
    }
}
