<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Tag;

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
        // 로그인 중인 유저의 정보를 View로 전달
        $user = \Auth::user();
        // 메모 일람을 습득
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        return view('home', compact('user', 'memos'));
    }

    public function create()
    {
        // 로그인 중인 유저의 정보를 View로 전달
        $user = \Auth::user();
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        return view('create', compact('user', 'memos'));
    }

    public function store(Request $request)
    {
        $data = $request->all();
        // dd($data);
        // POSTされたデータをDB（memosテーブル）に挿入
        // MEMOモデルにDBへ保存する命令を出す

        // 같은 태그가 있는지 확인
        $exist_tag = Tag::where('name', $data['tag'])->where('user_id', $data['user_id'])->first();
        if(empty($exist_tag['id'])) {
            // 먼저 태그를 Insert
            $tag_id = Tag::insertGetId(['name' => $data['tag'], 'user_id' => $data['user_id']]);
        } else {
            $tag_id = $exist_tag['id'];
        }

        // 태그 ID를 memos 테이블에 넣는다
        $memo_id = Memo::insertGetId([
            'content' => $data['content'],
            'user_id' => $data['user_id'],
            'tag_id' => $tag_id,
            'status' => 1
        ]);
        
        // リダイレクト処理
        return redirect()->route('home');
    }

    public function edit($id)
    {
        // 해당하는 ID의 메모를 DB에서 습득
        $user = \Auth::user();
        $memo = Memo::where('status', 1)->where('id', $id)->where('user_id', $user['id'])->first();
        // dd($memo);
        $memos = Memo::where('user_id', $user['id'])->where('status', 1)->orderBy('updated_at', 'DESC')->get();
        // 습득한 메모를 View로 전달
        $tags = Tag::where('user_id', $user['id'])->get();
        return view('edit', compact('memo', 'user', 'memos', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $inputs = $request->all();
        Memo::where('id', $id)->update(['content' => $inputs['content'], 'tag_id' => $inputs['tag_id']]);
        return redirect()->route('home');
    }

    public function delete(Request $request, $id)
    {
        $inputs = $request->all();
        Memo::where('id', $id)->update(['status' => 2]);
        return redirect()->route('home')->with('success', '메모를 삭제했습니다.');
    }
}
