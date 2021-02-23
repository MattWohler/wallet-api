<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $target
 * @property string $token
 * @property array $scopes
 * @property boolean $is_active
 */
class ApiToken extends Model
{
    /** @var array - The attributes that are mass assignable. */
    protected $fillable = [
        'name',
        'target',
        'token',
        'scopes',
        'is_active'
    ];

    /** @var array - The attributes that should be casted to native types. */
    protected $casts = [
        'id' => 'integer',
        'scopes' => 'array',
        'is_active' => 'boolean'
    ];
}
