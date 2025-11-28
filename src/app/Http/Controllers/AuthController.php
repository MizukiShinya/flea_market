<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AuthRequest;
use App\Models\User;

class AuthController extends Controller
{
    // ログインフォーム表示
    public function login(){
        return view('auth.login');
    }
    // 登録フォーム表示
    public function create(){
        return view('auth.register');
    }
    // ユーザー登録処理
    public function store(AuthRequest $request){
        $data=$request->validated();

        // ユーザー作成
        $user=User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // プロフィール自動作成後、作成したユーザーでログイン
        $user->profile()->create([]);
        Auth::login($user);

        // mypage.index にリダイレクト
        return redirect()->intended(route('mypage.profile'));
    }
    // ログイン処理
    public function authenticate(Request $request){
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        // 空白削除
        $credentials = array_map('trim', $credentials);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // プロフィール無ければ自動作成
            if (!$user->profile) {
                $user->profile()->create([]);
            }

            // ログイン後、常に profile をロード
            $user->load('profile');

            // もし login リンクに redirect が指定されていたらそこに飛ぶ
            if ($request->has('redirect')) {
                return redirect()->to($request->input('redirect'));
            }

            return redirect()->intended(route('item.index'));
        }
        return back()->withErrors([
            'email' => 'メールアドレスかパスワードが正しくありません。',
        ]);
    }
    // ログアウト処理
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // ログイン画面にリダイレクト
        return redirect()->route('login');
    }
}
