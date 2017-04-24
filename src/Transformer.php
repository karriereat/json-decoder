<?php

namespace Karriere\JsonDecoder;

interface Transformer
{
    /**
     * register field, array, alias and callback bindings.
     *
     * @param ClassBindings $classBindings
     */
    public function register(ClassBindings $classBindings);

    /**
     * @return string the full qualified class name that the transformer transforms
     */
    public function transforms();
}
