<?php

namespace App\Exceptions;

use RuntimeException;

/** Dilempar ketika sebuah business rule (BR) dilanggar. Pesannya aman ditampilkan ke pengguna. */
class AturanBisnisException extends RuntimeException
{
}
