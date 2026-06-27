<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('id');
        });

        foreach (User::all() as $user) {
            $user->forceFill([
                'code' => 'SPU' . str_pad($user->id, 3, '0', STR_PAD_LEFT)
            ])->saveQuietly();
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
