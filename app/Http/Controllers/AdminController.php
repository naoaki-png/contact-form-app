<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index(IndexContactRequest $request)
    {
        $categories = Category::all();
        $tags = Tag::all();

        $validated = $request->validated();

        if (session()->has('errors')) {
            view()->share('errors', session()->get('errors'));
        }

        $contacts = Contact::query()
            ->keyword($request->keyword)
            ->gender($request->gender)
            ->categoryId($request->category_id)
            ->date($request->date)
            ->with('category')
            ->paginate(7);

        return view('admin.index', compact('categories', 'tags', 'contacts'));
    }

    public function show($id)
    {
        $contact = Contact::with(['category', 'tags'])->findOrFail($id);

        return view('admin.show', compact('contact'));

    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->route('admin.index')->with('message', 'お問い合わせを削除しました。');
    }

    public function downloadCsv(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'keyword' => 'nullable|string|max:255',
            'gender' => 'nullable|integer|in:0,1,2,3',
            'category_id' => 'nullable|integer|exists:categories,id',
            'date' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return redirect()->route('admin.index')
                ->withErrors($validator)
                ->withInput();
        }

        // 3. 検索条件（フィルタ）を反映させるクエリを作成
        $query = Contact::with('category');

        // キーワード検索（氏名）
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->keyword.'%')
                    ->orWhere('last_name', 'like', '%'.$request->keyword.'%');
            });
        }

        // 性別検索（フィルタ未指定、または'all'の時は全件、0〜3に対応）
        if ($request->filled('gender') && $request->gender !== 'all') {
            $query->where('gender', $request->gender);
        }

        // カテゴリ検索
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 日付検索（仕様のdateを考慮。created_atの日付部分と比較する例）
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // フィルタ未指定時は、全件を新着順（latest）で取得
        $contacts = $query->latest()->get();

        // 4. ダウンロード応答を作成
        return response()->streamDownload(function () use ($contacts) {
            $stream = fopen('php://output', 'w');

            // Excel文字化け防止のBOMを追加
            fwrite($stream, pack('C*', 0xEF, 0xBB, 0xBF));

            // ヘッダー行（1行目）を出力
            fputcsv($stream, [
                'ID',
                '氏名',
                '性別',
                'メール',
                '電話',
                '住所',
                '建物',
                'カテゴリ',
                '内容',
                '作成日時',
            ]);

            // データの書き込み
            foreach ($contacts as $contact) {
                // 性別を数値（1, 2, 3）から文字列に変換
                $genderText = match ((int) $contact->gender) {
                    1 => '男性',
                    2 => '女性',
                    3 => 'その他',
                    default => '不明',
                };

                fputcsv($stream, [
                    $contact->id,
                    $contact->first_name.' '.$contact->last_name,
                    $genderText,
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building,
                    $contact->category ? $contact->category->content : 'なし',
                    $contact->detail,
                    $contact->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($stream);
        }, 'contacts_'.date('YmdHis').'.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
