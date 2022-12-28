<?php

namespace App\Http\Controllers\Admin;

use App\Models\Usuario;
use App\Models\Pessoa;
use App\Http\Controllers\Controller;
use App\Rules\CpfCnpj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
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
            ->orderBy('usuarios.nome')
            ->get();

            return $this->sendResponse($data);
    }

    public function store(Request $request)
    {
        $pessoa = Pessoa::where('cpf_cnpj',$request->cpf_cnpj)->first();

        Validator::make(
            $request->all(),
            $this->rules($request, $pessoa['id'])
        )->validate();

        DB::transaction(function () use ($request) {
            $inputs = $request->all();

            $pessoa = Pessoa::updateOrCreate(
                ['cpf_cnpj' => $request->cpf_cnpj],
                $inputs
            );

            $inputs['pessoa_id'] = $pessoa->id;
            $inputs['senhaUsuario'] = bcrypt($request->input('senhaUsuario'));

            $user = Usuario::updateOrCreate(
                ['pessoa_id' => $inputs['pessoa_id']],
                $inputs
            );
            return $this->sendResponse($user, "Usuário criado com sucesso", 201);
        });

    }

    public function show($id)
    {
        $item = Usuario::person()->findOrFail($id);

        return $this->sendResponse($item);
    }

    public function update(Request $request, $id)
    {
        $item = Usuario::findOrFail($id);

        Validator::make(
            $request->all(),
            $this->rules($request, $item->pessoa_id)
        )->validate();

        DB::transaction(function () use ($request, $item) {
            $inputs = $request->except(['cpf_cnpj', 'email']);

            $item->fill($inputs)->save();

            $pessoa = Pessoa::find($item->pessoa_id);
            $pessoa->fill($inputs)->save();

        });

        return $this->sendResponse($item, "Usuário atualizado com sucesso", 201);

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

    private function rules(Request $request, $primaryKey = null, bool $changeMessages = false)
    {
        $rules = [
            'nome' => ['required', 'max:120'],
            'cpf_cnpj' => ['required', 'max:14', new CpfCnpj],
            'senhaUsuario' => ['sometimes', 'required', 'min:8', 'confirmed'],
        ];

        $messages = [];

        return !$changeMessages ? $rules : $messages;
    }
}
