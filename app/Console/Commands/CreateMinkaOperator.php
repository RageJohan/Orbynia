<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Validator;

class CreateMinkaOperator extends Command
{
    protected $signature = 'orbynia:create-operator {--name=} {--email=}';
    protected $description = 'Crea un operador del Panel de MINKA sin exponer su contraseña en la terminal.';

    public function handle(): int
    {
        $name = trim((string) ($this->option('name') ?: $this->ask('Nombre')));
        $email = strtolower(trim((string) ($this->option('email') ?: $this->ask('Correo'))));
        $password = (string) $this->secret('Contraseña del Panel de MINKA');
        $confirmation = (string) $this->secret('Repite la contraseña');

        $validator = Validator::make(compact('name', 'email', 'password', 'confirmation'), [
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', PasswordRule::min(12), 'same:confirmation'],
        ]);

        if ($validator->fails()) {
            $this->error($validator->errors()->first());
            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_minka_operator' => true,
        ]);
        $this->info('Operador creado. Su acceso es exclusivo del Panel de MINKA.');
        return self::SUCCESS;
    }
}