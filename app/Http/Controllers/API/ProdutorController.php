<?php

namespace App\Http\Controllers\API;

use App\Models\Produtor;
use Illuminate\Http\Request;
use App\Models\Pessoa;
use App\Models\Usuario;
use App\Rules\CpfCnpj;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProdutorController extends BaseController
{

    public function index(Request $request)
    {
        $data = Produtor::person()
            ->when(!empty($request->search), function ($q) use ($request) {
                return  $q->where(function ($query) use ($request) {
                    return $query->where('pessoas.nome', 'LIKE', "%$request->search%")
                        ->orWhere('pessoas.cpf_cnpj', 'LIKE', "%$request->search%");
                });
            })
            ->when(!empty($request->start_date), function ($q) use ($request) {
                return $q->whereDate('usuarios.created_at', $request->start_date);
            })
            ->with('pessoa')
            ->orderBy('created_at', 'desc')
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
                $inputs['senhaUsuario'] = bcrypt($request->cpf_cnpj);

                $pessoa = Pessoa::updateOrCreate(
                    ['cpf_cnpj' => $request->cpf_cnpj],
                    $inputs
                );

                $inputs['pessoa_id'] = $pessoa->id;

                $produtor = Produtor::updateOrCreate(
                    ['pessoa_id' => $inputs['pessoa_id']],
                    $inputs
                );

                Usuario::updateOrCreate(
                    ['pessoa_id' => $pessoa->id],
                    $inputs
                );
                return $this->sendResponse($produtor, "Produtor criado com sucesso", 201);
            });
        } catch (Exception $e) {
            return $this->sendError("", $e->getMessage(), 500);
        }
    }


    public function show($id)
    {
        $item = Produtor::person()
            ->where('produtors.id', $id)
            ->firstOrFail();

        return $this->sendResponse($item);
    }

    public function destroy($id)
    {
        $item = Produtor::findOrFail($id);

        if (auth()->id() != $item->id) {
            try {
                $item->delete();
                return $this->sendResponse($item, "Produtor deletado com sucesso", 201);
            } catch (\Exception $e) {
                return $this->sendError("", $e->getMessage(), 500);
            }
        } else {
            return $this->sendError("", "Você não tem permissão para excluir esse produtor", 404);
        }
    }

    private function rules(Request $request, $primaryKey = null, bool $changeMessages = false)
    {
        $rules = [
            'nome' => ['required', 'max:120'],
            'cpf_cnpj' => ['required', 'max:14', new CpfCnpj],
        ];

        $messages = [];

        return !$changeMessages ? $rules : $messages;
    }
}
