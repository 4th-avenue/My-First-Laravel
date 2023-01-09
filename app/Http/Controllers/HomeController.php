<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function create()
    {
        // 로그인 중인 유저의 정보를 View로 전달
        $user = \Auth::user();
        return view('create', compact('user'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        // dd($data);
        // POSTされたデータをDB（memosテーブル）に挿入
        // MEMOモデルにDBへ保存する命令を出す
        $memo_id = Memo::insertGetId([
            'content' => $data['content'],
            'user_id' => $data['user_id'],
            'status' => 1
        ]);
        
        // リダイレクト処理
        return redirect()->route('home');
    }
}
