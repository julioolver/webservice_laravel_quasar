<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    private $repository;

//    public function __construct(Formacao $formacao)
//    {
//        $this->repository = $formacao;
//    }

    public function index(Request $request)
    {
        return $request->user();
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = $this->validator($data);

        if ($validator->fails())
        {
            return $validator->errors();
        }

        $user = User::create(
            [
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => bcrypt($data['password']),
            ]
        );

        $user->token = $user->createToken($user->email)->accessToken;
        return $user;

    }

    public function login(Request $request)
    {
        $data = $request->all();

        $validator = $this->validator($data, 'login');

        if ($validator->fails())
        {
            return $validator->errors();
        }


        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']]))
        {
            $user = auth()->user();
            $user->token = $user->createToken($user->email)->accessToken;

            return $user;
        }else{
            return [
                'status' => false
            ];
        }
    }

    public function edit($id)
    {
        try
        {
            if (!($formacaoAcademica = $this->repository->find($id)))
            {

                return $this->mensagemErro();
            }

            $formAction = route('admin.formacoes.update', $formacaoAcademica->id);

            return view(
                'admin.formacoes.form', [
                                          'formAction'        => $formAction,
                                          'formacaoAcademica' => $formacaoAcademica,
                                      ]
            );
        }
        catch (\Exception $e)
        {
            $this->mensagemErro();
        }
    }

    public function update(Request $request, $id)
    {
        try
        {
            if (!($formacao = Formacao::find($id)))
            {
                $mensagem = "Houve um erro na alteração do registro.";
                return $this->mensagemErro();
            }

            $dados = $request->all();
            $formacao->fill($dados);
            $formacao->save();

            $mensagem = 'Registro alterado com sucesso';
            return $this->retornaMensagem('admin.formacoes.index', $mensagem);
        }
        catch (\Exception $e)
        {
            return $this->mensagemErro();
        }
    }

    public function destroy($id)
    {

        try
        {
            if (!($formacao = $this->repository->find($id)))
            {
                \Session::flash('mensagem_erro', 'Ooops... Ocorreu uma inconsistência para excluir :(');
                return redirect()->back();
            }
            $formacao->delete();

            \Session::flash('mensagem', 'Registro Excluído com sucesso');
            return redirect()->back();
            //return json_encode(['success' => true, 'mensagem' => $mensagem]);

        }
        catch (\Exception $e)
        {
            \Session::flash('mensagem_erro', 'Ooops... Ocorreu uma inconsistência para excluir :(');
            return redirect()->back();
        }
    }

    public function validator($data, $type = null)
    {
        switch ($type){
            case 'login':
                $dados = [
                    'email'    => 'required|string|email|max:255',
                    'password' => 'required|string|min:6',
                ];
            break;
            default:
                $dados =  [
                    'name'     => 'required|string|max:255',
                    'email'    => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:6|confirmed',
                ];
        }
        return Validator::make(
            $data, $dados
        );
    }
}
