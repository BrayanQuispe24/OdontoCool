<?php

namespace App\Traits;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @method static void created(\Closure|string $callback)
 * @method static void updated(\Closure|string $callback)
 * @method static void deleted(\Closure|string $callback)
 */
trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        static::created(function ($model) {
            static::logAction('CREACIÓN', $model);
        });

        static::updated(function ($model) {
            if ($model->isDirty()) {
                static::logAction('MODIFICACIÓN', $model);
            }
        });

        static::deleted(function ($model) {
            static::logAction('ELIMINACIÓN', $model);
        });
    }

    protected static function logAction(string $actionType, $model)
    {
        try {
            $user = Auth::user();
            $personaId = $user ? $user->persona_id : null;
            $moduleName = class_basename($model);

            $detalles = [
                'modelo_id' => $model->getKey(),
                'atributos' => $model->getAttributes(),
            ];

            if ($actionType === 'MODIFICACIÓN') {
                $detalles['cambios'] = $model->getChanges();
                $detalles['original'] = array_intersect_key($model->getRawOriginal(), $model->getChanges());
            }

            Bitacora::create([
                'persona_id' => $personaId,
                'accion' => "{$actionType} de registro en {$moduleName} (ID: {$model->getKey()})",
                'modulo' => $moduleName,
                'fecha' => now()->toDateString(),
                'hora' => now()->toTimeString(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'detalles' => $detalles,
            ]);
        } catch (\Exception $e) {
            logger()->error("Error writing audit log: " . $e->getMessage());
        }
    }
}
