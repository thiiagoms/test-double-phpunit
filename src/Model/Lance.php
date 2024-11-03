<?php

declare(strict_types=1);

namespace Thiiagoms\Model;

use Thiiagoms\Model\Usuario;

class Lance
{
    private Usuario $usuario;

    private float $valor;

    public function __construct(Usuario $usuario, float $valor)
    {
        $this->usuario = $usuario;
        $this->valor = $valor;
    }

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function getValor(): float
    {
        return $this->valor;
    }
}
