<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        // 1. バリデーションエラー (422) の出し分け設定
        $this->renderable(function (ValidationException $e) {
            $currentRequest = request();

            if ($currentRequest->is('api/*')) {
                // エラー内容（配列）を一度取り出します
                $errors = $e->errors();

                // ⭐ もし「性別」のエラーが含まれていたら、強制的に仕様書通りの文字に書き換えます
                if (isset($errors['gender'])) {
                    $errors['gender'] = ['性別の値が不正です'];
                }

                $firstError = collect($errors)->flatten()->first();
                $message = $firstError;

                if ($currentRequest->is('api/v1/contacts')) {
                    if ($currentRequest->isMethodSafe() === false && in_array($currentRequest->method(), ['POST', 'PUT'])) {
                        $extraCount = collect($errors)->flatten()->count() - 1;

                        $message = $extraCount > 0
                            ? "{$firstError} (and {$extraCount} more errors)"
                            : $firstError;
                    }

                    if ($currentRequest->isMethod('get')) {
                        // 【AP01: 一覧】の時は外側のメッセージを定型文にします
                        $message = 'The given data was invalid.';
                    }
                }

                return response()->json([
                    'message' => $message,
                    'errors' => $errors, // 書き換えたエラーを返します
                ], 422);
            }
        });
        $this->renderable(function (NotFoundHttpException $e) {
            // URLにapiが含まれている、またはPostmanなどのツールからのアクセスの場合は常にJSONを返します
            if (request()->is('api/*') || request()->wantsJson()) {
                return response()->json([
                    'error' => 'お問い合わせが見つかりませんでした。',
                ], 404);
            }
        });

    }
}
