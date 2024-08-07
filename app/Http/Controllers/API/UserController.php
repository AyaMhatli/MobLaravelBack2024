<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  

    public function index()
    {
        // Récupérer tous les utilisateurs
        $users = User::all();
        
        // Retourner une collection de ressources d'utilisateurs
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   /* public function store(Request $request)
    {
        // Valider les données envoyées par le client
        $request->validate([
            'office_id' => 'required|exists:offices,id',
            'department_id' => 'required|exists:departments,id',
            'role_id' => 'required',
            'role' => 'required',
            'name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'status' => 'required',
        ]);

        // Créer un nouvel utilisateur
     $user = User::create($request->all());

        // Retourner la ressource de l'utilisateur nouvellement créé
        return response()->json($user, 201);
    }*/

    
        public function getName($id)
        {
            $user = User::findOrFail($id);
    
            return response()->json(['name' => $user->name]);
        }
        public function getAuthenticatedUser()
        {
            $user = Auth::user();
    
            return response()->json([
                'name' => $user->name,
                'email' => $user->email,
                'username'=>$user->username,
                'avatar'=>$user->avatar,
            ]);
        }
        public function updateAuthenticatedUser(Request $request,$id)
        {
            $user = Auth::user();
    
            $validatedData = $request->validate([
                'username' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:6|confirmed',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate avatar

            ]);
    
            /*$user->name = $request->input('name');
            $user->email = $request->input('email');*/
            $user = User::findOrFail($id);
            $user->username = $validatedData['username'];
            $user->email = $validatedData['email'];
    
         /*   if ($request->filled('password')) {
                $user->password = Hash::make($request->input('password'));
            }*/
            if (!empty($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
            }
            if ($request->hasFile('avatar')) {

                $avatar = $request->file('avatar');
                $avatatName = time() . '.' . $avatar->getClientOriginalExtension();
                $avatar->move(public_path('images'), $avatarName);
                $user->$avatar = $avatarName;

            }


                // Delete the old avatar if it exists
               /* if ($user->avatar) {
                    Storage::delete('public/avatars/' . $user->avatar);
               
                
                }
                $file = $request->file('avatar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/avatars', $filename);
                $user->avatar = $filename;
            }*/
    
             $user->save();
            
    
           return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        
    }
      /*  public function getUserDepartment(Request $request)
        {
            $user = Auth::user();
            $department = $user->department;
    
            return response()->json([
                'department' => $department,
            ]);
        }*/
    public function getUserDepartment(Request $request)
{
    $user = Auth::user();
    $departmentName = $user->department ? $user->department->name : null;

    return response()->json([
        'department_name' => $departmentName,
    ]);
}
public function getUserDepartmentLetter(Request $request)
{
    $user = Auth::user();
    $departmentLetter = $user->department ? $user->department->letter : null;

    return response()->json([
        'department_letter' => $departmentLetter,
    ]);
}
 public function login(Request $request)
        {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
    
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }
        public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
    
    }

