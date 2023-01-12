<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Memo;
use App\Models\Tag;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 모든 메소드들이 호출되기 전에 먼저 호출되는 메소드
        view()->composer('*', function ($view) {
            // get the current user
            $user = \Auth::user();
            // インスタンス化
            $memoModel = new Memo();
            $memos = $memoModel->myMemo( \Auth::id() );
            
            // タグに取得
            $tagModel = new Tag();
            $tags = $tagModel->where('user_id', \Auth::id())->get();
            
            $view->with('user', $user)->with('memos', $memos)->with('tags', $tags);
        });
    }
}
