<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaymentLog extends Model
{
    protected $table = 'payment_logs';

    protected $fillable = [
        'order_id',
        'event',
        'result',
        'detail',
        'source',
    ];

    public static function record(int $orderId, string $event, string $result, string $detail, string $source = ''): void
    {
        try {
            static::ensureTable();
            static::create([
                'order_id' => $orderId,
                'event' => $event,
                'result' => $result,
                'detail' => mb_substr($detail, 0, 255),
                'source' => $source !== '' ? $source : null,
            ]);
        } catch (\Throwable $exception) {
            logger()->warning('Payment log failed', [
                'order_id' => $orderId,
                'event' => $event,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public static function ensureTable(): void
    {
        if (Schema::hasTable('payment_logs')) {
            return;
        }

        Schema::create('payment_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->index();
            $table->string('event', 64);
            $table->string('result', 16);
            $table->string('detail', 255)->nullable();
            $table->string('source', 32)->nullable();
            $table->timestamps();
        });
    }
}
