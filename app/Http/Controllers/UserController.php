<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;



class UserController extends Controller
{
    /**
     * mostrar usuarios
     */
    public function index()
    {
        //mostrar todo los usuarios registrados con trycatch
        try {
            $users = User::paginate(10);
            if ($users->count()===0) {
                //count() === 0
                $data = [
                    'status'=>400,
                    'message'=>'no hay ningun registro de estudiante'
                ];

                return response()->json($data,400);
            }
            return response()->json(
                [
                    'status' => true,
                    'data' => $users->items(),             // Solo los usuarios de esta página
                    'total' => $users->total(),            // Total de usuarios en la base
                    'pagina_actual' => $users->currentPage(), // Número de página actual
                    'ultima_pagina' => $users->lastPage(),

                ],200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'massage' => 'error en el servidor'], 500);
        }
    }

    /* forma personalizada que no trae todo el formato paginate paginate(10)
     'status' => true,
                'data' => $users->items(),             // Solo los usuarios de esta página
                'total' => $users->total(),            // Total de usuarios en la base
                'pagina_actual' => $users->currentPage(), // Número de página actual
                'ultima_pagina' => $users->lastPage(),
 */

 /* simplePaginate(10)
     'status' => true,
    'data' => $users->items(),              // Lista de usuarios actuales
    'pagina_actual' => $users->currentPage(),
    'siguiente_pagina' => $users->nextPageUrl(),
    'anterior_pagina' => $users->previousPageUrl(),

 */



    /**
     * Store crea un usuario
     */
    public function store(Request $request)
    {
        //
        $validar = Validator::make($request->all(),
        [
            'name'=>'required|min:5|max:100',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:8',

        ]);
        //ver si no hubo un error en las validaciones
        //porque si no cumple los requisito este if pone los errores
        if ($validar->fails()) {
            return response()->json([
                'status'=>false,
                'message'=>$validar->errors()
            ],422);
        }
//manejo de error es util para el crud por si dado caso hay error en la conexion
        try {
            $user=User::create($request->all());
            return response()->json([
                'status'=>true,
                'message'=>'usuario registrado con exito',
                'user'=>$user

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'=>false,
                'message'=>'error en el servidor',

            ],500);
        }
    }

    /**
     * muestra un solo usuarion con su id
     */
    public function show(string $id){

    try {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Usuario no encontrado'
            ], 404); // 404: No encontrado
        }

        return response()->json([
            'status' => true,
            'user' => $user
        ], 200); // 200: OK

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error en el servidor'
        ], 500); // 500: Error interno del servidor
    }
}

//editar un usuario con el id
public function update(Request $request, string $id){
    $user = User::findOrFail($id);

    $validar = Validator::make($request->all(),[
        'name'=>'required',
        'email'=>[
            'required',
            'email',
            'unique:users',
            Rule::unique('users')->ignore($user->id),
        ],
        'password'=>'nullable|min:8'
    ]);

    if ($validar->fails()) {
        return response()->json([
            'status'=>false,
            'message'=>$validar->errors()

        ],422);
    }

    try {
       $data= $request->all();
       if ($request->filled('password')) {
        $data['password']=bcrypt($request->password);
       }else{
        unset($data['password']);
       }
       $user->update($data);

       return response()->json([
        'status'=>true,
        'message'=>'usuario modificado con exito',
        'user'=>$data

       ],200);
    } catch (\Exception $th) {
        return response()->json([
            'status'=>false,
            'message'=>'error en el servidor'
        ],500);
    }

}


    /**
     * eliminar un usuario
     */
    public function destroy(string $id)
    {
        //
        try{
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'status'=>true,
            'message'=>'usuario eliminado'

        ],200);
    }catch(\Exception $e){
        return response()->json([
            'status'=>false,
            'message'=>'error en el servidor'

        ],500);
    }
    }
}
