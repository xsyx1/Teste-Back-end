<?php

namespace App\Http\Controllers\API;

use App\Models\Propriedade;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PropriedadeController extends BaseController
{

    public function index(Request $request)
    {
        $data =  Propriedade::when(!empty($request->nomePropriedade), function ($q) use ($request) {
                return $q->whereDate('nomePropriedade', $request->nomePropriedade);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse($data);
    }

    public function store(Request $request)
    {
        Validator::make(
            $request->all(),
            $this->rules($request, $pessoa['id'] ?? null)
        )->validate();

        try {
            DB::transaction(function () use ($request) {
                $propriedade = Propriedade::updateOrCreate($request->all());

                return $this->sendResponse($propriedade, "Propriedade criado com sucesso", 201);
            });
        } catch (Exception $e) {
            return $this->sendError("", $e->getMessage(), 500);
        }
    }


    public function show($id)
    {
        $item = Propriedade::query()
            ->where('id', $id)
            ->firstOrFail();

        return $this->sendResponse($item);
    }

    public function destroy($id)
    {
        $item = Propriedade::findOrFail($id);

        if (auth()->id() != $item->id) {
            try {
                $item->delete();
                return $this->sendResponse($item, "Propriedade deletado com sucesso", 201);
            } catch (\Exception $e) {
                return $this->sendError("", $e->getMessage(), 500);
            }
        } else {
            return $this->sendError("", "Você não tem permissão para excluir esse propriedade", 404);
        }
    }

    private function rules(Request $request, $primaryKey = null, bool $changeMessages = false)
    {
        $rules = [
            'nomePropriedade' => ['required', 'max:120'],
            'cadastroRural' => ['required', 'max:120'],
        ];

        $messages = [];

        return !$changeMessages ? $rules : $messages;
    }
}
